<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "G10A_users_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error){
    die("Connexion failed: ". $conn->connect_error);
}

?>