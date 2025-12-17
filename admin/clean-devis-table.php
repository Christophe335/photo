<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Nettoyage complet de la table devis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la structure actuelle
    echo "<h2>Structure actuelle de la table devis :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Défaut</th></tr>";
    $problematicColumns = [];
    foreach ($structure as $column) {
        $style = '';
        if (strpos($column['Field'], 'client_') !== false && $column['Field'] !== 'client_id') {
            $style = ' style="background-color: #ffebee; color: red;"';
            $problematicColumns[] = $column['Field'];
        }
        echo "<tr$style>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (!empty($problematicColumns)) {
        echo "<h3 style='color: red;'>🗑️ Colonnes problématiques détectées :</h3>";
        echo "<ul>";
        foreach ($problematicColumns as $col) {
            echo "<li style='color: red;'>$col (à supprimer)</li>";
        }
        echo "</ul>";
        
        echo "<h3>Suppression des colonnes parasites :</h3>";
        foreach ($problematicColumns as $col) {
            echo "<p>Suppression de '$col'...</p>";
            try {
                $db->exec("ALTER TABLE devis DROP COLUMN `$col`");
                echo "<p style='color: green;'>✓ Colonne '$col' supprimée</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Erreur suppression '$col': " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // Recréer complètement la table si nécessaire
    echo "<h3>🔄 Recréation propre de la table devis :</h3>";
    
    // Sauvegarder les données existantes si il y en a
    $stmt = $db->query("SELECT COUNT(*) as count FROM devis");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        echo "<p style='color: orange;'>⚠ $count devis existants - sauvegarde...</p>";
        $stmt = $db->query("SELECT * FROM devis");
        $existingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $existingData = [];
    }
    
    // Supprimer et recréer la table
    $db->exec("DROP TABLE IF EXISTS devis");
    
    $createSql = "
    CREATE TABLE devis (
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
        KEY client_id (client_id),
        KEY numero (numero)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    
    $db->exec($createSql);
    echo "<p style='color: green;'>✓ Table 'devis' recréée proprement</p>";
    
    // Restaurer les données si nécessaire
    if (!empty($existingData)) {
        echo "<p>Restauration des données existantes...</p>";
        $insertSql = "INSERT INTO devis (numero, client_id, date_creation, adresse_facturation, adresse_livraison, 
                      notes, total_ht, frais_port, tva, total_ttc, statut, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($insertSql);
        
        foreach ($existingData as $row) {
            $stmt->execute([
                $row['numero'] ?? 'DEV-' . $row['id'],
                $row['client_id'] ?? 1,
                $row['date_creation'] ?? date('Y-m-d H:i:s'),
                $row['adresse_facturation'] ?? '',
                $row['adresse_livraison'] ?? '',
                $row['notes'] ?? '',
                $row['total_ht'] ?? 0,
                $row['frais_port'] ?? 0,
                $row['tva'] ?? 0,
                $row['total_ttc'] ?? 0,
                $row['statut'] ?? 'brouillon',
                $row['created_at'] ?? date('Y-m-d H:i:s')
            ]);
        }
        echo "<p style='color: green;'>✓ " . count($existingData) . " devis restaurés</p>";
    }
    
    // Afficher la structure finale
    echo "<h2>✅ Structure finale de la table devis :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($structure as $column) {
        echo "<li><strong>" . $column['Field'] . "</strong> - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>🎉 Table devis nettoyée et prête !</h2>";
    echo "<p><a href='creer-devis.php' style='background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>→ Tester la création de devis</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>