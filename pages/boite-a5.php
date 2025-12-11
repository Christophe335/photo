<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Boîte personnalisée <span style="font-family: 'Roboto', sans-serif; font-weight: 700; font-size: 32px;">A5</span></h1>
        <img class="centre-div pose" src="../images/bandeaux/box-1.webp" alt="Un bandeau présentant des boîtes personnalisées au format A5">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Créez votre boîte personnalisée au format A5 avec vos photos préférées.
        </p>
    </div>
    <section class="section2" id="Boîte format A5">
    <div class="container">
        <h2 class="title-h3 centre-text">Boîte format A5</h2>
        <p class="paragraphe">Nous proposons un service de A à Z, avec un fort accent sur la personnalisation. Nous veillons à ce que votre identité de marque prenne forme. Nous proposons des Boîtes avec une touche douce en cachemire ou une finition soft touch avec un design de votre choix. Nos boîtes peuvent être utilisées pour une large gamme d'applications dans presque n'importe quelle industrie ou situation. Pensez : cadeaux classiques, marketing créatif, branding employeur, anniversaires, cadeaux pour nouveau-né, ...</p> 
    </div>
            <img class="centre-div" src="../images/produits/boiteA5-2.webp" width="400px" height="300px" alt="Un magnet en bamboo personnalisé avec une photo d'un couple de mariés"> 

        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peleman Box A5- 40 mm');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
