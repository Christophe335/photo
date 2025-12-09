<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Album Photos</h1>
        <img class="centre-div pose" src="../images/bandeaux/album-1.webp" alt="Un bandeau présentant divers albums photos personnalisés">
            
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Créez un magnifique album photo pour conserver vos plus beaux souvenirs.
        </p>
    </div>    
        <section class="section1">
        <div class="container">
            <h2 class="title-h3 centre-text">ALBUM PHOTO</h2>
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
                require_once __DIR__ . '/../includes/tableau.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A4 Landscape (black mirror)');
                 ?>
            </div>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau.php';
                        
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
                require_once __DIR__ . '/../includes/tableau.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A4 Portrait (black mirror)');
                 ?>
            </div>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Photo Book A3 Landscape (black mirror)');
                 ?>
            </div>
        </div>
        <div>
            <div class="ontop">
                <img class="centre-div" src="../images/produits/personnalisation-1.webp" alt="Album photo entièrement personnalisé">
            </div>
            <div class="onbottom">
                <img class="centre-div" src="../images/produits/personnalisation-2.webp" alt="Album photo entièrement personnalisé">
            </div>
        </div>
        </br>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
    <section class="section2">
    <div class="container">
        <h2 class="title-h3 centre-text">ALBUM PHOTO À FENÊTRE</h2>
        <p class="paragraphe">Un livre relié en tissu kashmir classique avec une fenêtre découpée dans la couverture. 
        Ce détail élégant crée une véritable sensation de profondeur et ajoute une interaction 
        ludique avec la première page. Vous n'avez pas besoin d'un système de reliure. 
        Vous pouvez choisir parmi différentes couleurs de kashmir ou en Noir, adapté à toutes les occasions.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/album-photo-kasmir-a-fenetre-1.webp" alt="Livre relier en tissus finition kasmir avec fenêtre découpée sur la couverture">
        </div>
        <div class="colonne-3 ligne">
            <div class="posCouleur">
                <img src="../images/couleurs/big/oyster-B.webp" alt="Couleur kasmir oyster">
                <p>Kashmir</br>Oyster</p>
            </div>
            <div class="posCouleur">
                <img src="../images/couleurs/big/red-B.webp" alt="Couleur kasmir red">
                <p>Kashmir</br>Red</p>
            </div>
            <div class="posCouleur">
                <img src="../images/couleurs/big/ultra-marine-B.webp" alt="Couleur kasmir ultra-marine">
                <p>Kashmir</br>Ultra Marine</p>
            </div>
            <div class="posCouleur">
                <img src="../images/couleurs/big/black-silk-B.webp" alt="Couleur kasmir black silk">
                <p>Black</br>Silk</p>
            </div>

        </div>
        
    </div>
    
</br>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Photo Books with window');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
