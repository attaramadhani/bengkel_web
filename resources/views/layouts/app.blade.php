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
    <script src="https://unpkg.com/lucide@0.292.0"></script>
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
        
        /* Modern Floating Toast Notifications */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .toast {
            background: rgba(255, 255, 255, 0.95);
            border-left: 5px solid #3b82f6;
            padding: 16px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 320px;
            backdrop-filter: blur(10px);
            transform: translateX(120%);
            animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .toast.toast-success {
            border-left-color: #10b981;
        }
        .toast.toast-error {
            border-left-color: #ef4444;
        }
        .toast-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .toast-success .toast-icon {
            color: #10b981;
        }
        .toast-error .toast-icon {
            color: #ef4444;
        }
        .toast-message {
            color: #1f2937;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .toast.fade-out {
            opacity: 0;
            transform: translateX(120%) scale(0.9);
        }
        @keyframes toastSlideIn {
            to {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Floating Toast Container -->
    <div class="toast-container" id="toast-container"></div>

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

            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();

        // Modern Toast Notification Function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let iconName = 'check-circle-2';
            if (type === 'error') iconName = 'alert-circle';
            
            toast.innerHTML = `
                <div class="toast-icon"><i data-lucide="${iconName}"></i></div>
                <div class="toast-message">${message}</div>
            `;
            container.appendChild(toast);
            lucide.createIcons();
            
            setTimeout(() => {
                toast.classList.add('fade-out');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }

        // Trigger Laravel Flash Session Toasts
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
</body>
</html>
