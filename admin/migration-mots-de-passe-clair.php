<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Migration des mots de passe - Affichage en clair</h2>";
    
    // Vérifier les clients sans mot de passe clair
    $stmt = $db->prepare("
        SELECT id, email, prenom, nom, mot_de_passe 
        FROM clients 
        WHERE mot_de_passe_clair IS NULL OR mot_de_passe_clair = ''
    ");
    $stmt->execute();
    $clients_sans_mdp_clair = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Clients sans mot de passe en clair : " . count($clients_sans_mdp_clair) . "</p>";
    
    if (!empty($clients_sans_mdp_clair)) {
        echo "<h3>Génération de mots de passe pour les clients existants :</h3>";
        
        foreach ($clients_sans_mdp_clair as $client) {
            // Générer un mot de passe simple basé sur l'ID
            $nouveau_mdp = 'Client' . str_pad($client['id'], 3, '0', STR_PAD_LEFT) . '!';
            
            // Mettre à jour avec le nouveau mot de passe
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt_update = $db->prepare("UPDATE clients SET mot_de_passe = ?, mot_de_passe_clair = ? WHERE id = ?");
            $stmt_update->execute([$hash, $nouveau_mdp, $client['id']]);
            
            echo "<p>✓ Client #{$client['id']} ({$client['email']}) - Nouveau mot de passe : <strong>$nouveau_mdp</strong></p>";
        }
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>Migration terminée !</strong> Tous les clients ont maintenant un mot de passe visible dans l'administration.";
        echo "</div>";
    } else {
        echo "<p style='color: green;'>✓ Tous les clients ont déjà un mot de passe en clair défini.</p>";
    }
    
    // Statistiques finales
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN mot_de_passe_clair IS NOT NULL AND mot_de_passe_clair != '' THEN 1 END) as avec_clair
        FROM clients
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Statistiques :</h3>";
    echo "<ul>";
    echo "<li>Total clients : {$stats['total']}</li>";
    echo "<li>Avec mot de passe clair : {$stats['avec_clair']}</li>";
    echo "<li>Pourcentage : " . round(($stats['avec_clair'] / max(1, $stats['total'])) * 100, 1) . "%</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
</style>