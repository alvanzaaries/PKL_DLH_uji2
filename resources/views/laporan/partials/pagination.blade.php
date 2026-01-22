{{-- Pagination Component --}}
@if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
    <div class="pagination-wrapper">
        {{-- Showing X to Y of Z entries --}}
        <div class="pagination-info">
            Menampilkan <strong>{{ $items->firstItem() }}</strong> sampai <strong>{{ $items->lastItem() }}</strong> dari
            <strong>{{ $items->total() }}</strong> data
        </div>

        {{-- Pagination Links --}}
        <div class="pagination-links">
            {{ $items->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif