@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('header_title', 'Detail Transaksi #' . substr($transaksi->id_transaksi, 0, 8))

@section('content')
<style>
    /* Styling khusus struk untuk cetak */
    .receipt-print {
        display: none;
    }
    
    @media print {
        /* Sembunyikan elemen non-cetak */
        .no-print,
        .sidebar,
        header,
        .toast-container,
        .sidebar-overlay,
        #mobile-toggle {
            display: none !important;
        }

        /* Reset layout utama agar mendukung pencetakan */
        .dashboard-container,
        .main-content {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            width: auto !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        
        body {
            background: white !important;
            color: black !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Tampilkan struk cetak */
        .receipt-print {
            display: block !important;
            width: 76mm; /* Ukuran standar kertas thermal printer */
            margin: 0 auto;
            padding: 10px 5px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            box-sizing: border-box;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .receipt-header h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .receipt-header p {
            margin: 0;
            font-size: 11px;
        }
        
        .receipt-divider {
            text-align: center;
            margin: 6px 0;
            letter-spacing: -1px;
            font-weight: bold;
        }
        
        .receipt-meta {
            font-size: 11px;
        }
        .receipt-meta p {
            margin: 3px 0;
        }
        
        .receipt-items {
            margin: 8px 0;
        }
        .receipt-item-row {
            margin-bottom: 6px;
        }
        .receipt-item-row .item-name {
            font-weight: bold;
            word-break: break-all;
        }
        .receipt-item-row .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 2px;
        }
        
        .receipt-totals {
            margin-top: 8px;
        }
        .receipt-totals .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .receipt-totals .total-row .total-val {
            font-weight: bold;
            font-size: 13px;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
        }
        
        @page {
            margin: 0;
            size: auto;
        }
    }
</style>

<!-- Tampilan Struk Cetak Thermal (Hanya muncul saat di-print) -->
<div class="receipt-print">
    <div class="receipt-header">
        <h2>BENGKEL PRO</h2>
        <p>Jl. Raya Utama No. 45, Tokyo</p>
        <p>Telp: 0812-3456-7890</p>
    </div>
    
    <div class="receipt-divider">================================</div>
    
    <div class="receipt-meta">
        <p>No. Trans : #{{ substr($transaksi->id_transaksi, 0, 8) }}</p>
        <p>Waktu     : {{ $transaksi->created_at->format('d/m/Y H:i') }}</p>
        <p>Kasir     : {{ $transaksi->user->username ?? 'System' }}</p>
    </div>
    
    <div class="receipt-divider">--------------------------------</div>
    
    <div class="receipt-items">
        @foreach($transaksi->details as $detail)
            <div class="receipt-item-row">
                <div class="item-name">{{ $detail->barang->nama_barang ?? $detail->jasa->nama_jasa ?? '-' }}</div>
                <div class="item-details">
                    <span>{{ $detail->qty }} x Rp {{ number_format($detail->id_barang ? ($detail->barang->harga_jual ?? 0) : ($detail->jasa->harga_jasa ?? 0), 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="receipt-divider">--------------------------------</div>
    
    <div class="receipt-totals">
        <div class="total-row">
            <span>TOTAL :</span>
            <span class="total-val">Rp {{ number_format($transaksi->total_pembayaran, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Metode:</span>
            <span>{{ strtoupper($transaksi->metode_bayar) }}</span>
        </div>
        <div class="total-row">
            <span>Status:</span>
            <span>{{ strtoupper($transaksi->status_bayar) }}</span>
        </div>
    </div>
    
    <div class="receipt-divider">================================</div>
    
    <div class="receipt-footer">
        <p>TERIMA KASIH</p>
        <p>Atas Kunjungan Anda</p>
        <p>Layanan Garansi Cek di Website</p>
    </div>
</div>

<div class="no-print" style="margin-bottom: 2rem;">
    <a href="{{ route('transaksi.index') }}" class="btn" style="background: white; border: 1px solid var(--card-border);">
        <i data-lucide="arrow-left"></i> Kembali
    </a>
</div>

<div class="detail-grid no-print">
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
