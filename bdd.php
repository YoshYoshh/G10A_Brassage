<?php

// --- Identifiants pour la base de données PostgreSQL ---
$host = 'app.garageisep.com';
$port = '5432';
$db_name = 'app_db';
$username = 'app_user'; 
$password = 'apppassword';

// DSN (Data Source Name) pour PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$db_name";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tentative de connexion
    $pdo = new PDO($dsn, $username, $password, $options);
    //echo "<h1>Connexion à la base de données réussie !</h1>";

    
} catch (\PDOException $e) {
    die("Erreur : Impossible de communiquer avec la base de données. Erreur : " . $e->getMessage());
}

?>