<?php
header('Content-Type: application/json');

// Debug : ajouter les logs d'erreur
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../../includes/database.php';
    
    $db = Database::getInstance()->getConnection();
    
    // Récupérer TOUS les clients actifs
    $stmt = $db->query("
        SELECT id, nom, prenom, email, telephone, societe
        FROM clients 
        WHERE actif = 1
        ORDER BY nom, prenom 
        LIMIT 100
    ");
    
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug : loguer le nombre de clients trouvés
    error_log("Nombre de clients trouvés: " . count($clients));
    
    echo json_encode($clients);
    
} catch (Exception $e) {
    // Loguer l'erreur complète
    error_log("Erreur get-clients.php: " . $e->getMessage() . " - " . $e->getTraceAsString());
    
    // Retourner l'erreur pour debug
    echo json_encode(['error' => $e->getMessage(), 'debug' => true]);
}
?>