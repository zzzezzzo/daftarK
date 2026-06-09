import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const payload = window.dashboardChartData;
    if (!payload) {
        return;
    }

    let period = 'daily';
    const charts = {};

    const currency = (value) =>
        new Intl.NumberFormat('ar-EG', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);

    const isDark = () => document.documentElement.classList.contains('dark');

    const themeColors = () => ({
        grid: isDark() ? 'rgba(148,163,184,0.12)' : 'rgba(148,163,184,0.25)',
        text: isDark() ? '#94a3b8' : '#64748b',
        legend: isDark() ? '#cbd5e1' : '#475569',
    });

    const scaleOptions = () => {
        const c = themeColors();
        return {
            grid: { color: c.grid },
            ticks: { color: c.text },
        };
    };

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                rtl: true,
                labels: { font: { family: 'Cairo, sans-serif' }, padding: 16, color: themeColors().legend },
            },
            tooltip: {
                rtl: true,
                callbacks: {
                    label: (ctx) => `${ctx.dataset.label}: ${currency(ctx.parsed.y ?? ctx.parsed)} ج.م`,
                },
            },
        },
    };

    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        charts.sales = new Chart(salesCtx, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: {
                ...baseOptions,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: scaleOptions(),
                    y: {
                        beginAtZero: true,
                        ...scaleOptions(),
                        ticks: { ...scaleOptions().ticks, callback: (v) => currency(v) },
                    },
                },
            },
        });
    }

    const purchasesCtx = document.getElementById('purchasesChart');
    if (purchasesCtx) {
        charts.purchases = new Chart(purchasesCtx, {
            type: 'bar',
            data: { labels: [], datasets: [] },
            options: {
                ...baseOptions,
                scales: {
                    x: scaleOptions(),
                    y: {
                        beginAtZero: true,
                        ...scaleOptions(),
                        ticks: { ...scaleOptions().ticks, callback: (v) => currency(v) },
                    },
                },
            },
        });
    }

    const statusCtx = document.getElementById('paymentStatusChart');
    if (statusCtx) {
        charts.status = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: payload.paymentStatus.labels,
                datasets: [{
                    data: payload.paymentStatus.data,
                    backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${ctx.parsed} فاتورة`,
                        },
                    },
                },
            },
        });
    }

    const productsCtx = document.getElementById('topProductsChart');
    if (productsCtx) {
        charts.products = new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: payload.topProducts.labels.length ? payload.topProducts.labels : ['لا توجد بيانات'],
                datasets: [{
                    label: 'الكمية المباعة',
                    data: payload.topProducts.data.length ? payload.topProducts.data : [0],
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                }],
            },
            options: {
                indexAxis: 'y',
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.x} وحدة`,
                        },
                    },
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, ...scaleOptions().ticks }, ...scaleOptions() },
                    y: scaleOptions(),
                },
            },
        });
    }

    function updateTrendCharts() {
        const source = payload[period];
        if (!source || !charts.sales || !charts.purchases) {
            return;
        }

        charts.sales.data.labels = source.labels;
        charts.sales.data.datasets = [
            {
                label: 'المبيعات',
                data: source.sales,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.35,
            },
            {
                label: 'المرتجعات',
                data: source.returns,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.08)',
                fill: true,
                tension: 0.35,
            },
            {
                label: 'المتحصل',
                data: source.collected,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.08)',
                fill: true,
                tension: 0.35,
            },
        ];
        charts.sales.update();

        charts.purchases.data.labels = source.labels;
        charts.purchases.data.datasets = [{
            label: 'المشتريات',
            data: source.purchases,
            backgroundColor: '#a855f7',
            borderRadius: 4,
        }];
        charts.purchases.update();
    }

    document.querySelectorAll('[data-period]').forEach((button) => {
        button.addEventListener('click', () => {
            period = button.dataset.period;

            document.querySelectorAll('[data-period]').forEach((btn) => {
                btn.classList.remove('ui-segment-btn-active');
            });

            button.classList.add('ui-segment-btn-active');

            const hint = document.getElementById('periodHint');
            if (hint) {
                hint.textContent = period === 'daily'
                    ? 'آخر 30 يوم'
                    : 'آخر 12 شهر';
            }

            updateTrendCharts();
        });
    });

    updateTrendCharts();

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            setTimeout(() => {
                Object.values(charts).forEach((chart) => {
                    const c = themeColors();
                    if (chart.options.plugins?.legend?.labels) {
                        chart.options.plugins.legend.labels.color = c.legend;
                    }
                    ['x', 'y'].forEach((axis) => {
                        if (chart.options.scales?.[axis]) {
                            chart.options.scales[axis].grid = { color: c.grid };
                            chart.options.scales[axis].ticks = {
                                ...chart.options.scales[axis].ticks,
                                color: c.text,
                            };
                        }
                    });
                    chart.update();
                });
            }, 50);
        });
    });
});
