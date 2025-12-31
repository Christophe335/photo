<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Panneaux Photo</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/panneau-photo-1.webp" alt="Un bandeau présentant des panneaux photo personnalisés">
    <section class="section1" id="Bamboo et Acrylique">
    <div class="container">
        <h2 class="title-h3 centre-text">Pannneaux photo</h2>
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/panneau photo-2.webp" width="250px" height="250px" alt="2 panneaux photo représentant une photo de famille et une photo de bébé"> 
        </div>
        <div class="colonne-6">
            <p class="paragraphe">Faciles à utiliser tout en étant élégants et sophistiqués, ces panneaux en bois Peel & Stick peuvent être accrochés au mur ou exposés sur une étagère. Les panneaux ne nécessitent aucun outil spécial et peuvent être utilisés en orientation portrait comme paysage. Choisissez votre photo préférée, retirez la feuille protectrice et montez votre photo dessus.</p> 
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/panneau photo-3.webp" width="200px" height="300px" alt="Panneau photo représentant la piscine d'un hotel de luxe dans un pays tropical"> 
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & stick Wood Panel');
            ?>
        </div>
        </br>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/panneau photo-1.webp" width="300px" height="300px" alt="3 panneaux photo représentant une photo d'un couple', une photo de femme et une photo de bébé"> 
        </div>
        <div class="colonne-6">
            <p class="paragraphe">Nos panneaux photo faciles à réaliser offrent une excellente solution pour un choix photo populaire. Aucune machine requise, il suffit de décoller et de coller votre photo sur le panneau photo auto-adhésif. Vos clients peuvent suspendre ou poser ces panneaux photo au design moderne. Les inserts de support sont inclus.</p> 
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/panneau photo-4.webp" width="200px" height="300px" alt="Panneau photo représentant un diplôme"> 
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & stick Light Panel');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
