<?php
// Traitement du formulaire de contact
if ($_POST) {
    // Récupération et nettoyage des données
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $societe = htmlspecialchars(trim($_POST['societe'] ?? ''));
    $rue = htmlspecialchars(trim($_POST['rue'] ?? ''));
    $code_postal = htmlspecialchars(trim($_POST['code_postal'] ?? ''));
    $ville = htmlspecialchars(trim($_POST['ville'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Validation des champs obligatoires
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est obligatoire";
    }
    if (empty($telephone)) $errors[] = "Le téléphone est obligatoire";
    if (empty($message)) $errors[] = "Le message est obligatoire";
    
    if (empty($errors)) {
        // Préparation de l'email
        $to = "contact@votre-site.com"; // Remplacez par votre email
        $subject = "Nouveau message de contact - " . $nom . " " . $prenom;
        
        $email_body = "Nouveau message de contact reçu :\n\n";
        $email_body .= "Nom : " . $nom . "\n";
        $email_body .= "Prénom : " . $prenom . "\n";
        if (!empty($societe)) $email_body .= "Société : " . $societe . "\n";
        if (!empty($rue)) $email_body .= "Adresse : " . $rue . "\n";
        if (!empty($code_postal)) $email_body .= "Code postal : " . $code_postal . "\n";
        if (!empty($ville)) $email_body .= "Ville : " . $ville . "\n";
        $email_body .= "Email : " . $email . "\n";
        $email_body .= "Téléphone : " . $telephone . "\n\n";
        $email_body .= "Message :\n" . $message . "\n";
        
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Envoi de l'email
        if (mail($to, $subject, $email_body, $headers)) {
            $success_message = "Votre message a été envoyé avec succès !";
        } else {
            $error_message = "Erreur lors de l'envoi du message. Veuillez réessayer.";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Résultat</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .result-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #24256d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background: #1a1b4d;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <?php if (isset($success_message)): ?>
            <h2 class="success">✅ Message envoyé !</h2>
            <p><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <h2 class="error">❌ Erreur</h2>  
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <a href="../formulaires/contact.php" class="btn-back">Retour au formulaire</a>
    </div>
</body>
</html>