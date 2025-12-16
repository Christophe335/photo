<?php
session_start();
require_once '../includes/database.php';

// Test direct du formulaire sans AJAX
if (!isset($_SESSION['client_id'])) {
    echo "Vous devez être connecté.";
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les informations du client
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        echo "Client non trouvé.";
        exit;
    }
    
    echo "<h2>Test formulaire de modification</h2>";
    echo "<p>Client: " . htmlspecialchars($client['prenom'] . ' ' . $client['nom']) . "</p>";
    
    // Inclure le contenu du formulaire
    include 'ajax/get-modifier-form.php';
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>