<?php
// Debug simple pour get-clients
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Debug get-clients.php</h3>";

try {
    echo "1. Tentative de chargement de database.php...<br>";
    require_once '../../includes/database.php';
    echo "2. ✓ Database.php chargé<br>";
    
    echo "3. Tentative de connexion...<br>";
    $db = Database::getInstance()->getConnection();
    echo "4. ✓ Connexion établie<br>";
    
    echo "5. Requête clients...<br>";
    $stmt = $db->query("
        SELECT id, nom, prenom, email, entreprise, adresse, ville, code_postal, 
               adresse_livraison, telephone
        FROM clients 
        ORDER BY nom, prenom
    ");
    echo "6. ✓ Requête exécutée<br>";
    
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "7. ✓ Résultats récupérés: " . count($clients) . " clients<br>";
    
    echo "8. JSON encodé:<br>";
    $json = json_encode($clients);
    echo "<pre>" . htmlspecialchars($json) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERREUR à l'étape: " . $e->getMessage() . "</p>";
    echo "<p>Fichier: " . $e->getFile() . "</p>";
    echo "<p>Ligne: " . $e->getLine() . "</p>";
    echo "<p>Trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>