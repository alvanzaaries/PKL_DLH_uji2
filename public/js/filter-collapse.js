/**
 * Filter Collapse Component - Reusable JavaScript
 * Handles filter toggle, active filter counting, and initialization
 */

// Toggle Filter visibility
function toggleFilter() {
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterIcon');

    if (filterBody && filterIcon) {
        filterBody.classList.toggle('show');
        filterIcon.classList.toggle('collapsed');
    }
}

// Count and display active filters
function updateActiveFilterCount(filterParams = []) {
    const params = new URLSearchParams(window.location.search);
    let count = 0;

    // Default filter parameters if not provided
    const defaultFilterParams = [
        'nama', 'search', 'kabupaten', 'jenis_produksi',
        'kapasitas', 'pemberi_izin', 'sumber_bahan_baku',
        'bulan', 'tahun', 'status'
    ];

    const paramsToCheck = filterParams.length > 0 ? filterParams : defaultFilterParams;

    paramsToCheck.forEach(param => {
        if (params.get(param)) count++;
    });

    const countElement = document.getElementById('activeFilterCount');
    if (countElement) {
        if (count > 0) {
            countElement.textContent = `(${count} filter aktif)`;
            countElement.style.color = '#a7f3d0';
            countElement.style.fontWeight = '600';
        } else {
            countElement.textContent = '';
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    updateActiveFilterCount();

    // Add hover functionality to filter card
    const filterCard = document.querySelector('.filter-card');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterIcon');

    if (filterCard && filterBody && filterIcon) {
        // Expand filter when mouse enters the filter card
        filterCard.addEventListener('mouseenter', function () {
            if (!filterBody.classList.contains('show')) {
                filterBody.classList.add('show');
                filterIcon.classList.remove('collapsed');
            }
        });

        // Collapse filter when mouse leaves the filter card
        filterCard.addEventListener('mouseleave', function () {
            if (filterBody.classList.contains('show')) {
                filterBody.classList.remove('show');
                filterIcon.classList.add('collapsed');
            }
        });
    }
});
