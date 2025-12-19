<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Album Photos Personnalisé</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/album-1.webp" alt="Un bandeau présentant divers albums photos personnalisés">
    <div class="container">  
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Créez un magnifique album photo pour conserver vos plus beaux souvenirs.
        </p>
    </div>    
        <section class="section1" id="Album Photo">
        <div class="container">
            <h2 class="title-h3 centre-text">ALBUM PHOTO Personnalisé</h2>
            <p>Donnez à vos livres photo une finition professionnelle et un aspect de haute qualité avec notre PhotoBook Resin. Notre système breveté de reliure thermique en acier garantit une liaison solide et durable.</p>
            
        </div>
        <div class="container">
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/photo-book-a5-landscape-1.webp" alt="Album photo A4 paysage présentant la face avant grise avec sa personnalisation">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/photo-book-a4-landscape-2.webp" alt="Album photo A4 paysage présentant la face avant noir avec sa personnalisation">
                </div>
            </div>
            </br>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A4 Landscape (black mirror)');
                 ?>
            </div>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A5 Landscape (black mirror)');
                 ?>
            </div>
            <p class="paragraphe">La version A4 format portrait, notre best-seller de notre Livre Photo. Un album photo classique avec une finition professionnelle et un aspect haute qualité. Notre système de reliure thermique en acier breveté assure une liaison solide et durable. La manière parfaite de célébrer et partager les moments uniques de la vie.</p>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/photo-book-a4-portrait-1.webp" alt="Album photo A4 portrait présentant la face avant noire avec sa personnalisation">
                </div>
                <div class="colonne-2 onright">
                    <img class="centre-div" src="../images/produits/photo-book-a3-landscape-1.webp" alt="Album photo A3 paysage ouvert à plat présentant des photos imprimées au format A3">
                </div>
            </div>
            </br>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A4 Portrait (black mirror)');
                 ?>
            </div>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A3 Landscape (black mirror)');
                 ?>
            </div>
        </div>
    </section>

</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
