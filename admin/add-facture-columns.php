<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Colonnes à ajouter
    $columns_to_add = [
        'numero_facture' => "ALTER TABLE commandes ADD COLUMN numero_facture VARCHAR(50) NULL DEFAULT NULL",
        'date_facture' => "ALTER TABLE commandes ADD COLUMN date_facture TIMESTAMP NULL DEFAULT NULL"
    ];
    
    foreach ($columns_to_add as $column => $sql) {
        $stmt = $db->prepare("SHOW COLUMNS FROM commandes LIKE '$column'");
        $stmt->execute();
        if ($stmt->fetch() === false) {
            echo "Ajout de la colonne $column...\n";
            $db->exec($sql);
            echo "Colonne $column ajoutée avec succès!\n";
        } else {
            echo "La colonne $column existe déjà.\n";
        }
    }
    
    echo "\nMise à jour des colonnes de facturation terminée !\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>