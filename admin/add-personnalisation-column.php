<?php
// Inclure la config DB sans exiger l'authentification admin (exécution CLI)
require_once __DIR__ . '/../includes/database.php';

$db = Database::getInstance()->getConnection();
try {
    // Utiliser une instruction ALTER TABLE standard (MySQL plus ancien peut ne pas supporter IF NOT EXISTS)
    $sql = "ALTER TABLE produits ADD COLUMN personnalisation TINYINT(1) NOT NULL DEFAULT 0";
    $db->exec($sql);
    echo "Colonne 'personnalisation' ajoutée ou déjà présente.\n";
} catch (Exception $e) {
    echo "Erreur lors de l'ajout de la colonne personnalisation : " . $e->getMessage();
}

?>
