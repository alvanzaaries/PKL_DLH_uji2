@extends('PNBP.layouts.app')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Sistem Rekonsiliasi</span>
                        <span class="block text-green-600 xl:inline">Data LHK</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Platform terpadu untuk pengelolaan, validasi, dan pelaporan data sumber daya hutan. Unggah dan proses data rekonsiliasi triwulan dengan mudah dan akurat.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            @if (auth()->check())
                                @php
                                    $role = (auth()->user()->role ?? 'user');
                                    $dashboardUrl = $role === 'admin' ? route('dashboard.index') : route('user.upload');
                                @endphp
                                <a href="{{ route('dashboard.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 md:py-4 md:text-lg">
                                    Masuk ke Dashboard
                                </a>
                            @else
                                
                            @endif
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            @if (auth()->check())
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('welcome-logout-form').submit();"
                                   class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 md:py-4 md:text-lg">
                                    Logout
                                </a>
                                <form id="welcome-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('dashboard.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 md:py-4 md:text-lg">
                                    Masuk ke Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-green-50 flex items-center justify-center h-full">
        <!-- Illustration -->
        <svg class="h-56 w-56 text-green-200" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
            <path d="M7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/>
        </svg>
    </div>
</div>
@endsection