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

    // Utilisation du système de tableau existant avec quantité par défaut
    afficherTableauPersonnalisation($famille, $quantiteParDefaut);
} else {
    echo '<p style="text-align: center; color: #666;">Type de personnalisation non spécifié.</p>';
}
?>