<?php
// Script d'administration : supprime la colonne `personnalisation_ref` de la table personnalisation_liaisons
// Usage : ouvrir ce fichier via le navigateur (admin) ou en CLI PHP. Faites une sauvegarde de la base avant.
require_once __DIR__ . '/../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Vérifier si la colonne existe
    $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'personnalisation_liaisons' AND COLUMN_NAME = 'personnalisation_ref'");
    $stmt->execute();
    $exists = intval($stmt->fetchColumn() ?? 0);

    if ($exists === 0) {
        echo "Colonne personnalisation_ref introuvable — rien à faire.";
        exit(0);
    }

    // Exécuter l'ALTER TABLE pour supprimer la colonne.
    $db->exec("ALTER TABLE personnalisation_liaisons DROP COLUMN personnalisation_ref");

    echo "Colonne personnalisation_ref supprimée avec succès.";
} catch (Exception $e) {
    echo "Erreur lors de la suppression : " . htmlspecialchars($e->getMessage());
    exit(1);
}

?>
