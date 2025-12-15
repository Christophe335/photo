<?php
// Fichier de traitement du mot de passe oublié
session_start();
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mot-de-passe-oublie.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

// Validation des données
$errors = [];

if (empty($email)) {
    $errors[] = "L'adresse e-mail est requise.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'adresse e-mail invalide.";
}

if (!empty($errors)) {
    $_SESSION['forgot_errors'] = $errors;
    $_SESSION['forgot_email'] = $email;
    header('Location: mot-de-passe-oublie.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si l'email existe
    $stmt = $db->prepare("SELECT id, prenom, nom FROM clients WHERE email = ? AND actif = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Générer un token de réinitialisation
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Sauvegarder le token en base
        $stmt = $db->prepare("UPDATE clients SET token_reset = ?, token_reset_expiration = ? WHERE id = ?");
        $stmt->execute([$token, $expiration, $user['id']]);
        
        // Dans une version future, envoyer l'email ici
        // Pour le moment, on affiche juste un message de confirmation
        
        $_SESSION['success_message'] = "Si cette adresse e-mail existe dans notre base, vous recevrez un lien de réinitialisation dans quelques minutes.";
    } else {
        // Ne pas révéler si l'email existe ou non pour des raisons de sécurité
        $_SESSION['success_message'] = "Si cette adresse e-mail existe dans notre base, vous recevrez un lien de réinitialisation dans quelques minutes.";
    }
    
} catch (Exception $e) {
    error_log("Erreur mot de passe oublié: " . $e->getMessage());
    $_SESSION['forgot_errors'] = ["Une erreur technique s'est produite. Veuillez réessayer."];
}

header('Location: mot-de-passe-oublie.php');
exit;
?>