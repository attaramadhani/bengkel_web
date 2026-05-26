@extends('layouts.app')

@section('title', 'Edit User')
@section('header_title', 'Edit Data User')

@section('content')
<div class="data-card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('user.update', $user->id_user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            @error('username') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password Baru <small style="color: var(--text-muted);">(kosongkan jika tidak ingin mengganti)</small></label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" class="form-control" style="padding-right: 50px;" placeholder="Minimal 6 karakter">
                <button type="button" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9fb3c8; padding: 0; display: flex; align-items: center; justify-content: center; outline: none;">
                    <i data-lucide="eye" id="eyeIcon" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            @error('password') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="kasir" {{ $user->role === 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('user.index') }}" class="btn" style="background: var(--bg-color); border: 1px solid var(--card-border); color: var(--text-main);">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.setAttribute('data-lucide', 'eye');
            } else {
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            }
            
            lucide.createIcons();
        });
    });
</script>
@endsection
