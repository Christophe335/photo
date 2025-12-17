<?php
require_once '../includes/database.php';

try {
    // Tester si les tables devis existent
    $stmt = $pdo->query("SHOW TABLES LIKE 'devis'");
    $devis_exists = $stmt->rowCount() > 0;
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'devis_items'");
    $devis_items_exists = $stmt->rowCount() > 0;
    
    echo "<h2>État des tables de devis :</h2>";
    echo "<p>Table 'devis' : " . ($devis_exists ? "✅ Existe" : "❌ N'existe pas") . "</p>";
    echo "<p>Table 'devis_items' : " . ($devis_items_exists ? "✅ Existe" : "❌ N'existe pas") . "</p>";
    
    if (!$devis_exists || !$devis_items_exists) {
        echo "<h3>Création des tables manquantes...</h3>";
        
        // Lire et exécuter le script SQL
        $sql = file_get_contents('../sql/create_table_devis.sql');
        $pdo->exec($sql);
        
        echo "<p>✅ Tables créées avec succès !</p>";
        
        // Vérifier à nouveau
        $stmt = $pdo->query("SHOW TABLES LIKE 'devis'");
        $devis_exists = $stmt->rowCount() > 0;
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'devis_items'");
        $devis_items_exists = $stmt->rowCount() > 0;
        
        echo "<p>Vérification : Table 'devis' : " . ($devis_exists ? "✅ Créée" : "❌ Erreur") . "</p>";
        echo "<p>Vérification : Table 'devis_items' : " . ($devis_items_exists ? "✅ Créée" : "❌ Erreur") . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>