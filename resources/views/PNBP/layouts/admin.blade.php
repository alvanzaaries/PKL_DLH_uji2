<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Pelaporan PNBP')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="{{ asset('js/pnbp/tailwind-config.js') }}"></script>

    <link rel="icon" type="image/png" href="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="{{ asset('css/pnbp/admin-layout.css') }}" rel="stylesheet">
</head>
@php
    $authUser = auth()->user();
    $name = $authUser?->name ?? 'Administrator';
    $email = $authUser?->email ?? '';
    // Membuat inisial nama untuk avatar dari nama pengguna.
    $initials = collect(explode(' ', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
    $initials = $initials !== '' ? mb_strtoupper($initials) : 'AD';
@endphp

<body
    class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden selection:bg-primary selection:text-white">

    <aside id="sidebar"
        class="w-64 bg-surface-light dark:bg-surface-dark border-r border-gray-200 dark:border-gray-700 flex-shrink-0 flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('pnbp.landing') }}"
                class="text-xl font-bold text-primary tracking-tight flex items-center gap-2">
                <img src="{{ asset('img/logoDLHK.png') }}" alt="Logo Jawa Tengah" style="width: 240px; height: 45px;">
            </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                href="{{ route('dashboard.index') }}">
                <span
                    class="material-icons-outlined mr-3 {{ request()->routeIs('dashboard.*') ? '' : 'text-gray-400 group-hover:text-primary dark:text-gray-500 dark:group-hover:text-primary' }}">dashboard</span>
                Dashboard
            </a>

            <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reconciliations.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                href="{{ route('reconciliations.index') }}">
                <span
                    class="material-icons-outlined mr-3 {{ request()->routeIs('reconciliations.*') ? '' : 'text-gray-400 group-hover:text-primary dark:text-gray-500 dark:group-hover:text-primary' }}">history</span>
                Arsip Rekonsiliasi
            </a>

            <a class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                href="{{ route('admin.settings.index') }}">
                <span
                    class="material-icons-outlined mr-3 {{ request()->routeIs('admin.settings.*') ? '' : 'text-gray-400 group-hover:text-primary dark:text-gray-500 dark:group-hover:text-primary' }}">settings</span>
                Pengaturan
            </a>
        </nav>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div
                    class="h-9 w-9 rounded-full bg-gradient-to-tr from-primary to-blue-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        title="Logout">
                        <span class="material-icons-outlined text-xl">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header
            class="md:hidden h-16 bg-surface-light dark:bg-surface-dark border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4">
            <span class="text-lg font-bold text-primary">Pelaporan PNBP</span>
            <div class="flex items-center gap-2">
                <button id="toggleSidebar" class="text-gray-500" type="button" title="Menu">
                    <span class="material-icons-outlined">menu</span>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                            @yield('header', 'Dashboard')</h1>
                        @hasSection('subheader')
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@yield('subheader')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('header_actions')
                    </div>
                </div>

                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer
            class="border-t border-gray-200 dark:border-gray-700 bg-surface-light dark:bg-surface-dark px-6 py-4 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-icons-outlined text-primary text-sm">eco</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">Dinas Lingkungan Hidup &
                            Kehutanan</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">© {{ date('Y') }} DLHK Provinsi Jawa
                            Tengah</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Sistem Berjalan Normal</span>
                    </div>
                    <div class="hidden md:block h-4 w-px bg-gray-200 dark:bg-gray-700"></div>
                    <span class="hidden md:block text-xs text-gray-400 dark:text-gray-500">v1.0.0</span>
                </div>
            </div>
        </footer>
    </main>

    <script>
        // Inisialisasi tema gelap/terang dan toggle sidebar.
        (function () {
            const root = document.documentElement;
            const body = document.body;
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldDark = stored ? stored === 'dark' : prefersDark;

            // Menerapkan tema gelap/terang pada root dan body.
            const applyDark = (on) => {
                if (on) {
                    root.classList.add('dark');
                    body.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                    body.classList.remove('dark');
                }
            };

            applyDark(shouldDark);

            // Mengubah tema dan menyimpan preferensi ke localStorage.
            const toggle = () => {
                const now = !(root.classList.contains('dark') || body.classList.contains('dark'));
                applyDark(now);
                localStorage.setItem('theme', now ? 'dark' : 'light');
            };

            document.getElementById('toggleDark')?.addEventListener('click', toggle);
            document.getElementById('toggleDarkDesktop')?.addEventListener('click', toggle);
            document.getElementById('toggleDarkSettings')?.addEventListener('click', toggle);

            // Menampilkan/menyembunyikan sidebar pada perangkat kecil.
            document.getElementById('toggleSidebar')?.addEventListener('click', () => {
                const s = document.getElementById('sidebar');
                if (!s) return;
                s.classList.toggle('hidden');
            });
        })();
    </script>

</body>

</html>