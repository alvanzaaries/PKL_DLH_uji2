// Menyiapkan interaksi tombol dan grafik pada halaman detail rekonsiliasi.
document.addEventListener('DOMContentLoaded', function () {
    // Logika tombol reset filter
    const resetBtn = document.getElementById('resetBtn');
    if (resetBtn) {
        // Mengosongkan pencarian dan submit ulang filter.
        resetBtn.addEventListener('click', function(){
            const f = document.getElementById('filterForm');
            if(!f) return;
            f.querySelector('input[name="search"]').value = '';
            f.submit();
        });
    }

    // Logika chart sebaran wilayah
    const canvas = document.getElementById('wilayahChart');
    if (canvas && typeof Chart !== 'undefined') {
        try {
            // Get data from data attributes
            const rawLabels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
            const rawData = JSON.parse(canvas.getAttribute('data-values') || '[]');

            if (rawLabels.length > 0) {
                const ctx = canvas.getContext('2d');
                const legendContainer = document.getElementById('wilayahLegend');

                // Palet IBM Carbon: 5 hijau, 5 teal, 5 ungu
                const carbonPalette = [
                    '#a7f0ba', '#6fdc8c', '#42be65', '#24a148', '#198038',
                    '#9ef0f0', '#3ddbd9', '#08bdba', '#009d9a', '#007d79',
                    '#e8daff', '#d4bbff', '#be95ff', '#a56eff', '#8a3ffc'
                ];
                const dynamicColors = rawLabels.map((_, i) => carbonPalette[i % carbonPalette.length]);

                const isDark = document.documentElement.classList.contains('dark');
                const legendTextColor = isDark ? '#e5e7eb' : '#374151';
                const tooltipBg = isDark ? '#111827' : '#ffffff';
                const tooltipBorder = isDark ? '#374151' : '#e5e7eb';
                const totalValue = rawData.reduce((sum, v) => sum + (Number(v) || 0), 0);

                // Plugin untuk menampilkan persentase pada irisan doughnut.
                const percentageLabels = {
                    id: 'percentageLabels',
                    // Menggambar label persentase setelah dataset dirender.
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        const meta = chart.getDatasetMeta(0);
                        if (!meta || !meta.data) return;

                        ctx.save();
                        ctx.font = '13px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = isDark ? '#f9fafb' : '#111827';

                        meta.data.forEach((arc, i) => {
                            const value = Number(rawData[i]) || 0;
                            if (!value || !totalValue) return;
                            const percentage = (value / totalValue) * 100;
                            if (percentage < 2) return;

                            const pos = arc.tooltipPosition();
                            ctx.fillText(`${percentage.toFixed(1)}%`, pos.x, pos.y);
                        });

                        ctx.restore();
                    }
                };

                const chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    plugins: [percentageLabels],
                    data: {
                        labels: rawLabels,
                        datasets: [{
                            data: rawData,
                            backgroundColor: dynamicColors, 
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                                position: 'right',
                                labels: {
                                    boxWidth: 10,
                                    font: { size: 10 },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                if (i > 9) return null; 
                                                const meta = chart.getDatasetMeta(0);
                                                const style = meta.controller.getStyle(i);
                                                return {
                                                    text: label,
                                                    fillStyle: style.backgroundColor,
                                                    strokeStyle: style.borderColor,
                                                    lineWidth: style.borderWidth,
                                                    hidden: isNaN(data.datasets[0].data[i]) || meta.data[i].hidden,
                                                    index: i
                                                };
                                            }).filter(item => item !== null);
                                        }
                                        return [];
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                borderColor: tooltipBorder,
                                borderWidth: 1,
                                titleColor: legendTextColor,
                                bodyColor: legendTextColor,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {label += ': '};
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(context.raw);
                                        return label;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: 0
                        }
                    }
                });

                // Merender legend kustom agar selaras dengan tema.
                const renderLegend = () => {
                    if (!legendContainer) return;
                    legendContainer.innerHTML = '';

                    const darkMode = document.documentElement.classList.contains('dark');
                    const textClass = darkMode ? 'text-gray-200' : 'text-gray-700';

                    rawLabels.forEach((label, i) => {
                        if (i > 9) return;
                        const item = document.createElement('div');
                        item.className = `flex items-center gap-2 text-xs ${textClass} transition-transform duration-200 hover:translate-x-1`;

                        const dot = document.createElement('span');
                        dot.className = 'inline-block w-2 h-2 rounded-full';
                        dot.style.backgroundColor = dynamicColors[i];

                        const text = document.createElement('span');
                        text.textContent = label;

                        item.appendChild(dot);
                        item.appendChild(text);
                        legendContainer.appendChild(item);
                    });
                };

                renderLegend();

                // Mengamati perubahan tema (dark/light) agar legend ikut diperbarui.
                const observer = new MutationObserver(() => renderLegend());
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            }
        } catch (e) {
            console.error("Error parsing chart data or initializing chart:", e);
        }
    }

    // AJAX pagination untuk tabel detail rekonsiliasi.
    const detailsContainer = document.getElementById('detailsTableContainer');
    if (detailsContainer) {
        document.addEventListener('click', function (event) {
            const link = event.target.closest('#detailsTableContainer a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#')) return;

            event.preventDefault();

            detailsContainer.classList.add('opacity-60');

            fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (res) {
                    if (!res.ok) throw new Error('Gagal memuat halaman');
                    return res.text();
                })
                .then(function (html) {
                    detailsContainer.innerHTML = html;
                    window.history.replaceState(null, '', href);
                })
                .catch(function () {
                    // fallback jika gagal, lakukan navigasi biasa
                    window.location.href = href;
                })
                .finally(function () {
                    detailsContainer.classList.remove('opacity-60');
                });
        });
    }
});
