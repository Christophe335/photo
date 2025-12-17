<?php
require_once 'functions.php';
checkAuth();

echo "<h1>Test process-devis.php</h1>";

// Simuler une requête POST simple
$_POST = [
    'type_client' => 'nouveau',
    'nouveau_nom' => 'Test',
    'nouveau_prenom' => 'User',
    'nouveau_email' => 'test@example.com',
    'nouveau_telephone' => '0123456789',
    'nouveau_societe' => 'Test Company',
    'nouveau_adresse' => '123 Test Street',
    'nouveau_ville' => 'Test City',
    'nouveau_code_postal' => '12345',
    'designation' => ['Article de test'],
    'quantite' => [1],
    'prix_unitaire' => [10.00],
    'remise_valeur' => [0],
    'remise_type' => ['percent'],
    'frais_port' => 0,
    'notes' => 'Test devis'
];

echo "<h2>Données POST simulées :</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Test de process-devis.php :</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f5f5f5;'>";

// Capturer la sortie de process-devis.php
ob_start();
include 'process-devis.php';
$output = ob_get_clean();

echo "Sortie brute de process-devis.php :<br>";
echo "<code>" . htmlspecialchars($output) . "</code>";
echo "</div>";

echo "<h2>Analyse :</h2>";
if (json_decode($output)) {
    echo "<p style='color: green;'>✓ La sortie est du JSON valide</p>";
} else {
    echo "<p style='color: red;'>❌ La sortie n'est PAS du JSON valide</p>";
    echo "<p>Vérifiez s'il y a des erreurs PHP, des warnings ou du contenu HTML avant le JSON.</p>";
}
?>