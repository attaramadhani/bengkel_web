@extends('layouts.app')

@section('title', 'Kelola User')
@section('header_title', 'Kelola Data User')

@section('content')
<div class="header-actions">
    <h2 class="section-title"><i data-lucide="users"></i> Daftar User (Admin & Kasir)</h2>
    <a href="{{ route('user.create') }}" class="btn btn-primary">
        <i data-lucide="user-plus"></i> Tambah User
    </a>
</div>

<div class="data-card">
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td style="font-weight: 600;">{{ $user->username }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-success' : 'badge-warning' }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </td>
                <td>{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('user.edit', $user->id_user) }}" class="btn-icon">
                            <i data-lucide="edit-2" size="18"></i>
                        </a>
                        @if($user->id_user !== auth()->user()->id_user)
                        <form action="{{ route('user.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon" style="color: #ef4444; border-color: #fca5a5;">
                                <i data-lucide="trash-2" size="18"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
