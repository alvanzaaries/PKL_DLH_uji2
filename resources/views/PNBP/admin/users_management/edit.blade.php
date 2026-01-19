@extends('PNBP.layouts.admin')

@section('title', 'Edit User - Pelaporan PNBP')
@section('header', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto">
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6" role="alert">
            <p class="font-bold">Berhasil</p>
            <p class="text-sm mt-1">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Edit User</h2>
            <p class="text-sm text-gray-600">Ubah informasi user dan password.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <p class="py-2 px-3 bg-gray-50 rounded-md text-sm text-gray-700">
                    @if (($user->role ?? 'user') === 'admin')
                        Admin (Superadmin)
                    @else
                        User
                    @endif
                </p>
                @if ($user->id === auth()->id())
                    <p class="text-xs text-gray-500 mt-1">Anda sedang mengedit akun yang sedang login.</p>
                @endif
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-semibold text-gray-800">Ubah Password</h3>
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border">
                    </div>
                </div>

                <div class="mt-4">
                    <button form="resetPasswordForm" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Reset password untuk user ini? Password baru akan ditampilkan di notifikasi.');">
                        Reset Password (Generate)
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
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
