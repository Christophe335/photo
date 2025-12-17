<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Diagnostic - Structure de la table devis_items</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si la table devis_items existe
    $stmt = $db->query("SHOW TABLES LIKE 'devis_items'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La table 'devis_items' n'existe pas</p>";
        echo "<p>Création de la table...</p>";
        
        // Créer la table
        $sql = "
        CREATE TABLE IF NOT EXISTS devis_items (
            id int(11) NOT NULL AUTO_INCREMENT,
            devis_id int(11) NOT NULL,
            produit_id int(11) NULL,
            designation varchar(255) NOT NULL,
            description text,
            quantite decimal(10,2) NOT NULL,
            prix_unitaire decimal(10,2) NOT NULL,
            remise_type enum('percent','euro') DEFAULT 'percent',
            remise_valeur decimal(10,2) DEFAULT 0.00,
            total_ligne decimal(10,2) NOT NULL,
            ordre int(3) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY devis_id (devis_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->exec($sql);
        echo "<p style='color: green;'>✓ Table 'devis_items' créée</p>";
    } else {
        echo "<p style='color: green;'>✓ La table 'devis_items' existe</p>";
    }
    
    // Afficher la structure de la table
    echo "<h2>Structure de la table devis_items :</h2>";
    $stmt = $db->query("DESCRIBE devis_items");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($structure as $column) {
        echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>