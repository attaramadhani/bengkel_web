@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('header_title', 'Detail Transaksi #' . substr($transaksi->id_transaksi, 0, 8))

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('transaksi.index') }}" class="btn" style="background: white; border: 1px solid var(--card-border);">
        <i data-lucide="arrow-left"></i> Kembali
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="data-card">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--card-border);">
            <h3 style="font-size: 1rem;">Rincian Barang & Jasa</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Tipe</th>
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->details as $detail)
                <tr>
                    <td>{{ $detail->barang->nama_barang ?? $detail->jasa->nama_jasa ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $detail->id_barang ? 'badge-success' : 'badge-warning' }}">
                            {{ $detail->id_barang ? 'Barang' : 'Jasa' }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($detail->id_barang ? ($detail->barang->harga_jual ?? 0) : ($detail->jasa->harga_jasa ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td style="font-weight: 600;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 800; font-size: 1.1rem;">
                    <td colspan="4" style="text-align: right; padding: 1.5rem;">TOTAL AKHIR</td>
                    <td style="color: var(--accent-primary); padding: 1.5rem;">Rp {{ number_format($transaksi->total_pembayaran, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="data-card" style="padding: 1.5rem;">
        <h3 style="font-size: 1rem; margin-bottom: 1.5rem;">Informasi Transaksi</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <div class="stat-label">Waktu Transaksi</div>
                <div style="font-weight: 600;">{{ $transaksi->created_at->format('d F Y, H:i:s') }}</div>
            </div>
            <div>
                <div class="stat-label">Kasir</div>
                <div style="font-weight: 600;">{{ $transaksi->user->username ?? 'System' }}</div>
            </div>
            <div>
                <div class="stat-label">Metode Bayar</div>
                @if($transaksi->metode_bayar === 'midtrans')
                    <span class="badge" style="background: #ede9fe; color: #7c3aed;">MIDTRANS</span>
                @else
                    <span class="badge" style="background: #f0fdf4; color: #166534;">CASH</span>
                @endif
            </div>
            <div>
                <div class="stat-label">Status Bayar</div>
                @if($transaksi->status_bayar === 'lunas')
                    <span class="badge badge-success">LUNAS</span>
                @elseif($transaksi->status_bayar === 'pending')
                    <span class="badge badge-warning">PENDING</span>
                @else
                    <span class="badge" style="background: #fef2f2; color: #dc2626;">GAGAL</span>
                @endif
            </div>
        </div>
        
        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem; justify-content: center;" onclick="window.print()">
            <i data-lucide="printer"></i> Cetak Struk
        </button>
    </div>
</div>
@endsection
