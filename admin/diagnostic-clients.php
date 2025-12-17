<?php
require_once 'functions.php';
checkAuth();
require_once '../includes/database.php';

echo "<h1>Diagnostic - Clients de la base de données</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la structure de la table
    echo "<h2>Structure de la table clients :</h2>";
    $stmt = $db->query("DESCRIBE clients");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($structure);
    echo "</pre>";
    
    // Compter le nombre total de clients
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Nombre total de clients : " . $count['total'] . "</h2>";
    
    // Compter les clients actifs
    $stmt = $db->query("SELECT COUNT(*) as actifs FROM clients WHERE actif = 1");
    $actifs = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Clients actifs : " . $actifs['actifs'] . "</h2>";
    
    // Afficher les premiers clients
    echo "<h2>Premiers clients de la table :</h2>";
    $stmt = $db->query("SELECT * FROM clients LIMIT 10");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($clients);
    echo "</pre>";
    
    // Test de l'endpoint AJAX
    echo "<h2>Test de l'endpoint AJAX :</h2>";
    echo "<p><a href='ajax/get-clients.php' target='_blank'>Tester get-clients.php</a></p>";
    echo "<p><a href='ajax/get-client-details.php?id=1' target='_blank'>Tester get-client-details.php?id=1</a></p>";
    
} catch (Exception $e) {
    echo "<h2>Erreur : " . $e->getMessage() . "</h2>";
}
?>