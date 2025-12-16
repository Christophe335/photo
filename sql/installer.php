<?php
/**
 * Script d'installation de la base de données
 * 
 * Ce script exécute les requêtes SQL pour créer les tables nécessaires
 * au système de gestion des clients.
 */

require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Configurer PDO pour les requêtes bufferisées
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    
    echo "<h1>Installation de la base de données</h1>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px;'>";
    
    // Lire le fichier SQL d'installation
    $sql_file = __DIR__ . '/install_database.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("Fichier SQL d'installation non trouvé: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Séparer les requêtes (en utilisant les délimiteurs)
    $sql_statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $success_count = 0;
    $error_count = 0;
    
    echo "<h2>Exécution des requêtes SQL...</h2>";
    echo "<ul>";
    
    foreach ($sql_statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0 || strpos(trim($statement), 'DELIMITER') === 0) {
            continue; // Ignorer les commentaires, lignes vides et délimiteurs
        }
        
        // Nettoyer et préparer la requête
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            $success_count++;
            
            // Extraire le nom de la table ou action
            $action = '';
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                $action = "Table '{$matches[1]}' créée";
            } elseif (preg_match('/CREATE INDEX.*?`?(\w+)`?/i', $statement, $matches)) {
                $action = "Index '{$matches[1]}' créé";
            } elseif (preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                $action = "Table '{$matches[1]}' modifiée";
            } elseif (preg_match('/CREATE PROCEDURE.*?(\w+)/i', $statement, $matches)) {
                $action = "Procédure '{$matches[1]}' créée";
            } else {
                $action = "Requête exécutée avec succès";
            }
            
            echo "<li style='color: green;'>✓ $action</li>";
            
        } catch (PDOException $e) {
            $error_msg = $e->getMessage();
            
            // Ignorer certaines erreurs non critiques
            if (strpos($error_msg, 'already exists') !== false || 
                strpos($error_msg, 'Duplicate key name') !== false ||
                strpos($error_msg, 'Duplicate column name') !== false) {
                echo "<li style='color: orange;'>⚠ " . htmlspecialchars($error_msg) . " (ignoré)</li>";
            } else {
                $error_count++;
                echo "<li style='color: red;'>✗ Erreur: " . htmlspecialchars($error_msg) . "</li>";
            }
        }
    }
    
    echo "</ul>";
    
    echo "<h2>Résumé de l'installation</h2>";
    echo "<p><strong>Requêtes réussies:</strong> $success_count</p>";
    
    if ($error_count > 0) {
        echo "<p style='color: red;'><strong>Erreurs:</strong> $error_count</p>";
    } else {
        echo "<p style='color: green;'><strong>Installation terminée avec succès!</strong></p>";
    }
    
    // Vérifier que les tables principales existent
    echo "<h2>Vérification des tables</h2>";
    $tables_to_check = ['clients', 'client_sessions', 'login_attempts'];
    
    foreach ($tables_to_check as $table) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `$table`");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            $stmt->closeCursor(); // Libérer le résultat pour éviter les erreurs de buffer
            echo "<p style='color: green;'>✓ Table '$table' existe (contient $count enregistrements)</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Problème avec la table '$table': " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<h2>Étapes suivantes</h2>";
    echo "<ol>";
    echo "<li>Testez la création d'un compte sur la page d'inscription</li>";
    echo "<li>Vérifiez que la connexion fonctionne correctement</li>";
    echo "<li>Configurez les emails si vous voulez l'activation par email</li>";
    echo "<li><strong>Supprimez ce fichier d'installation en production!</strong></li>";
    echo "</ol>";
    
    echo "<p style='background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 20px;'>";
    echo "<strong>⚠ ATTENTION:</strong> Supprimez ce fichier d'installation une fois l'installation terminée pour des raisons de sécurité.";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial, sans-serif; margin: 20px; padding: 20px; border: 1px solid red;'>";
    echo "<h2>Erreur d'installation</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Vérifiez:</strong></p>";
    echo "<ul>";
    echo "<li>La configuration de la base de données dans includes/database.php</li>";
    echo "<li>Que le serveur MySQL est démarré</li>";
    echo "<li>Que la base de données existe</li>";
    echo "<li>Que l'utilisateur MySQL a les permissions nécessaires</li>";
    echo "</ul>";
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