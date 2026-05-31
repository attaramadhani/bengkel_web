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
        $filter = $request->get('filter', 'mingguan');
        $query = Transaksi::query();

        if ($filter === 'mingguan') {
            $week = $request->get('minggu', now()->format('Y-\WW'));
            try {
                $startOfWeek = \Carbon\Carbon::parse($week)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($week)->endOfWeek();
            } catch (\Exception $e) {
                $week = now()->format('Y-\WW');
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
            }
            $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            $label = 'Minggu ke-' . substr($week, 6) . ' (' . $startOfWeek->format('d M Y') . ' - ' . $endOfWeek->format('d M Y') . ')';

            // Pendapatan chart mingguan (populasi 7 hari)
            $revenueRaw = Transaksi::select(
                    DB::raw('CAST(created_at AS DATE) as date_label'),
                    DB::raw('SUM(total_pembayaran) as total')
                )
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->get();

            $revenueData = collect();
            for ($date = clone $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
                $dateStr = $date->toDateString();
                $match = $revenueRaw->firstWhere('date_label', $dateStr);
                $revenueData->push((object)[
                    'label' => $date->format('D, d M'),
                    'total' => $match ? (float)$match->total : 0.0
                ]);
            }
            $chartTitle = 'Tren Pendapatan Mingguan';
        } elseif ($filter === 'bulanan') {
            $bulan = $request->get('bulan', now()->format('Y-m'));
            try {
                $startOfMonth = \Carbon\Carbon::parse($bulan . '-01')->startOfMonth();
                $endOfMonth = \Carbon\Carbon::parse($bulan . '-01')->endOfMonth();
            } catch (\Exception $e) {
                $bulan = now()->format('Y-m');
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
            }
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            $label = 'Bulan ' . $startOfMonth->format('F Y');

            // Pendapatan chart bulanan (populasi semua tanggal bulan ini)
            $revenueRaw = Transaksi::select(
                    DB::raw('CAST(created_at AS DATE) as date_label'),
                    DB::raw('SUM(total_pembayaran) as total')
                )
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->get();

            $revenueData = collect();
            for ($date = clone $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $dateStr = $date->toDateString();
                $match = $revenueRaw->firstWhere('date_label', $dateStr);
                $revenueData->push((object)[
                    'label' => $date->format('d'),
                    'total' => $match ? (float)$match->total : 0.0
                ]);
            }
            $chartTitle = 'Tren Pendapatan Bulanan';
        } else { // tahunan
            $tahun = $request->get('tahun', now()->year);
            $query->whereYear('created_at', $tahun);
            $label = 'Tahun ' . $tahun;

            // Pendapatan chart tahunan (populasi 12 bulan)
            $revenueRaw = Transaksi::select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month_num'),
                    DB::raw('SUM(total_pembayaran) as total')
                )
                ->whereYear('created_at', $tahun)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
                ->get();

            $revenueData = collect(range(1, 12))->map(function ($m) use ($revenueRaw) {
                $match = $revenueRaw->first(fn($item) => (int)$item->month_num == $m);
                return (object)[
                    'label' => \Carbon\Carbon::create()->month($m)->format('M'),
                    'total' => $match ? (float)$match->total : 0.0
                ];
            });
            $chartTitle = 'Tren Pendapatan Tahunan';
        }

        // Summary
        $totalPendapatan = (clone $query)->sum('total_pembayaran');
        $totalTransaksi = (clone $query)->count();

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
            'filter', 'label', 'totalPendapatan', 'totalTransaksi', 'transaksis', 'chartTitle'
        ));
    }
}
