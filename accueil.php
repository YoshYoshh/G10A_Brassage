<?php
session_start();

// vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // si utilisateur pas connecté, on le redirige vers la page de connexion
    header("Location: login_register/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <title>BRASS'ART</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="accueil.css" />
</head>

<body>
    <header>
        <nav>
            <h1>BRASS'ART</h1>
        </nav>
    </header>

    <section class="hero">
        <div>
            <h1>Découvrez le processus de brassage de la bière</h1>
            <p>Apprenez étape par étape comment créer votre propre bière, des ingrédients à la fermentation, avec des
                images pour illustrer chaque étape.</p>
            <a href="login_register/logout.php" class="button" id="logout-button">Se déconnecter</a>
            <a href="en_savoir_plus.html" class="button-outline">En savoir plus</a>
        </div>
    </section>

    <section id="what-we-do">
        <h2 class="centered">Ce que nous faisons</h2>
        <p class="centered">Voici une brève description de notre activité.</p>
    </section>

    <section id="services">
        <h2 class="centered">Services</h2>
        <p class="centered">Accédez à nos fonctionnalités principales ci-dessous.</p>
        <div class="services-list">
            <div class="service-item">
                <img src="images/mannette.png" alt="Icône moteur">
                <h3>Configuration du moteur</h3>
                <p>Ici, vous pouvez contrôler et configurer le moteur.</p>
                <a href="configurer.html" class="button-outline all-caps">Configurer</a>
            </div>
            <div class="service-item">
                <img src="images/graphique.png" alt="Icône graphique">
                <h3>Données du moteur</h3>
                <p>Visualisez ici toutes les données relatives au moteur.</p>
                <a href="donnees.html" class="button-outline">Voir les données</a>
            </div>
        </div>
    </section>

    <section id="tabs">
        <h2 class="centered">Utilisations et explications</h2>

        <div class="tabs">
            <input type="radio" name="tabs" id="t1" checked>
            <label for="t1">Machine</label>

            <input type="radio" name="tabs" id="t2">
            <label for="t2">Résultat attendu</label>

            <input type="radio" name="tabs" id="t3">
            <label for="t3">Logo de BRASS’ART</label>

            <div class="panels">
                <div class="panel" id="p1">
                    <img src="images/melangeurbrassage.png" alt="Mélangeur de brassage">
                    <h3>Le mélangeur</h3>
                    <p>Le mélangeur est la machine qui utilise le moteur que nous étudions.</p>
                </div>
                <div class="panel" id="p2">
                    <img src="images/biereblonde.png" alt="Bière blonde">
                    <h3>Une bière</h3>
                    <p>C’est une bière blonde, exemple de résultat final.</p>
                </div>
                <div class="panel" id="p3">
                    <img src="images/logo_brassart.png" alt="Logo de Brass'art">
                    <h3>Logo</h3>
                    <p>Logo officiel de notre site web.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-widgets">
            <div class="widget">
                <h5>À propos de Brass’art</h5>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum
                    tristique.</p>
            </div>
            <div class="widget">
                <h5>Liens utiles</h5>
                <ul>
                    <li><a href="#">Phasellus gravida semper nisi</a></li>
                    <li><a href="#">Suspendisse nisl elit</a></li>
                    <li><a href="#">Pellentesque habitant morbi</a></li>
                    <li><a href="#">Etiam sollicitudin ipsum</a></li>
                </ul>
            </div>
            <div class="widget">
                <h5>Réseaux sociaux</h5>
                <ul class="social-links">
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Pinterest</a></li>
                    <li><a href="#">Google</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Brass’art. Tous droits réservés.</p>
        </div>
    </footer>
</body>

</html>