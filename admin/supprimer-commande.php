<?php
require_once 'functions.php';

// Vérifier l'authentification admin
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

require_once '../includes/database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $commande_id = $input['commande_id'] ?? 0;
    
    if (!$commande_id) {
        throw new Exception('ID de commande manquant');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Vérifier que la commande existe
    $stmt = $db->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        throw new Exception('Commande non trouvée');
    }
    
    // Commencer une transaction pour assurer l'intégrité des données
    $db->beginTransaction();
    
    try {
        // Supprimer d'abord l'historique de la commande
        $stmt = $db->prepare("DELETE FROM commande_historique WHERE commande_id = ?");
        $stmt->execute([$commande_id]);
        
        // Supprimer les items de la commande
        $stmt = $db->prepare("DELETE FROM commande_items WHERE commande_id = ?");
        $stmt->execute([$commande_id]);
        
        // Supprimer la commande elle-même
        $stmt = $db->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$commande_id]);
        
        // Valider la transaction
        $db->commit();
        
        // Log de l'action
        error_log("Suppression commande #{$commande['numero_commande']} (ID: $commande_id) par admin");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Commande supprimée avec succès'
        ]);
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Erreur suppression commande: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>