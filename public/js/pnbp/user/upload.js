// Menangani interaksi dropzone dan submit di halaman upload user.
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
        // Memperbarui tampilan berdasarkan file yang dipilih.
        function updateUI(file) {
            if (file) {
                emptyState.classList.add('hidden');
                fileInfo.classList.remove('hidden');
                filenameDisplay.textContent = file.name;
                filesizeDisplay.textContent = (file.size / 1024).toFixed(2) + ' KB';
                dropZone.classList.add('border-green-500', 'bg-green-50');
                dropZone.classList.remove('border-gray-300');
            } else {
                resetUI();
            }
        }

        // Mengembalikan UI ke kondisi awal tanpa file.
        function resetUI() {
            fileInput.value = '';
            emptyState.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('border-green-500', 'bg-green-50');
            dropZone.classList.add('border-gray-300');
        }

        // Menangani perubahan file input.
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateUI(this.files[0]);
            }
        });

        if (removeBtn) {
            // Menghapus pilihan file dan reset UI.
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                resetUI();
            });
        }

        // Membuat seluruh dropzone bisa diklik untuk membuka file picker.
        dropZone.addEventListener('click', function(e) {
            // Cegah trigger jika klik tombol hapus.
            if (e.target && (e.target.id === 'remove-file' || (e.target.closest && e.target.closest('#remove-file')))) {
                return;
            }
            fileInput.click();
        });

        // Menghentikan perilaku default browser saat drag & drop.
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        // Menambahkan highlight saat drag masuk/over.
        function highlight() {
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }

        // Menghapus highlight saat drag keluar/drop tanpa file terpilih.
        function unhighlight() {
            if (fileInput.files.length === 0) {
                dropZone.classList.remove('border-green-500', 'bg-green-50');
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
