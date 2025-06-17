<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm){
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brass'Art</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action = "login_register.php" method="post">
                <h2>Connexion</h2>
                <?= showError($errors['login']); ?>
                <input type = "email" name = "email" placeholder="Email" required>
                <input type = "password" name = "password" placeholder="Mot de passe" required>
                <button type = "submit" name = "login">Se connecter</button>
                <p>Pas de compte ? <a href="#" onclick="showForm('register-form')">S'inscrire</a></p>
            </form>
        </div>

        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action = "login_register.php" method="post">
                <h2>Inscription</h2>
                <?= showError($errors['register']); ?>
                <input type = "text" name = "nom" placeholder="Nom" required>
                <input type = "text" name = "prenom" placeholder="Prénom" required>
                <input type = "email" name = "email" placeholder="Email" required>
                <input type = "password" name = "password" placeholder="Mot de passe" required>
                <button type = "submit" name = "register">S'inscrire</button>
                <p>Déjà un compte ? <a href="#" onclick="showForm('login-form')">Se connecter</a></p>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>