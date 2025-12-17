<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Structure de la table devis_items</h2>";
    $stmt = $db->query("DESCRIBE devis_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Données actuelles dans devis_items</h2>";
    $stmt = $db->query("SELECT * FROM devis_items LIMIT 5");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($items)) {
        echo "<table border='1'>";
        echo "<tr>";
        foreach (array_keys($items[0]) as $key) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";
        
        foreach ($items as $item) {
            echo "<tr>";
            foreach ($item as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune donnée dans la table devis_items</p>";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>