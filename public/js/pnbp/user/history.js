// Menyiapkan chart riwayat upload user saat DOM siap.
document.addEventListener('DOMContentLoaded', () => {
    const categoryCanvas = document.getElementById('volumeCategoryChart');
    const jenisCanvas = document.getElementById('volumeJenisChart');

    if (categoryCanvas && typeof Chart !== 'undefined') {
        const labels = JSON.parse(categoryCanvas.getAttribute('data-labels') || '[]');
        const values = JSON.parse(categoryCanvas.getAttribute('data-values') || '[]');
        const unitMap = JSON.parse(categoryCanvas.getAttribute('data-units') || '{}');

        // Menyusun data dan mengurutkan menurun agar chart rapi.
        const entries = labels.map((label, i) => ({
            label,
            value: Number(values[i] || 0),
            unit: unitMap[label] || ''
        }));
        entries.sort((a, b) => b.value - a.value);

        const sortedLabels = entries.map(e => e.label);
        const sortedValues = entries.map(e => e.value);
        const sortedUnitMap = {};
        entries.forEach(e => { sortedUnitMap[e.label] = e.unit; });

        // Palet IBM Carbon: 5 hijau, 5 teal, 5 ungu
        const carbonPalette = [
            '#a7f0ba', '#6fdc8c', '#42be65', '#24a148', '#198038',
            '#9ef0f0', '#3ddbd9', '#08bdba', '#009d9a', '#007d79',
            '#e8daff', '#d4bbff', '#be95ff', '#a56eff', '#8a3ffc'
        ];
        const colors = sortedLabels.map((_, i) => carbonPalette[i % carbonPalette.length]);

        new Chart(categoryCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: sortedLabels,
                datasets: [{
                    data: sortedValues,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    },
                    tooltip: {
                        callbacks: {
                            // Menampilkan label tooltip dengan satuan.
                            label: (context) => {
                                const label = context.label ? `${context.label}: ` : '';
                                const value = Number(context.raw || 0);
                                const unit = sortedUnitMap[context.label] || '';
                                return `${label}${value.toLocaleString('id-ID')} ${unit}`.trim();
                            }
                        }
                    }
                }
            }
        });
    }

    if (jenisCanvas && typeof Chart !== 'undefined') {
        const labels = JSON.parse(jenisCanvas.getAttribute('data-labels') || '[]');
        const values = JSON.parse(jenisCanvas.getAttribute('data-values') || '[]');
        const units = JSON.parse(jenisCanvas.getAttribute('data-units') || '[]');

        // Menyusun data dan mengurutkan menurun untuk sebaran volume.
        const entries = labels.map((label, i) => ({
            label,
            value: Number(values[i] || 0),
            unit: units[i] || ''
        }));
        entries.sort((a, b) => b.value - a.value);

        const sortedLabels = entries.map(e => e.label);
        const sortedValues = entries.map(e => e.value);
        const sortedUnits = entries.map(e => e.unit);

        new Chart(jenisCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: sortedLabels,
                datasets: [{
                    label: 'Total Volume',
                    data: sortedValues,
                    backgroundColor: '#22c55e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { maxRotation: 45, minRotation: 0 }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            // Menampilkan tooltip volume dengan satuan per jenis.
                            label: (context) => {
                                const value = Number(context.raw || 0);
                                const unit = sortedUnits[context.dataIndex] || '';
                                return `${value.toLocaleString('id-ID')} ${unit}`.trim();
                            }
                        }
                    }
                }
            }
        });
    }
});
