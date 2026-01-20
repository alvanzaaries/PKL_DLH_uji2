@extends('PNBP.layouts.admin')

@section('title', 'Pengaturan - Pelaporan PNBP')
@section('header', 'Pengaturan Sistem')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- 1. Pengaturan Tampilan (Tema) --}}
    <div class="bg-white dark:bg-surface-dark shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="material-icons-outlined mr-2">palette</span>
            Tampilan & Tema
        </h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
            Sesuaikan tampilan aplikasi dengan preferensi Anda (Mode Terang / Gelap).
        </p>
        <button id="toggleDarkSettings" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            <span class="material-icons-outlined mr-2">dark_mode</span>
            Ganti Mode Tampilan
        </button>
    </div>

    {{-- 2. Manajemen User (Shortcut) --}}
    <div class="bg-white dark:bg-surface-dark shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="material-icons-outlined mr-2">group</span>
            Manajemen Pengguna
        </h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
            Kelola akun pengguna yang memiliki akses ke sistem ini.
        </p>
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-900 dark:text-white font-semibold">
                Total User: {{ $usersCount ?? '-' }}
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary_hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Kelola User
            </a>
        </div>
    </div>

    {{-- 3. Manajemen KPH (Master Data) --}}
    <div class="col-span-1 md:col-span-2 bg-white dark:bg-surface-dark shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="material-icons-outlined mr-2">dns</span>
            Master Data KPH
        </h3>
        
        <div class="mb-6 bg-gray-50 dark:bg-gray-800 p-4 rounded-md">
            <form action="{{ route('admin.settings.kph.store') }}" method="POST" class="flex gap-4">
                @csrf
                <div class="flex-1">
                    <label for="nama" class="sr-only">Nama KPH</label>
                    <input type="text" name="nama" id="nama" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Masukkan Nama KPH Baru..." required>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span class="material-icons-outlined mr-1">add</span> Tambah
                </button>
            </form>
            @error('nama')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama KPH</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($kphs as $kph)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $kph->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.settings.kph.destroy', $kph->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus KPH ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                        <span class="material-icons-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data KPH.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
