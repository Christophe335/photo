<?php
/**
 * Gestionnaire d'envoi d'email pour les commandes avec fichiers
 */
class EmailManager {
    
    private $destinataire = 'webmaster@general-cover.com';
    
    /**
     * Envoie un email de confirmation de commande avec les fichiers joints
     */
    public function envoyerConfirmationCommande($panierData, $fichiers = [], $numeroCommande = null, $clientInfo = []) {
        try {
            // Préparer le contenu de l'email
            $sujet = 'Nouvelle commande - ' . date('d/m/Y H:i');
            $message = $this->genererMessageCommande($panierData, $fichiers, $numeroCommande, $clientInfo);
            // S'assurer que le logo utilise une URL absolue dans les emails
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
            $logo_url = $scheme . '://' . $host . '/images/logo-icon/logo.svg';
            $message = str_replace('src="../images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $message);
            $message = str_replace('src="/images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $message);
            
            // Tenter d'envoyer avec PHPMailer si disponible (permet d'attacher des fichiers en mémoire)
            $vendorAutoload = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($vendorAutoload)) {
                require_once $vendorAutoload;
            }

            // Préparer les pièces jointes transformées: chaque élément sera ['name','type','content']
            $preparedAttachments = [];
            foreach ($fichiers as $f) {
                $name = null;
                $url = null;
                $raw = null;
                // Normaliser différentes formes possibles
                if (is_array($f)) {
                    // Accept various possible keys coming from different upload scripts
                    if (isset($f['url'])) $url = $f['url'];
                    if (isset($f['src'])) $url = $f['src'];
                    if (isset($f['data'])) $raw = $f['data'];
                    if (isset($f['content'])) $raw = $f['content'];
                    if (isset($f['dataUrl'])) $url = $f['dataUrl'];
                    if (isset($f['data_url'])) $url = $f['data_url'];
                    if (isset($f['originalDataUrl'])) $url = $f['originalDataUrl'];
                    // Name normalization
                    $name = $f['nom'] ?? ($f['name'] ?? ($f['original_name'] ?? ($f['filename'] ?? null)));
                } elseif (is_string($f)) {
                    // string could be a path or data URL
                    if (strpos($f, 'data:') === 0) {
                        $url = $f;
                    } elseif (file_exists($f)) {
                        $raw = @file_get_contents($f);
                        $name = basename($f);
                    } else {
                        $url = $f;
                    }
                }

                // If raw binary provided
                if ($raw !== null) {
                    $mime = @finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $raw) ?: 'application/octet-stream';
                    if (!$name) $name = 'file_' . uniqid();
                    $preparedAttachments[] = ['name' => $name, 'type' => $mime, 'content' => $raw];
                    continue;
                }

                if ($url) {
                    // data:base64 case
                    if (preg_match('#^data:([^;]+);base64,(.*)$#', $url, $m)) {
                        $mime = $m[1];
                        $data = base64_decode($m[2]);
                        if ($data !== false) {
                            if (!$name) $name = 'image_' . uniqid() . '.' . explode('/', $mime)[1] ?? '';
                            $preparedAttachments[] = ['name' => $name, 'type' => $mime, 'content' => $data];
                            continue;
                        }
                    }

                    // URL or relative path: try retrieving
                    $data = @file_get_contents($url);
                    if ($data === false) {
                        // try as local path under document root
                        $localPath = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($url, '/\\');
                        if (file_exists($localPath)) {
                            $data = @file_get_contents($localPath);
                        }
                    }
                    if ($data !== false && $data !== null) {
                        $tmpFile = tempnam(sys_get_temp_dir(), 'att_');
                        file_put_contents($tmpFile, $data);
                        $mime = @mime_content_type($tmpFile) ?: 'application/octet-stream';
                        @unlink($tmpFile);
                        if (!$name) $name = basename(parse_url($url, PHP_URL_PATH) ?: ('file_' . uniqid()));
                        $preparedAttachments[] = ['name' => $name, 'type' => $mime, 'content' => $data];
                        continue;
                    }
                }

                // nothing matched, skip
                error_log('EmailManager: pièce jointe ignorée (format non pris en charge)');
            }

            // Si PHPMailer est installé, l'utiliser (meilleure gestion des pièces jointes en mémoire)
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
                        $mail->isSendmail();
                        $mail->Sendmail = $laragonSendmail . ' -t -i';
                    } elseif ($isLocal) {
                        $mail->isSMTP();
                        $mail->Host = '127.0.0.1';
                        $mail->Port = 1025; // MailHog / Mailcatcher default
                        $mail->SMTPAuth = false;
                        $mail->SMTPSecure = false;
                        $mail->SMTPAutoTLS = false;
                    }

                    $mail->setFrom('webmaster@general-cover.com', 'General Cover');
                    $mail->addAddress($this->destinataire);
                    $mail->Subject = $sujet;
                    $mail->isHTML(true);
                    $mail->Body = $message;

                    // Joindre les pièces préparées
                    // Log des pièces préparées pour debug
                    $attachmentNames = array_map(function($a){ return $a['name'] ?? 'unnamed'; }, $preparedAttachments);
                    error_log('EmailManager: pièces préparées pour envoi: ' . json_encode($attachmentNames));

                    // Dump debug des pieces préparées (ne contient pas les binary content en clair)
                    try {
                        $storageDir = __DIR__ . '/../storage';
                        if (!is_dir($storageDir)) @mkdir($storageDir, 0777, true);
                        $dump = [];
                        foreach ($preparedAttachments as $p) {
                            $dump[] = [
                                'name' => $p['name'] ?? null,
                                'type' => $p['type'] ?? null,
                                'size' => isset($p['content']) ? strlen($p['content']) : null
                            ];
                        }
                        $dumpFile = $storageDir . '/debug_prepared_attachments_' . time() . '.json';
                        @file_put_contents($dumpFile, json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        error_log('EmailManager: dump preparedAttachments écrit dans ' . $dumpFile);
                    } catch (Exception $e) {
                        error_log('EmailManager: erreur écriture dump preparedAttachments: ' . $e->getMessage());
                    }

                    foreach ($preparedAttachments as $att) {
                        $mail->addStringAttachment($att['content'], $att['name'], 'base64', $att['type']);
                    }

                    $sent = $mail->send();
                    if ($sent) {
                        error_log("Email de commande envoyé avec pièces jointes à " . $this->destinataire);
                        return true;
                    }
                } catch (Exception $e) {
                    error_log('PHPMailer error (commande): ' . $e->getMessage());
                    // fallthrough to fallback
                }
            }

