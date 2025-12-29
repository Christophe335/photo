<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/database.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = ['success' => false];
if ($ref === '' && $id <= 0) {
    echo json_encode($result);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    if ($id > 0) {
        $stmt = $db->prepare('SELECT * FROM produits WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
    } else {
        $stmt = $db->prepare("SELECT * FROM produits WHERE TRIM(reference) = ? LIMIT 1");
        $stmt->execute([$ref]);
    }
    $row = $stmt->fetch();
    if ($row) {
        // Retourner uniquement les champs utiles
        $result = [
            'success' => true,
            'id' => $row['id'],
            'reference' => $row['reference'],
            'designation' => $row['designation'],
            'format' => $row['format'] ?? '',
            'couleur' => $row['couleur'] ?? ($row['couleur_interieur'] ?? ''),
            'conditionnement' => $row['conditionnement'] ?? '',
            'prixVente' => isset($row['prixVente']) ? floatval($row['prixVente']) : (isset($row['prix']) ? floatval($row['prix']) : 0)
        ];
    }
} catch (Exception $e) {
    // ignore
}

echo json_encode($result);
