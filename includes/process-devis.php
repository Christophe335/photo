<?php
// Traitement du formulaire de devis avec images
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
    
    // Gestion des fichiers uploadés
    $uploaded_files = [];
    $upload_errors = [];
    
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $upload_dir = '../uploads/devis/' . date('Y-m-d') . '/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        foreach ($_FILES['images']['name'] as $key => $filename) {
            if (!empty($filename)) {
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_size = $_FILES['images']['size'][$key];
                $file_type = $_FILES['images']['type'][$key];
                $file_error = $_FILES['images']['error'][$key];
                
                // Vérifications
                if ($file_error !== UPLOAD_ERR_OK) {
                    $upload_errors[] = "Erreur lors de l'upload de " . $filename;
                    continue;
                }
                
                if (!in_array($file_type, $allowed_types)) {
                    $upload_errors[] = "Type de fichier non autorisé pour " . $filename;
                    continue;
                }
                
                if ($file_size > $max_file_size) {
                    $upload_errors[] = "Fichier trop volumineux : " . $filename;
                    continue;
                }
                
                // Générer un nom de fichier unique
                $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $destination)) {
                    $uploaded_files[] = [
                        'original_name' => $filename,
                        'new_name' => $new_filename,
                        'path' => $destination,
                        'size' => $file_size
                    ];
                } else {
                    $upload_errors[] = "Erreur lors de la sauvegarde de " . $filename;
                }
            }
        }
    }
    
    if (empty($errors) && empty($upload_errors)) {
        // Préparation de l'email
        $to = "devis@votre-site.com"; // Remplacez par votre email
        $subject = "Nouvelle demande de devis - " . $nom . " " . $prenom;
        
        $email_body = "Nouvelle demande de devis reçue :\n\n";
        $email_body .= "Nom : " . $nom . "\n";
        $email_body .= "Prénom : " . $prenom . "\n";
        if (!empty($societe)) $email_body .= "Société : " . $societe . "\n";
        if (!empty($rue)) $email_body .= "Adresse : " . $rue . "\n";
        if (!empty($code_postal)) $email_body .= "Code postal : " . $code_postal . "\n";
        if (!empty($ville)) $email_body .= "Ville : " . $ville . "\n";
        $email_body .= "Email : " . $email . "\n";
        $email_body .= "Téléphone : " . $telephone . "\n\n";
        if (!empty($message)) $email_body .= "Message :\n" . $message . "\n\n";
        
        if (!empty($uploaded_files)) {
            $email_body .= "Fichiers joints (" . count($uploaded_files) . ") :\n";
            foreach ($uploaded_files as $file) {
                $email_body .= "- " . $file['original_name'] . " (" . round($file['size']/1024, 2) . " KB)\n";
            }
        }
        
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Envoi de l'email
        if (mail($to, $subject, $email_body, $headers)) {
            $success_message = "Votre demande de devis a été envoyée avec succès !";
            if (!empty($uploaded_files)) {
                $success_message .= " " . count($uploaded_files) . " image(s) jointe(s).";
            }
        } else {
            $error_message = "Erreur lors de l'envoi de la demande. Veuillez réessayer.";
        }
    } else {
        $all_errors = array_merge($errors, $upload_errors);
        $error_message = implode("<br>", $all_errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis - Résultat</title>
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
        .uploaded-files {
            margin-top: 20px;
            text-align: left;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <?php if (isset($success_message)): ?>
            <h2 class="success">✅ Demande envoyée !</h2>
            <p><?php echo $success_message; ?></p>
            
            <?php if (!empty($uploaded_files)): ?>
                <div class="uploaded-files">
                    <h4>Images reçues :</h4>
                    <ul>
                        <?php foreach ($uploaded_files as $file): ?>
                            <li><?php echo $file['original_name']; ?> (<?php echo round($file['size']/1024, 2); ?> KB)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
        <?php elseif (isset($error_message)): ?>
            <h2 class="error">❌ Erreur</h2>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <a href="../formulaires/devis.php" class="btn-back">Retour au formulaire</a>
    </div>
</body>
</html>
