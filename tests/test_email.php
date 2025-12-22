<?php
require_once __DIR__ . '/../includes/email-manager.php';

// Test minimal pour EmailManager::envoyerConfirmationCommande
$em = new EmailManager();

$panier = [
    [
        'prix' => 12.5,
        'quantite' => 2,
        'details' => ['code' => 'TEST001', 'designation' => 'Produit test']
    ]
];

// Petite image factice encodée en base64
$data = 'PNGFAKE';
$dataUrl = 'data:image/png;base64,' . base64_encode($data);

$fichiers = [
    [
        'dataUrl' => $dataUrl,
        'name' => 'essai.png',
        'nom' => 'essai.png',
        'type' => 'image/png'
    ]
];

$clientInfo = ['prenom' => 'Test', 'nom' => 'Utilisateur', 'email' => 'client@example.com'];

echo "Lancement du test envoyerConfirmationCommande...\n";
$res = $em->envoyerConfirmationCommande($panier, $fichiers, 'TEST-'.time(), $clientInfo);
var_dump($res);

echo "Fin du test.\n";
