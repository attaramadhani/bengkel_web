@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header_title', 'Riwayat Transaksi')

@section('content')
<div class="header-actions">
    <h2 class="section-title"><i data-lucide="history"></i> Daftar Transaksi Selesai</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('transaksi.export') }}" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
            <i data-lucide="file-spreadsheet"></i> Ekspor Excel
        </a>
        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Transaksi Baru
        </a>
    </div>
</div>

<div class="data-card">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Total Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $trx)
            <tr>
                <td>{{ $trx->created_at->format('d M Y, H:i') }}</td>
                <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">#{{ substr($trx->id_transaksi, 0, 8) }}</code></td>
                <td>{{ $trx->user->username ?? 'System' }}</td>
                <td>
                    @if($trx->metode_bayar === 'midtrans')
                        <span class="badge" style="background: #ede9fe; color: #7c3aed;">MIDTRANS</span>
                    @else
                        <span class="badge" style="background: #f0fdf4; color: #166534;">CASH</span>
                    @endif
                </td>
                <td>
                    @if($trx->status_bayar === 'lunas')
                        <span class="badge badge-success">LUNAS</span>
                    @elseif($trx->status_bayar === 'pending')
                        <span class="badge badge-warning">PENDING</span>
                    @else
                        <span class="badge" style="background: #fef2f2; color: #dc2626;">GAGAL</span>
                    @endif
                </td>
                <td style="font-weight: 700; color: var(--accent-primary);">Rp {{ number_format($trx->total_pembayaran, 0, ',', '.') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('transaksi.show', $trx->id_transaksi) }}" class="btn-icon" title="Detail">
                            <i data-lucide="eye" size="18"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    Belum ada riwayat transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
