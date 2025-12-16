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
    $nouveau_statut = $input['statut'] ?? '';
    
    if (!$commande_id || !$nouveau_statut) {
        throw new Exception('Paramètres manquants');
    }
    
    // Vérifier que le statut est valide
    $statuts_valides = ['en_attente', 'confirmee', 'en_preparation', 'en_cours', 'expediee', 'livree', 'annulee'];
    if (!in_array($nouveau_statut, $statuts_valides)) {
        throw new Exception('Statut invalide');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Récupérer la commande actuelle
    $stmt = $db->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        throw new Exception('Commande non trouvée');
    }
    
    $ancien_statut = $commande['statut'];
    
    // Mettre à jour le statut
    $stmt = $db->prepare("UPDATE commandes SET statut = ?, date_modification = NOW() WHERE id = ?");
    $stmt->execute([$nouveau_statut, $commande_id]);
    
    // Ajouter une entrée dans l'historique
    $stmt = $db->prepare("
        INSERT INTO commande_historique (commande_id, ancien_statut, nouveau_statut, date_changement, commentaire)
        VALUES (?, ?, ?, NOW(), ?)
    ");
    $commentaire = "Statut modifié par l'administrateur";
    $stmt->execute([$commande_id, $ancien_statut, $nouveau_statut, $commentaire]);
    
    // Actions spéciales selon le nouveau statut
    if ($nouveau_statut === 'expediee') {
        // Génération d'un numéro de suivi si pas déjà présent
        if (empty($commande['numero_suivi'])) {
            $numero_suivi = 'FR' . date('Ymd') . str_pad($commande_id, 6, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("UPDATE commandes SET numero_suivi = ?, date_expedition = NOW() WHERE id = ?");
            $stmt->execute([$numero_suivi, $commande_id]);
        }
        
        // TODO: Envoyer email de notification d'expédition
    }
    
    if ($nouveau_statut === 'livree') {
        // Marquer comme livrée
        $stmt = $db->prepare("UPDATE commandes SET date_livraison = NOW() WHERE id = ?");
        $stmt->execute([$commande_id]);
        
        // Générer automatiquement la facture
        $numero_facture = 'F' . date('Y') . str_pad($commande_id, 6, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("UPDATE commandes SET numero_facture = ?, date_facture = NOW() WHERE id = ?");
        $stmt->execute([$numero_facture, $commande_id]);
        
        // TODO: Envoyer email avec la facture
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Statut mis à jour avec succès',
        'nouveau_statut' => $nouveau_statut
    ]);
    
} catch (Exception $e) {
    error_log("Erreur update statut: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>