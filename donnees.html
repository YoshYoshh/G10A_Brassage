<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Données moteur</title>
  <link rel="stylesheet" href="donnees.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container fade-in">
    <h1>Données en temps réel</h1>

    <canvas id="graphique" width="800" height="400" style="margin-bottom: 2rem;"></canvas>

    <div id="donnees-moteur">Chargement des données...</div>
  </div>

  <footer>
    <a href="accueil.php">
      <button>Retour à l’accueil</button>
    </a>
  </footer>

  <script>
    const ctx = document.getElementById('graphique').getContext('2d');
    const maxPoints = 30;

    const data = {
      labels: [],
      datasets: [
        {
          label: 'Vitesse (RPM)',
          borderColor: '#007acc',
          backgroundColor: 'transparent',
          data: [],
          fill: false
        },
        {
          label: 'Température (°C)',
          borderColor: '#cc3300',
          backgroundColor: 'transparent',
          data: [],
          fill: false
        }
      ]
    };

    const chart = new Chart(ctx, {
      type: 'line',
      data: data,
      options: {
        animation: false,
        responsive: true,
        scales: {
          x: { display: false },
          y: { beginAtZero: true }
        },
        plugins: {
          legend: { position: 'top' }
        }
      }
    });

    function chargerDonnees() {
      fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'commande=get_data'
      })
        .then(r => r.json())
        .then(d => {
          const now = new Date().toLocaleTimeString();

          data.labels.push(now);
          data.datasets[0].data.push(d.speed);
          data.datasets[1].data.push(d.temp);

          if (data.labels.length > maxPoints) {
            data.labels.shift();
            data.datasets[0].data.shift();
            data.datasets[1].data.shift();
          }

          chart.update();

          document.getElementById('donnees-moteur').innerText =
            `Vitesse : ${d.speed} RPM\nTempérature : ${d.temp} °C`;
        })
        .catch(() => {
          document.getElementById('donnees-moteur').innerText = "Erreur de lecture des données.";
        });
    }

    setInterval(chargerDonnees, 1000);
    chargerDonnees();
  </script>
</body>

</html>
