<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Configurer le moteur</title>
  <link rel="stylesheet" href="configurer.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container fade-in">
    <h1>Configuration du moteur</h1>

    <div class="controle">
      <button onclick="envoyerCommande('start')">Démarrer</button>
      <button onclick="envoyerCommande('stop')">Arrêter</button>
    </div>

    <div class="controle">
      <label for="vitesse">Vitesse :</label>
      <input type="range" id="vitesse" min="0" max="100" value="50" onchange="envoyerCommande('speed:' + this.value)">
      <span id="valeur-vitesse">50</span> %
    </div>

    <div id="etat">État du moteur : Inconnu</div>
    
    <canvas id="graphique-vitesse" width="800" height="300" style="margin-top: 2rem;"></canvas>
  </div>

  <footer>
    <a href="accueil.php">
      <button>Retour à l’accueil</button>
    </a>
  </footer>

  <script>
    const maxPoints = 30;
    const vitesses = [];
    const temps = [];

    const ctx = document.getElementById('graphique-vitesse').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: temps,
        datasets: [{
          label: 'Vitesse envoyée (%)',
          borderColor: '#007acc',
          backgroundColor: 'transparent',
          data: [],
          fill: false
        }]
      },
      options: {
        animation: false,
        responsive: true,
        scales: {
          x: { display: false },
          y: { beginAtZero: true, max: 100 }
        },
        plugins: {
          legend: { position: 'top' }
        }
      }
    });

    function envoyerCommande(cmd) {
      // Met à jour l'affichage texte si c'est une commande de vitesse
      if (cmd.startsWith('speed:')) {
        const val = cmd.split(':')[1];
        document.getElementById('valeur-vitesse').innerText = val;

        // Ajoute la donnée au graphique
        const now = new Date().toLocaleTimeString();
        temps.push(now);
        vitesses.push(Number(val));

        if (vitesses.length > maxPoints) {
          vitesses.shift();
          temps.shift();
        }

        chart.data.labels = [...temps];
        chart.data.datasets[0].data = [...vitesses];
        chart.update();
      }

      // Envoi de la commande à la carte
      fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'commande=' + encodeURIComponent(cmd)
      })
        .then(res => res.text())
        .then(data => {
          document.getElementById('etat').innerText = "Réponse de la carte : " + data;
        })
        .catch(err => {
          document.getElementById('etat').innerText = "Erreur de communication avec la carte.";
          console.error(err);
        });
    }
  </script>
</body>

</html>
