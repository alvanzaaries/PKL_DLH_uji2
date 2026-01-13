<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Sistem Informasi DLHK</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* * DESIGN SYSTEM: PURE INSTITUTIONAL GREEN
         * Constraints: Only Green & White palette. No Gold/Yellow.
         * Typography: Clean Sans-serif (Inter).
         * Style: Flat, Functional, High Contrast.
         */
        :root {
            /* Primary Palette (DLHK Jateng Inspired Green) */
            --color-primary: #1A4030;        /* Deep Forest Green - Sidebar Bg */
            --color-primary-hover: #2E5444;  /* Slightly Lighter Green - Hover state */
            --color-white: #FFFFFF;

            /* Backgrounds & Neutrals */
            --bg-body: #F3F4F6;              /* Clean light gray background */
            --bg-surface: #FFFFFF;
            --border-color: #E5E7EB;

            /* Text Colors */
            --text-dark: #111827;            /* Near black for primary text */
            --text-muted: #6B7280;           /* Gray for secondary text */
            --text-on-primary: #FFFFFF;
            --text-on-primary-muted: #A3BDB0; /* Desaturated green-gray for inactive labels */

            /* Dimensions */
            --sidebar-width: 270px;
            --header-height: 64px;
        }

        /* Base Typography */
        body { 
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            font-size: 15px; /* Slightly smaller base size for professional look */
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        /* --- SIDEBAR COMPONENT --- */
        .app-sidebar {
            width: var(--sidebar-width);
            background-color: var(--color-primary);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform 0.3s ease-in-out;
            /* Flat design, no shadow, just a subtle dark border for definition */
            border-right: 1px solid rgba(0,0,0,0.2);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            background-color: rgba(0,0,0,0.1); /* Subtle darkening */
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        /* Logo Placeholder Style */
        .logo-placeholder {
            width: 36px;
            height: 36px;
            background-color: var(--color-white);
            border-radius: 4px; /* Minimal rounded corners */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--color-primary);
            font-size: 0.8rem;
        }

        .brand-text {
            color: var(--text-on-primary);
            font-weight: 600;
            font-size: 1rem;
            margin-left: 12px;
            line-height: 1.2;
        }
        .brand-text small {
            display: block;
            font-weight: 400;
            opacity: 0.8;
            font-size: 0.75rem;
        }

        .sidebar-menu {
            flex: 1;
            padding: 1.5rem 0;
            overflow-y: auto;
        }
        
        .menu-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-on-primary-muted);
            margin: 1.5rem 1.25rem 0.5rem;
            font-weight: 600;
        }
        
        /* Nav Link Styles - Pure Green & White */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.925rem;
            font-weight: 500;
            border-left: 3px solid transparent; /* Placeholder for active indicator */
            margin-bottom: 2px;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: var(--color-white);
        }

        /* Active State - STRICTLY WHITE contrast */
        .nav-link.active {
            background-color: var(--color-primary-hover);
            color: var(--color-white);
            border-left-color: var(--color-white); /* White indicator instead of gold */
            font-weight: 600;
        }

        .nav-icon {
            width: 20px; text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
            opacity: 0.75;
        }
        
        .nav-link.active .nav-icon {
            opacity: 1;
            color: var(--color-white);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            background-color: rgba(0,0,0,0.15);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        
        /* --- MAIN CONTENT --- */
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
            /* Clean separator bottom border */
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

        /* --- UTILITIES & RESPONSIVE --- */
        /* Custom Alert Box - Green/White only */
        .alert-institutional {
            border-left-width: 4px;
            border-radius: 2px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
        }
        .alert-success-inst {
            background-color: #F1FDF4; /* Very pale green bg */
            border-color: var(--color-primary);
        }
        .alert-success-inst .icon { color: var(--color-primary); }
        .alert-success-inst .title { color: var(--color-primary); font-weight: 700; font-size: 0.9rem; }
        .alert-success-inst .text { color: #166534; font-size: 0.875rem; margin-top: 0.25rem;}

        .sidebar-overlay {
            display: none; 
            position: fixed; inset: 0; 
            background: rgba(0,0,0,0.5); 
            z-index: 45;
            backdrop-filter: blur(1px);
        }

        @media (max-width: 768px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.show { transform: translateX(0); }
            .app-main { margin-left: 0; }
            .sidebar-overlay.show { display: block; }
            .topbar { padding: 0 1rem; }
            .page-content { padding: 1rem; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="app-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-placeholder">
                LOGO
            </div>
            <div class="brand-text">
                Sistem Informasi
                <small>DLHK Provinsi Jateng</small>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Pelaporan & Data</div>
            
            <a href="{{ route('data.industri') }}" class="nav-link {{ request()->routeIs('data.industri') || request()->routeIs('industri.laporan') || request()->routeIs('laporan.preview') ? 'active' : '' }}">
                <i class="fas fa-city nav-icon"></i>
                Monitoring Laporan
            </a>

            <a href="{{ route('laporan.upload.form') }}" class="nav-link {{ request()->routeIs('laporan.upload.form') ? 'active' : '' }}">
                <i class="fas fa-file-upload nav-icon"></i>
                Input Laporan
            </a>

            <a href="{{ route('laporan.rekap') }}" class="nav-link {{ request()->routeIs('laporan.rekap') || request()->routeIs('laporan.detail') ? 'active' : '' }}">
                <i class="fas fa-chart-bar nav-icon"></i>
                Rekapitulasi
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-sm bg-white/10 flex items-center justify-center text-white font-bold text-xs">
                    AD
                </div>
                <div class="text-white">
                    <p class="text-sm font-semibold leading-tight">Admin Dinas</p>
                    <p class="text-xs opacity-70 leading-tight">DLHK Prov.</p>
                </div>
            </div>
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
                <button class="text-gray-500 hover:text-[#1B5E20] transition relative p-1">
                    <i class="far fa-bell text-lg"></i>
                </button>
            </div>
        </header>

        <div class="page-content">
            @if(session('success'))
            <div class="mb-6 alert-institutional alert-success-inst shadow-sm">
                <i class="fas fa-check-circle icon mt-0.5 mr-3"></i>
                <div>
                    <h3 class="title">Data Berhasil Disimpan</h3>
                    <p class="text">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @yield('content')
        </div>
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