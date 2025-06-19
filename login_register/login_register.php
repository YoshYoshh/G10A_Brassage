<?php
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();
require_once 'config.php'; // ton config.php charge déjà le .env

if (isset($_POST['register'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkEmail = $stmt->get_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = "Un compte associé à cet email existe déjà.";
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    } else {
        $verification_token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, password, role, verification_token) VALUES (?, ?, ?, ?, 'user', ?)");
        $stmt->bind_param("sssss", $nom, $prenom, $email, $password, $verification_token);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['MAIL_USERNAME'];
                $mail->Password   = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = $_ENV['MAIL_PORT'];
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Veuillez vérifier votre compte';

                $verification_link = $_ENV['APP_URL'] . "/login_register/verify.php?token=" . $verification_token;
                $mail->Body    = "<h1>Merci pour votre inscription !</h1><p>Cliquez sur le lien suivant pour activer votre compte : <a href='$verification_link'>Activer mon compte</a></p>";
                $mail->AltBody = "Merci de vous être inscrit ! Copiez-collez ce lien dans votre navigateur pour activer votre compte : $verification_link";
                $mail->send();

                $_SESSION['status'] = "Inscription réussie ! Un e-mail de vérification a été envoyé.";
                header("Location: index.php");
                exit();
            } catch (Exception $e) {
                error_log("Erreur PHPMailer: " . $mail->ErrorInfo);
                $_SESSION['register_error'] = "Impossible d'envoyer l'e-mail de vérification. Veuillez réessayer.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['register_error'] = "Erreur lors de la création du compte.";
            header("Location: index.php");
            exit();
        }
    }
}


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nom, prenom, email, password, role, is_verified FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                $_SESSION['login_error'] = "Votre compte n'a pas encore été vérifié. Veuillez consulter vos e-mails.";
                $_SESSION['active_form'] = 'login';
                header("Location: index.php");
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];

            header("Location: ../accueil.php");
            exit();
        } else {
            $_SESSION['login_error'] = "L'email ou le mot de passe est incorrect.";
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "L'email ou le mot de passe est incorrect.";
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
}
?>
