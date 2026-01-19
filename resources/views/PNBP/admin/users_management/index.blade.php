@extends('PNBP.layouts.admin')

@section('title', 'Manajemen User - SIP-Jateng')
@section('header', 'Manajemen User')

@section('content')
<div class="max-w-6xl mx-auto">
    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-600 text-green-800 dark:text-green-200 p-4 mb-6" role="alert">
            <p class="font-bold">Berhasil</p>
            <p class="text-sm mt-1">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar User</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola akun user, role, dan password.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
            Tambah User
        </a>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if (($user->role ?? 'user') === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset password untuk user ini? Password baru akan ditampilkan di notifikasi.');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">Reset Password</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
