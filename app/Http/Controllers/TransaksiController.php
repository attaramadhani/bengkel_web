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
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaksi = Transaksi::find($request->order_id);
            if ($transaksi) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaksi->update(['status_bayar' => 'lunas']);
                } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                    $transaksi->update(['status_bayar' => 'gagal']);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['user', 'details.barang', 'details.jasa']);
        return view('transaksi.show', compact('transaksi'));
    }
}
