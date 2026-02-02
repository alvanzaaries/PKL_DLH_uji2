@extends('PNBP.layouts.admin')

@section('title', 'Raw Excel - ' . $reconciliation->original_filename)
@section('header')
    Raw View: {{ $reconciliation->original_filename }}
@endsection

@section('content')
    {{-- Kontainer Tampilan Raw Excel --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        {{-- Tab sheet Excel --}}
        <div class="bg-white border rounded shadow-sm p-4">
            <style>
                .raw-sheet-header {
                    text-align: center;
                    font-weight: 600;
                    color: #1f2937;
                    margin-bottom: 1rem;
                    line-height: 1.6;
                }
                .raw-sheet-header .raw-note {
                    font-weight: 500;
                    font-style: italic;
                    color: #374151;
                    margin-top: 0.5rem;
                }
            </style>
            @if (!empty($sheets))
                <div class="border-b mb-4">
                    <div class="flex flex-wrap gap-2" id="raw-sheet-tabs">
                        @foreach ($sheets as $index => $sheet)
                            <button
                                type="button"
                                class="px-3 py-2 text-sm rounded-t border border-b-0 {{ $index === 0 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200' }}"
                                data-sheet-tab="sheet-{{ $index }}"
                                data-sheet-url="{{ route('reconciliations.raw.sheet', ['reconciliation' => $reconciliation->id, 'sheetIndex' => $index]) }}"
                            >
                                {{ $sheet['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="overflow-auto" id="raw-sheet-panels">
                    @foreach ($sheets as $index => $sheet)
                        <div
                            class="raw-sheet-panel {{ $index === 0 ? '' : 'hidden' }}"
                            data-sheet-panel="sheet-{{ $index }}"
                            data-sheet-loaded="{{ $index === 0 ? '1' : '0' }}"
                        >
                            @if ($index === 0)
                                {!! $sheet['html'] !!}
                            @else
                                <div class="text-gray-500 text-sm">Memuat sheet...</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-600">Sheet tidak ditemukan pada file ini.</div>
            @endif
        </div>
        {{-- Tombol kembali ke detail rekonsiliasi --}}
        <div class="mt-4">
            <a href="{{ route('reconciliations.show', $reconciliation->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">&larr; Kembali</a>
        </div>
    </div>

    <script src="{{ asset('js/pnbp/admin/reconciliations/raw.js') }}">  </script>
@endsection
