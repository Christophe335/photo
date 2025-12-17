<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Correction complète de la table devis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Colonnes requises par process-devis.php
    $requiredColumns = [
        'numero' => 'VARCHAR(50) NOT NULL UNIQUE',
        'client_id' => 'INT(11) NOT NULL',
        'date_creation' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
        'adresse_facturation' => 'TEXT',
        'adresse_livraison' => 'TEXT',
        'notes' => 'TEXT',
        'total_ht' => 'DECIMAL(10,2) DEFAULT 0.00',
        'frais_port' => 'DECIMAL(10,2) DEFAULT 0.00',
        'tva' => 'DECIMAL(10,2) DEFAULT 0.00',
        'total_ttc' => 'DECIMAL(10,2) DEFAULT 0.00',
        'statut' => "ENUM('brouillon','envoye','accepte','refuse','expire') DEFAULT 'brouillon'",
        'date_envoi' => 'DATETIME NULL',
        'date_acceptation' => 'DATETIME NULL',
        'date_expiration' => 'DATETIME NULL',
        'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
        'updated_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ];
    
    // Vérifier si la table existe
    $stmt = $db->query("SHOW TABLES LIKE 'devis'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La table 'devis' n'existe pas</p>";
        echo "<p>Création de la table complète...</p>";
        
        $sql = "CREATE TABLE devis (
            id INT(11) NOT NULL AUTO_INCREMENT,
            numero VARCHAR(50) NOT NULL UNIQUE,
            client_id INT(11) NOT NULL,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            adresse_facturation TEXT,
            adresse_livraison TEXT,
            notes TEXT,
            total_ht DECIMAL(10,2) DEFAULT 0.00,
            frais_port DECIMAL(10,2) DEFAULT 0.00,
            tva DECIMAL(10,2) DEFAULT 0.00,
            total_ttc DECIMAL(10,2) DEFAULT 0.00,
            statut ENUM('brouillon','envoye','accepte','refuse','expire') DEFAULT 'brouillon',
            date_envoi DATETIME NULL,
            date_acceptation DATETIME NULL,
            date_expiration DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->exec($sql);
        echo "<p style='color: green;'>✓ Table 'devis' créée avec toutes les colonnes</p>";
    } else {
        echo "<p style='color: green;'>✓ La table 'devis' existe</p>";
        
        // Vérifier les colonnes existantes
        $stmt = $db->query("DESCRIBE devis");
        $existingColumns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = $row['Field'];
        }
        
        echo "<h3>Colonnes existantes :</h3>";
        echo "<ul>";
        foreach ($existingColumns as $col) {
            echo "<li>$col</li>";
        }
        echo "</ul>";
        
        echo "<h3>Ajout des colonnes manquantes :</h3>";
        
        // Ajouter les colonnes manquantes
        foreach ($requiredColumns as $column => $definition) {
            if (!in_array($column, $existingColumns)) {
                echo "<p>Ajout de la colonne '$column'...</p>";
                try {
                    $db->exec("ALTER TABLE devis ADD COLUMN $column $definition");
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
    echo "<h2>Structure finale de la table devis :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 12px;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    foreach ($structure as $column) {
        echo "<tr>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2 style='color: green;'>✅ Table devis prête pour l'utilisation !</h2>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>