<?php
session_start();

// Configuration de base pour l'admin
define('ADMIN_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));

// Inclusion de la base de données
require_once BASE_PATH . '/includes/database.php';

/**
 * Vérification simple de l'authentification (à améliorer selon vos besoins)
 */
function checkAuth() {
    // Pour l'instant, authentification basique (à remplacer par un vrai système)
    if (!isset($_SESSION['admin_logged'])) {
        $_SESSION['admin_logged'] = true; // Auto-login pour le développement
    }
    return $_SESSION['admin_logged'];
}

/**
 * Obtenir toutes les familles de produits
 */
function getFamilles() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT DISTINCT famille FROM produits WHERE famille IS NOT NULL AND famille != '' ORDER BY famille");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Obtenir un produit par ID
 */
function getProduitById($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Créer un nouveau produit
 */
function creerProduit($data) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "INSERT INTO produits (
        famille, nomDeLaFamille, reference, designation, format, 
        ordre,
        prixAchat, prixVente, conditionnement, matiere, couleur_interieur,
        couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3,
        couleur_ext4, imageCoul4, couleur_ext5, imageCoul5, couleur_ext6, imageCoul6,
        couleur_ext7, imageCoul7, couleur_ext8, imageCoul8, couleur_ext9, imageCoul9,
        couleur_ext10, imageCoul10, couleur_ext11, imageCoul11, couleur_ext12, imageCoul12,
        couleur_ext13, imageCoul13
    ) VALUES (
        :famille, :nomDeLaFamille, :reference, :designation, :format,
        :ordre,
        :prixAchat, :prixVente, :conditionnement, :matiere, :couleur_interieur,
        :couleur_ext1, :imageCoul1, :couleur_ext2, :imageCoul2, :couleur_ext3, :imageCoul3,
        :couleur_ext4, :imageCoul4, :couleur_ext5, :imageCoul5, :couleur_ext6, :imageCoul6,
        :couleur_ext7, :imageCoul7, :couleur_ext8, :imageCoul8, :couleur_ext9, :imageCoul9,
        :couleur_ext10, :imageCoul10, :couleur_ext11, :imageCoul11, :couleur_ext12, :imageCoul12,
        :couleur_ext13, :imageCoul13
    )";
    
    $stmt = $db->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Mettre à jour un produit
 */
function modifierProduit($id, $data) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "UPDATE produits SET 
        famille = :famille, nomDeLaFamille = :nomDeLaFamille, reference = :reference, 
        ordre = :ordre,
        designation = :designation, format = :format, prixAchat = :prixAchat, 
        prixVente = :prixVente, conditionnement = :conditionnement, matiere = :matiere, 
        couleur_interieur = :couleur_interieur,
        couleur_ext1 = :couleur_ext1, imageCoul1 = :imageCoul1, couleur_ext2 = :couleur_ext2, 
        imageCoul2 = :imageCoul2, couleur_ext3 = :couleur_ext3, imageCoul3 = :imageCoul3,
        couleur_ext4 = :couleur_ext4, imageCoul4 = :imageCoul4, couleur_ext5 = :couleur_ext5, 
        imageCoul5 = :imageCoul5, couleur_ext6 = :couleur_ext6, imageCoul6 = :imageCoul6,
        couleur_ext7 = :couleur_ext7, imageCoul7 = :imageCoul7, couleur_ext8 = :couleur_ext8, 
        imageCoul8 = :imageCoul8, couleur_ext9 = :couleur_ext9, imageCoul9 = :imageCoul9,
        couleur_ext10 = :couleur_ext10, imageCoul10 = :imageCoul10, couleur_ext11 = :couleur_ext11, 
        imageCoul11 = :imageCoul11, couleur_ext12 = :couleur_ext12, imageCoul12 = :imageCoul12,
        couleur_ext13 = :couleur_ext13, imageCoul13 = :imageCoul13,
        updated_at = NOW()
        WHERE id = :id";
    
    $data['id'] = $id;
    $stmt = $db->prepare($sql);
    return $stmt->execute($data);
}

/**
 * Supprimer un produit
 */
function supprimerProduit($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM produits WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Rechercher des produits
 */
function rechercherProduits($terme = '', $famille = '', $limit = 50, $offset = 0) {
    $db = Database::getInstance()->getConnection();
    
    $where = [];
    $params = [];
    
    if (!empty($terme)) {
        $where[] = "(reference LIKE ? OR designation LIKE ? OR famille LIKE ?)";
        $params[] = "%$terme%";
        $params[] = "%$terme%";
        $params[] = "%$terme%";
    }
    
    if (!empty($famille)) {
        $where[] = "famille = ?";
        $params[] = $famille;
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
           // Tri par famille, puis par champ 'ordre' (valeurs numériques d'abord), puis par reference
           $sql = "SELECT * FROM produits $whereClause ORDER BY famille ASC, (CASE WHEN ordre IS NULL OR ordre = 0 THEN 1 ELSE 0 END) ASC, (CASE WHEN ordre IS NULL OR ordre = 0 THEN NULL ELSE ordre END) ASC, reference ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Compter les produits pour la pagination
 */
function compterProduits($terme = '', $famille = '') {
    $db = Database::getInstance()->getConnection();
    
    $where = [];
    $params = [];
    
    if (!empty($terme)) {
        $where[] = "(reference LIKE ? OR designation LIKE ? OR famille LIKE ?)";
        $params[] = "%$terme%";
        $params[] = "%$terme%";
        $params[] = "%$terme%";
    }
    
    if (!empty($famille)) {
        $where[] = "famille = ?";
        $params[] = $famille;
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $sql = "SELECT COUNT(*) FROM produits $whereClause";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchColumn();
}
?>