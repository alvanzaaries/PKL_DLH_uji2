document.addEventListener('DOMContentLoaded', function () {
    // Reset Button Logic
    const resetBtn = document.getElementById('resetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(){
            const f = document.getElementById('filterForm');
            if(!f) return;
            f.querySelector('input[name="search"]').value = '';
            f.submit();
        });
    }

    // Chart Logic
    const canvas = document.getElementById('wilayahChart');
    if (canvas && typeof Chart !== 'undefined') {
        try {
            // Get data from data attributes
            const rawLabels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
            const rawData = JSON.parse(canvas.getAttribute('data-values') || '[]');

            if (rawLabels.length > 0) {
                const ctx = canvas.getContext('2d');

                // Dynamic Colors Generation
                const dynamicColors = rawLabels.map((_, i) => {
                    const hue = (i * 137.508) % 360; 
                    return `hsl(${hue}, 65%, 55%)`;
                });

                new Chart(ctx, {
                    type: 'doughnut',
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
                                display: true,
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
            }
        } catch (e) {
            console.error("Error parsing chart data or initializing chart:", e);
        }
    }
});
