<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalJasa = Jasa::count();
        $totalTransaksi = Transaksi::count();
        $pendapatan = Transaksi::sum('total_pembayaran');
        
        $recentTransactions = Transaksi::with('user')->latest()->take(5)->get();
        $lowStockBarang = Barang::where('stok', '<', 5)->get();

        return view('dashboard', compact(
            'totalBarang', 
            'totalJasa', 
            'totalTransaksi', 
            'pendapatan', 
            'recentTransactions',
            'lowStockBarang'
        ));
    }
}
