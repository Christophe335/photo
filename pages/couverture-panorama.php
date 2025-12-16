<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Couverture Panorama</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/panorama-1.webp" alt="Un bandeau présentant des couvertures panoramiques personnalisées">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Format panoramique pour mettre en valeur vos plus beaux paysages.
        </p>
    </div>
<section class="section1" id="Couverture rigide Panorama">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama</h2>
        
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/couverture-panorama-1.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant 2 modèles différents">  
        </div>
        <div class="colonne-6">
            <p>Personnalisez le recto, le dos et le verso de votre livre avec le Panorama HardCover Les Panorama Hard Covers sont un outil de personnalisation convivial et économique créé en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures rigides uniques préfabriquées vous permettent de personnaliser facilement le recto, le dos et le verso de votre HardCover. C'est un concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle.Les Panorama Hard Covers sont la solution parfaite de fabrication de caisses pour toutes vos applications uniques ou en petite série.</p>
        </div>
        <div class="colonne-5 onright">
            <img src="../images/produits/couverture-panorama-2.webp" alt="Couverture rigide de livre photo panoramique format portrait montrant bien que l'image couvre toute la surface">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Panorama Hard Cover');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Couverture rigide Panorama personnalisée">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama personnalisée</h2>
        <p class="paragraphe">Le Hard Cover Basic est une couverture préfabriquée avec un revêtement en papier et une fine couche de colle thermofusible sur ses bords extérieurs et intérieurs. Peleman a développé le Hard Cover Basic pour fabriquer un livre relié, un classeur à anneaux ou un présentoir parfaitement fini et personnalisé, sans utiliser de colles liquides, en petites ou grandes quantités.</p>
    </div>
    <img class="centre-div" src="../images/produits/hard-cover-basic-1.webp" alt="Présentation du système de couverture rigide panoramique personnalisée Hard Cover Basic"> 
    
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic A4 Portrait');
            afficherTableauProduits('Hard Cover Basic A4 Paysage');
            afficherTableauProduits('Hard Cover Basic A5 Portrait');
            afficherTableauProduits('Hard Cover Basic A5 Paysage');
            afficherTableauProduits('Hard Cover Basic Lettersize Portrait');
            ?>
        </div>
        </br>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/panorama-1.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo d'un caméléon">  
        </div>
        
        <div class="colonne-3 onright">
            <img src="../images/produits/panorama-2.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo de maisons sur pilotis aux Maldives">
        </div>
    </div>
        
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
