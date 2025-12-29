<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT id, reference, designation, prixVente, famille, format, matiere, conditionnement, couleur_interieur AS couleur FROM produits WHERE 1=1 ORDER BY famille, designation");

    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($produits);
} catch (Exception $e) {
    error_log('Erreur get-produits (ajax): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des produits']);
}

?>
