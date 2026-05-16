@extends('layouts.app')

@section('title', 'Tambah Jasa')
@section('header_title', 'Tambah Layanan Jasa Baru')

@section('content')
<div class="data-card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('jasa.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label" for="nama_jasa">Nama Layanan Jasa</label>
            <input type="text" id="nama_jasa" name="nama_jasa" class="form-control" value="{{ old('nama_jasa') }}" required placeholder="Contoh: Ganti Oli Mesin">
            @error('nama_jasa') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="harga_jasa">Harga Jasa (Rp)</label>
            <input type="number" id="harga_jasa" name="harga_jasa" class="form-control" value="{{ old('harga_jasa', 0) }}" required min="0">
            @error('harga_jasa') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('jasa.index') }}" class="btn" style="background: var(--bg-color); border: 1px solid var(--card-border); color: var(--text-main);">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Layanan Jasa</button>
        </div>
    </form>
</div>
@endsection
