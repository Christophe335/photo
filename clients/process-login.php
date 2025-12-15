<?php
// Fichier de traitement de la connexion
session_start();
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation des données
$errors = [];

if (empty($email)) {
    $errors[] = "L'adresse e-mail est requise.";
}

if (empty($password)) {
    $errors[] = "Le mot de passe est requis.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'adresse e-mail invalide.";
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_email'] = $email;
    header('Location: connexion.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Rechercher l'utilisateur par email
    $stmt = $db->prepare("SELECT id, nom, prenom, email, mot_de_passe, actif FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        if ($user['actif']) {
            // Connexion réussie
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_nom'] = $user['nom'];
            $_SESSION['client_prenom'] = $user['prenom'];
            $_SESSION['client_email'] = $user['email'];
            
            // Mettre à jour la dernière connexion
            $stmt = $db->prepare("UPDATE clients SET derniere_connexion = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Redirection vers le compte ou la page d'origine
            $redirect = $_SESSION['redirect_after_login'] ?? 'mon-compte.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $_SESSION['login_errors'] = ["Votre compte n'est pas activé. Veuillez vérifier vos e-mails."];
        }
    } else {
        $_SESSION['login_errors'] = ["E-mail ou mot de passe incorrect."];
    }
    
} catch (Exception $e) {
    error_log("Erreur de connexion: " . $e->getMessage());
    $_SESSION['login_errors'] = ["Une erreur technique s'est produite. Veuillez réessayer."];
}

$_SESSION['login_email'] = $email;
header('Location: connexion.php');
exit;
?>