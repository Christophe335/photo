<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Pochette de Prospection Personnalisée</h1>
    </div>
        <img style= "width: 100%;" class="centre-div pose" src="../images/bandeaux/pochette-1.webp" alt="Un bandeau présentant des pochettes de prospection personnalisées">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Pochettes professionnelles personnalisées pour vos présentations.
        </p>
    </div>
<section class="section1" id="Pochette simple">
    <div class="container">
        <h2 class="title-h3 centre-text">pochette simple personnalisée - format A4</h2>
        
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/pochette-4.webp" alt="Pochette souple de prospection couleur blanche au format A4 personnalisée avec un logo et du texte">  
        </div>
        <div class="colonne-6">
            <p> Le Pocket Folder Plus est une couverture de qualité supérieure  fabriquée dans un matériau souple mais résistant. C’est la solution  idéale si vous souhaitez créer des présentations personnalisées et  reliées de façon permanente.</p>
        </div>
        <div class="colonne-5 onright">
            <img src="../images/produits/pochette-prospection-2.webp" alt="Pochette souple de prospection couleur blanche au format A4 personnalisée avec un logo et du texte, présentée recto et verso">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Soft Cover A4');
            ?>
        </div>
</section>
<section class="section2" id="Pochette Plus">
    <div class="container">
        <h2 class="title-h3 centre-text">pochette plus personnalisée - format A4</h2>
        
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/pochette-prospection-1.webp" alt="Pochette souple de prospection avec un rabat intérieur,couleur blanche au format A4 personnalisée, présentée ouverte" width="300" height="300">  
        </div>
        <div class="colonne-6">
            <p> Vous envisagez de faire de la prospection, organiser une réunion  importante ou vous souhaitez présenter vos documents de manière élégante ? Une alternative aux chemises de présentation en carton. Au recto et  au verso de la couverture, vous trouverez des poches pour ajouter des  documents supplémentaires. Deux découpes dans Pocket Folder Plus où vous pouvez insérer votre carte de visite. Impressionnez vos clients et  prospects avec cette nouvelle chemise de présentation personnalisée.</p>
        </div>
        <div class="colonne-5 onright">
            <img src="../images/produits/pochette-prospection-4.webp" alt="Pochette souple de prospection couleur blanche au format A4 personnalisée avec un logo et du texte, présentée recto et verso" width="300" height="300">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Pocket Folder Plus');
            ?>
        </div>
</section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
