@extends('PNBP.layouts.admin')

@section('title', 'Raw Excel - ' . $reconciliation->original_filename)
@section('header')
    Raw View: {{ $reconciliation->original_filename }}
@endsection

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white border rounded shadow-sm p-4 overflow-auto">
            {!! $rawHtml !!}
        </div>
        <div class="mt-4">
            <a href="{{ route('reconciliations.show', $reconciliation->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">&larr; Kembali</a>
        </div>
    </div>
@endsection
