<?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
 * Alias pour getProduitById
 */
function getProduit($id) {
    return getProduitById($id);
}
/**
 * Créer un nouveau produit
 */
function creerProduit($data) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "INSERT INTO produits (
        famille, nomDeLaFamille, reference, designation, format, 
        ordre, est_compose, composition_auto,
        prixAchat, prixVente, conditionnement, matiere, couleur_interieur,
        couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3,
        couleur_ext4, imageCoul4, couleur_ext5, imageCoul5, couleur_ext6, imageCoul6,
        couleur_ext7, imageCoul7, couleur_ext8, imageCoul8, couleur_ext9, imageCoul9,
        couleur_ext10, imageCoul10, couleur_ext11, imageCoul11, couleur_ext12, imageCoul12,
        couleur_ext13, imageCoul13
    ) VALUES (
        :famille, :nomDeLaFamille, :reference, :designation, :format,
        :ordre, :est_compose, :composition_auto,
        :prixAchat, :prixVente, :conditionnement, :matiere, :couleur_interieur,
        :couleur_ext1, :imageCoul1, :couleur_ext2, :imageCoul2, :couleur_ext3, :imageCoul3,
        :couleur_ext4, :imageCoul4, :couleur_ext5, :imageCoul5, :couleur_ext6, :imageCoul6,
        :couleur_ext7, :imageCoul7, :couleur_ext8, :imageCoul8, :couleur_ext9, :imageCoul9,
        :couleur_ext10, :imageCoul10, :couleur_ext11, :imageCoul11, :couleur_ext12, :imageCoul12,
        :couleur_ext13, :imageCoul13
    )";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute($data);
    
    if ($result) {
        return $db->lastInsertId();
    }
    return false;
}

/**
 * Mettre à jour un produit
 */
function modifierProduit($id, $data) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "UPDATE produits SET 
        famille = :famille, nomDeLaFamille = :nomDeLaFamille, reference = :reference, 
        ordre = :ordre, est_compose = :est_compose, composition_auto = :composition_auto,
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
    // Utiliser la fonction qui gère aussi les compositions
    return supprimerProduitAvecCompositions($id);
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

/**
 * Ajouter les composants d'un article composé
 */
function ajouterComposants($produitParentId, $composants) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $db->beginTransaction();
        
        // Supprimer les composants existants (au cas où)
        $stmt = $db->prepare("DELETE FROM produit_compositions WHERE produit_parent_id = ?");
        $stmt->execute([$produitParentId]);
        
        // Ajouter les nouveaux composants
        if (!empty($composants)) {
            $sql = "INSERT INTO produit_compositions (produit_parent_id, produit_enfant_id, quantite, ordre_affichage) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            
            foreach ($composants as $index => $composant) {
                $stmt->execute([
                    $produitParentId,
                    $composant['id'],
                    $composant['quantite'],
                    $index
                ]);
            }
        }
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollback();
        error_log("Erreur ajout composants: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupérer les composants d'un article composé
 */
function getComposantsProduit($produitId) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT 
                pc.produit_enfant_id as id,
                pc.quantite,
                pc.ordre_affichage,
                p.reference,
                p.designation,
                p.prixVente as prix
            FROM produit_compositions pc
            JOIN produits p ON pc.produit_enfant_id = p.id
            WHERE pc.produit_parent_id = ?
            ORDER BY pc.ordre_affichage, pc.id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$produitId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Vérifier si un produit est composé
 */
function estProduitCompose($produitId) {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT est_compose FROM produits WHERE id = ?");
    $stmt->execute([$produitId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (bool)$result['est_compose'] : false;
}

/**
 * Récalculer automatiquement le prix et la désignation d'un article composé
 */
function recalculerArticleCompose($produitId) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $composants = getComposantsProduit($produitId);
        
        if (empty($composants)) {
            return true;
        }
        
        // Calculer la nouvelle désignation
        $designations = array_map(function($c) {
            return $c['designation'];
        }, $composants);
        $nouvelleDesignation = implode(' + ', $designations);
        
        // Calculer le nouveau prix
        $nouveauPrix = array_reduce($composants, function($total, $c) {
            return $total + ($c['prix'] * $c['quantite']);
        }, 0);
        
        // Estimer le prix d'achat (70% du prix de vente)
        $nouveauPrixAchat = $nouveauPrix * 0.7;
        
        // Mettre à jour le produit
        $sql = "UPDATE produits 
                SET designation = ?, prixVente = ?, prixAchat = ?
                WHERE id = ? AND composition_auto = 1";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $nouvelleDesignation,
            $nouveauPrix,
            $nouveauPrixAchat,
            $produitId
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur recalcul article composé: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprimer un produit et ses compositions
 */
function supprimerProduitAvecCompositions($id) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $db->beginTransaction();
        
        // Supprimer les compositions où ce produit est parent ou enfant
        $stmt = $db->prepare("DELETE FROM produit_compositions WHERE produit_parent_id = ? OR produit_enfant_id = ?");
        $stmt->execute([$id, $id]);
        
        // Supprimer le produit
        $stmt = $db->prepare("DELETE FROM produits WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        $db->commit();
        return $result;
        
    } catch (Exception $e) {
        $db->rollback();
        error_log("Erreur suppression produit avec compositions: " . $e->getMessage());
        return false;
    }
}
?>