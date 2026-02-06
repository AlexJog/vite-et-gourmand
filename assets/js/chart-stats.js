// Graphique des statistiques
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('chartCommandes');
    
    if (canvas && typeof Chart !== 'undefined') {
        // Récupérer les données depuis les attributs data
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const data = JSON.parse(canvas.dataset.values || '[]');
        
        const ctx = canvas.getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de commandes',
                    data: data,
                    backgroundColor: '#6B8E23',
                    borderColor: '#556B2F',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});