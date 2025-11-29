<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

// Traitement des actions
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $result = supprimerProduit($_GET['id']);
    
    if ($result) {
        $_SESSION['message'] = 'Produit supprimé avec succès';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erreur lors de la suppression du produit';
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: index.php');
    exit;
}

// Paramètres de recherche et pagination
$recherche = $_GET['recherche'] ?? '';
$familleSelectionnee = $_GET['famille'] ?? '';
$pageActuelle = max(1, intval($_GET['page'] ?? 1));
$produitsParPage = 20;

// Récupérer les familles pour le filtre
$familles = getFamilles();

// Rechercher les produits pour la pagination
$totalProduitsRecherche = compterProduits($recherche, $familleSelectionnee);
$totalPages = ceil($totalProduitsRecherche / $produitsParPage);
$offset = ($pageActuelle - 1) * $produitsParPage;

$produits = rechercherProduits($recherche, $familleSelectionnee, $produitsParPage, $offset);

// Calculer les statistiques globales
$db = Database::getInstance()->getConnection();
$stats = $db->query("
    SELECT 
        COUNT(*) as total_produits,
        SUM(CASE WHEN prixAchat IS NOT NULL AND prixAchat > 0 THEN prixAchat ELSE 0 END) as valeur_stock,
        SUM(CASE WHEN prixVente IS NOT NULL AND prixVente > 0 THEN prixVente ELSE 0 END) as valeur_vente
    FROM produits
")->fetch(PDO::FETCH_ASSOC);

$totalProduits = $stats['total_produits'] ?? 0;
$valeurStock = $stats['valeur_stock'] ?? 0;
$valeurVente = $stats['valeur_vente'] ?? 0;

// Inclure le header
include 'header.php';

// Inclure le footer
include 'footer.php';
?>