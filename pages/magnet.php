<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Magnets</h1>
        <img class="centre-div pose" src="../images/bandeaux/magnet-1.webp" alt="Un bandeau présentant des magnets personnalisés">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Transformez vos photos en magnets décoratifs pour votre réfrigérateur.
        </p>
    </div>
<section class="section1" id="Magnets classiques">
    <div class="container">
        <h2 class="title-h3 centre-text">Magnets</h2>
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/magnet-1.webp" width="300px" height="300px" alt="Un magnet personnalisé avec une photo de femme portant des lunettes de soleil"> 
            <img class="centre-div" src="../images/produits/magnet-2.webp" width="200px" height="200px" alt="Un magnet personnalisé avec une photo de femme tenant dans ces bras un bébé" style="margin: -70px 0 0 280px;">  
        </div>
        <div class="colonne-2">
            <p class="paragraphe">Voulez-vous égayer votre réfrigérateur ? Portez les messages magnétiques sur le réfrigérateur à un niveau supérieur en ajoutant des photos ? Connaissez-vous déjà par cœur le numéro de votre pizzeria préférée ? Super ! Parce que vous pouvez maintenant transformer vos photos en aimants avec le Framed Magnet.</p> 
        </div>
        
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Framed Magnet');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Bamboo Magnets">
    <div class="container">
        <h2 class="title-h3 centre-text">Magnets</h2>
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/bamboo-magnet-2.webp" width="300px" height="300px" alt="Un magnet en bamboo personnalisé avec une photo d'un couple de mariés"> 
            <img class="centre-div" src="../images/produits/bamboo-magnet-1.webp" width="200px" height="200px" alt="Vue recto et verso d'un magnet en bamboo personnalisé avec une photo de jeunes mariés se tenant dans les bras" style="margin: -175px 0 0 280px;">  
        </div>
        <div class="colonne-2">
            <p class="paragraphe">Un aimant DIY élégant en bambou durable pour mettre en valeur vos photos. Vous n'avez pas besoin de machine grâce à la couche auto-adhésive. Retirez le film protecteur et collez votre photo sur l'aimant. Apportez une touche personnelle et de la couleur à votre cuisine, le centre névralgique de toutes les activités. Une excellente façon d'afficher de doux souvenirs et de garder ces listes de courses importantes sous les yeux.</p> 
        </div>
        
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Bamboo Collection Magnet');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
