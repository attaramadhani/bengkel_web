@extends('layouts.app')

@section('title', 'Edit Barang')
@section('header_title', 'Edit Data Barang')

@section('content')
<div class="data-card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label" for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
            @error('nama_barang') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="harga_jual">Harga Jual (Rp)</label>
            <input type="number" id="harga_jual" name="harga_jual" class="form-control" value="{{ old('harga_jual', (int)$barang->harga_jual) }}" required min="0">
            @error('harga_jual') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="stok">Stok Saat Ini</label>
            <input type="number" id="stok" name="stok" class="form-control" value="{{ old('stok', $barang->stok) }}" required min="0">
            @error('stok') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('barang.index') }}" class="btn" style="background: var(--bg-color); border: 1px solid var(--card-border); color: var(--text-main);">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
