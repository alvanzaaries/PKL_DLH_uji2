// Menangani dropzone dan submit pada form upload admin.
document.addEventListener('DOMContentLoaded', function () {
    // --- Logika Dropzone ---
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-upload');
    const emptyState = document.getElementById('empty-state');
    const fileInfo = document.getElementById('file-info');
    const filenameDisplay = document.getElementById('selected-filename');
    const filesizeDisplay = document.getElementById('selected-filesize');
    const removeBtn = document.getElementById('remove-file');

    if (dropZone && fileInput) {
        // Kelas untuk state aktif/sukses (tema primary).
        // Catatan: kelas mengikuti template Blade.
        const activeClasses = ['border-primary', 'bg-primary/5', 'dark:bg-primary/10'];
        const defaultBorder = ['border-gray-300', 'dark:border-gray-600'];

        // Memperbarui tampilan dropzone berdasarkan file yang dipilih.
        function updateUI(file) {
            if (file) {
                emptyState.classList.add('hidden');
                fileInfo.classList.remove('hidden');
                filenameDisplay.textContent = file.name;
                filesizeDisplay.textContent = (file.size / 1024).toFixed(2) + ' KB';
                
                dropZone.classList.add(...activeClasses);
                dropZone.classList.remove(...defaultBorder);
            } else {
                resetUI();
            }
        }

        // Mengembalikan tampilan dropzone ke kondisi awal.
        function resetUI() {
            fileInput.value = '';
            emptyState.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            
            dropZone.classList.remove(...activeClasses);
            dropZone.classList.add(...defaultBorder);
        }

        // Menangani perubahan file input.
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                updateUI(this.files[0]);
            }
        });

        if (removeBtn) {
            // Menghapus file yang sudah dipilih.
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                resetUI();
            });
        }

        // Membuka file picker saat dropzone diklik.
        dropZone.addEventListener('click', function(e) {
            // Cegah trigger jika klik tombol hapus.
            if (e.target && (e.target.id === 'remove-file' || (e.target.closest && e.target.closest('#remove-file')))) {
                return;
            }
            fileInput.click();
        });

        // Mencegah aksi default browser saat drag & drop.
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        // Menambahkan highlight saat drag masuk/over.
        function highlight(e) {
            dropZone.classList.add(...activeClasses);
        }

        // Menghapus highlight saat drag keluar jika belum ada file.
        function unhighlight(e) {
            if (fileInput.files.length === 0) {
                dropZone.classList.remove(...activeClasses);
            }
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        dropZone.addEventListener('drop', handleDrop, false);

        // Memproses file yang dijatuhkan ke dropzone.
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateUI(files[0]);
            }
        }
    }

    // --- Logika Submit Form ---
    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('uploadBtn');
    const spinner = document.getElementById('uploadSpinner');
    const text = document.getElementById('uploadBtnText');

    if (form && btn && spinner && text) {
        // Mengunci tombol dan menampilkan spinner saat submit.
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            spinner.classList.remove('hidden');
            text.textContent = 'Mengupload…';
            form.setAttribute('aria-busy', 'true');
        });
    }
});
