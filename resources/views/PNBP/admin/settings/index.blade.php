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

    {{-- 3. Manajemen KPH (Data Master) --}}
    {{-- 3. Manajemen KPH (Data Master) --}}
    <div class="col-span-1 md:col-span-2 bg-white dark:bg-surface-dark shadow-md rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        
        {{-- Header & Alat --}}
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                    <span class="material-icons-outlined mr-2 text-primary">dns</span>
                    Data KPH/KPS
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Total KPH/KPS Terdaftar: <span class="font-semibold text-gray-900 dark:text-white">{{ $kphs->count() }}</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                {{-- Input Pencarian --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-outlined text-gray-400 text-sm">search</span>
                    </div>
                    <input type="text" id="searchKph" class="block w-full sm:w-64 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out" placeholder="Cari nama KPH/KPS...">
                </div>

                {{-- Tombol Tambah --}}
                {{-- Buka modal tambah KPH. --}}
                <button type="button" onclick="openKphModal()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary_hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm transition-all hover:shadow-md">
                    <span class="material-icons-outlined text-sm mr-2">add</span>
                    Tambah KPH/KPS
                </button>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="kphTable">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nama KPH/KPS
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($kphs as $kph)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white kph-name">
                                    {{ $kph->nama }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <!-- Konfirmasi sebelum menghapus KPH. -->
                                <form action="{{ route('admin.settings.kph.destroy', $kph->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $kph->nama }}?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20" title="Hapus">
                                        <span class="material-icons-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-icons-outlined text-gray-300 text-4xl mb-2">folder_off</span>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada data KPH/KPS yang ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    {{-- Baris hasil kosong (disembunyikan secara default) --}}
                    <tr id="noResultRow" class="hidden">
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            Tidak ditemukan KPH/KPS dengan nama tersebut.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Catatan footer/pagination (opsional) --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan seluruh data KPH/KPS aktif.
            </p>
        </div>
    </div>

    {{-- MODAL TAMBAH KPH --}}
    <div id="kphModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Latar modal --}}
            <!-- Klik backdrop untuk menutup modal. -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeKphModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Content --}}
            <div class="inline-block align-bottom bg-white dark:bg-surface-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('admin.settings.kph.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-surface-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-icons-outlined text-green-600">add_business</span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Tambah KPH/KPS Baru
                                </h3>
                                <div class="mt-4">
                                    <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama KPH/KPS</label>
                                    <input type="text" name="nama" id="nama" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md p-2" placeholder="Contoh: KPH/KPS ..." required>
                                    @error('nama')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary_hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <!-- Menutup modal tanpa menyimpan. -->
                        <button type="button" onclick="closeKphModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script JavaScript untuk pencarian dan modal --}}
    <script src="{{ asset('js/pnbp/admin/settings/index.js') }}"></script>

</div>
@endsection
