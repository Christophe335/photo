<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Diagnostic - Problème colonne numero vs numero_devis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la structure actuelle de la table devis
    echo "<h2>Structure actuelle de la table devis :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasNumero = false;
    $hasNumeroDevis = false;
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    foreach ($structure as $column) {
        echo "<tr>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'numero') {
            $hasNumero = true;
        }
        if ($column['Field'] === 'numero_devis') {
            $hasNumeroDevis = true;
        }
    }
    echo "</table>";
    
    echo "<h3>Analyse :</h3>";
    echo "<p>Colonne 'numero' : " . ($hasNumero ? "✓ Existe" : "❌ Manquante") . "</p>";
    echo "<p>Colonne 'numero_devis' : " . ($hasNumeroDevis ? "⚠ Existe (problématique)" : "✓ N'existe pas") . "</p>";
    
    if ($hasNumeroDevis && !$hasNumero) {
        echo "<h3 style='color: orange;'>🔧 Correction nécessaire :</h3>";
        echo "<p>Il faut renommer 'numero_devis' en 'numero' pour correspondre au script PHP</p>";
        
        echo "<p>Renommage de la colonne...</p>";
        $db->exec("ALTER TABLE devis CHANGE numero_devis numero VARCHAR(50) NOT NULL UNIQUE");
        echo "<p style='color: green;'>✓ Colonne renommée de 'numero_devis' vers 'numero'</p>";
        
    } elseif ($hasNumeroDevis && $hasNumero) {
        echo "<h3 style='color: red;'>⚠ Conflit détecté :</h3>";
        echo "<p>Les deux colonnes existent. Suppression de 'numero_devis'...</p>";
        
        $db->exec("ALTER TABLE devis DROP COLUMN numero_devis");
        echo "<p style='color: green;'>✓ Colonne 'numero_devis' supprimée</p>";
        
    } elseif (!$hasNumero && !$hasNumeroDevis) {
        echo "<h3 style='color: red;'>❌ Aucune colonne numero :</h3>";
        echo "<p>Ajout de la colonne 'numero'...</p>";
        
        $db->exec("ALTER TABLE devis ADD COLUMN numero VARCHAR(50) NOT NULL UNIQUE AFTER id");
        echo "<p style='color: green;'>✓ Colonne 'numero' ajoutée</p>";
        
    } else {
        echo "<p style='color: green;'>✓ Structure correcte</p>";
    }
    
    // Vérifier la structure finale
    echo "<h2>Structure finale :</h2>";
    $stmt = $db->query("DESCRIBE devis");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($structure as $column) {
        $style = ($column['Field'] === 'numero') ? ' style="color: green; font-weight: bold;"' : '';
        echo "<li$style>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>✅ Problème résolu - Test possible</h2>";
    echo "<p><a href='creer-devis.php' style='background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>→ Tester la création de devis</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>