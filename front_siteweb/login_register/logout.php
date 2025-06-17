<?php
session_start();

// Vider toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header("Location: index.php?logout=1");
exit();
?>