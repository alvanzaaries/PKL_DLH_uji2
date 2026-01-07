<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SISUDAH - Sistem Rekonsiliasi LHK')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-green-600">SISUDAH</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                     <a href="{{ url('/') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Beranda</a>
                    <a href="{{ route('reconciliations.index') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('reconciliations.*') ? 'text-gray-600 font-semibold' : '' }}">Data Rekonsiliasi</a>
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
                &copy; {{ date('Y') }} Kementerian Lingkungan Hidup dan Kehutanan. SISUDAH v1.0
            </p>
        </div>
    </footer>
</body>
</html>