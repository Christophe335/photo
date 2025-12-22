<?php
session_start();
// Récupérer le panier JSON envoyé par le JS
$input = file_get_contents('php://input');
$panier = json_decode($input, true);

// Accepter les tableaux vides et les tableaux avec des éléments
if (is_array($panier)) {
    // Fusionner intelligemment avec le panier session existant afin de ne pas écraser
    // des informations riches (ex: photos avec dataUrl) déjà présentes en session.
    $sessionPanier = $_SESSION['panier'] ?? [];

    // Indexer le panier session par id pour accès rapide
    $sessionMap = [];
    foreach ($sessionPanier as $item) {
        if (isset($item['id'])) $sessionMap[$item['id']] = $item;
    }

    $merged = [];
    // Parcourir les éléments reçus et fusionner
    foreach ($panier as $incoming) {
        $id = $incoming['id'] ?? null;
        if ($id && isset($sessionMap[$id])) {
            $existing = $sessionMap[$id];

            // Fusionner photos : préférer les objets complets contenant dataUrl/src
            $existingPhotos = $existing['photos'] ?? [];
            $incomingPhotos = $incoming['photos'] ?? [];

            $hasExistingDataUrl = false;
            foreach ($existingPhotos as $p) {
                if (is_array($p) && (!empty($p['dataUrl']) || !empty($p['url']) || !empty($p['src']))) {
                    $hasExistingDataUrl = true;
                    break;
                }
            }

            if ($hasExistingDataUrl) {
                $incoming['photos'] = $existingPhotos;
            } else {
                // incoming may contain richer data; if incoming photos are objects with dataUrl, keep them
                $hasIncomingData = false;
                foreach ($incomingPhotos as $p) {
                    if (is_array($p) && (!empty($p['dataUrl']) || !empty($p['url']) || !empty($p['src']))) {
                        $hasIncomingData = true;
                        break;
                    }
                }
                if (!$hasIncomingData && !empty($existingPhotos)) {
                    // fallback to existing filenames if incoming lacks data
                    $incoming['photos'] = $existingPhotos;
                }
            }

            // Conserver d'autres champs session si manquants
            if (empty($incoming['details']) && !empty($existing['details'])) {
                $incoming['details'] = $existing['details'];
            }

            $merged[] = $incoming;
            // Marquer cet id comme consommé
            unset($sessionMap[$id]);
        } else {
            $merged[] = $incoming;
        }
    }

    // Ajouter les éléments restants de la session qui n'ont pas été fournis par le client
    foreach ($sessionMap as $left) {
        $merged[] = $left;
    }

    $_SESSION['panier'] = $merged;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'panier_recu' => $panier,
        'panier_count' => count($merged),
        'session_apres' => $_SESSION['panier']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Format panier invalide',
        'input' => $input,
        'decoded' => $panier
    ]);
}
