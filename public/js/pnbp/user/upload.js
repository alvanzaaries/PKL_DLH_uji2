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

        function resetUI() {
            fileInput.value = '';
            emptyState.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            dropZone.classList.remove('border-green-500', 'bg-green-50');
            dropZone.classList.add('border-gray-300');
        }

        fileInput.addEventListener('change', function() {
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

        // Whole box clickable
        dropZone.addEventListener('click', function(e) {
            // Prevent if clicking remove button (though remove button is hidden in empty state, and outside empty state logic handles it? 
            // In user blade, removeBtn IS inside dropZone container? No, let's check structure.
            // <div id="drop-zone"> ... <div id="file-info"> ... <button id="remove-file"> ... </div> </div>
            // Yes, remove button is inside dropZone. So we need to stop propagation or check target.
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
        
        function highlight() {
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }

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
