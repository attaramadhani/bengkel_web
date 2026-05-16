@extends('layouts.app')

@section('title', 'Tambah User')
@section('header_title', 'Tambah User Baru')

@section('content')
<div class="data-card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('user.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Contoh: kasir2">
            @error('username') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            @error('password') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="kasir">Kasir</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('user.index') }}" class="btn" style="background: var(--bg-color); border: 1px solid var(--card-border); color: var(--text-main);">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan User</button>
        </div>
    </form>
</div>
@endsection
