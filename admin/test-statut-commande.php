<?php
require_once 'functions.php';
require_once '../includes/database.php';

// Vérifier l'authentification admin
checkAuth();

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer une commande de test
    $stmt = $db->prepare("SELECT * FROM commandes ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        echo "Aucune commande trouvée pour le test";
        exit;
    }
    
    echo "<h2>Test de mise à jour de statut</h2>";
    echo "<p>Commande de test: #{$commande['numero_commande']} (ID: {$commande['id']})</p>";
    echo "<p>Statut actuel: {$commande['statut']}</p>";
    
    // Test de mise à jour vers 'annulee'
    echo "<h3>Test d'annulation...</h3>";
    
    $nouveau_statut = 'annulee';
    $stmt = $db->prepare("UPDATE commandes SET statut = ?, date_modification = NOW() WHERE id = ?");
    $result = $stmt->execute([$nouveau_statut, $commande['id']]);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Mise à jour réussie vers statut: $nouveau_statut</p>";
        
        // Vérifier le changement
        $stmt = $db->prepare("SELECT statut, date_modification FROM commandes WHERE id = ?");
        $stmt->execute([$commande['id']]);
        $updated = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>Nouveau statut: {$updated['statut']}</p>";
        echo "<p>Date modification: {$updated['date_modification']}</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Erreur lors de la mise à jour</p>";
    }
    
    // Remettre le statut original
    echo "<h3>Remise du statut original...</h3>";
    $stmt = $db->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
    $stmt->execute([$commande['statut'], $commande['id']]);
    echo "<p style='color: blue;'>Statut remis à: {$commande['statut']}</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>