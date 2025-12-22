<?php
session_start();
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$id = $data['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}

$panier = $_SESSION['panier'] ?? [];
$before_count = count($panier);

$panier = array_values(array_filter($panier, function($item) use ($id) {
    return (($item['id'] ?? null) !== $id);
}));

$_SESSION['panier'] = $panier;

echo json_encode([
    'success' => true,
    'removed_id' => $id,
    'before_count' => $before_count,
    'after_count' => count($panier),
    'session_apres' => $_SESSION['panier']
]);

?>
