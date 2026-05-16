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
                    Belum ada data layanan jasa.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
