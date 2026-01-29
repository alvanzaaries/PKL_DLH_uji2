// Inisialisasi tema gelap/terang dan toggle sidebar.
(function () {
    const root = document.documentElement;
    const body = document.body;
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldDark = stored ? stored === 'dark' : prefersDark;

    // Menerapkan tema gelap/terang pada root dan body.
    const applyDark = (on) => {
        if (on) {
            root.classList.add('dark');
            body.classList.add('dark');
        } else {
            root.classList.remove('dark');
            body.classList.remove('dark');
        }
    };

    applyDark(shouldDark);

    // Mengubah tema dan menyimpan preferensi ke localStorage.
    const toggle = () => {
        const now = !(root.classList.contains('dark') || body.classList.contains('dark'));
        applyDark(now);
        localStorage.setItem('theme', now ? 'dark' : 'light');
    };

    document.getElementById('toggleDark')?.addEventListener('click', toggle);
    document.getElementById('toggleDarkDesktop')?.addEventListener('click', toggle);
    document.getElementById('toggleDarkSettings')?.addEventListener('click', toggle);

    // Menampilkan/menyembunyikan sidebar pada perangkat kecil.
    document.getElementById('toggleSidebar')?.addEventListener('click', () => {
        const s = document.getElementById('sidebar');
        if (!s) return;
        s.classList.toggle('hidden');
    });
})();