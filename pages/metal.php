<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Alu - Print</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/impression-metal-1.webp" alt="Un bandeau présentant des impressions sur métal personnalisées" loading="lazy">
    <section class="section1" id="Plaque - Angle arrondi pour imprimante A4">
    <div class="container">
        <h2 class="title-h3 centre-text">Alu-Print</h2>
        <p class="paragraphe">La technique d'impression sur métal est un procédé unique pour imprimer des plaques nominatives ou des photos sur métal. Ces plaques peuvent être utilisées pour l'identification, la personnalisation et la signalétique. Elles sont résistantes aux intempéries, aux UV et permanentes en couleur. Idéales pour un usage en extérieur.</p>
    </div>
    <div class="ligne">
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-3.webp" alt="Photo imprimée sur une plaque en métal, montrant un homme aux cheveux gris" loading="lazy">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-2.webp" alt="Photo imprimée sur une plaque en métal, montrant une femme aux cheveux gris" loading="lazy">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-4.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Room 361" loading="lazy">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-5.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Guest Hotel" loading="lazy">  
        </div>
        <div class="colonne-7">
            <img class="centre-div" src="../images/produits/impression-metal-6.webp" alt="Photo imprimée sur une plaque en métal, montrant un texte marqué Guest Hotel" loading="lazy">  
        </div>
    </div>
    <h3 class="sous-titre-h3">Plaque angle arrondi</h3>
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
<section class="section2" id="Plaque - Angle arrondi pour imprimante A3">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-coin-arrondi-5.webp" width="150px" height="150px" alt="Plaque en métal avec des coins arrondis, montrant un homme qui porte sa fille sur les épaules" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque angle arrondi grand format</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-coin-arrondi-6.webp" width="260px" height="180px" alt="2 Plaque en métal avec des coins arrondis, montrant sur la première un couple portant chacun un enfant et sur l'autre portrait d'une jeune femme souriante" loading="lazy">
        </div>
    </div>
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
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-1.webp" width="150px" height="150px" alt="Plaque en métal angles droits, montrant le portrait un homme aux cheveux gris" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque angle droit grand format</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/panneau photo-5.webp" width="150px" height="150px" alt="2 Plaque en métal angles droits, montrant une jeune fille qui ceuille des fleurs dans un champ" loading="lazy">
        </div>
    </div>
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
<section class="section2" id="Plaque - Angle droit pour imprimante A4">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-angle-droit-1.webp" width="200px" height="200px" alt="Plaque en métal angles droits, montrant un baquebot en mer" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque angle droit</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-angle-droit-2.webp" width="200px" height="200px" alt="2 Plaque en métal angles droits, montrant immitation de texture bois avec une inscription" loading="lazy">
        </div>
    </div>
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
<section class="section1" id="Plaque carré angle arrondi">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-coin-arrondi-2.webp" width="150px" height="150px" alt="Plaque en métal avec des coins arrondis, présentant le logo d'un bar">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque carré avec angle arrondi</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-coin-arrondi-3.webp" width="150px" height="150px" alt="Plaque en métal avec des coins arrondis, montrant une femme aux cheveux gris" loading="lazy">
        </div>
    </div>
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
<section class="section2" id="Plaque - Ovale ou Circulaire">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-oval-1.webp" width="150px" height="150px" alt="Plaque oval, présentant la photo d'un homme" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque ovale ou circulaire</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-oval-5.webp" width="150px" height="150px" alt="Plaque circulaire, montrant une famille qui courtsur la plage" loading="lazy">
        </div>
    </div>
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates oval and circle for A4 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Plaque pliée">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-plier-1.webp" width="150px" height="150px" alt="Plaque plié avec inscription du prénom John" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque pliée</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-plier-2.webp" width="150px" height="150px" alt="Plaque plié présentant la carte des vins" loading="lazy">
        </div>
    </div>
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates bended for A4 Printer');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Plaque flexible">
    <div class="ligne">
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-flexible-1.webp" width="150px" height="150px" alt="Plaque flexible apposée sur un mug" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="sous-titre-h3">Plaque flexible - Personnalisation d'objets</h3>
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/plaque-flexible-2.webp" width="150px" height="150px" alt="Plaque flexible seule avec impression d'un nom et prénom" loading="lazy">
        </div>
    </div>
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Print-In Plates felexible thin');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
