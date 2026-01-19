@extends('PNBP.layouts.admin')

@section('title', 'Riwayat Rekonsiliasi - Pelaporan PNBP')
@section('header', 'Riwayat Rekonsiliasi')
@section('subheader', 'Daftar semua proses rekonsiliasi yang telah diunggah ke dalam sistem.')

@section('header_actions')
    <a href="{{ route('reconciliations.create') }}" class="w-full md:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary hover:bg-primary_hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
        <span class="material-icons-outlined text-lg mr-2">cloud_upload</span>
        Upload Data Baru
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 border-l-4 border-green-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-icons-outlined text-green-500">check_circle</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('reconciliations.index') }}" class="flex flex-col lg:flex-row gap-4 justify-between items-center">
            <div class="relative w-full lg:w-96 group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-icons-outlined text-gray-400 group-focus-within:text-primary transition-colors">search</span>
                </div>
                <input
                    name="search"
                    value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow"
                    placeholder="Cari file atau user..."
                    type="text"
                />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative w-full sm:w-32">
                    <select name="year" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                        <option value="">Semua Tahun</option>
                        @foreach(($availableYears ?? []) as $y)
                            <option value="{{ $y }}" @selected((string)request('year') === (string)$y)>{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <span class="material-icons-outlined text-sm">expand_more</span>
                    </div>
                </div>

                <div class="relative w-full sm:w-40">
                    <select name="quarter" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white appearance-none cursor-pointer shadow-sm">
                        <option value="">Semua Triwulan</option>
                        @for($q = 1; $q <= 4; $q++)
                            <option value="{{ $q }}" @selected((string)request('quarter') === (string)$q)>Triwulan {{ $q }}</option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <span class="material-icons-outlined text-sm">expand_more</span>
                    </div>
                </div>

                <button class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm" type="submit">
                    <span class="material-icons-outlined text-lg mr-2">filter_alt</span>
                    Terapkan
                </button>

                @if(request()->filled('search') || request()->filled('year') || request()->filled('quarter'))
                    <a href="{{ route('reconciliations.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <span class="material-icons-outlined text-lg mr-2">restart_alt</span>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Tahun</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Triwulan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">File</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Diunggah Oleh</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Waktu Upload</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-surface-light dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($reconciliations as $item)
                        @php
                            $uploader = $item->uploader;
                            $uName = $uploader?->name ?? '-';
                            $uInitials = collect(explode(' ', trim((string)$uName)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                            $uInitials = $uInitials !== '' ? mb_strtoupper($uInitials) : 'U';
                            $isAdmin = $uploader && (($uploader->role ?? 'user') === 'admin');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $item->year }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    TW{{ (int)$item->quarter }} — Triwulan {{ $item->quarter }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="material-icons-outlined text-green-500 mr-2 text-xl">description</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $item->original_filename }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">{{ $uInitials }}</div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $uName }}</div>
                                        @if($uploader)
                                            <div class="text-xs text-primary dark:text-primary bg-primary/10 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $isAdmin ? 'Admin' : 'User' }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700" title="Detail" href="{{ route('reconciliations.show', $item->id) }}">
                                        <span class="material-icons-outlined">visibility</span>
                                    </a>
                                    <form action="{{ route('reconciliations.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? Semua detail data akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Hapus" type="submit">
                                            <span class="material-icons-outlined">delete_outline</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-gray-50/30 dark:bg-gray-800/20">
                            <td class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500 italic" colspan="6">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($reconciliations, 'total'))
            <div class="bg-surface-light dark:bg-surface-dark px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            Menampilkan <span class="font-medium text-gray-900 dark:text-white">{{ $reconciliations->firstItem() ?? 0 }}</span>
                            sampai <span class="font-medium text-gray-900 dark:text-white">{{ $reconciliations->lastItem() ?? 0 }}</span>
                            dari <span class="font-medium text-gray-900 dark:text-white">{{ $reconciliations->total() }}</span> hasil
                        </p>
                    </div>
                    <div>
                        {{ $reconciliations->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
