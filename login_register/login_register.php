<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

if (isset($_POST['register'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows>0){
        $_SESSION['register_error'] = "Un compte associé à cet email existe déjà.";
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (nom,prenom,email,password,role) VALUES ('$nom','$prenom', '$email','$password','user')");
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result->num_rows>0){
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])){
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];

            if ($user['role']==='admin'){
                header("Location: admin_page.php");
            } else {
                header("Location: user_page.php"); 
            }
            exit();
        }
    } 
    $_SESSION['login_error'] = "L'email ou le mot de passe est incorrect.";
    $_SESSION['active_form'] = 'login';

    header("Location: index.php");
    exit();
}

?>