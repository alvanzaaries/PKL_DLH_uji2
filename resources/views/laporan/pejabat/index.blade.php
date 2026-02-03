@extends('laporan.layouts.layout')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Pejabat Penandatangan</h1>
                <p class="text-gray-600">Atur pejabat yang bertanda tangan di Bukti Tanda Terima.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Tambah -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Pejabat Baru</h3>
                    <form action="{{ route('pejabat.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">
                                Nama Lengkap
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="nama" type="text" name="nama" placeholder="Contoh: Widi Hartanto" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nip">
                                NIP (Opsional)
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="nip" type="text" name="nip" placeholder="Nomor Induk Pegawai">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="pangkat">
                                Pangkat / Golongan
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="pangkat" type="text" name="pangkat" placeholder="Contoh: Pembina Utama Muda">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="jabatan">
                                Jabatan
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="jabatan" type="text" name="jabatan" placeholder="Kepala Dinas..." required
                                value="Kepala Dinas Lingkungan Hidup dan Kehutanan Provinsi Jawa Tengah">
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    class="form-checkbox h-4 w-4 text-green-600">
                                <span class="ml-2 text-gray-700 text-sm">Set sebagai Pejabat Aktif</span>
                            </label>
                        </div>
                        <div class="flex items-center justify-end">
                            <button
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition"
                                type="submit">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Pejabat</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50 text-gray-700 uppercase leading-normal">
                                <tr>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama & NIP</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jabatan</th>
                                    <th
                                        class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($pejabats as $p)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-left whitespace-nowrap">
                                            @if($p->is_active)
                                                <span
                                                    class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs font-bold">Aktif</span>
                                            @else
                                                <form action="{{ route('pejabat.activate', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-gray-400 hover:text-green-600 text-xs font-semibold py-1 px-2 border border-gray-300 rounded hover:border-green-600 transition">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800">{{ $p->nama }}</div>
                                                    <div class="text-xs">{{ $p->nip ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <div class="font-medium">{{ $p->jabatan }}</div>
                                            <div class="text-xs text-gray-500">{{ $p->pangkat }}</div>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center">
                                                @if(!$p->is_active)
                                                    <form action="{{ route('pejabat.destroy', $p->id) }}" method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection