<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengkel App | @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: var(--bg-color);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-icon {
            padding: 8px;
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-muted);
            border: 1px solid var(--card-border);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-icon:hover {
            color: var(--accent-primary);
            border-color: var(--accent-primary);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">BENGKEL PRO</div>
            <ul class="nav-links">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transaksi.create') }}" class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                        <i data-lucide="shopping-cart"></i> Transaksi Baru
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') ? 'active' : '' }}">
                        <i data-lucide="history"></i> Riwayat Transaksi
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                <li style="padding: 1rem 0 0.5rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">
                    Admin Panel
                </li>
                <li class="nav-item">
                    <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                        <i data-lucide="package"></i> Stok Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jasa.index') }}" class="nav-link {{ request()->routeIs('jasa.*') ? 'active' : '' }}">
                        <i data-lucide="wrench"></i> Layanan Jasa
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <i data-lucide="users"></i> Kelola User
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-3"></i> Laporan
                    </a>
                </li>
                @endif
            </ul>

            <div style="margin-top: auto;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                        <i data-lucide="log-out"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>@yield('header_title', 'Overview')</h1>
                <div class="user-profile">
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--text-main);">{{ auth()->user()->username }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">{{ auth()->user()->role }}</div>
                    </div>
                    <div class="avatar"></div>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert" style="background: #fff5f5; color: #c53030; border: 1px solid #fed7d7;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
