<?php
require_once 'functions.php';

// Vérifier l'authentification  
checkAuth();

header('Content-Type: application/json');

// Vérifications de base
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$terme = trim($_POST['terme'] ?? '');

if (empty($terme)) {
    echo json_encode([]);
    exit;
}

if (strlen($terme) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Recherche simple et sûre avec les champs supplémentaires
    $sql = "SELECT id, reference, designation, prixVente, format, matiere, 
                   couleur_interieur, couleur_ext1, couleur_ext2, couleur_ext3, 
                   couleur_ext4, couleur_ext5, couleur_ext6, couleur_ext7, 
                   couleur_ext8, couleur_ext9, couleur_ext10, couleur_ext11, 
                   couleur_ext12, couleur_ext13
            FROM produits 
            WHERE (reference LIKE ? OR designation LIKE ? OR format LIKE ? OR matiere LIKE ?)
            ORDER BY reference ASC 
            LIMIT 20";
    
    $searchTerm = "%$terme%";
    $stmt = $db->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Nettoyer les données et construire la liste des couleurs
    $results = [];
    foreach ($articles as $article) {
        // Construire la liste des couleurs disponibles
        $couleurs = [];
        if (!empty($article['couleur_interieur'])) {
            $couleurs[] = 'Intérieur: ' . $article['couleur_interieur'];
        }
        
        for ($i = 1; $i <= 13; $i++) {
            $couleurField = 'couleur_ext' . $i;
            if (!empty($article[$couleurField])) {
                $couleurs[] = 'Ext' . $i . ': ' . $article[$couleurField];
            }
        }
        
        $results[] = [
            'id' => (int)$article['id'],
            'reference' => (string)$article['reference'],
            'designation' => (string)$article['designation'],
            'prixVente' => (float)$article['prixVente'],
            'format' => (string)($article['format'] ?? ''),
            'matiere' => (string)($article['matiere'] ?? ''),
            'couleurs' => $couleurs
        ];
    }
    
    echo json_encode($results);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>