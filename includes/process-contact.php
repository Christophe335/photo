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
        // Destinataire principal
        $to = "webmaster@general-cover.com";

        // Sujet
        $subject = "Contact - " . $nom . " " . $prenom;

        // URL du logo (utilisé dans l'email)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $logo_url = $scheme . '://' . $host . '/images/logo-icon/logo.svg';

        // Corps HTML de l'email pour l'équipe
        $html = '<html><body style="font-family:Arial,sans-serif;color:#333">';
        $html .= '<div style="max-width:700px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:8px">';
        $html .= '<div style="text-align:center;margin-bottom:20px">
        <img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;">
        <h3>Bindy Studio - General Cover</h3>
        <p>9 rue de la gare, 70000 Vallerois le Bois</p>
        <p>Téléphone : 03 84 78 38 39</p>
        </div>';
        $html .= '<h2 style="color:#24256d">Nouveau message de contact</h2>';
        $html .= '<table style="width:100%;border-collapse:collapse">';
        $addRow = function($label, $value) { return '<tr><td style="padding:6px 8px;border-top:1px solid #f0f0f0;width:30%;font-weight:600">' . $label . '</td><td style="padding:6px 8px;border-top:1px solid #f0f0f0">' . nl2br(htmlspecialchars($value)) . '</td></tr>'; };
        $html .= $addRow('Nom', $nom);
        $html .= $addRow('Prénom', $prenom);
        if (!empty($societe)) $html .= $addRow('Société', $societe);
        if (!empty($rue)) $html .= $addRow('Adresse', $rue);
        if (!empty($code_postal)) $html .= $addRow('Code postal', $code_postal);
        if (!empty($ville)) $html .= $addRow('Ville', $ville);
        $html .= $addRow('Email', $email);
        $html .= $addRow('Téléphone', $telephone);
        $html .= $addRow('Message', $message);
        $html .= '</table>';
        $html .= '<p style="margin-top:18px;color:#666">Message envoyé depuis le site <strong>Bindy Studio</strong>.</p>';
        $html .= '</div></body></html>';

        // Headers pour email HTML
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: Bindy Studio <webmaster@general-cover.com>';
        $headers[] = 'Reply-To: ' . $email;

        // encoder le sujet en UTF-8 (base64) pour éviter les problèmes d'accents
        $subject_admin = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        // Envoi à l'équipe
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $sent_admin = mail($to, $subject_admin, $html, implode("\r\n", $headers));

        // Préparation et envoi d'un email de confirmation à l'utilisateur
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $subject_user = 'Votre message a bien été envoyé - Bindy Studio';
            $html_user = '<html><body style="font-family:Arial,sans-serif;color:#333">';
            $html_user .= '<div style="max-width:700px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:8px">';
            $html_user .= '<div style="text-align:center;margin-bottom:20px">
            <img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;"><h3>Bindy Studio - General Cover</h3>
            <p>9 rue de la gare, 70000 Vallerois le Bois</p>
            <p>Téléphone : 03 84 78 38 39</p></div>';
            $html_user .= '<h2 style="color:#24256d">Merci, votre message a bien été reçu</h2>';
            $html_user .= '<p>Bonjour ' . htmlspecialchars($prenom) . ',</p>';
            $html_user .= '<p>Nous avons bien reçu votre message. Voici un récapitulatif :</p>';
            $html_user .= '<table style="width:100%;border-collapse:collapse">';
            $html_user .= $addRow('Nom', $nom);
            $html_user .= $addRow('Prénom', $prenom);
            if (!empty($societe)) $html_user .= $addRow('Société', $societe);
            $html_user .= $addRow('Email', $email);
            $html_user .= $addRow('Téléphone', $telephone);
            $html_user .= $addRow('Message', $message);
            $html_user .= '</table>';
            $html_user .= '<p style="margin-top:18px">Nous restons à votre disposition, n\'hésitez pas à nous recontacter pour toute demande complémentaire.</p>';
            $html_user .= '<p>Cordialement,<br>Bindy Studio</p>';
            $html_user .= '</div></body></html>';

            $headers_user = [];
            $headers_user[] = 'MIME-Version: 1.0';
            $headers_user[] = 'Content-type: text/html; charset=UTF-8';
            $headers_user[] = 'Content-Transfer-Encoding: 8bit';
            $headers_user[] = 'From: Bindy Studio <webmaster@general-cover.com>';
            $headers_user[] = 'Reply-To: webmaster@general-cover.com';

            $subject_user_encoded = '=?UTF-8?B?' . base64_encode($subject_user) . '?=';
            $sent_user = mail($email, $subject_user_encoded, $html_user, implode("\r\n", $headers_user));
        }

        if ($sent_admin) {
            // Rediriger vers le formulaire avec flag pour afficher la popup
            header('Location: ../formulaires/contact.php?sent=1');
            exit;
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
