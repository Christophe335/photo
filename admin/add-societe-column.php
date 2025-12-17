<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Ajout de la colonne Société</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si la colonne existe déjà
    $stmt = $db->query("SHOW COLUMNS FROM clients LIKE 'societe'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✓ La colonne 'societe' existe déjà</p>";
    } else {
        // Ajouter la colonne société après l'ID
        $db->exec("ALTER TABLE clients ADD COLUMN societe VARCHAR(100) NULL AFTER id");
        echo "<p>✓ Colonne 'societe' ajoutée avec succès</p>";
    }
    
    // Vérifier la nouvelle structure
    echo "<h2>Nouvelle structure de la table clients :</h2>";
    $stmt = $db->query("DESCRIBE clients");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    foreach ($structure as $column) {
        echo $column['Field'] . " - " . $column['Type'] . ($column['Null'] == 'YES' ? ' (nullable)' : ' (required)') . "\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>