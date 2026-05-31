<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\Jasa;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['user', 'details'])->latest()->get();

        // Sinkronisasi otomatis status transaksi Midtrans yang masih pending (dibuat dalam 24 jam terakhir)
        foreach ($transaksis as $transaksi) {
            if ($transaksi->metode_bayar === 'midtrans' && $transaksi->status_bayar === 'pending') {
                if ($transaksi->created_at && $transaksi->created_at->gt(now()->subDay())) {
                    try {
                        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

                        $status = \Midtrans\Transaction::status($transaksi->id_transaksi);
                        if (isset($status->transaction_status)) {
                            $trxStatus = $status->transaction_status;
                            if ($trxStatus == 'capture' || $trxStatus == 'settlement') {
                                $transaksi->update(['status_bayar' => 'lunas']);
                            } elseif ($trxStatus == 'expire' || $trxStatus == 'cancel' || $trxStatus == 'deny') {
                                $transaksi->update(['status_bayar' => 'gagal']);
                            }
                        }
                    } catch (\Exception $e) {
                        // Lewati jika ada kendala koneksi ke API Midtrans
                    }
                }
            }
        }

        // Ambil ulang data transaksi setelah sinkronisasi
        $transaksis = Transaksi::with(['user', 'details'])->latest()->get();
        return view('transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $jasas = Jasa::all();
        $midtransClientKey = env('MIDTRANS_CLIENT_KEY');
        return view('transaksi.create', compact('barangs', 'jasas', 'midtransClientKey'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:barang,jasa',
            'items.*.id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
            'metode_bayar' => 'required|in:cash,midtrans',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            $transaksi = Transaksi::create([
                'id_user' => $user->id_user,
                'total_pembayaran' => 0,
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => $request->metode_bayar === 'cash' ? 'lunas' : 'pending',
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $subtotal = 0;
                $id_barang = null;
                $id_jasa = null;

                if ($item['type'] == 'barang') {
                    $barang = Barang::findOrFail($item['id']);
                    if ($barang->stok < $item['qty']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
                    }
                    $subtotal = $barang->harga_jual * $item['qty'];
                    $id_barang = $barang->id_barang;
                    $barang->decrement('stok', $item['qty']);
                } else {
                    $jasa = Jasa::findOrFail($item['id']);
                    $subtotal = $jasa->harga_jasa * $item['qty'];
                    $id_jasa = $jasa->id_jasa;
                }

                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $id_barang,
                    'id_jasa' => $id_jasa,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $transaksi->update(['total_pembayaran' => $total]);

            // Jika Midtrans, buat Snap Token
            if ($request->metode_bayar === 'midtrans') {
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $transaksi->id_transaksi,
                        'gross_amount' => (int) $total,
                    ],
                    'customer_details' => [
                        'first_name' => $user->username,
                    ],
                ];

                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                    return response()->json([
                        'status' => 'midtrans',
                        'snap_token' => $snapToken,
                        'transaksi_id' => $transaksi->id_transaksi,
                    ]);
                } catch (\Exception $e) {
                    // Bypass: Jika Midtrans gagal (offline), fallback ke cash
                    $transaksi->update([
                        'metode_bayar' => 'cash',
                        'status_bayar' => 'lunas',
                    ]);
                    return response()->json([
                        'status' => 'bypass',
                        'message' => 'Midtrans offline, transaksi disimpan sebagai Cash.',
                        'transaksi_id' => $transaksi->id_transaksi,
                    ]);
                }
            }

            // Cash langsung selesai
            return response()->json([
                'status' => 'cash',
                'message' => 'Transaksi berhasil disimpan!',
                'transaksi_id' => $transaksi->id_transaksi,
            ]);
        });
    }

    // Callback dari Midtrans setelah pembayaran selesai
    public function midtransCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        // Format gross_amount agar selalu presisi dengan 2 digit desimal sesuai standar Midtrans signature
        $grossAmount = number_format((float) $request->gross_amount, 2, '.', '');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $grossAmount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaksi = Transaksi::find($request->order_id);
            if ($transaksi) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaksi->update(['status_bayar' => 'lunas']);
                } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
                    $transaksi->update(['status_bayar' => 'gagal']);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function export(Request $request)
    {
        $filter = $request->get('filter');
        $query = Transaksi::with(['user', 'details.barang', 'details.jasa']);

        if ($filter) {
            if ($filter === 'harian') {
                $tanggal = $request->get('tanggal', now()->toDateString());
                $query->whereDate('created_at', $tanggal);
                $filename = "transaksi-harian-{$tanggal}.csv";
            } elseif ($filter === 'bulanan') {
                $bulan = $request->get('bulan', now()->format('Y-m'));
                $query->whereYear('created_at', substr($bulan, 0, 4))
                      ->whereMonth('created_at', substr($bulan, 5, 2));
                $filename = "transaksi-bulanan-{$bulan}.csv";
            } else { // tahunan
                $tahun = $request->get('tahun', now()->year);
                $query->whereYear('created_at', $tahun);
                $filename = "transaksi-tahunan-{$tahun}.csv";
            }
        } else {
            $filename = "semua-transaksi-" . now()->format('Y-m-d-His') . ".csv";
        }

        $transaksis = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transaksis) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Tanggal',
                'ID Transaksi',
                'Kasir',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Rincian Item (Qty)',
                'Total Pembayaran (Rp)'
            ]);

            foreach ($transaksis as $trx) {
                $itemDetails = [];
                foreach ($trx->details as $detail) {
                    if ($detail->barang) {
                        $itemDetails[] = $detail->barang->nama_barang . " (x" . $detail->qty . ")";
                    } elseif ($detail->jasa) {
                        $itemDetails[] = $detail->jasa->nama_jasa . " (x" . $detail->qty . ")";
                    }
                }
                
                fputcsv($file, [
                    $trx->created_at->format('Y-m-d H:i:s'),
                    $trx->id_transaksi,
                    $trx->user->username ?? 'System',
                    strtoupper($trx->metode_bayar),
                    strtoupper($trx->status_bayar),
                    implode(", ", $itemDetails),
                    $trx->total_pembayaran
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'details.barang', 'details.jasa']);

        // Sinkronisasi otomatis status transaksi Midtrans yang masih pending ke API Midtrans
        if ($transaksi->metode_bayar === 'midtrans' && $transaksi->status_bayar === 'pending') {
            try {
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

                $status = \Midtrans\Transaction::status($transaksi->id_transaksi);
                if (isset($status->transaction_status)) {
                    $trxStatus = $status->transaction_status;
                    if ($trxStatus == 'capture' || $trxStatus == 'settlement') {
                        $transaksi->update(['status_bayar' => 'lunas']);
                    } elseif ($trxStatus == 'expire' || $trxStatus == 'cancel' || $trxStatus == 'deny') {
                        $transaksi->update(['status_bayar' => 'gagal']);
                    }
                }
            } catch (\Exception $e) {
                // Lewati jika ada kendala koneksi ke API Midtrans
            }
        }

        return view('transaksi.show', compact('transaksi'));
    }
}
