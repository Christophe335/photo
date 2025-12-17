<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Test - Société du client</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer le client ID 1 (Christophe)
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = 1");
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Données complètes du client ID 1 :</h2>";
    echo "<pre>";
    print_r($client);
    echo "</pre>";
    
    echo "<h2>Colonne société spécifiquement :</h2>";
    if (isset($client['societe'])) {
        echo "<p>Valeur de societe : '" . $client['societe'] . "'</p>";
        echo "<p>Type : " . gettype($client['societe']) . "</p>";
        echo "<p>Vide ? " . (empty($client['societe']) ? 'OUI' : 'NON') . "</p>";
        echo "<p>Null ? " . (is_null($client['societe']) ? 'OUI' : 'NON') . "</p>";
    } else {
        echo "<p style='color: red;'>La colonne 'societe' n'existe pas dans le résultat !</p>";
    }
    
    // Test d'ajout d'une société
    echo "<h2>Test d'ajout d'une société :</h2>";
    $updateStmt = $db->prepare("UPDATE clients SET societe = 'Mouillet Photos SARL' WHERE id = 1");
    if ($updateStmt->execute()) {
        echo "<p style='color: green;'>✓ Société ajoutée</p>";
        
        // Récupérer à nouveau
        $stmt->execute();
        $clientUpdated = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Nouvelle valeur : '" . $clientUpdated['societe'] . "'</p>";
        
        echo "<p><a href='client-details.php?id=1' style='background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>→ Voir la page client-details</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Erreur lors de l'ajout</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>