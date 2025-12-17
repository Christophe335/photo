<?php
// Test ultra simple
echo "Test de base OK<br>";

try {
    echo "Test 1: Inclusion du fichier database<br>";
    $path = '../../includes/database.php';
    echo "Chemin: $path<br>";
    echo "Fichier existe: " . (file_exists($path) ? 'OUI' : 'NON') . "<br>";
    
    if (file_exists($path)) {
        require_once $path;
        echo "Test 2: Fichier inclus avec succès<br>";
        
        echo "Test 3: Création instance Database<br>";
        $instance = Database::getInstance();
        echo "Test 4: Instance créée<br>";
        
        $db = $instance->getConnection();
        echo "Test 5: Connexion obtenue<br>";
        
        // Test simple
        $stmt = $db->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "Test 6: Requête simple réussie - résultat: " . $result['test'] . "<br>";
        
        // Test table clients
        $stmt = $db->query("SHOW TABLES LIKE 'clients'");
        echo "Test 7: Table clients " . ($stmt->rowCount() > 0 ? 'existe' : 'n\'existe pas') . "<br>";
        
    } else {
        echo "ERREUR: Fichier database.php introuvable!<br>";
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
}
?>