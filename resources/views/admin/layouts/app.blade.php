<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - DLHK Jateng')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="{{ asset('js/pnbp/tailwind-config.js') }}"></script>

    <link rel="icon" type="image/png" href="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

@php
    $authUser = auth()->user();
    $name = $authUser?->name ?? 'Administrator';
    $email = $authUser?->email ?? '';
    $initials = collect(explode(' ', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
    $initials = $initials !== '' ? mb_strtoupper($initials) : 'AD';
@endphp

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased min-h-screen flex flex-col">

    {{-- Header/Navbar --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo & Title --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                        <img src="{{ asset('img/logoDLHK.png') }}" alt="Logo DLHK" style="width: 200px; height: 38px;">
                    </a>
                    <span class="hidden md:block h-6 w-px bg-gray-300 dark:bg-gray-600"></span>
                    <span class="hidden md:block text-sm font-medium text-gray-600 dark:text-gray-300">Admin Panel</span>
                </div>

                {{-- Navigation --}}
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('admin.users.index') }}" 
                       class="text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400' }} transition-colors">
                        <span class="flex items-center gap-1">
                            <span class="material-icons-outlined text-lg">people</span>
                            Kelola User
                        </span>
                    </a>
                </nav>

                {{-- User Menu --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xs">
                            {{ $initials }}
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors" title="Logout">
                            <span class="material-icons-outlined">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">@yield('header', 'Admin Panel')</h1>
                        @hasSection('subheader')
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@yield('subheader')</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @yield('header_actions')
                    </div>
                </div>
            </div>

            {{-- Page Content --}}
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <span class="material-icons-outlined text-green-600 dark:text-green-400 text-sm">eco</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">Dinas Lingkungan Hidup & Kehutanan</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">© {{ date('Y') }} DLHK Provinsi Jawa Tengah</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">
                        <span class="material-icons-outlined text-sm">arrow_back</span>
                        Kembali ke Dashboard PNBP
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
