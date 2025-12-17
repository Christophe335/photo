<?php
header('Content-Type: application/json');

// Debug : ajouter les logs d'erreur
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID client manquant ou invalide']);
    exit;
}

$client_id = $_GET['id'];

try {
    require_once '../../includes/database.php';
    
    $db = Database::getInstance()->getConnection();
    
    // Récupérer le client
    $stmt = $db->prepare("
        SELECT id, nom, prenom, email, telephone, societe,
               adresse, ville, code_postal, pays
        FROM clients 
        WHERE id = ? AND actif = 1
    ");
    
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug : loguer la requête
    error_log("Recherche client ID: $client_id, trouvé: " . ($client ? 'oui' : 'non'));
    
    if ($client) {
        echo json_encode($client);
    } else {
        echo json_encode(['error' => 'Client non trouvé en base', 'id_recherche' => $client_id]);
    }
    
} catch (Exception $e) {
    // Loguer l'erreur complète
    error_log("Erreur get-client-details.php: " . $e->getMessage());
    
    // Retourner l'erreur pour debug
    echo json_encode(['error' => $e->getMessage(), 'debug' => true]);
}
?>