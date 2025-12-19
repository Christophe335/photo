<?php
/**
 * Gestionnaire d'envoi d'email pour les commandes avec fichiers
 */
class EmailManager {
    
    private $destinataire = 'webmaster@general-cover.com';
    
    /**
     * Envoie un email de confirmation de commande avec les fichiers joints
     */
    public function envoyerConfirmationCommande($panierData, $fichiers = []) {
        try {
            // Préparer le contenu de l'email
            $sujet = 'Nouvelle commande avec personnalisation - ' . date('d/m/Y H:i');
            $message = $this->genererMessageCommande($panierData, $fichiers);
            
            // En-têtes pour l'email
            $headers = [];
            $headers[] = 'From: noreply@general-cover.com';
            $headers[] = 'Reply-To: noreply@general-cover.com';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'X-Mailer: PHP/' . phpversion();
            
            // Envoi de l'email
            $result = mail($this->destinataire, $sujet, $message, implode("\r\n", $headers));
            
            if ($result) {
                error_log("Email de commande envoyé avec succès à " . $this->destinataire);
                return true;
            } else {
                error_log("Échec de l'envoi de l'email de commande");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Génère le contenu HTML de l'email de commande
     */
    private function genererMessageCommande($panierData, $fichiers) {
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
    <div class="header">
        <h1>Nouvelle Commande avec Personnalisation</h1>
        <p>Reçue le ' . date('d/m/Y à H:i') . '</p>
    </div>
    
    <div class="content">
        <h2>Détails de la commande</h2>';
        
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
            }
            
            $html .= '</tbody>
            </table>
            
            <div class="total">
                <strong>Total de la commande : ' . number_format($totalCommande, 2, ',', ' ') . ' € HT</strong>
            </div>';
        }
        
        // Section fichiers joints
        if (!empty($fichiers)) {
            $html .= '<div class="fichiers">
                <h3>📁 Fichiers de personnalisation joints</h3>
                <ul>';
            
            foreach ($fichiers as $fichier) {
                $html .= '<li>' . htmlspecialchars($fichier) . '</li>';
            }
            
            $html .= '</ul>
                <p><em>Note : Les fichiers ont été sauvegardés sur le serveur et sont prêts pour le traitement.</em></p>
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
    public function envoyerConfirmationClient($emailClient, $panierData) {
        if (empty($emailClient) || !filter_var($emailClient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        $sujet = 'Confirmation de votre commande personnalisée';
        $message = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { background: #f05124; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Merci pour votre commande !</h1>
    </div>
    
    <div class="content">
        <h2>Votre commande a bien été reçue</h2>
        <p>Nous avons bien reçu votre commande avec personnalisation le ' . date('d/m/Y à H:i') . '.</p>
        
        <p>Notre équipe va traiter votre demande et vous recontacter sous 24-48h pour confirmer les détails et la mise en production.</p>
        
        <p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p>
        
        <p>Cordialement,<br>
        L\'équipe General Cover</p>
    </div>
</body>
</html>';
        
        $headers = [];
        $headers[] = 'From: noreply@general-cover.com';
        $headers[] = 'Reply-To: webmaster@general-cover.com';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        return mail($emailClient, $sujet, $message, implode("\r\n", $headers));
    }
}
?>