document.addEventListener('DOMContentLoaded', function () {
    // --- Dropzone Logic ---
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-upload');
    const emptyState = document.getElementById('empty-state');
    const fileInfo = document.getElementById('file-info');
    const filenameDisplay = document.getElementById('selected-filename');
    const filesizeDisplay = document.getElementById('selected-filesize');
    const removeBtn = document.getElementById('remove-file');

    if (dropZone && fileInput) {
        // Classes untuk state aktif/sukses (Tema Primary)
        // Note: Using configured classes matching the Blade template
        const activeClasses = ['border-primary', 'bg-primary/5', 'dark:bg-primary/10'];
        const defaultBorder = ['border-gray-300', 'dark:border-gray-600'];

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

        function resetUI() {
            fileInput.value = '';
            emptyState.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            
            dropZone.classList.remove(...activeClasses);
            dropZone.classList.add(...defaultBorder);
        }

        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                updateUI(this.files[0]);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                resetUI();
            });
        }

        dropZone.addEventListener('click', function(e) {
            // Prevent triggering if clicked on remove button
            if (e.target && (e.target.id === 'remove-file' || (e.target.closest && e.target.closest('#remove-file')))) {
                return;
            }
            fileInput.click();
        });

        // Drag and Drop Events
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function highlight(e) {
            dropZone.classList.add(...activeClasses);
        }

        function unhighlight(e) {
            // Only remove highlight if no file is selected (or dragging left)
            // Logic adapted: original code removed highlight on dragleave if no file selected? 
            // Original code: if (fileInput.files.length === 0) dropZone.classList.remove(...activeClasses);
            // This allows the "active" style to stay if a file is already there? 
            // Actually, usually you want drag-over style to be distinct or same. 
            // Based on original code:
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

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateUI(files[0]);
            }
        }
    }

    // --- Form Submit Logic ---
    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('uploadBtn');
    const spinner = document.getElementById('uploadSpinner');
    const text = document.getElementById('uploadBtnText');

    if (form && btn && spinner && text) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            spinner.classList.remove('hidden');
            text.textContent = 'Mengupload…';
            form.setAttribute('aria-busy', 'true');
        });
    }
});
