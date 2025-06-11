<?php

session_start();
require_once 'config.php';

if (isset($_POST[''])){
    $nom = $_POST[''];
    $prenom = $_POST[''];
    $email = $_POST[''];
    $password = $_POST[''];
}

?>