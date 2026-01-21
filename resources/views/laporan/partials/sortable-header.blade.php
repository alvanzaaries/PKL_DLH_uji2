{{-- Sortable Column Header Component --}}
{{-- Usage: @include('laporan.partials.sortable-header', ['column' => 'tanggal', 'label' => 'Tanggal']) --}}

@php
    $currentSort = request('sort_by');
    $currentDirection = request('sort_direction', 'asc');
    $isActive = $currentSort === $column;
    $nextDirection = ($isActive && $currentDirection === 'asc') ? 'desc' : 'asc';

    // Build URL with all current query parameters
    $params = request()->except(['sort_by', 'sort_direction']);
    $params['sort_by'] = $column;
    $params['sort_direction'] = $nextDirection;
    $sortUrl = request()->url() . '?' . http_build_query($params);
@endphp

<a href="{{ $sortUrl }}"
    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
    <span>{{ $label }}</span>
    <span style="display: inline-flex; flex-direction: column; font-size: 0.7rem; line-height: 0.6;">
        @if($isActive)
            @if($currentDirection === 'asc')
                <i class="fas fa-sort-up" style="color: #0F2F24; margin-bottom: -2px;"></i>
                <i class="fas fa-sort-down" style="color: #D1D5DB;"></i>
            @else
                <i class="fas fa-sort-up" style="color: #D1D5DB; margin-bottom: -2px;"></i>
                <i class="fas fa-sort-down" style="color: #0F2F24;"></i>
            @endif
        @else
            <i class="fas fa-sort-up" style="color: #D1D5DB; margin-bottom: -2px;"></i>
            <i class="fas fa-sort-down" style="color: #D1D5DB;"></i>
        @endif
    </span>
</a>