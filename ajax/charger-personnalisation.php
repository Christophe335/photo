<?php
// Inclusion du système de tableau personnalisé
require_once __DIR__ . '/../includes/tableau-personnalisation.php';

header('Content-Type: text/html; charset=utf-8');

// Traitement de la requête
if (isset($_GET['type'])) {
    $type = $_GET['type'];
    // Conversion du type vers la famille de produits
    $famille = $type;

    // Déterminer la quantité par défaut envoyée (quantite produit × conditionnement)
    $quantite = isset($_GET['quantite']) ? intval($_GET['quantite']) : 1;
    $conditionnement = isset($_GET['conditionnement']) ? intval($_GET['conditionnement']) : 1;
    $quantiteParDefaut = max(1, $quantite * max(1, $conditionnement));

    // Récupérer référence produit et format (optionnels) pour filtrer via la table de liaison
    $produit_ref = isset($_GET['produit_ref']) ? trim($_GET['produit_ref']) : null;
    $produit_format = isset($_GET['produit_format']) ? trim($_GET['produit_format']) : null;

    // Utilisation du système de tableau existant avec quantité par défaut et filtres optionnels
    afficherTableauPersonnalisation($famille, $quantiteParDefaut, $produit_ref, $produit_format);
} else {
    echo '<p style="text-align: center; color: #666;">Type de personnalisation non spécifié.</p>';
}
?>