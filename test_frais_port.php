<?php
// Test des frais de port
echo "<h2>Test de calcul des frais de port</h2>";

function calculer_frais_port($total_ht) {
    return ($total_ht > 200) ? 0 : 13.95;
}

$tests = [
    ['total' => 50, 'attendu' => 13.95, 'description' => 'Commande 50€ HT'],
    ['total' => 150, 'attendu' => 13.95, 'description' => 'Commande 150€ HT'], 
    ['total' => 200, 'attendu' => 13.95, 'description' => 'Commande 200€ HT (limite)'],
    ['total' => 200.01, 'attendu' => 0, 'description' => 'Commande 200.01€ HT (gratuit)'],
    ['total' => 250, 'attendu' => 0, 'description' => 'Commande 250€ HT'],
    ['total' => 500, 'attendu' => 0, 'description' => 'Commande 500€ HT'],
];

echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th style='padding: 10px;'>Description</th><th style='padding: 10px;'>Total HT</th><th style='padding: 10px;'>Frais attendus</th><th style='padding: 10px;'>Frais calculés</th><th style='padding: 10px;'>Résultat</th></tr>";

foreach ($tests as $test) {
    $calcule = calculer_frais_port($test['total']);
    $ok = ($calcule == $test['attendu']);
    
    echo "<tr>";
    echo "<td style='padding: 10px;'>" . $test['description'] . "</td>";
    echo "<td style='padding: 10px;'>" . number_format($test['total'], 2, ',', ' ') . " €</td>";
    echo "<td style='padding: 10px;'>" . ($test['attendu'] > 0 ? number_format($test['attendu'], 2, ',', ' ') . " €" : "Gratuit") . "</td>";
    echo "<td style='padding: 10px;'>" . ($calcule > 0 ? number_format($calcule, 2, ',', ' ') . " €" : "Gratuit") . "</td>";
    echo "<td style='padding: 10px; color: " . ($ok ? "green" : "red") . ";'>" . ($ok ? "✓ OK" : "✗ ERREUR") . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Règles de calcul :</h3>";
echo "<ul>";
echo "<li>Frais de port : 13.95€</li>";
echo "<li>Gratuit si commande > 200€ HT</li>";
echo "<li>Payant si commande ≤ 200€ HT</li>";
echo "</ul>";
?>