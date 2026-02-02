<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Sistem Informasi DLHK</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 270px;
            --color-primary: #1A4030;
            --color-white: #FFFFFF;
            --header-height: 64px;
            --bg-body: #F3F4F6;
            --bg-surface: #FFFFFF;
            --border-color: #E5E7EB;
            --text-dark: #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* --- SIDEBAR COMPONENT (MODIFIED) --- */
        .app-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1A4030 0%, #143024 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            border-right: none;
        }

        .sidebar-header {
            height: 80px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background-color: transparent;
            margin-bottom: 10px;
        }

        .brand-text {
            color: #FFFFFF;
            font-weight: 700;
            font-size: 1.1rem;
            margin-left: 12px;
            margin-top: 10px;
            line-height: 1.1;
            letter-spacing: -0.01em;
        }

        .brand-text small {
            display: block;
            font-weight: 400;
            opacity: 0.7;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        .sidebar-menu {
            flex: 1;
            padding: 0 1rem;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }

        .menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.4);
            margin: 1.5rem 0 0.75rem 0.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .menu-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin-left: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.85rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.25s ease;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 4px;
            border-left: none;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
            transform: translateX(3px);
        }

        /* --- PERUBAHAN UTAMA: ACTIVE STATE --- */
        .nav-link.active {
            background-color: #FFFFFF;
            /* Putih Solid */
            color: #1A4030;
            /* Teks Hijau Gelap (Sesuai warna sidebar) */
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .nav-icon {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: transform 0.2s;
        }

        /* Icon Active berubah menjadi hijau juga */
        .nav-link.active .nav-icon {
            transform: scale(1.1);
            color: #1A4030;
        }

        .sidebar-footer {
            padding: 1.5rem 1rem;
            background: transparent;
            border-top: none;
        }

        .user-card {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s;
        }

        .user-card:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Main Content & Utility */
        .app-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--header-height);
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .page-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .page-content {
            padding: 2rem;
            max-width: 1400px;
        }

        /* Alert Styles */
        .alert-institutional {
            border-left-width: 4px;
            border-radius: 2px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
        }

        .alert-success-inst {
            background-color: #F1FDF4;
            border-color: #1A4030;
        }

        .alert-success-inst .icon {
            color: #1A4030;
        }

        .alert-success-inst .title {
            color: #1A4030;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .alert-success-inst .text {
            color: #166534;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 45;
            backdrop-filter: blur(1px);
        }

        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.show {
                transform: translateX(0);
            }

            .app-main {
                margin-left: 0;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar {
                padding: 0 1rem;
            }

            .page-content {
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="app-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-placeholder">
                <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah"
                    style="width: 42px; height: 42px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
            </div>
            <div class="brand-text">
                <div style="color:#fbbf24">PELAPORAN PUHH</div>
                <small>DLHK PROVINSI JAWA TENGAH</small>
            </div>
        </div>

        <nav class="sidebar-menu">

            <a href="{{ route('welcome') }}" class="nav-link">
                <i class="fas fa-home nav-icon"></i>
                <span>Beranda</span>
            </a>

            <div class="menu-label">Menu Utama</div>

            @auth
                @if(Auth::user()->role === 'admin')

                    <a href="{{ route('laporan.index') }}"
                        class="nav-link {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.preview') || request()->routeIs('data.industri') || request()->routeIs('laporan.industri') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie nav-icon"></i> <span>Dashboard</span>
                    </a>

                    <a href="{{ route('laporan.monitoring') }}"
                        class="nav-link {{ request()->routeIs('laporan.monitoring') ? 'active' : '' }}">
                        <i class="fas fa-desktop nav-icon"></i> <span>Monitoring</span>
                    </a>
                @endif

            @endauth

            <a href="{{ route('laporan.rekap') }}"
                class="nav-link {{ request()->routeIs('laporan.rekap') || request()->routeIs('laporan.detail') ? 'active' : '' }}">
                <i class="fas fa-table nav-icon"></i>
                <span>Rekapitulasi</span>
            </a>

            <a href="{{ route('laporan.bukti') }}"
                class="nav-link {{ request()->routeIs('laporan.bukti') ? 'active' : '' }}">
                <i class="fas fa-search nav-icon"></i>
                <span>Unduh Bukti</span>
            </a>



            @auth
                @if(Auth::user()->role === 'admin')
                    <div class="menu-label">Pengelolaan Data</div>
                    <a href="{{ route('laporan.upload.form') }}"
                        class="nav-link {{ request()->routeIs('laporan.upload.form') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle nav-icon"></i>
                        <span>Input Laporan</span>
                    </a>


                @endif
            @endauth

            <div class="mt-auto mb-2 border-t border-white/10 mx-2"></div>


        </nav>

        <div class="sidebar-footer">
            @guest
                <a href="{{ route('login') }}" class="user-card group">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white group-hover:bg-white/20 transition">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                        </div>
                        <div class="text-white">
                            <p class="text-sm font-semibold">Masuk Sistem</p>
                        </div>
                    </div>
                </a>
                <div style="margin-top:0.75rem;">
                    <a href="{{ route('template.download') }}" class="btn btn-export"
                        style="display:flex; align-items:center; gap:8px; width:100%; justify-content:center;">
                        <i class="fas fa-file-download"></i>
                        <span>Download Template</span>
                    </a>
                </div>
            @else
                <div class="user-card">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div
                            class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-green-700 flex-shrink-0 flex items-center justify-center text-white font-bold text-sm shadow-inner border border-white/10">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-white overflow-hidden">
                            <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] opacity-60 truncate">Admin DLHK</p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-red-500/20 text-white/50 hover:text-red-400 transition"
                            title="Keluar">
                            <i class="fas fa-power-off text-xs"></i>
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </aside>

    <main class="app-main">
        <header class="topbar">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-600 hover:text-primary transition" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Tinjauan Dashboard')</h1>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Hari ini</span>
                    <span class="text-sm font-bold text-gray-800">{{ now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>
                {{-- --}}
            </div>
        </header>

        <div class="page-content">
            @if (session('success'))
                <div class="mb-6 alert-institutional alert-success-inst shadow-sm">
                    <i class="fas fa-check-circle icon mt-0.5 mr-3"></i>
                    <div>
                        <h3 class="title">Berhasil </h3>
                        <p class="text">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto border-t border-gray-200 bg-white px-6 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 max-w-[1400px]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-leaf text-emerald-600 text-sm"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-800">Dinas Lingkungan Hidup & Kehutanan</span>
                        <span class="text-xs text-gray-500">© {{ date('Y') }} DLHK Provinsi Jawa Tengah</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Sistem Berjalan Normal</span>
                    </div>
                    <!-- <div class="hidden md:block h-4 w-px bg-gray-200"></div> -->
                    <!-- <span class="hidden md:block text-xs text-gray-400">v1.0.0</span> -->
                </div>
            </div>
        </footer>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>

    @stack('scripts')
</body>

</html>