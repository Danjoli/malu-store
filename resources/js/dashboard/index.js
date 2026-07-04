import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {

    const ctx = document.getElementById('salesChart');

    if (!ctx || !window.DASHBOARD) {
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.DASHBOARD.months,
            datasets: [
                {
                    label: 'Vendas',
                    data: window.DASHBOARD.sales,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.2)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

});
