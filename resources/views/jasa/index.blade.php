@extends('layouts.app')

@section('title', 'Layanan Jasa')
@section('header_title', 'Layanan Jasa')

@section('content')
<div class="header-actions">
    <h2 class="section-title"><i data-lucide="wrench"></i> Daftar Layanan Jasa</h2>
    <a href="{{ route('jasa.create') }}" class="btn btn-primary">
        <i data-lucide="plus"></i> Tambah Jasa
    </a>
</div>

<form action="{{ route('jasa.index') }}" method="GET" class="flex-row-mobile-stack" style="margin-bottom: 1.5rem; align-items: flex-end;">
    <div style="position: relative; flex: 1;">
        <input type="text" name="search" class="form-control" placeholder="Cari layanan jasa..." value="{{ request('search') }}" style="padding-right: 45px;">
        <i data-lucide="search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; height: 18px;"></i>
    </div>
    @if(request('search'))
        <a href="{{ route('jasa.index') }}" class="btn" style="background: #ef4444; color: white;">
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
                <th>Nama Jasa</th>
                <th>Harga Jasa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jasas as $jasa)
            <tr>
                <td>{{ $jasa->nama_jasa }}</td>
                <td>Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('jasa.edit', $jasa->id_jasa) }}" class="btn-icon">
                            <i data-lucide="edit-2" size="18"></i>
                        </a>
                        <form action="{{ route('jasa.destroy', $jasa->id_jasa) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan jasa ini?');" style="display:inline;">
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
                <td colspan="3" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    @if(request('search'))
                        Tidak ada layanan jasa yang cocok dengan pencarian "{{ request('search') }}".
                    @else
                        Belum ada data layanan jasa.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
