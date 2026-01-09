<?php
require_once __DIR__ . '/functions.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE produits");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Colonnes table produits:\n";
foreach ($cols as $c) {
    echo "- $c\n";
}

?>
