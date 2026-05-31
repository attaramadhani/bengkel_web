@extends('layouts.app')

@section('title', 'Laporan Workshop')
@section('header_title', 'Laporan & Analisis')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Filter Section -->
<div class="data-card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form action="{{ route('laporan.index') }}" method="GET" class="flex-row-mobile-stack" style="align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Filter</label>
            <select name="filter" id="filter-type" class="form-control" onchange="toggleFilterInputs()">
                <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="tahunan" {{ $filter == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; display: none;" id="input-mingguan">
            <label class="form-label">Minggu</label>
            <input type="week" name="minggu" class="form-control" value="{{ request('minggu', now()->format('Y-\WW')) }}">
        </div>
        <div class="form-group" style="margin-bottom: 0; display: none;" id="input-bulanan">
            <label class="form-label">Bulan</label>
            <input type="month" name="bulan" class="form-control" value="{{ request('bulan', now()->format('Y-m')) }}">
        </div>
        <div class="form-group" style="margin-bottom: 0; display: none;" id="input-tahunan">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="{{ request('tahun', now()->year) }}" min="2020" max="{{ now()->year }}">
        </div>
        <div class="flex-row-mobile-stack" style="gap: 10px; margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="filter"></i> Terapkan
            </button>
            <a href="{{ route('transaksi.export', request()->all()) }}" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                <i data-lucide="file-spreadsheet"></i> Ekspor Excel
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Pendapatan — {{ $label }}</div>
        <div class="stat-value" style="color: var(--accent-primary);">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Jumlah Transaksi — {{ $label }}</div>
        <div class="stat-value">{{ $totalTransaksi }}</div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="data-card" style="padding: 2rem; margin-bottom: 2rem;">
    <h2 class="section-title"><i data-lucide="trending-up"></i> {{ $chartTitle }}</h2>
    <div style="height: 300px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="detail-grid" style="margin-bottom: 2rem;">
    <div class="data-card" style="padding: 2rem;">
        <h2 class="section-title"><i data-lucide="package"></i> Top 5 Barang Terlaris</h2>
        <div style="height: 250px;">
            <canvas id="topBarangChart"></canvas>
        </div>
    </div>
    <div class="data-card" style="padding: 2rem;">
        <h2 class="section-title"><i data-lucide="wrench"></i> Top 5 Jasa Terpopuler</h2>
        <div style="height: 250px;">
            <canvas id="topJasaChart"></canvas>
        </div>
    </div>
</div>

<!-- Transaction Table -->
<h2 class="section-title"><i data-lucide="list"></i> Daftar Transaksi — {{ $label }}</h2>
<div class="data-card">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID</th>
                <th>Kasir</th>
                <th>Item</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $trx)
            <tr>
                <td>{{ $trx->created_at->format('d M Y, H:i') }}</td>
                <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">#{{ substr($trx->id_transaksi, 0, 8) }}</code></td>
                <td>{{ $trx->user->username ?? 'System' }}</td>
                <td>{{ $trx->details->count() }} item</td>
                <td style="font-weight: 700;">Rp {{ number_format($trx->total_pembayaran, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Toggle filter inputs
    function toggleFilterInputs() {
        const type = document.getElementById('filter-type').value;
        document.getElementById('input-mingguan').style.display = type === 'mingguan' ? 'block' : 'none';
        document.getElementById('input-bulanan').style.display = type === 'bulanan' ? 'block' : 'none';
        document.getElementById('input-tahunan').style.display = type === 'tahunan' ? 'block' : 'none';
    }
    toggleFilterInputs();

    // Revenue Chart
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($revenueData->pluck('label')),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($revenueData->pluck('total')),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true, tension: 0.4, borderWidth: 3,
                pointBackgroundColor: '#fff', pointBorderColor: '#3b82f6', pointBorderWidth: 2, pointRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
    });

    // Top Barang
    new Chart(document.getElementById('topBarangChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($topBarang->map(fn($item) => $item->barang->nama_barang ?? 'Unknown')),
            datasets: [{ data: @json($topBarang->pluck('total_qty')), backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'], borderRadius: 8 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
    });

    // Top Jasa
    new Chart(document.getElementById('topJasaChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($topJasa->map(fn($item) => $item->jasa->nama_jasa ?? 'Unknown')),
            datasets: [{ data: @json($topJasa->pluck('total_count')), backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endsection
