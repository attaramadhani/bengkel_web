@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Overview')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Barang</div>
        <div class="stat-value">{{ $totalBarang }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Layanan Jasa</div>
        <div class="stat-value">{{ $totalJasa }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Transaksi Selesai</div>
        <div class="stat-value">{{ $totalTransaksi }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp {{ number_format($pendapatan, 0, ',', '.') }}</div>
    </div>
</div>

<div class="detail-grid">
    <!-- Recent Transactions -->
    <div>
        <h2 class="section-title"><i data-lucide="history"></i> Transaksi Terakhir</h2>
        <div class="data-card">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trx)
                    <tr>
                        <td>#{{ substr($trx->id_transaksi, 0, 8) }}</td>
                        <td>{{ $trx->user->username ?? 'System' }}</td>
                        <td>Rp {{ number_format($trx->total_pembayaran, 0, ',', '.') }}</td>
                        <td>{{ $trx->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div>
        <h2 class="section-title"><i data-lucide="alert-triangle"></i> Stok Menipis</h2>
        <div class="data-card">
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockBarang as $barang)
                    <tr>
                        <td>{{ $barang->nama_barang }}</td>
                        <td><span class="badge badge-warning">{{ $barang->stok }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: var(--text-muted);">Semua stok aman</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
