<?php
session_start();
// Récupérer le panier JSON envoyé par le JS
$input = file_get_contents('php://input');
$panier = json_decode($input, true);
if (is_array($panier)) {
    $_SESSION['panier'] = $panier;
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'panier_recu' => $panier,
        'session_apres' => $_SESSION
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Format panier invalide',
        'input' => $input
    ]);
}
