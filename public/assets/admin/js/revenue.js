/* public/assets/admin/js/revenue/index.js */

document.addEventListener('DOMContentLoaded', function() {
    // Lấy dữ liệu từ biến Global được khai báo trong Blade
    const config = window.RevenueConfig || {};

    // ==========================================
    // 1. BIỂU ĐỒ DOANH THU (MAIN CHART)
    // ==========================================
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const labels = config.chartLabels || [];
        const dataRevenue = config.chartRevenue || [];
        const dataProfit = config.chartProfit || [];

        let mainChartConfig = {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Doanh thu',
                        data: dataRevenue,
                        backgroundColor: 'rgba(13, 159, 110, 0.1)',
                        borderColor: '#3c783cff',
                        pointRadius: 4,
                        pointBackgroundColor: '#83b4a3ff',
              
                        pointHoverRadius: 6,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Lợi nhuận',
                        data: dataProfit,
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderColor: '#10b981',
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                    
                        pointHoverRadius: 6,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + 'đ';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#9ca3af' } },
                    y: {
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', { notation: "compact" }).format(value);
                            }
                        },
                        grid: { color: "rgba(234, 236, 244, 0.4)", borderDash: [2] },
                        border: { display: false }
                    }
                }
            }
        };

        let mainChart = new Chart(ctx, mainChartConfig);

        // Xử lý nút chuyển đổi loại biểu đồ
        const toggleButtons = document.querySelectorAll('#chartTypeToggle button');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('active')) return;

                toggleButtons.forEach(b => {
                    b.classList.remove('active', 'bg-white', 'shadow-sm', 'text-primary');
                    b.classList.add('text-muted');
                });
                this.classList.add('active', 'bg-white', 'shadow-sm', 'text-primary');
                this.classList.remove('text-muted');

                const newType = this.getAttribute('data-type');
                mainChart.destroy();

                let newConfig = JSON.parse(JSON.stringify(mainChartConfig));
                newConfig.type = newType;

                if (newType === 'bar') {
                    newConfig.data.datasets.forEach(ds => {
                        ds.fill = true;
                        ds.tension = 0;
                        ds.borderWidth = 0;
                        ds.borderRadius = 4;
                        ds.barPercentage = 0.6;
                        ds.categoryPercentage = 0.8;

                        if (ds.label === 'Doanh thu') {
                            ds.backgroundColor = '#3c783cff';
                            ds.hoverBackgroundColor = '#2a532aff';
                        } else {
                            ds.backgroundColor = '#1cc88a';
                            ds.hoverBackgroundColor = '#17a673';
                        }
                    });
                    newConfig.options.scales.x.grid = { display: false };
                    newConfig.options.scales.x.stacked = false;
                }

                // Re-assign callbacks after JSON parse
                newConfig.options.plugins.tooltip.callbacks.label = function(context) {
                    let label = context.dataset.label || '';
                    if (label) label += ': ';
                    label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + 'đ';
                    return label;
                };
                newConfig.options.scales.y.ticks.callback = function(value) {
                    return new Intl.NumberFormat('vi-VN', { notation: "compact" }).format(value);
                };

                mainChart = new Chart(ctx, newConfig);
            });
        });
    }

    // ==========================================
    // 2. BIỂU ĐỒ TRẠNG THÁI (PIE CHART)
    // ==========================================
    const ctxPie = document.getElementById('orderStatusChart');
    if (ctxPie) {
        const statusRawData = config.orderStatusStats || {};
        const hasData = statusRawData && Object.keys(statusRawData).length > 0;

        const statusColors = {
            'completed': '#10b981', 'pending': '#f59e0b', 'processing': '#3b82f6',
            'cancelled': '#ef4444', 'refunded': '#212529'
        };

        let statusLabels = hasData ? Object.keys(statusRawData) : ['Chưa có dữ liệu'];
        let statusValues = hasData ? Object.values(statusRawData) : [1];
        let statusBgColors = hasData ? statusLabels.map(l => statusColors[l] || '#d1d5db') : ['#e9ecef'];

        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusBgColors,
                    hoverOffset: 4,
                    borderWidth: 0
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: hasData,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

    // ==========================================
    // 3. BIỂU ĐỒ DANH MỤC (CATEGORY DOUGHNUT)
    // ==========================================
    const ctxCat = document.getElementById('categoryChart');
    if (ctxCat) {
        const catLabels = config.catLabels || [];
        const catValues = config.catValues || [];
        const catColors = ['#0d9f6e', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'];

        if (catLabels.length > 0) {
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catValues,
                        backgroundColor: catColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw;
                                    return ' ' + label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                                }
                            }
                        }
                    },
                }
            });
        }
    }

    // ==========================================
    // 4. FLATPICKR
    // ==========================================
    const dateRangeInput = document.getElementById('dateRangePicker');
    const hiddenFrom = document.getElementById('date_from');
    const hiddenTo = document.getElementById('date_to');

    if (dateRangeInput && config.dates) {
        flatpickr(dateRangeInput, {
            mode: "range",
            dateFormat: "d/m/Y",
            defaultDate: [config.dates.from, config.dates.to],
            locale: "vn",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const fromDate = instance.formatDate(selectedDates[0], "Y-m-d");
                    const toDate = instance.formatDate(selectedDates[1], "Y-m-d");
                    hiddenFrom.value = fromDate;
                    hiddenTo.value = toDate;
                }
            }
        });
    }
});