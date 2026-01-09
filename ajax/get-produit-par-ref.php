<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/database.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
    $result = ['reference' => $ref, 'designation' => '', 'produit_exists' => false, 'liaison_exists' => false, 'liaison_id' => null];
if ($ref !== '') {
    try {
        $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT designation FROM produits WHERE reference = ? LIMIT 1');
            $stmt->execute([$ref]);
            $r = $stmt->fetch();
            if ($r) {
                $result['designation'] = $r['designation'];
                $result['produit_exists'] = true;
            }

            // Vérifier si une liaison existe déjà pour cette référence
            $stmt2 = $db->prepare('SELECT id FROM personnalisation_liaisons WHERE produit_ref = ? LIMIT 1');
            $stmt2->execute([$ref]);
            $lr = $stmt2->fetch();
            if ($lr) {
                $result['liaison_exists'] = true;
                $result['liaison_id'] = $lr['id'];
            }
    } catch (Exception $e) {
        // ignore
    }
}

echo json_encode($result);
