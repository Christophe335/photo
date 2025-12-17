<?php
require_once '../../includes/database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Test 1: Récupérer tous les clients
    echo "<h3>Test 1: Tous les clients</h3>";
    $stmt = $db->query("SELECT id, nom, prenom, email FROM clients ORDER BY nom, prenom");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Nombre de clients trouvés: " . count($clients) . "</p>";
    foreach ($clients as $client) {
        echo "<p>Client {$client['id']}: {$client['nom']} {$client['prenom']} ({$client['email']})</p>";
    }
    
    // Test 2: Récupérer un client spécifique
    if (!empty($clients)) {
        $premier_client_id = $clients[0]['id'];
        echo "<h3>Test 2: Client ID {$premier_client_id}</h3>";
        
        $stmt = $db->prepare("
            SELECT id, nom, prenom, email, telephone, entreprise, 
                   adresse, ville, code_postal, pays, adresse_livraison
            FROM clients 
            WHERE id = ?
        ");
        
        $stmt->execute([$premier_client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($client) {
            echo "<pre>" . print_r($client, true) . "</pre>";
            echo "<h4>Format JSON:</h4>";
            echo "<pre>" . json_encode($client, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color: red;'>Client non trouvé</p>";
        }
    } else {
        echo "<p style='color: orange;'>Aucun client trouvé pour tester</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>