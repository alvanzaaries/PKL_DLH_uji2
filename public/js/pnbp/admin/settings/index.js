document.addEventListener('DOMContentLoaded', function () {
    // --- 1. SEARCH FUNCTIONALITY ---
    const searchInput = document.getElementById('searchKph');
    const table = document.getElementById('kphTable');
    const noResultRow = document.getElementById('noResultRow');

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            let hasVisibleRow = false;

            // Loop rows (mulai index 1 karena index 0 adalah header)
            for (let i = 1; i < rows.length; i++) {
                // Skip row "noResultRow" atau "empty state" bawaan
                if (rows[i].id === 'noResultRow' || rows[i].cells.length < 2) continue;

                const nameCell = rows[i].querySelector('.kph-name');
                if (nameCell) {
                    const txtValue = nameCell.textContent || nameCell.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                        hasVisibleRow = true;
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }

            // Show/Hide "Tidak ditemukan" message
            if (noResultRow) {
                if (!hasVisibleRow && filter !== '') {
                    noResultRow.classList.remove('hidden');
                } else {
                    noResultRow.classList.add('hidden');
                }
            }
        });
    }

    // --- 2. MODAL FUNCTIONALITY ---
    // Expose functions globally for onclick handlers in HTML if needed, 
    // or attach event listeners here if IDs are present.
    // The Blade template uses onclick="openKphModal()" and closeKphModal().
    // We can define these on window.
    
    window.openKphModal = function() {
        const modal = document.getElementById('kphModal');
        const input = document.getElementById('nama');
        if (modal) {
            modal.classList.remove('hidden');
            if (input) input.focus(); 
        }
    };

    window.closeKphModal = function() {
        const modal = document.getElementById('kphModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Close modal on ESC key
    document.addEventListener('keydown', function(evt) {
        if (evt.key === 'Escape') {
            window.closeKphModal();
        }
    });
});
