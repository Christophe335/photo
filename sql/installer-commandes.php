<?php
/**
 * Installeur simplifié pour le système de commandes
 * 
 * Ce script ajoute uniquement les tables et colonnes nécessaires
 * pour le système de commandes sans conflit avec l'existant.
 */

require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    
    echo "<h1>Installation du système de commandes</h1>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
    
    $success_count = 0;
    $error_count = 0;
    
    echo "<h2>Mise à jour de la table clients...</h2>";
    echo "<ul>";
    
    // 1. Ajouter les colonnes d'adresse de livraison
    $columns_to_add = [
        'adresse_livraison_differente' => "TINYINT(1) DEFAULT 0 COMMENT 'Adresse livraison différente'",
        'adresse_livraison' => "TEXT NULL COMMENT 'Adresse de livraison'",
        'code_postal_livraison' => "VARCHAR(10) NULL COMMENT 'Code postal livraison'",
        'ville_livraison' => "VARCHAR(50) NULL COMMENT 'Ville livraison'",
        'pays_livraison' => "VARCHAR(50) NULL COMMENT 'Pays livraison'"
    ];
    
    foreach ($columns_to_add as $column_name => $column_def) {
        try {
            // Vérifier si la colonne existe
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'clients' 
                AND COLUMN_NAME = ?
            ");
            $stmt->execute([$column_name]);
            $exists = $stmt->fetchColumn();
            
            if (!$exists) {
                $db->exec("ALTER TABLE clients ADD COLUMN $column_name $column_def");
                echo "<li style='color: green;'>✓ Colonne '$column_name' ajoutée</li>";
                $success_count++;
            } else {
                echo "<li style='color: orange;'>⚠ Colonne '$column_name' existe déjà</li>";
            }
        } catch (PDOException $e) {
            echo "<li style='color: red;'>✗ Erreur colonne '$column_name': " . htmlspecialchars($e->getMessage()) . "</li>";
            $error_count++;
        }
    }
    
    echo "</ul>";
    
    // 2. Créer les tables de commandes
    echo "<h2>Création des tables de commandes...</h2>";
    echo "<ul>";
    
    // Table commandes
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS commandes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_id INT NOT NULL,
                numero_commande VARCHAR(20) NOT NULL UNIQUE,
                statut ENUM('en_attente', 'confirmee', 'en_preparation', 'en_cours', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
                sous_total DECIMAL(10,2) NOT NULL,
                tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                frais_livraison DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                total DECIMAL(10,2) NOT NULL,
                adresse_facturation TEXT NOT NULL,
                code_postal_facturation VARCHAR(10) NOT NULL,
                ville_facturation VARCHAR(50) NOT NULL,
                pays_facturation VARCHAR(50) NOT NULL,
                adresse_livraison TEXT,
                code_postal_livraison VARCHAR(10),
                ville_livraison VARCHAR(50),
                pays_livraison VARCHAR(50),
                mode_paiement VARCHAR(50) DEFAULT 'carte_bancaire',
                statut_paiement ENUM('en_attente', 'paye', 'echec', 'rembourse') DEFAULT 'en_attente',
                reference_paiement VARCHAR(100),
                date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date_confirmation TIMESTAMP NULL,
                date_expedition TIMESTAMP NULL,
                date_livraison TIMESTAMP NULL,
                numero_suivi VARCHAR(50) NULL,
                transporteur VARCHAR(50) NULL,
                commentaire_client TEXT,
                note_interne TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_commandes_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<li style='color: green;'>✓ Table 'commandes' créée</li>";
        $success_count++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color: orange;'>⚠ Table 'commandes' existe déjà</li>";
        } else {
            echo "<li style='color: red;'>✗ Erreur table 'commandes': " . htmlspecialchars($e->getMessage()) . "</li>";
            $error_count++;
        }
    }
    
    // Table commande_items
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS commande_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                commande_id INT NOT NULL,
                produit_code VARCHAR(50) NOT NULL,
                designation TEXT NOT NULL,
                format VARCHAR(100),
                couleur VARCHAR(50),
                conditionnement VARCHAR(100),
                quantite INT NOT NULL,
                prix_unitaire DECIMAL(10,2) NOT NULL,
                total_ligne DECIMAL(10,2) NOT NULL,
                donnees_produit JSON COMMENT 'Détails produit au moment de la commande',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_commande_items_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<li style='color: green;'>✓ Table 'commande_items' créée</li>";
        $success_count++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color: orange;'>⚠ Table 'commande_items' existe déjà</li>";
        } else {
            echo "<li style='color: red;'>✗ Erreur table 'commande_items': " . htmlspecialchars($e->getMessage()) . "</li>";
            $error_count++;
        }
    }
    
    // Table commande_historique
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS commande_historique (
                id INT AUTO_INCREMENT PRIMARY KEY,
                commande_id INT NOT NULL,
                ancien_statut VARCHAR(50),
                nouveau_statut VARCHAR(50) NOT NULL,
                commentaire TEXT,
                date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_commande_historique_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<li style='color: green;'>✓ Table 'commande_historique' créée</li>";
        $success_count++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color: orange;'>⚠ Table 'commande_historique' existe déjà</li>";
        } else {
            echo "<li style='color: red;'>✗ Erreur table 'commande_historique': " . htmlspecialchars($e->getMessage()) . "</li>";
            $error_count++;
        }
    }
    
    // Table login_attempts
    try {
        $db->exec("
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) NOT NULL COMMENT 'Email utilisé pour la tentative',
                ip_address VARCHAR(45) NOT NULL COMMENT 'Adresse IP',
                success TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si succès, 0 si échec',
                user_agent TEXT NULL COMMENT 'User agent',
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<li style='color: green;'>✓ Table 'login_attempts' créée</li>";
        $success_count++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color: orange;'>⚠ Table 'login_attempts' existe déjà</li>";
        } else {
            echo "<li style='color: red;'>✗ Erreur table 'login_attempts': " . htmlspecialchars($e->getMessage()) . "</li>";
            $error_count++;
        }
    }
    
    echo "</ul>";
    
    // 3. Créer les index nécessaires
    echo "<h2>Création des index...</h2>";
    echo "<ul>";
    
    $indexes = [
        'commandes' => ['client_id', 'numero_commande', 'statut', 'date_commande'],
        'commande_items' => ['commande_id'],
        'commande_historique' => ['commande_id'],
        'login_attempts' => ['email', 'ip_address', 'attempted_at']
    ];
    
    foreach ($indexes as $table => $index_columns) {
        foreach ($index_columns as $column) {
            $index_name = "idx_{$table}_{$column}";
            try {
                // Vérifier si l'index existe
                $stmt = $db->prepare("
                    SELECT COUNT(*) 
                    FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND INDEX_NAME = ?
                ");
                $stmt->execute([$table, $index_name]);
                $exists = $stmt->fetchColumn();
                
                if (!$exists) {
                    $db->exec("CREATE INDEX $index_name ON $table($column)");
                    echo "<li style='color: green;'>✓ Index '$index_name' créé</li>";
                    $success_count++;
                } else {
                    echo "<li style='color: orange;'>⚠ Index '$index_name' existe déjà</li>";
                }
            } catch (PDOException $e) {
                echo "<li style='color: red;'>✗ Erreur index '$index_name': " . htmlspecialchars($e->getMessage()) . "</li>";
                $error_count++;
            }
        }
    }
    
    echo "</ul>";
    
    echo "<h2>Résumé de l'installation</h2>";
    echo "<p><strong>Opérations réussies:</strong> $success_count</p>";
    
    if ($error_count > 0) {
        echo "<p style='color: red;'><strong>Erreurs:</strong> $error_count</p>";
    } else {
        echo "<p style='color: green;'><strong>Installation terminée avec succès!</strong></p>";
    }
    
    // Test des tables
    echo "<h2>Vérification des tables</h2>";
    $tables_to_check = ['clients', 'commandes', 'commande_items', 'commande_historique', 'login_attempts'];
    
    foreach ($tables_to_check as $table) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `$table`");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            $stmt->closeCursor();
            echo "<p style='color: green;'>✓ Table '$table' opérationnelle ($count enregistrements)</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Problème avec la table '$table': " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<h2>Étapes suivantes</h2>";
    echo "<ol>";
    echo "<li>Le système de commandes est maintenant opérationnel</li>";
    echo "<li>Testez la création d'un compte client</li>";
    echo "<li>Ajoutez des produits au panier et passez une commande de test</li>";
    echo "<li>Vérifiez le suivi des commandes dans l'espace client</li>";
    echo "<li><strong>Supprimez ce fichier d'installation en production!</strong></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial, sans-serif; margin: 20px; padding: 20px; border: 1px solid red;'>";
    echo "<h2>Erreur d'installation</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";
?>

<style>
body {
    background-color: #f5f5f5;
    font-family: Arial, sans-serif;
}
h1, h2 {
    color: #333;
}
ul {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
li {
    margin: 5px 0;
}
</style>