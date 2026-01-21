{{-- Pagination Component  --}}
@if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
    <div class="pagination-wrapper"
        style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: #F9FAFB; border-top: 1px solid #E5E7EB;">
        {{-- Showing X to Y of Z entries --}}
        <div class="pagination-info" style="font-size: 0.875rem; color: #6B7280;">
            Menampilkan <strong>{{ $items->firstItem() }}</strong> sampai <strong>{{ $items->lastItem() }}</strong> dari
            <strong>{{ $items->total() }}</strong> data
        </div>

        {{-- Pagination Links --}}
        <div class="pagination-links">
            {{ $items->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <style>
        /* Custom Pagination Styling */
        .pagination {
            margin: 0;
            display: flex;
            gap: 4px;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            border: 1px solid #D1D5DB;
            background-color: white;
            color: #374151;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background-color: #F3F4F6;
            border-color: #9CA3AF;
        }

        .pagination .page-item.active .page-link {
            background-color: #0F2F24;
            border-color: #0F2F24;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #F3F4F6;
            border-color: #E5E7EB;
            color: #9CA3AF;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>
@endif