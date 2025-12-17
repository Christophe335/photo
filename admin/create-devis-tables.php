<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Création des tables de devis</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Lire et exécuter le fichier SQL
    $sql = file_get_contents('../sql/create_table_devis.sql');
    
    // Diviser les requêtes
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            echo "<p>Exécution : " . substr($query, 0, 50) . "...</p>";
            $db->exec($query);
        }
    }
    
    echo "<p style='color: green;'>✓ Tables de devis créées avec succès</p>";
    
    // Vérifier que les tables existent
    $stmt = $db->query("SHOW TABLES LIKE 'devis%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Tables créées :</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>