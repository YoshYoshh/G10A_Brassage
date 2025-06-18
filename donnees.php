<?php
// Connexion à la base de données
require_once 'login_register/config.php';

// connexion à la table vitesse_moteur et récupération des données
$sql_vitesse = "SELECT vitesse_rmp FROM vitesse_moteur ORDER BY id ASC";
$result_vitesse = $conn->query($sql_vitesse);

$vitesses= [];
if ($result_vitesse->num_rows > 0) {
    while ($row = $result_vitesse->fetch_assoc()) {
        $vitesses[] = $row['vitesse_rmp'];
    }
} else {
    $vitesses = [];
}

// connexion à la table vitesse_moteur et récupération des données
$sql_temperature = "SELECT valeurs FROM temperatures ORDER BY id ASC";
$result_temperature = $conn->query($sql_temperature);

$temperatures= [];
if ($result_temperature->num_rows > 0) {
    while ($row = $result_temperature->fetch_assoc()) {
        $temperatures[] = $row['valeurs'];
    }
} else {
    $temperatures = [];
}

?>

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

    <!-- Graphique en haut -->
    <canvas id="graphique-donnees" width="800" height="400" style="margin-bottom: 2rem;"></canvas>

    <!-- Données textuelles en dessous -->
    <div id="donnees-moteur">Chargement des données...</div>
  </div>

  <footer>
    <a href="accueil.php">
      <button>Retour à l’accueil</button>
    </a>
  </footer>

  <script>
    const maxPoints = 30;
    // Initialisation des données du graphique
    const vitessePHP = <?php echo json_encode($vitesses); ?>;
    const temperaturePHP = <?php echo json_encode($temperatures); ?>
    const temps = vitessePHP.map((_, index) => `Mesure ${index + 1}`);

    const ctx = document.getElementById('graphique-donnees').getContext('2d');
    
    const data = {
      labels: temps,
      datasets: [
        {
          label: 'Vitesse (RPM)',
          borderColor: '#007acc',
          backgroundColor: 'transparent',
          data: vitessePHP,
          fill: false
        },
        {
          label: 'Température (°C)',
          borderColor: '#cc3300',
          backgroundColor: 'transparent',
          data: temperaturePHP,
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
          x: { display: true, // Afficher l'axe X pour voir les labels initiaux
                title: { 
                  display: true, 
                  text: 'Temps' 
                }
              },
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

          // Ajout des nouvelles données
          data.labels.push(now);
          data.datasets[0].data.push(d.speed);
          data.datasets[1].data.push(d.temp);

          // Limite à maxPoints
          if (data.labels.length > maxPoints) {
            data.labels.shift();
            data.datasets[0].data.shift();
            data.datasets[1].data.shift();
          }

          chart.update();

          // Affichage texte
          document.getElementById('donnees-moteur').innerText =
            `Vitesse : ${d.speed} RPM\nTempérature : ${d.temp} °C`;
        })
        .catch(error => {
                console.error("Erreur lors du chargement des données:", error);
                document.getElementById('donnees-moteur').innerText = "Erreur de lecture des données.";
            });
    }

    chargerDonnees();
    setInterval(chargerDonnees, 1000);
  </script>
</body>

</html>
