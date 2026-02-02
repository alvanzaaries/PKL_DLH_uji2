(function () {
    const tabContainer = document.getElementById('raw-sheet-tabs');
    const panels = document.querySelectorAll('.raw-sheet-panel');

    if (!tabContainer || panels.length === 0) return;

    function renderHeader(panel) {
        const table = panel.querySelector('table');
        if (!table) return;

        const rows = Array.from(table.querySelectorAll('tr'));
        const lines = [];
        let lastHeaderRowIndex = -1;

        for (let i = 0; i < rows.length; i++) {
            const text = rows[i].textContent.replace(/\s+/g, ' ').trim();
            if (text) {
                lines.push(text);
                lastHeaderRowIndex = i;
                if (/catatan/i.test(text) || lines.length >= 6) {
                    break;
                }
            } else if (lines.length > 0 && i > lastHeaderRowIndex + 1) {
                break;
            }
        }

        if (lines.length === 0) return;

        const header = document.createElement('div');
        header.className = 'raw-sheet-header';
        header.innerHTML = lines
            .map(function (line) {
                if (/catatan/i.test(line)) {
                    return '<div class="raw-note">' + line + '</div>';
                }
                return '<div>' + line + '</div>';
            })
            .join('');

        table.parentNode.insertBefore(header, table);

        if (lastHeaderRowIndex >= 0) {
            for (let i = 0; i <= lastHeaderRowIndex; i++) {
                rows[i].style.display = 'none';
            }
        }
    }

    tabContainer.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-sheet-tab]');
        if (!btn) return;

        const target = btn.getAttribute('data-sheet-tab');
        const url = btn.getAttribute('data-sheet-url');

        tabContainer.querySelectorAll('[data-sheet-tab]').forEach(function (tab) {
            if (tab.getAttribute('data-sheet-tab') === target) {
                tab.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                tab.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
            } else {
                tab.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                tab.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
            }
        });

        panels.forEach(function (panel) {
            if (panel.getAttribute('data-sheet-panel') === target) {
                panel.classList.remove('hidden');

                if (panel.getAttribute('data-sheet-loaded') === '0' && url) {
                    panel.setAttribute('data-sheet-loaded', 'loading');
                    panel.innerHTML = '<div class="text-gray-500 text-sm">Memuat sheet...</div>';

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (res) {
                            if (!res.ok) throw new Error('Gagal memuat sheet');
                            return res.json();
                        })
                        .then(function (data) {
                            panel.innerHTML = data.html || '<div class="text-gray-500 text-sm">Sheet kosong.</div>';
                            panel.setAttribute('data-sheet-loaded', '1');
                            renderHeader(panel);
                        })
                        .catch(function () {
                            panel.innerHTML = '<div class="text-red-600 text-sm">Gagal memuat sheet.</div>';
                            panel.setAttribute('data-sheet-loaded', '0');
                        });
                }
            } else {
                panel.classList.add('hidden');
            }
        });
    });

    panels.forEach(function (panel) {
        if (panel.getAttribute('data-sheet-loaded') === '1') {
            renderHeader(panel);
        }
    });
})();