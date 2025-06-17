<?php
// ... (début du fichier identique) ...
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();
require_once 'config.php';

if (isset($_POST['register'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkEmail = $stmt->get_result();
    
    if ($checkEmail->num_rows > 0){
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
                $mail->Host       = 'smtp.gmail.com';   
                $mail->SMTPAuth   = true;
                $mail->Username   = 'yoshi8872@gmail.com'; 
                $mail->Password   = 'qqum uuvs yjlo hzhr';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->setFrom('no-reply@brassart.com', 'Brass\'Art');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Veuillez verifier votre compte';
                $verification_link = "http://localhost/G10A_Brassage/login_register/verify.php?token=" . $verification_token;
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


if (isset($_POST['login'])){
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

            // Tout est bon, on connecte l'utilisateur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['email'];

            // Redirection (peu importe le rôle pour l'instant)
            header("Location: ../accueil.php");
            exit();
            
        } else {
            // Mot de passe incorrect
            $_SESSION['login_error'] = "L'email ou le mot de passe est incorrect.";
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        }
    } else {
        // Email non trouvé
        $_SESSION['login_error'] = "L'email ou le mot de passe est incorrect.";
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
}
?>