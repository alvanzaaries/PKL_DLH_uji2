@extends('PNBP.layouts.admin')

@section('title', 'Tambah User - Pelaporan PNBP')
@section('header', 'Tambah User')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-700 text-red-700 dark:text-red-200 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Tambah User --}}
    <div class="bg-white dark:bg-surface-dark shadow rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Tambah User</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Buat akun baru untuk admin atau user.</p>
        </div>

        {{-- Form Input User Baru --}}
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border" required>
            </div>

            <!-- Role dikelola sistem: akun baru selalu berperan sebagai user. -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Password</label>
                    <input type="password" name="password" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border" required>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimal 6 karakter.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border" required>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
