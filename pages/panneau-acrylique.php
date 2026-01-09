<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Panneaux Acrylique</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/panneau-acrylique-1.webp" alt="Un bandeau présentant des panneaux acryliques personnalisés" loading="lazy">
    <section class="section1" id="Panneaux Acrylique">
    <div class="container">
        <h2 class="title-h3 centre-text">Pannneaux acrylique</h2>
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/panneau-acrylic-1.webp" width="250px" height="250px" alt="Panneau en bamboo avec partie inférieur visible et sur le reste de la surface une vitre acrylique présentant une photo de famille" loading="lazy"> 
        </div>
        <div class="colonne-6">
            <p class="paragraphe">Découvrez la dernière addition à notre gamme en pleine expansion de panneaux d'affichage. Fabriqués en matériau de haute qualité résistant aux UV et aux fissures, ces nouveaux supports en acrylique transformeront n'importe quelle impression en présentoirs exquis. Les aimants puissants dans les quatre coins assurent un positionnement parfait et vous permettent de changer les images ou impressions en quelques secondes. Aucun outil requis. Disponibles en une large variété de tailles, ces blocs DIY faciles à utiliser sont parfaits pour les photos, menus, récompenses, heures d'ouverture, promotions, actions de vente et plus encore.</p> 
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/panneau-acrylic-2.webp" width="350px" height="350px" alt="Panneau en bamboo recouvert d'une vitre acrylique présentant une photo de plusieurs surfeurs sur une plage" loading="lazy"> 
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-choix.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Acrylic Panel');
            ?>
        </div>
        </br>
        <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/peel-stick-panel-1.webp" width="300px" height="300px" alt="Panneau acrylique simple avec sytème autocollant présentant une photo de 3 femmes sur la plage" loading="lazy"> 
        </div>
        <div class="colonne-6">
            <p class="paragraphe">Créez vos propres présentations personnalisées avec les Panneaux Simples à décoller et coller. Ce produit prêt à l'emploi dispose d'une zone auto-adhésive qui vous permet de coller tout type d'impression directement sur le Panneau. Il est parfait pour les présentations sur comptoir, en showroom ou promotionnelles.</p> 
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/peel-stick-panel-2.webp" width="300px" height="300px" alt="Panneau acrylique double avec sytème autocollant présentant une photo d'un menu de restaurant" loading="lazy"> 
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & Stick Panel');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Panneaux Acrylique">
    <div class="container">
        <h2 class="title-h3 centre-text">Pannneau présentoir personnalisé</h2>
            <img class="centre-div" src="../images/produits/hard_cover_basic_display-1.webp" width="400px" height="250px" alt="Présentation du produit de personnalisation de panneau avec une feuille autocollante à imprimer" loading="lazy"> 
            <p class="paragraphe">Les Hard Cover Basic Display sont des présentoirs personnalisables à  l'aide de la machine de personnalisation Hard Cover Maker 650.</p> 
    </div>
        <div class="tableau-container">
            <?php
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic Display');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
