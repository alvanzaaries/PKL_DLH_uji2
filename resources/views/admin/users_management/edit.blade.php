@extends('admin.layouts.app')

@section('title', 'Edit User - Admin Panel')
@section('header', 'Edit User')
@section('subheader', 'Ubah informasi akun pengguna')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-600 dark:border-green-700 text-green-800 dark:text-green-200 p-4 mb-6 rounded-r-lg" role="alert">
            <div class="flex items-center gap-2">
                <span class="material-icons-outlined text-green-600 dark:text-green-400">check_circle</span>
                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-700 text-red-700 dark:text-red-200 p-4 mb-6 rounded-r-lg" role="alert">
            <div class="flex items-start gap-2">
                <span class="material-icons-outlined text-red-500 mt-0.5">error</span>
                <div>
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul class="list-disc pl-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Edit User --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <span class="material-icons-outlined text-green-600 dark:text-green-400">edit</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit User</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ubah informasi user dan password.</p>
                </div>
            </div>
        </div>

        {{-- Form Update Data User --}}
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Role</label>
                <select name="role" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @if ($user->id === auth()->id())
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                        <span class="material-icons-outlined text-xs">warning</span>
                        Anda sedang mengedit akun yang sedang login. Hati-hati mengubah role sendiri.
                    </p>
                @endif
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Admin memiliki akses penuh termasuk mengelola user lain.</p>
            </div>

            {{-- Bagian Ubah Password --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="material-icons-outlined text-sm">lock</span>
                    Ubah Password
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Password Baru</label>
                        <input type="password" name="password" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm py-2 px-3 border focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="mt-4">
                    <button form="resetPasswordForm" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors" onclick="return confirm('Reset password untuk user ini?');">
                        <span class="material-icons-outlined text-sm mr-2">lock_reset</span>
                        Reset Password
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-icons-outlined text-sm mr-2">arrow_back</span>
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                    <span class="material-icons-outlined text-sm mr-2">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <form id="resetPasswordForm" action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection
