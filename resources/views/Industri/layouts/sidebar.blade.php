<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DLHK Kalimantan Tengah')</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @stack('styles')
    <style>
        :root {
            --primary: #0f172a;
            --accent: #24a148;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
            --success: #16a34a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            display: flex;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, rgb(26, 64, 48) 0%, #0f2a22 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 15px 12px 20px;
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .sidebar-text {
            flex: 1;
            line-height: 1.3;
        }

        .sidebar-text-top {
            font-size: 9px;
            font-weight: 600;
            color: #fbbf24;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
        }

        .sidebar-text-bottom {
            font-size: 10px;
            font-weight: 700;
            color: #86efac;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: #4CAF50;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: #4CAF50;
        }

        .menu-icon {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
            width: 100%;
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @stack('styles')
    </style>
</head>

<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah" onerror="this.src='{{ asset('img/logoDLHK.png') }}'">
            </div>
            <div class="sidebar-text">
                <p class="sidebar-text-top">Pemerintah Provinsi Jawa Tengah</p>
                <p class="sidebar-text-bottom">Dinas Lingkungan Hidup<br>dan Kehutanan</p>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('industri.dashboard') }}"
                class="menu-item {{ request()->routeIs('industri.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large menu-icon"></i>
                <span class="menu-text">Beranda</span>
            </a>

            <a href="{{ route('industri-primer.index') }}"
                class="menu-item {{ request()->routeIs('industri-primer.*') ? 'active' : '' }}">
                <i class="fas fa-industry menu-icon"></i>
                <span class="menu-text">Industri Primer</span>
            </a>

            <a href="{{ route('industri-sekunder.index') }}"
                class="menu-item {{ request()->routeIs('industri-sekunder.*') ? 'active' : '' }}">
                <i class="fas fa-microchip menu-icon"></i>
                <span class="menu-text">Industri Sekunder</span>
            </a>

            <a href="{{ route('tptkb.index') }}" class="menu-item {{ request()->routeIs('tptkb.*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt menu-icon"></i>
                <span class="menu-text">TPTKB</span>
            </a>

            <a href="{{ route('perajin.index') }}"
                class="menu-item {{ request()->routeIs('perajin.*') ? 'active' : '' }}">
                <i class="fas fa-tools menu-icon"></i>
                <span class="menu-text">Perajin</span>
            </a>

            <div style="margin-top: 30px; padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                @guest
                    <a href="{{ route('login', ['from' => url()->current()]) }}" class="menu-item"
                        style="background: rgba(76, 175, 80, 0.2); border-radius: 8px; justify-content: center;">
                        <i class="fas fa-sign-in-alt menu-icon"></i>
                        <span class="menu-text" style="font-weight: 600;">Login Admin</span>
                    </a>
                @else
                    <div style="color: rgba(255, 255, 255, 0.9); margin-bottom: 15px; text-align: center;">
                        <i class="fas fa-user-circle" style="font-size: 36px; margin-bottom: 8px;"></i>
                        <div style="font-size: 13px; font-weight: 600;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 11px; opacity: 0.7;">{{ Auth::user()->email }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="menu-item"
                            style="width: 100%; background: rgba(239, 68, 68, 0.2); border: none; cursor: pointer; border-radius: 8px; justify-content: center;">
                            <i class="fas fa-sign-out-alt menu-icon"></i>
                            <span class="menu-text" style="font-weight: 600;">Logout</span>
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Navigation -->
        <nav>
            <div class="nav-content">
                <div class="logo-area">
                    <span class="logo-text">Dinas Lingkungan Hidup dan Kehutanan</span>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    @auth
                        <div class="user-info">
                            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            <span style="font-size: 14px; font-weight: 500;">{{ Auth::user()->name }}</span>
                        </div>
                    @else
                        <a href="{{ route('login', ['from' => url()->current()]) }}"
                            style="padding: 8px 20px; background: var(--accent); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; transition: all 0.2s;">
                            <i class="fas fa-sign-in-alt"></i> Portal Login
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success"
                    style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: none;">
                    <span style="font-size: 20px;">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error"
                    style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: none;">
                    <span style="font-size: 20px;">⚠</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </nav>

        <script>
            // Auto-hide alerts after 1.5 seconds
            document.addEventListener('DOMContentLoaded', function () {
                const alerts = document.querySelectorAll('.alert');
                if (alerts.length > 0) {
                    setTimeout(function () {
                        alerts.forEach(function (alert) {
                            alert.style.transition = 'opacity 0.3s ease';
                            alert.style.opacity = '0';
                            setTimeout(function () {
                                alert.remove();
                            }, 300);
                        });
                    }, 1500);
                }
            });
        </script>

        <div style="flex: 1;">
            @yield('content')
        </div>

        <!-- Footer -->
        <!-- <footer style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px 24px; margin-top: auto;">
            <div
                style="max-width: 1400px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 32px; height: 32px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-leaf" style="color: #059669; font-size: 14px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Dinas Lingkungan Hidup &
                            Kehutanan</div>
                        <div style="font-size: 12px; color: #64748b;">© {{ date('Y') }} DLHK Provinsi Jawa Tengah</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b;">
                        <span
                            style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite;"></span>
                        <span>Sistem Berjalan Normal</span>
                    </div>
                    <span style="font-size: 12px; color: #94a3b8;">v1.0.0</span>
                </div>
            </div>
        </footer> -->
        <style>
            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }
        </style>
    </div>

    @stack('scripts')
</body>

</html>