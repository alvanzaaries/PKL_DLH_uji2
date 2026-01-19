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
                        <a href="{{ route('pnbp.landing') }}" class="text-2xl font-bold text-green-600">Pelaporan PNBP</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if (auth()->check() && ((auth()->user()->role ?? 'user') === 'user'))
                        <a href="{{ route('welcome') }}" class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-700 hover:text-green-700 hover:bg-green-50">Beranda</a>
                        <a href="{{ route('user.upload') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('user.upload') ? 'bg-green-600 text-white' : 'text-gray-700 hover:text-green-700 hover:bg-green-50' }}">Upload</a>
                        <a href="{{ route('user.history') }}" class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('user.history') ? 'bg-green-600 text-white' : 'text-gray-700 hover:text-green-700 hover:bg-green-50' }}">Riwayat</a>

                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</button>
                        </form>
                    @elseif (auth()->check() && ((auth()->user()->role ?? 'user') === 'admin'))
                        <a href="{{ route('welcome') }}" class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-700 hover:text-green-700 hover:bg-green-50">Beranda</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Beranda</a>
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
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
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Kementerian Lingkungan Hidup dan Kehutanan. Pelaporan PNBP v1.0
            </p>
        </div>
    </footer>
</body>
</html>