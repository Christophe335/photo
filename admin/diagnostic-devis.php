<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Diagnostic - Structure de la table devis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si la table devis existe
    $stmt = $db->query("SHOW TABLES LIKE 'devis'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ La table 'devis' n'existe pas</p>";
        echo "<p>Création de la table...</p>";
        
        // Créer la table
        $sql = "
        CREATE TABLE IF NOT EXISTS devis (
            id int(11) NOT NULL AUTO_INCREMENT,
            numero varchar(50) NOT NULL UNIQUE,
            client_id int(11) NOT NULL,
            date_creation datetime DEFAULT CURRENT_TIMESTAMP,
            adresse_facturation text,
            adresse_livraison text,
            notes text,
            total_ht decimal(10,2) DEFAULT 0.00,
            frais_port decimal(10,2) DEFAULT 0.00,
            tva decimal(10,2) DEFAULT 0.00,
            total_ttc decimal(10,2) DEFAULT 0.00,
            statut enum('brouillon','envoye','accepte','refuse','expire') DEFAULT 'brouillon',
            date_envoi datetime NULL,
            date_acceptation datetime NULL,
            date_expiration datetime NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->exec($sql);
        echo "<p style='color: green;'>✓ Table 'devis' créée</p>";
    } else {
        echo "<p style='color: green;'>✓ La table 'devis' existe</p>";
    }
    
    // Afficher la structure de la table
    echo "<h2>Structure actuelle de la table devis :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    foreach ($structure as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Vérifier si la colonne numero existe
    $hasNumero = false;
    foreach ($structure as $column) {
        if ($column['Field'] === 'numero') {
            $hasNumero = true;
            break;
        }
    }
    
    if (!$hasNumero) {
        echo "<h2 style='color: red;'>❌ La colonne 'numero' est manquante !</h2>";
        echo "<p>Ajout de la colonne numero...</p>";
        
        $db->exec("ALTER TABLE devis ADD COLUMN numero VARCHAR(50) NOT NULL UNIQUE AFTER id");
        echo "<p style='color: green;'>✓ Colonne 'numero' ajoutée</p>";
        
        // Réafficher la structure mise à jour
        echo "<h3>Structure mise à jour :</h3>";
        $stmt = $db->query("DESCRIBE devis");
        $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul>";
        foreach ($structure as $column) {
            echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>✓ La colonne 'numero' existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>