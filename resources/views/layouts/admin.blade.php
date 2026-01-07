<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - SISUDAH')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white hidden md:flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <span class="text-xl font-bold text-green-500 tracking-wider">SISUDAH</span>
            </div>
            
            <div class="flex-grow overflow-y-auto py-4">
                <nav class="space-y-1 px-2">
                    <a href="{{ route('dashboard.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-home mr-3 text-lg {{ request()->routeIs('dashboard.*') ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500' }}"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('reconciliations.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reconciliations.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-file-excel mr-3 text-lg {{ request()->routeIs('reconciliations.*') ? 'text-green-500' : 'text-gray-400 group-hover:text-green-500' }}"></i>
                        Data Rekonsiliasi
                    </a>

                    <!-- Placeholder Links -->
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-users mr-3 text-lg text-gray-400 group-hover:text-green-500"></i>
                         Manajemen User
                    </a>
                    <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-cog mr-3 text-lg text-gray-400 group-hover:text-green-500"></i>
                        Pengaturan
                    </a>
                </nav>
            </div>

            <div class="border-t border-gray-800 p-4">
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-400">Login sebagai</p>
                        <p class="text-sm font-medium text-white">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar (Mobile trigger could go here) -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-green-600">Lihat Situs Depan</a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
