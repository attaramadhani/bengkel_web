@extends('layouts.app')

@section('title', 'Stok Barang')
@section('header_title', 'Stok Barang')

@section('content')
<div class="header-actions">
    <h2 class="section-title"><i data-lucide="package"></i> Daftar Stok Barang</h2>
    <a href="{{ route('barang.create') }}" class="btn btn-primary">
        <i data-lucide="plus"></i> Tambah Barang
    </a>
</div>

<form action="{{ route('barang.index') }}" method="GET" class="flex-row-mobile-stack" style="margin-bottom: 1.5rem; align-items: flex-end;">
    <div style="position: relative; flex: 1;">
        <input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="{{ request('search') }}" style="padding-left: 45px;">
        <i data-lucide="search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; height: 18px;"></i>
    </div>
    @if(request('search'))
        <a href="{{ route('barang.index') }}" class="btn" style="background: #ef4444; color: white;">
            <i data-lucide="x"></i> Reset
        </a>
    @endif
    <button type="submit" class="btn btn-primary">
        <i data-lucide="filter"></i> Cari
    </button>
</form>

<div class="data-card">
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $barang)
            <tr>
                <td>{{ $barang->nama_barang }}</td>
                <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                <td>
                    @if($barang->stok < 5)
                        <span class="badge badge-warning">{{ $barang->stok }}</span>
                    @else
                        <span class="badge badge-success">{{ $barang->stok }}</span>
                    @endif
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('barang.edit', $barang->id_barang) }}" class="btn-icon">
                            <i data-lucide="edit-2" size="18"></i>
                        </a>
                        <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon" style="color: #ef4444; border-color: #fca5a5;">
                                <i data-lucide="trash-2" size="18"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    @if(request('search'))
                        Tidak ada barang yang cocok dengan pencarian "{{ request('search') }}".
                    @else
                        Belum ada data barang.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
