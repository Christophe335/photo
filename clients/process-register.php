<?php
// Fichier de traitement de l'inscription
session_start();
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: creer-compte.php');
    exit;
}

// Récupération des données du formulaire
$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$telephone = trim($_POST['telephone'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$code_postal = trim($_POST['code_postal'] ?? '');
$ville = trim($_POST['ville'] ?? '');
$pays = $_POST['pays'] ?? 'France';
$newsletter = isset($_POST['newsletter']) ? 1 : 0;
$cgv = isset($_POST['cgv']) ? 1 : 0;

// Validation des données
$errors = [];

if (empty($prenom)) {
    $errors[] = "Le prénom est requis.";
}

if (empty($nom)) {
    $errors[] = "Le nom est requis.";
}

if (empty($email)) {
    $errors[] = "L'adresse e-mail est requise.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'adresse e-mail invalide.";
}

if (empty($password)) {
    $errors[] = "Le mot de passe est requis.";
} elseif (strlen($password) < 6) {
    $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
}

if ($password !== $password_confirm) {
    $errors[] = "Les mots de passe ne correspondent pas.";
}

if (empty($adresse)) {
    $errors[] = "L'adresse est requise.";
}

if (empty($code_postal)) {
    $errors[] = "Le code postal est requis.";
}

if (empty($ville)) {
    $errors[] = "La ville est requise.";
}

if (!$cgv) {
    $errors[] = "Vous devez accepter les conditions générales de vente.";
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_data'] = $_POST;
    header('Location: creer-compte.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['register_errors'] = ["Cette adresse e-mail est déjà utilisée."];
        $_SESSION['register_data'] = $_POST;
        header('Location: creer-compte.php');
        exit;
    }
    
    // Créer le compte
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $token_activation = bin2hex(random_bytes(32));
    
    $stmt = $db->prepare("
        INSERT INTO clients (
            prenom, nom, email, mot_de_passe, telephone, adresse, 
            code_postal, ville, pays, newsletter, token_activation, 
            date_creation, actif
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)
    ");
    
    $stmt->execute([
        $prenom, $nom, $email, $password_hash, $telephone, 
        $adresse, $code_postal, $ville, $pays, $newsletter, $token_activation
    ]);
    
    $client_id = $db->lastInsertId();
    
    // Pour le moment, on active directement le compte
    // Dans une version future, vous pourrez envoyer un email d'activation
    
    // Connexion automatique après inscription
    $_SESSION['client_id'] = $client_id;
    $_SESSION['client_nom'] = $nom;
    $_SESSION['client_prenom'] = $prenom;
    $_SESSION['client_email'] = $email;
    
    $_SESSION['success_message'] = "Votre compte a été créé avec succès ! Bienvenue !";
    header('Location: mon-compte.php');
    exit;
    
} catch (Exception $e) {
    error_log("Erreur création compte: " . $e->getMessage());
    $_SESSION['register_errors'] = ["Une erreur technique s'est produite. Veuillez réessayer."];
    $_SESSION['register_data'] = $_POST;
    header('Location: creer-compte.php');
    exit;
}
?>