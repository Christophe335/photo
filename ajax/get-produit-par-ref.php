<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/database.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$result = ['reference' => $ref, 'designation' => ''];
if ($ref !== '') {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT designation FROM produits WHERE reference = ? LIMIT 1');
        $stmt->execute([$ref]);
        $r = $stmt->fetch();
        if ($r) $result['designation'] = $r['designation'];
    } catch (Exception $e) {
        // ignore
    }
}

echo json_encode($result);
