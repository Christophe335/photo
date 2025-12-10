<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Impression sur Métal</h1>
        <img class="centre-div pose" src="../images/bandeaux/impression-metal-1.webp" alt="Un bandeau présentant des impressions sur métal personnalisées">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Un support durable et moderne pour vos impressions les plus précieuses.
        </p>
    </div>
    <section class="section1" id="Plaque - Angle arrondi pour imprimante A4">
    <div class="container">
        <h2 class="title-h3 centre-text">Impression sur métal</h2>
        <p class="paragraphe">La technique d'impression sur métal est un procédé unique pour imprimer des plaques nominatives ou des photos sur métal. Ces plaques peuvent être utilisées pour l'identification, la personnalisation et la signalétique. Elles sont résistantes aux intempéries, aux UV et permanentes en couleur. Idéales pour un usage en extérieur.</p>
    </div>
    <div class="ligne">
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-3.webp" alt="Photo imprimée sur une plaque en métal, montrant un homme aux cheveux gris">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-2.webp" alt="Photo imprimée sur une plaque en métal, montrant une femme aux cheveux gris">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-4.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Room 361">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-5.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Guest Hotel">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-6.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Guest Hotel">  
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates with round corners for A4 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Plaque - Angle arrondi pour imprimante A3">
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates with round corners for A3 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</section>
<section class="section1" id="Plaque - Angle droit pour imprimante A3">
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates straight corners for A3 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Plaque - Angle droit pour imprimante A4">
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates straight corners for A4 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Plaque - Angle droit pour imprimante A4">
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates square round corners A4');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
