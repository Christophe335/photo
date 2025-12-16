<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Ajouter la colonne pour le mot de passe en clair
    $stmt = $db->prepare("SHOW COLUMNS FROM clients LIKE 'mot_de_passe_clair'");
    $stmt->execute();
    
    if ($stmt->fetch() === false) {
        echo "Ajout de la colonne mot_de_passe_clair...\n";
        $db->exec("ALTER TABLE clients ADD COLUMN mot_de_passe_clair VARCHAR(255) NULL DEFAULT NULL");
        echo "Colonne ajoutée avec succès!\n";
    } else {
        echo "La colonne mot_de_passe_clair existe déjà.\n";
    }
    
    echo "\nMise à jour terminée !\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>