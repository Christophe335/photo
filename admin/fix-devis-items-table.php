<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Correction de la table devis_items</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si la table existe
    $stmt = $db->query("SHOW TABLES LIKE 'devis_items'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La table 'devis_items' n'existe pas</p>";
        echo "<p>Création de la table...</p>";
        
        $createSql = "
        CREATE TABLE devis_items (
            id INT(11) NOT NULL AUTO_INCREMENT,
            devis_id INT(11) NOT NULL,
            produit_id INT(11) NULL,
            designation VARCHAR(255) NOT NULL,
            description TEXT,
            quantite DECIMAL(10,2) NOT NULL,
            prix_unitaire DECIMAL(10,2) NOT NULL,
            remise_type ENUM('percent','euro') DEFAULT 'percent',
            remise_valeur DECIMAL(10,2) DEFAULT 0.00,
            total_ligne DECIMAL(10,2) NOT NULL,
            ordre INT(3) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY devis_id (devis_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->exec($createSql);
        echo "<p style='color: green;'>✓ Table 'devis_items' créée</p>";
    } else {
        echo "<p style='color: green;'>✓ La table 'devis_items' existe</p>";
        
        // Vérifier la structure actuelle
        echo "<h2>Structure actuelle de la table devis_items :</h2>";
        $stmt = $db->query("DESCRIBE devis_items");
        $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $existingColumns = [];
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Défaut</th></tr>";
        foreach ($structure as $column) {
            $existingColumns[] = $column['Field'];
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Colonnes requises
        $requiredColumns = [
            'devis_id' => 'INT(11) NOT NULL',
            'produit_id' => 'INT(11) NULL',
            'designation' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'quantite' => 'DECIMAL(10,2) NOT NULL',
            'prix_unitaire' => 'DECIMAL(10,2) NOT NULL',
            'remise_type' => "ENUM('percent','euro') DEFAULT 'percent'",
            'remise_valeur' => 'DECIMAL(10,2) DEFAULT 0.00',
            'total_ligne' => 'DECIMAL(10,2) NOT NULL',
            'ordre' => 'INT(3) DEFAULT 0',
            'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
        ];
        
        echo "<h3>Ajout des colonnes manquantes :</h3>";
        
        // Vérifier et ajouter les colonnes manquantes
        foreach ($requiredColumns as $column => $definition) {
            if (!in_array($column, $existingColumns)) {
                echo "<p>Ajout de la colonne '$column'...</p>";
                try {
                    $db->exec("ALTER TABLE devis_items ADD COLUMN $column $definition");
                    echo "<p style='color: green;'>✓ Colonne '$column' ajoutée</p>";
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠ Erreur pour '$column': " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>✓ Colonne '$column' existe déjà</p>";
            }
        }
    }
    
    // Afficher la structure finale
    echo "<h2>Structure finale de la table devis_items :</h2>";
    $stmt = $db->query("DESCRIBE devis_items");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($structure as $column) {
        $style = in_array($column['Field'], ['remise_type', 'remise_valeur']) ? ' style="color: green; font-weight: bold;"' : '';
        echo "<li$style>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>✅ Table devis_items prête !</h2>";
    echo "<p><a href='creer-devis.php' style='background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>→ Tester la création de devis</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>