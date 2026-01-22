<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pelaporan PNBP - Sistem Rekonsiliasi LHK')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('pnbp.landing') }}" class="text-2xl font-bold text-green-600">Pelaporan
                            PNBP</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if (auth()->check() && ((auth()->user()->role ?? 'user') === 'user'))
                        <a href="{{ route('pnbp.landing') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-700 hover:text-green-700 hover:bg-green-50">Beranda</a>
                        <a href="{{ route('user.upload') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('user.upload') ? 'bg-green-600 text-white' : 'text-gray-700 hover:text-green-700 hover:bg-green-50' }}">Upload</a>
                        <a href="{{ route('user.history') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('user.history') ? 'bg-green-600 text-white' : 'text-gray-700 hover:text-green-700 hover:bg-green-50' }}">Riwayat</a>

                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</button>
                        </form>
                    @elseif (auth()->check() && ((auth()->user()->role ?? 'user') === 'admin'))
                        <a href="{{ route('pnbp.landing') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-700 hover:text-green-700 hover:bg-green-50">Beranda</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('pnbp.landing') }}"
                            class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Beranda</a>
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-auto border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
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
                    <div class="hidden md:block h-4 w-px bg-gray-200"></div>
                    <span class="hidden md:block text-xs text-gray-400">v1.0.0</span>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>