            // Fallback : construire un message MIME multipart avec les pièces jointes en mémoire
            $boundary = '==MULTIPART_' . md5(uniqid((string)time(), true));
            $headers = [];
            $headers[] = 'From: noreply@general-cover.com';
            $headers[] = 'Reply-To: noreply@general-cover.com';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

            $mime_body = "--" . $boundary . "\r\n";
            $mime_body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
            $mime_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $mime_body .= $message . "\r\n";

            foreach ($preparedAttachments as $att) {
                $mime_body .= "--" . $boundary . "\r\n";
                $mime_body .= 'Content-Type: ' . ($att['type'] ?? 'application/octet-stream') . '; name="' . addslashes($att['name']) . '"' . "\r\n";
                $mime_body .= 'Content-Disposition: attachment; filename="' . addslashes($att['name']) . '"' . "\r\n";
                $mime_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $mime_body .= chunk_split(base64_encode($att['content'])) . "\r\n";
            }

            $mime_body .= "--" . $boundary . "--\r\n";
            $result = mail($this->destinataire, $sujet, $mime_body, implode("\r\n", $headers));
            if ($result) {
                error_log("Email de commande envoyé (fallback) à " . $this->destinataire);
                return true;
            }
            error_log("Échec de l'envoi de l'email de commande (toutes méthodes)");
            return false;
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Génère le contenu HTML de l'email de commande
     */
    private function genererMessageCommande($panierData, $fichiers, $numeroCommande = null, $clientInfo = []) {
        // Construire une URL absolue pour le logo (utile dans les clients mail)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $logo_url = $scheme . '://' . $host . '/images/logo-icon/logo.svg';

        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { background: #2a256d; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .produit { border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; }
        .total { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .fichiers { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div style="text-align:center;margin-bottom:20px">
        <img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;">
        <h3>Bindy Studio - General Cover</h3>
        <p>9 rue de la gare, 70000 Vallerois le Bois</p>
        <p>Téléphone : 03 84 78 38 39</p>
    </div>
    <div class="header">
        <h1>Nouvelle Commande</h1>
        <p>Reçue le ' . date('d/m/Y à H:i') . '</p>
    </div>
    
    <div class="content">
        <h2>Détails de la commande</h2>';
        // Informations client / numéro de commande
        if (!empty($numeroCommande) || !empty($clientInfo)) {
            $html .= '<div style="margin-bottom:15px;padding:12px;background:#f1f1f1;border-radius:6px">';
            if (!empty($numeroCommande)) {
                $html .= '<p><strong>Numéro de commande :</strong> ' . htmlspecialchars($numeroCommande) . '</p>';
            }
            if (!empty($clientInfo) && is_array($clientInfo)) {
                $nomComplet = trim(($clientInfo['prenom'] ?? '') . ' ' . ($clientInfo['nom'] ?? ''));
                $societe = $clientInfo['societe'] ?? '';
                $email = $clientInfo['email'] ?? '';
                $telephone = $clientInfo['telephone'] ?? '';

                // Adresses : prendre plusieurs clés possibles (fr/en)
                $billing = $clientInfo['adresse_facturation'] ?? $clientInfo['billing_address'] ?? $clientInfo['adresse'] ?? '';
                $shipping = $clientInfo['adresse_livraison'] ?? $clientInfo['shipping_address'] ?? $clientInfo['livraison'] ?? '';

                // Si l'adresse est fournie sous forme de tableau, construire une chaîne lisible
                $formatAddress = function($addr) {
                    if (empty($addr)) return '';
                    if (is_string($addr)) return $addr;
                    if (!is_array($addr)) return '';
                    $parts = [];
                    foreach (['ligne1','ligne2','rue','address','street','street_address','adresse'] as $k) {
                        if (!empty($addr[$k])) $parts[] = $addr[$k];
                    }
                    $cp = $addr['code_postal'] ?? $addr['postal_code'] ?? $addr['zip'] ?? $addr['cp'] ?? '';
                    $city = $addr['ville'] ?? $addr['city'] ?? '';
                    if ($cp || $city) $parts[] = trim(($cp ? $cp . ' ' : '') . $city);
                    if (!empty($addr['pays'])) $parts[] = $addr['pays'];
                    if (!empty($addr['country'])) $parts[] = $addr['country'];
                    return implode("\n", $parts);
                };

                $billingStr = $formatAddress($billing);
                $shippingStr = $formatAddress($shipping);

                // Tableau 3 colonnes (Coordonnées | Facturation | Livraison) - compatible clients mail
                $html .= '<div style="margin-top:12px;padding:10px;background:#f9f9f9;border-radius:6px">';
                $html .= '<table width="100%" cellpadding="8" cellspacing="0" role="presentation" style="border-collapse:collapse;">';
                $html .= '<tr>';

                // Colonne 1 : Coordonnées
                $html .= '<td valign="top" style="width:33%;vertical-align:top;">';
                $html .= '<strong>Coordonnées</strong><br>';
                if (!empty($nomComplet)) $html .= htmlspecialchars($nomComplet) . '<br>';
                if (!empty($societe)) $html .= htmlspecialchars($societe) . '<br>';
                if (!empty($email)) $html .= 'Email: ' . htmlspecialchars($email) . '<br>';
                if (!empty($telephone)) $html .= 'Tél: ' . htmlspecialchars($telephone) . '<br>';
                $html .= '</td>';

                // Colonne 2 : Adresse de facturation
                $html .= '<td valign="top" style="width:33%;vertical-align:top;">';
                $html .= '<strong>Adresse de facturation</strong><br>';
                $html .= (!empty($billingStr) ? nl2br(htmlspecialchars($billingStr)) : '<span style="color:#999">Non fournie</span>');
                $html .= '</td>';

                // Colonne 3 : Adresse de livraison
                $html .= '<td valign="top" style="width:34%;vertical-align:top;">';
                $html .= '<strong>Adresse de livraison</strong><br>';
                $html .= (!empty($shippingStr) ? nl2br(htmlspecialchars($shippingStr)) : '<span style="color:#999">Non fournie</span>');
                $html .= '</td>';

                $html .= '</tr></table></div>';
            }
            $html .= '</div>';
        }
        
        if (!empty($panierData) && is_array($panierData)) {
            $html .= '<table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th>Format</th>
                        <th>Couleur</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
            
            $totalCommande = 0;
            foreach ($panierData as $item) {
                $totalLigne = $item['prix'] * $item['quantite'];
                $totalCommande += $totalLigne;
                
                $html .= '<tr>
                    <td>' . htmlspecialchars($item['details']['code'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($item['details']['designation'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($item['details']['format'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($item['details']['couleur'] ?? 'N/A') . '</td>
                    <td>' . intval($item['quantite']) . '</td>
                    <td>' . number_format($item['prix'], 2, ',', ' ') . ' € HT</td>
                    <td>' . number_format($totalLigne, 2, ',', ' ') . ' € HT</td>
                </tr>';

                // Si le client a fourni un détail de personnalisation, l'afficher sous la ligne
                if (!empty($item['details']['personnalisation_detail'])) {
                    $html .= '<tr><td colspan="7" style="background:#fff7e6;padding:8px;color:#333"><strong>Détail de la personnalisation :</strong> ' . nl2br(htmlspecialchars($item['details']['personnalisation_detail'])) . '</td></tr>';
                }
            }
            
            $html .= '</tbody>
            </table>';

            $fraisLivraison = ($totalCommande > 200) ? 0 : 13.95;
            $tvaCommande = ($totalCommande + $fraisLivraison) * 0.20;
            $totalTTC = $totalCommande + $fraisLivraison + $tvaCommande;

            $html .= '<div class="total">';
            $html .= '<p style="margin:6px 0">Total HT : <strong>' . number_format($totalCommande, 2, ',', ' ') . ' €</strong></p>';
            $html .= '<p style="margin:6px 0">Frais de port : <strong>' . ($fraisLivraison == 0 ? 'Gratuit' : number_format($fraisLivraison, 2, ',', ' ') . ' €') . '</strong></p>';
            $html .= '<p style="margin:6px 0">TVA (20%) : <strong>' . number_format($tvaCommande, 2, ',', ' ') . ' €</strong></p>';
            $html .= '<p style="margin:8px 0"><strong>Total TTC : ' . number_format($totalTTC, 2, ',', ' ') . ' €</strong></p>';
            $html .= '</div>';
        }

        // Section fichiers joints
        if (!empty($fichiers)) {
            $html .= '<div class="fichiers">
                <h3>📁 Fichiers de personnalisation joints</h3>
                <ul>';

            foreach ($fichiers as $fichier) {
                if (is_array($fichier)) {
                    $nomAffiche = $fichier['nom'] ?? ($fichier['name'] ?? 'Fichier');
                } else {
                    $nomAffiche = $fichier;
                }
                $html .= '<li>' . htmlspecialchars($nomAffiche) . '</li>';
            }

            $html .= '</ul>
                <p><em>Note : Les fichiers fournis par le client sont joints à cet email.</em></p>
            </div>';
        }

        $html .= '
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>Informations techniques :</strong></p>
            <ul>
                <li>IP du client : ' . htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'N/A') . '</li>
                <li>User Agent : ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . '</li>
                <li>Date/Heure : ' . date('d/m/Y H:i:s') . '</li>
            </ul>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Envoie un email de confirmation au client (optionnel)
     */
    public function envoyerConfirmationClient($emailClient, $panierData, $fichiers = [], $numeroCommande = null, $clientInfo = []) {
        if (empty($emailClient) || !filter_var($emailClient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }








            $sujet = 'Confirmation de votre commande personnalisée';
            $html = $this->genererMessageClient($panierData, $fichiers, $numeroCommande, $clientInfo);
            // S'assurer que le logo utilise une URL absolue dans l'email client
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
            $logo_url = $scheme . '://' . $host . '/images/logo-icon/logo.svg';
            $html = str_replace('src="../images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $html);
            $html = str_replace('src="/images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $html);

            $headers = [];
            $headers[] = 'From: noreply@general-cover.com';
            $headers[] = 'Reply-To: webmaster@general-cover.com';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            return mail($emailClient, $sujet, $html, implode("\r\n", $headers));
        }

        /**
         * Génère un message HTML compact pour le client avec récapitulatif
         */
        private function genererMessageClient($panierData, $fichiers, $numeroCommande = null, $clientInfo = []) {
            // Construire une URL absolue pour le logo (utile dans les clients mail)
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
            $logo_url = $scheme . '://' . $host . '/images/logo-icon/logo.svg';

            $html = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif} .header{background:#f05124;color:#fff;padding:18px;text-align:center} .content{padding:18px} table{width:100%;border-collapse:collapse} th,td{padding:8px;border-bottom:1px solid #eee;text-align:left} .fichiers{background:#f8f9fa;padding:10px;border-radius:6px;margin-top:12px}</style></head><body>';
            $html .= '<div style="text-align:center;margin-bottom:20px">'
                . '<img src="' . $logo_url . '" alt="Bindy Studio" style="max-width:120px;max-height:120px;width:auto;height:auto;">'
                . '<h3>Bindy Studio - General Cover</h3>'
                . '<p>9 rue de la gare, 70000 Vallerois le Bois</p>'
                . '<p>Téléphone : 03 84 78 38 39</p>'
                . '</div>';
            $html .= '<div class="header"><h1>Merci pour votre commande</h1></div>';
            $html .= '<div class="content">';
            $html .= '<p>Nous avons bien reçu votre commande' . (!empty($numeroCommande) ? ' <strong>#' . htmlspecialchars($numeroCommande) . '</strong>' : '') . ' le ' . date('d/m/Y \à H:i') . '.</p>';

            if (!empty($panierData) && is_array($panierData)) {
                $html .= '<h3>Récapitulatif</h3><table><thead><tr><th>Réf</th><th>Désignation</th><th>Qté</th><th>Prix</th></tr></thead><tbody>';
                $total = 0;
                foreach ($panierData as $item) {
                    $ref = htmlspecialchars($item['details']['code'] ?? '');
                    $des = htmlspecialchars($item['details']['designation'] ?? '');
                    $qty = intval($item['quantite'] ?? 0);
                    $prix = number_format($item['prix'] ?? 0, 2, ',', ' ') . ' €';
                    $html .= '<tr><td>' . $ref . '</td><td>' . $des . '</td><td>' . $qty . '</td><td>' . $prix . '</td></tr>';
                    $total += ($item['prix'] ?? 0) * $qty;
                    if (!empty($item['details']['personnalisation_detail'])) {
                        $html .= '<tr><td colspan="4" style="background:#fff7e6;padding:8px;color:#333"><strong>Détail de la personnalisation :</strong> ' . nl2br(htmlspecialchars($item['details']['personnalisation_detail'])) . '</td></tr>';
                    }
                }
                $html .= '</tbody></table>';
                $fraisPort = ($total > 200) ? 0 : 13.95;
                $tva = ($total + $fraisPort) * 0.20;
                $ttc = $total + $fraisPort + $tva;
                $html .= '<p><strong>Total HT : ' . number_format($total, 2, ',', ' ') . ' €</strong></p>';
                $html .= '<p>Frais de port : <strong>' . ($fraisPort == 0 ? 'Gratuit' : number_format($fraisPort, 2, ',', ' ') . ' €') . '</strong></p>';
                $html .= '<p>TVA (20%) : <strong>' . number_format($tva, 2, ',', ' ') . ' €</strong></p>';
                $html .= '<p style="margin-top:8px"><strong>Total TTC : ' . number_format($ttc, 2, ',', ' ') . ' €</strong></p>';
            }

            // Fichiers info
            if (!empty($fichiers)) {
                $count = count($fichiers);
                $html .= '<div class="fichiers"><strong>' . $count . ' fichier' . ($count > 1 ? 's' : '') . ' transmis :</strong><ul>';
                foreach ($fichiers as $f) {
                    if (is_array($f)) {
                        $nom = $f['nom'] ?? ($f['name'] ?? 'Fichier');
                    } else {
                        $nom = is_string($f) ? basename($f) : 'Fichier';
                    }
                    $ext = pathinfo($nom, PATHINFO_EXTENSION);
                    $html .= '<li>' . htmlspecialchars($nom) . (!empty($ext) ? ' (' . htmlspecialchars(strtolower($ext)) . ')' : '') . '</li>';
                }
                $html .= '</ul></div>';
            }

            if (!empty($clientInfo)) {
                $nomComplet = trim(($clientInfo['prenom'] ?? '') . ' ' . ($clientInfo['nom'] ?? ''));
                $societe = $clientInfo['societe'] ?? '';
                $email = $clientInfo['email'] ?? '';
                $telephone = $clientInfo['telephone'] ?? '';

                $billing = $clientInfo['adresse_facturation'] ?? $clientInfo['billing_address'] ?? $clientInfo['adresse'] ?? '';
                $shipping = $clientInfo['adresse_livraison'] ?? $clientInfo['shipping_address'] ?? $clientInfo['livraison'] ?? '';
                $formatAddress = function($addr) {
                    if (empty($addr)) return '';
                    if (is_string($addr)) return $addr;
                    if (!is_array($addr)) return '';
                    $parts = [];
                    foreach (['ligne1','ligne2','rue','address','street','street_address','adresse'] as $k) {
                        if (!empty($addr[$k])) $parts[] = $addr[$k];
                    }
                    $cp = $addr['code_postal'] ?? $addr['postal_code'] ?? $addr['zip'] ?? $addr['cp'] ?? '';
                    $city = $addr['ville'] ?? $addr['city'] ?? '';
                    if ($cp || $city) $parts[] = trim(($cp ? $cp . ' ' : '') . $city);
                    if (!empty($addr['pays'])) $parts[] = $addr['pays'];
                    if (!empty($addr['country'])) $parts[] = $addr['country'];
                    return implode("\n", $parts);
                };
                $billingStr = $formatAddress($billing);
                $shippingStr = $formatAddress($shipping);

                $html .= '<div style="margin-top:12px;padding:10px;background:#f9f9f9;border-radius:6px">';
                $html .= '<table width="100%" cellpadding="8" cellspacing="0" role="presentation" style="border-collapse:collapse;">';
                $html .= '<tr>';

                // Colonne 1 : Coordonnées
                $html .= '<td valign="top" style="width:33%;vertical-align:top;">';
                $html .= '<strong>Coordonnées</strong><br>';
                if (!empty($nomComplet)) $html .= htmlspecialchars($nomComplet) . '<br>';
                if (!empty($societe)) $html .= htmlspecialchars($societe) . '<br>';
                if (!empty($email)) $html .= 'Email: ' . htmlspecialchars($email) . '<br>';
                if (!empty($telephone)) $html .= 'Tél: ' . htmlspecialchars($telephone) . '<br>';
                $html .= '</td>';

                // Colonne 2 : Adresse de facturation
                $html .= '<td valign="top" style="width:33%;vertical-align:top;">';
                $html .= '<strong>Adresse de facturation</strong><br>';
                $html .= (!empty($billingStr) ? nl2br(htmlspecialchars($billingStr)) : '<span style="color:#999">Non fournie</span>');
                $html .= '</td>';

                // Colonne 3 : Adresse de livraison
                $html .= '<td valign="top" style="width:34%;vertical-align:top;">';
                $html .= '<strong>Adresse de livraison</strong><br>';
                $html .= (!empty($shippingStr) ? nl2br(htmlspecialchars($shippingStr)) : '<span style="color:#999">Non fournie</span>');
                $html .= '</td>';

                $html .= '</tr></table></div>';
            }

            $html .= '<p style="margin-top:14px">Notre équipe vous contactera bientôt pour confirmer les détails.</p>';
            $html .= '<p>Cordialement,<br>L\'équipe General Cover</p>';
            $html .= '</div></body></html>';

            // remplacer les chemins relatifs par l'URL absolue du logo
            $html = str_replace('src="../images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $html);
            $html = str_replace('src="/images/logo-icon/logo.svg"', 'src="' . $logo_url . '"', $html);

            return $html;
        }
}
?>