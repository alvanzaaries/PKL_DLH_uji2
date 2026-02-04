@extends('admin.layouts.app')

@section('title', 'Manajemen User - Admin Panel')
@section('header', 'Manajemen User')
@section('subheader', 'Kelola akun pengguna sistem')

@section('content')
<div class="space-y-6">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-600 text-green-800 dark:text-green-200 p-4 rounded-r-lg" role="alert">
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
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 rounded-r-lg" role="alert">
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

    {{-- Card Header --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar User</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Kelola akun user, role, dan password.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                    <span class="material-icons-outlined text-sm mr-2">person_add</span>
                    Tambah User
                </a>
            </div>
        </div>

        {{-- Tabel Daftar User --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if (($user->role ?? 'user') === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                        <span class="material-icons-outlined text-xs mr-1">admin_panel_settings</span>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200">
                                        <span class="material-icons-outlined text-xs mr-1">person</span>
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center text-green-600 hover:text-green-900 dark:hover:text-green-400 transition-colors">
                                    <span class="material-icons-outlined text-sm mr-1">edit</span>
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset password untuk user ini?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900 dark:hover:text-red-400 transition-colors">
                                        <span class="material-icons-outlined text-sm mr-1">lock_reset</span>
                                        Reset Password
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2">people_outline</span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada user.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
