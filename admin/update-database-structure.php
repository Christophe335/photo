<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier si la colonne existe déjà
    $stmt = $db->prepare("SHOW COLUMNS FROM commandes LIKE 'date_modification'");
    $stmt->execute();
    $column_exists = $stmt->fetch() !== false;
    
    if (!$column_exists) {
        echo "Ajout de la colonne date_modification...\n";
        $db->exec("ALTER TABLE commandes ADD COLUMN date_modification TIMESTAMP NULL DEFAULT NULL");
        echo "Colonne ajoutée avec succès!\n";
    } else {
        echo "La colonne date_modification existe déjà.\n";
    }
    
    // Vérifier si la table commande_historique existe
    $stmt = $db->prepare("SHOW TABLES LIKE 'commande_historique'");
    $stmt->execute();
    $table_exists = $stmt->fetch() !== false;
    
    if (!$table_exists) {
        echo "Création de la table commande_historique...\n";
        $sql = "
        CREATE TABLE commande_historique (
            id INT AUTO_INCREMENT PRIMARY KEY,
            commande_id INT NOT NULL,
            ancien_statut VARCHAR(50),
            nouveau_statut VARCHAR(50) NOT NULL,
            date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            commentaire TEXT,
            FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
        )";
        $db->exec($sql);
        echo "Table commande_historique créée avec succès!\n";
    } else {
        echo "La table commande_historique existe déjà.\n";
    }
    
    echo "\nMise à jour terminée avec succès!\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>