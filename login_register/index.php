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
        <div class="form-box active" id="login-form">
            <form action = "">
                <h2>Connexion</h2>
                <input type = "email" name = "email" placeholder="Email" required>
                <input type = "password" name = "password" placeholder="Mot de passe" required>
                <button type = "submit" name = "login">Se connecter</button>
                <p>Pas de compte ? <a href="#" onclick="showForm('register-form')">S'inscrire</a></p>
            </form>
        </div>

        <div class="form-box" id="register-form">
            <form action = "">
                <h2>Inscription</h2>
                <input type = "text" name = "nom" placeholder="Nom" required>
                <input type = "text" name = "prenom" placeholder="Prénom" required>
                <input type = "email" name = "email" placeholder="Email" required>
                <input type = "password" name = "password" placeholder="Mot de passe" required>
                <button type = "submit" name = "login">S'inscrire</button>
                <p>Déjà un compte ? <a href="#" onclick="showForm('login-form')">Se connecter</a></p>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>