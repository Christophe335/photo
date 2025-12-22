<?php
require_once 'includes/database.php';

echo "<h2>Test de la correction de l'ordre d'affichage</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Test de la nouvelle requête avec ordre
    echo "<h3>Requête avec la correction (ordre d'affichage respecté) :</h3>";
    $stmt = $db->prepare("SELECT reference, designation, ordre FROM produits WHERE famille = ? ORDER BY (CASE WHEN ordre IS NULL OR ordre = 0 THEN 1 ELSE 0 END) ASC, (CASE WHEN ordre IS NULL OR ordre = 0 THEN NULL ELSE ordre END) ASC, reference ASC LIMIT 10");
    $stmt->execute(['Peleman Box A4 - 45mm']);
    $produits = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Référence</th><th>Désignation</th><th>Ordre</th></tr>";
    foreach ($produits as $produit) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($produit['reference']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($produit['designation'], 0, 50)) . "...</td>";
        echo "<td>" . ($produit['ordre'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test de l'ancienne requête (pour comparaison)
    echo "<h3>Ancienne requête (ORDER BY reference seulement) :</h3>";
    $stmt2 = $db->prepare("SELECT reference, designation, ordre FROM produits WHERE famille = ? ORDER BY reference LIMIT 10");
    $stmt2->execute(['Peleman Box A4 - 45mm']);
    $produits2 = $stmt2->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Référence</th><th>Désignation</th><th>Ordre</th></tr>";
    foreach ($produits2 as $produit) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($produit['reference']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($produit['designation'], 0, 50)) . "...</td>";
        echo "<td>" . ($produit['ordre'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<p><a href='pages/boite-a4.php'>Tester sur la page boite-a4.php</a></p>";
?>