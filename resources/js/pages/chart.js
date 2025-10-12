import ApexCharts from 'apexcharts';

const currencyFormatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
});

document.addEventListener('DOMContentLoaded', () => {
    const chartElement = document.getElementById('incomeExpenseChart');
    if (!chartElement) {
        return;
    }

    let payload = { labels: [], ingresos: [], gastos: [] };

    try {
        payload = JSON.parse(chartElement.dataset.series ?? '{}');
    } catch (error) {
        console.error('No fue posible interpretar la información del gráfico', error);
    }

    if (!payload.labels?.length) {
        chartElement.innerHTML = '<div class="text-muted text-center py-5">Sin datos suficientes para graficar.</div>';
        return;
    }

    const options = {
        chart: {
            type: 'line',
            height: 360,
            toolbar: { show: false },
            fontFamily: 'var(--bs-body-font-family)'
        },
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        colors: ['#7f4fc9', '#ef4c4c'],
        markers: {
            size: 5,
            strokeWidth: 3,
            hover: {
                sizeOffset: 2,
            },
        },
        dataLabels: {
            enabled: true,
            formatter: (value) => currencyFormatter.format(value),
            background: {
                enabled: true,
                borderRadius: 6,
            },
            style: {
                fontSize: '11px',
                colors: ['#2f2f2f'],
            },
        },
        tooltip: {
            y: {
                formatter: (value) => currencyFormatter.format(value),
            },
        },
        series: [
            {
                name: 'Pagos',
                data: payload.ingresos ?? [],
            },
            {
                name: 'Gastos',
                data: payload.gastos ?? [],
            },
        ],
        xaxis: {
            categories: payload.labels,
            axisBorder: { show: false },
            labels: {
                style: {
                    colors: '#6c757d',
                },
            },
        },
        yaxis: {
            labels: {
                formatter: (value) => currencyFormatter.format(value),
            },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
        },
        grid: {
            borderColor: 'rgba(108, 117, 125, 0.15)',
            strokeDashArray: 4,
        },
    };

    const chart = new ApexCharts(chartElement, options);
    chart.render();
});
