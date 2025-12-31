<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Panneaux Bambou</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/bamboo-1.webp" alt="Un bandeau présentant des panneaux en bambou personnalisés">
<section class="section1" id="Panneaux Photo">
    <div class="container">
        <h2 class="title-h3 centre-text">Pannneaux Photo en bamboo</h2>
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/bamboo-1.webp" width="300px" height="300px" alt="Un magnet personnalisé avec une photo de femme portant des lunettes de soleil"> 
        </div>
        <div class="colonne-2">
            <p class="paragraphe">Un beau panneau photo en bambou écologique et respectueux de l'environnement, pour coller des photos en quelques secondes. Grâce à la couche auto-adhésive, vous n'avez pas besoin de machine. </br>Retirez le film protecteur et collez votre photo sur le panneau en bambou. Vous pouvez accrocher ce panneau au mur ou le poser verticalement. Grâce à son épaisseur de 28 mm, le panneau ne tombera pas. Vous pouvez l'utiliser en mode portrait ou paysage.</p> 
        </div>
        
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Bamboo Collection Photo');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Bamboo et Acrylique">
    <div class="container">
        <h2 class="title-h3 centre-text">Pannneau photo en bamboo et acrylique</h2>
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/bamboo-3.webp" width="300px" height="300px" alt="Panneau en bamboo avec partie inférieur visible et sur le reste de la surface une vitre acrylique présentant une photo de famille"> 
        </div>
        <div class="colonne-6">
            <p class="paragraphe">Ce panneau photo en bambou tendance est idéal pour changer régulièrement vos photos. Le panneau en acrylique se retire facilement grâce à la fermeture magnétique pratique. En autre version, complètement recouvert d'une vitre acrylique et de 4 petits aimants, le remplacement de la photo est aussi facile.</p> 
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/bamboo-4.webp" width="300px" height="300px" alt="Panneau en bamboo recouvert d'une vitre acrylique présentant une photo de plusieurs surfeurs sur une plage"> 
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Bamboo Collection Photo');
            ?>
        </div>
        </br>
    
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
