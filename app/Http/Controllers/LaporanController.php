<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\Jasa;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'harian');
        $tanggal = $request->get('tanggal', now()->toDateString());

        // Build query berdasarkan filter
        $query = Transaksi::query();

        if ($filter === 'harian') {
            $query->whereDate('created_at', $tanggal);
            $label = 'Hari ini (' . \Carbon\Carbon::parse($tanggal)->format('d M Y') . ')';
        } elseif ($filter === 'bulanan') {
            $bulan = $request->get('bulan', now()->format('Y-m'));
            $query->whereYear('created_at', substr($bulan, 0, 4))
                  ->whereMonth('created_at', substr($bulan, 5, 2));
            $label = 'Bulan ' . \Carbon\Carbon::parse($bulan . '-01')->format('F Y');
        } else { // tahunan
            $tahun = $request->get('tahun', now()->year);
            $query->whereYear('created_at', $tahun);
            $label = 'Tahun ' . $tahun;
        }

        // Summary
        $totalPendapatan = (clone $query)->sum('total_pembayaran');
        $totalTransaksi = (clone $query)->count();

        // 1. Pendapatan chart
        $revenueData = Transaksi::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_pembayaran) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 2. Top 5 Barang Terlaris
        $topBarang = DetailTransaksi::with('barang')
            ->whereNotNull('id_barang')
            ->select('id_barang', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('id_barang')
            ->orderBy('total_qty', 'DESC')
            ->take(5)
            ->get();

        // 3. Top 5 Jasa Terpopuler
        $topJasa = DetailTransaksi::with('jasa')
            ->whereNotNull('id_jasa')
            ->select('id_jasa', DB::raw('COUNT(*) as total_count'))
            ->groupBy('id_jasa')
            ->orderBy('total_count', 'DESC')
            ->take(5)
            ->get();

        // 4. Daftar Transaksi sesuai filter
        $transaksis = $query->with(['user', 'details'])->latest()->get();

        return view('laporan.index', compact(
            'revenueData', 'topBarang', 'topJasa',
            'filter', 'label', 'totalPendapatan', 'totalTransaksi', 'transaksis'
        ));
    }
}
