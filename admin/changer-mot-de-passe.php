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
    
    $client_id = $input['client_id'] ?? 0;
    $nouveau_mot_de_passe = $input['nouveau_mot_de_passe'] ?? '';
    
    if (!$client_id) {
        throw new Exception('ID client manquant');
    }
    
    if (empty($nouveau_mot_de_passe)) {
        throw new Exception('Nouveau mot de passe manquant');
    }
    
    if (strlen($nouveau_mot_de_passe) < 6) {
        throw new Exception('Le mot de passe doit contenir au moins 6 caractères');
    }
    
    $db = Database::getInstance()->getConnection();
    
    // Vérifier que le client existe
    $stmt = $db->prepare("SELECT id, email, prenom, nom FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception('Client non trouvé');
    }
    
    // Hacher le nouveau mot de passe pour l'authentification
    $mot_de_passe_hache = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
    
    // Mettre à jour le mot de passe (hashé pour auth + clair pour admin)
    $stmt = $db->prepare("UPDATE clients SET mot_de_passe = ?, mot_de_passe_clair = ? WHERE id = ?");
    $result = $stmt->execute([$mot_de_passe_hache, $nouveau_mot_de_passe, $client_id]);
    
    if (!$result) {
        throw new Exception('Erreur lors de la mise à jour en base de données');
    }
    
    // Log de l'action
    error_log("Changement de mot de passe pour client {$client['email']} (ID: $client_id) par admin");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Mot de passe modifié avec succès',
        'nouveau_mot_de_passe_clair' => $nouveau_mot_de_passe
    ]);
    
} catch (Exception $e) {
    error_log("Erreur changement mot de passe: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>