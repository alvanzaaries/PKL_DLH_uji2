// Menyiapkan UI tombol upload saat halaman siap.
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('uploadBtn');
    const spinner = document.getElementById('uploadSpinner');
    const text = document.getElementById('uploadBtnText');
    const hint = document.getElementById('uploadHint');

    if (form && btn && spinner && text) {
        // Menonaktifkan tombol dan menampilkan spinner saat submit.
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'pointer-events-none');
            spinner.classList.remove('hidden');
            if (hint) hint.classList.remove('hidden');
            text.textContent = 'Mengupload…';
            form.setAttribute('aria-busy', 'true');
        });
    }
});
