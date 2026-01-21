@extends('PNBP.layouts.app')

@section('content')
<div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-gray-100">
    <div class="flex flex-col lg:flex-row min-h-[600px]">
        <!-- Left Content -->
        <div class="w-full lg:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-white order-2 lg:order-1">
            <div class="max-w-2xl mx-auto lg:mx-0">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl mb-6">
                    <span class="block">Sistem Pelaporan</span>
                    <span class="block text-green-600">PNBP</span>
                </h1>
                <p class="mt-4 text-lg text-gray-500 mb-8 leading-relaxed">
                    Platform terpadu untuk pengelolaan, validasi, dan pelaporan PNBP. Unggah dan proses data rekonsiliasi triwulan dengan mudah dan akurat.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    @if (auth()->check())
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150 ease-in-out md:py-4 md:text-lg shadow-lg hover:shadow-green-500/30">
                            Masuk ke Dashboard
                        </a>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('welcome-logout-form').submit();"
                           class="inline-flex items-center justify-center px-8 py-3 border border-green-600 text-base font-medium rounded-lg text-green-700 bg-white hover:bg-green-50 transition duration-150 ease-in-out md:py-4 md:text-lg">
                            Logout
                        </a>
                        <form id="welcome-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150 ease-in-out md:py-4 md:text-lg shadow-lg hover:shadow-green-500/30">
                            Masuk ke Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Image -->
        <div class="w-full lg:w-1/2 bg-green flex items-center justify-center relative order-1 lg:order-2 p-10">
            <div class="absolute inset-0 bg-green-50/50 pattern-grid-lg text-green-100 mask-image-gradient-b"></div>
            <img src="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}" 
                 alt="Logo Jateng" 
                 class="relative z-10 h-64 w-64 md:h-80 md:w-80 object-contain drop-shadow-2xl transition-transform hover:scale-105 duration-500">
        </div>
    </div>
</div>
@endsection