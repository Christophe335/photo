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
    
    // Gestion des fichiers uploadés (ne pas stocker sur le site, les envoyer en pièces jointes)
    $uploaded_files = [];
    $upload_errors = [];
    $attachments = []; // contiendra ['name','type','content','size']

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['images']['name'] as $key => $filename) {
            if (empty($filename)) continue;

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

            // Lire le contenu temporaire sans le déplacer
            if (!is_uploaded_file($file_tmp)) {
                $upload_errors[] = "Fichier non disponible temporairement : " . $filename;
                continue;
            }

            $content = file_get_contents($file_tmp);
            if ($content === false) {
                $upload_errors[] = "Impossible de lire le fichier : " . $filename;
                continue;
            }

            $attachments[] = [
                'name' => basename($filename),
                'type' => $file_type,
                'content' => $content,
                'size' => $file_size
            ];

            $uploaded_files[] = [
                'original_name' => $filename,
                'size' => $file_size
            ];
        }
    }
    
    if (empty($errors) && empty($upload_errors)) {
        $to = "webmaster@general-cover.com";
        $subject = "Demande de devis - " . $nom . " " . $prenom;

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Calculer le chemin de base du site (gère si le site est dans un sous-dossier, ex /photo)
        $script_path = $_SERVER['SCRIPT_NAME'] ?? '';
        $site_base = dirname(dirname($script_path)); // ex. '/photo'
        if ($site_base === '/' || $site_base === '\\') $site_base = '';
        $logo_url = $scheme . '://' . $host . $site_base . '/images/logo-icon/logo.svg';
        // Nous n'utilisons plus de stockage web public pour les fichiers uploadés

        $html = '<html><body style="font-family:Arial,sans-serif;color:#333">';
        $html .= '<div style="max-width:800px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:8px">';
        $html .= '<div style="text-align:center;margin-bottom:20px">
        <img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;">
        <h3>Bindy Studio - General Cover</h3>
        <p>9 rue de la gare, 70000 Vallerois le Bois</p>
        <p>Téléphone : 03 84 78 38 39</p></div>';
        $html .= '<h2 style="color:#24256d">Nouvelle demande de devis</h2>';

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
        if (!empty($message)) $html .= $addRow('Message', $message);
        $html .= '</table>';

        if (!empty($uploaded_files)) {
            $html .= '<h4 style="margin-top:16px">Fichiers joints (' . count($uploaded_files) . ')</h4>';
            $html .= '<ul>';
            foreach ($uploaded_files as $file) {
                $html .= '<li>' . htmlspecialchars($file['original_name']) . ' (' . round($file['size']/1024,2) . ' KB)</li>';
            }
            $html .= '</ul>';
        }

        $html .= '<p style="margin-top:18px;color:#666">Message envoyé depuis le site <strong>Bindy Studio</strong>.</p>';
        $html .= '</div></body></html>';

        // Envoi : si PHPMailer est installé, on l'utilise pour attacher les fichiers
        $sent_admin = false;
        $subject_admin = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $vendorAutoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($vendorAutoload)) {
            require_once $vendorAutoload;
        }

        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->CharSet = 'UTF-8';

                // Détection d'un contexte local (laragon / localhost)
                $serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
                $isLocal = (stripos($serverHost, 'localhost') !== false) || (stripos($serverHost, '127.0.0.1') !== false) || (php_sapi_name() === 'cli-server');

                // Détection du sendmail de Laragon (Windows)
                $laragonSendmail = 'C:\\laragon\\bin\\sendmail\\sendmail.exe';
                if (file_exists($laragonSendmail)) {
                    // Utiliser le binaire sendmail de Laragon afin que les mails aillent dans C:\\laragon\\bin\\sendmail\\output
                    $mail->isSendmail();
                    $mail->Sendmail = $laragonSendmail . ' -t -i';
                } elseif ($isLocal) {
                    // Sinon tenter SMTP local (MailHog/Mailcatcher)
                    $mail->isSMTP();
                    $mail->Host = '127.0.0.1';
                    $mail->Port = 1025; // MailHog / Mailcatcher default
                    $mail->SMTPAuth = false;
                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;
                }

                $mail->setFrom('webmaster@general-cover.com', 'Bindy Studio');
                $mail->addAddress($to);
                $mail->addReplyTo($email);
                $mail->Subject = $subject;
                $mail->isHTML(true);
                $mail->Body = $html;

                // Joindre les fichiers en mémoire sans les stocker
                foreach ($attachments as $att) {
                    $mail->addStringAttachment($att['content'], $att['name'], 'base64', $att['type']);
                }

                $sent_admin = $mail->send();
            } catch (Exception $e) {
                error_log('PHPMailer error (admin): ' . $e->getMessage());
                $sent_admin = false;
            }
        } else {
            // Fallback : envoi manuel MIME multipart (déjà présentant)
            $boundary = '==MULTIPART_' . md5(uniqid(time(), true));

            $headers = [];
            $headers[] = 'From: Bindy Studio <webmaster@general-cover.com>';
            $headers[] = 'Reply-To: ' . $email;
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
            $headers[] = 'Content-Transfer-Encoding: 8bit';

            $mime_body = "--" . $boundary . "\r\n";
            $mime_body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
            $mime_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $mime_body .= $html . "\r\n";

            foreach ($attachments as $att) {
                $mime_body .= "--" . $boundary . "\r\n";
                $mime_body .= 'Content-Type: ' . $att['type'] . '; name="' . addslashes($att['name']) . '"' . "\r\n";
                $mime_body .= 'Content-Disposition: attachment; filename="' . addslashes($att['name']) . '"' . "\r\n";
                $mime_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $mime_body .= chunk_split(base64_encode($att['content'])) . "\r\n";
            }

            $mime_body .= "--" . $boundary . "--\r\n";
            $sent_admin = mail($to, $subject_admin, $mime_body, implode("\r\n", $headers));
        }

        // Email de confirmation à l'utilisateur
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $subject_user = 'Votre demande de devis a bien été reçue - Bindy Studio';
            $html_user = '<html><body style="font-family:Arial,sans-serif;color:#333">';
            $html_user .= '<div style="max-width:700px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:8px">';
            $html_user .= '<div style="text-align:center;margin-bottom:20px">
            <img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;">
            <h3>Bindy Studio - General Cover</h3>
            <p>9 rue de la gare, 70000 Vallerois le Bois</p>
            <p>Téléphone : 03 84 78 38 39</p></div>';
            $html_user .= '<h2 style="color:#24256d">Merci, nous avons bien reçu votre demande</h2>';
            $html_user .= '<p>Bonjour ' . htmlspecialchars($prenom) . ',</p>';
            $html_user .= '<p>Voici un récapitulatif de votre demande :</p>';
            $html_user .= '<table style="width:100%;border-collapse:collapse">';
            $html_user .= $addRow('Nom', $nom);
            $html_user .= $addRow('Prénom', $prenom);
            if (!empty($societe)) $html_user .= $addRow('Société', $societe);
            $html_user .= $addRow('Email', $email);
            $html_user .= $addRow('Téléphone', $telephone);
            if (!empty($message)) $html_user .= $addRow('Message', $message);
            $html_user .= '</table>';
            if (!empty($uploaded_files)) {
                $html_user .= '<p style="margin-top:12px">Fichiers reçus :</p><ul>';
                foreach ($uploaded_files as $file) {
                    $html_user .= '<li>' . htmlspecialchars($file['original_name']) . '</li>';
                }
                $html_user .= '</ul>';
            }
            $html_user .= '<p style="margin-top:18px">Nous restons à votre disposition, n\'hésitez pas à nous recontacter pour toute demande complémentaire.</p>';
            $html_user .= '<p>Cordialement,<br>Bindy Studio</p>';
            $html_user .= '</div></body></html>';

            // Envoi de l'email utilisateur (confirmation) — sans pièces jointes
            $sent_user = false;
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                try {
                    $mail2 = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail2->CharSet = 'UTF-8';

                    // Même détection locale et sendmail de Laragon pour l'email de confirmation
                    $serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
                    $isLocal = (stripos($serverHost, 'localhost') !== false) || (stripos($serverHost, '127.0.0.1') !== false) || (php_sapi_name() === 'cli-server');
                    $laragonSendmail = 'C:\\laragon\\bin\\sendmail\\sendmail.exe';
                    if (file_exists($laragonSendmail)) {
                        $mail2->isSendmail();
                        $mail2->Sendmail = $laragonSendmail . ' -t -i';
                    } elseif ($isLocal) {
                        $mail2->isSMTP();
                        $mail2->Host = '127.0.0.1';
                        $mail2->Port = 1025;
                        $mail2->SMTPAuth = false;
                        $mail2->SMTPSecure = false;
                        $mail2->SMTPAutoTLS = false;
                    }

                    $mail2->setFrom('webmaster@general-cover.com', 'Bindy Studio');
                    $mail2->addAddress($email);
                    $mail2->addReplyTo('webmaster@general-cover.com');
                    $mail2->Subject = $subject_user;
                    $mail2->isHTML(true);
                    $mail2->Body = $html_user;
                    $sent_user = $mail2->send();
                } catch (Exception $e) {
                    error_log('PHPMailer error (user): ' . $e->getMessage());
                    $sent_user = false;
                }
            } else {
                $headers_user = [];
                $headers_user[] = 'MIME-Version: 1.0';
                $headers_user[] = 'Content-type: text/html; charset=UTF-8';
                $headers_user[] = 'Content-Transfer-Encoding: 8bit';
                $headers_user[] = 'From: Bindy Studio <webmaster@general-cover.com>';
                $headers_user[] = 'Reply-To: webmaster@general-cover.com';

                $subject_user_encoded = '=?UTF-8?B?' . base64_encode($subject_user) . '?=';
                $sent_user = mail($email, $subject_user_encoded, $html_user, implode("\r\n", $headers_user));
            }
        }

        if ($sent_admin) {
            header('Location: ../formulaires/devis.php?sent=1');
            exit;
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
