<?php
require_once 'config.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // --- REQUÊTE PRÉPARÉE ---
    $stmt = $conn->prepare("SELECT verification_token, is_verified FROM users WHERE verification_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        
        if ($row['is_verified'] == 0){ // Comparaison non-stricte est plus sûre (0 vs '0')
            
            // --- REQUÊTE PRÉPARÉE ---
            $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_token = ? LIMIT 1");
            $update_stmt->bind_param("s", $token);

            if ($update_stmt->execute()){
                $_SESSION['status'] = "Votre compte a été vérifié avec succès ! Vous pouvez maintenant vous connecter.";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['status'] = "Erreur lors de la vérification. Veuillez réessayer.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['status'] = "Ce compte est déjà vérifié. Vous pouvez vous connecter.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Lien de vérification invalide ou expiré.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Accès non autorisé.";
    header("Location: index.php");
    exit();
}
?>