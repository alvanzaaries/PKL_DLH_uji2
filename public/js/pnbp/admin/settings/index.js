// Inisialisasi fitur halaman pengaturan saat DOM siap.
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. PENCARIAN KPH ---
    const searchInput = document.getElementById('searchKph');
    const table = document.getElementById('kphTable');
    const noResultRow = document.getElementById('noResultRow');

    if (searchInput && table) {
        // Menyaring baris tabel KPH berdasarkan input pencarian.
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            let hasVisibleRow = false;

            // Loop baris (mulai index 1 karena index 0 adalah header)
            for (let i = 1; i < rows.length; i++) {
                // Lewati baris "noResultRow" atau "empty state" bawaan
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

            // Tampilkan/Sembunyikan pesan "Tidak ditemukan"
            if (noResultRow) {
                if (!hasVisibleRow && filter !== '') {
                    noResultRow.classList.remove('hidden');
                } else {
                    noResultRow.classList.add('hidden');
                }
            }
        });
    }

    // --- 2. MODAL KPH ---
    // Definisikan fungsi global karena Blade memanggil onclick="openKphModal()" dan closeKphModal().
    
    // Membuka modal input KPH dan fokus ke input nama.
    window.openKphModal = function() {
        const modal = document.getElementById('kphModal');
        const input = document.getElementById('nama');
        if (modal) {
            modal.classList.remove('hidden');
            if (input) input.focus(); 
        }
    };

    // Menutup modal input KPH.
    window.closeKphModal = function() {
        const modal = document.getElementById('kphModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Menutup modal saat pengguna menekan tombol ESC.
    document.addEventListener('keydown', function(evt) {
        if (evt.key === 'Escape') {
            window.closeKphModal();
        }
    });
});
