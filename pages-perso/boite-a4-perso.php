<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 60px 0 0 0;">

        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/box-2perso.webp" alt="Un bandeau présentant des boîtes personnalisées au format A4" loading="lazy">
    <section class="section1" id="Coffret Prestige format A4 45mm">
    <div class="container">
        <h1 class="title-h3 centre-text">Coffret Prestige format A4 45mm personnalisée</h1>
        <p class="paragraphe">Nos coffret au format A4 offrent une grande variété de possibilités pour personnaliser selon vos besoins et préférences grace à une large gamme de couleurs, un format 21 x 29.7cm et une épaisseur de 45mm ou 90mm. Plusieurs couleur sont disponibles et vous pouvez aussi de prendre nos boîtes à recouvrir pour y appliquer la finition de votre choix.</p> 
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/boite-10.webp" width="400px" height="300px" alt="Boîte avec couvercle personnalisé" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="title-h3 centre-text">Couleurs disponibles</h3>
            <div class="ligne" style="margin-left: 20%;">
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-1.webp" width="50px" height="60px" alt="Couleur de boite Pastel blue" loading="lazy">
                    <p>Pastel Blue</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-2.webp" width="50px" height="60px" alt="Couleur de boite Pastel pink" loading="lazy">
                    <p>Pastel Pink</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-3.webp" width="50px" height="60px" alt="Couleur de boite Pastel yellow" loading="lazy">
                    <p>Pastel Yellow</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-4.webp" width="50px" height="60px" alt="Couleur de boite Pastel beige" loading="lazy">
                    <p>Pastel Beige</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-5.webp" width="50px" height="60px" alt="Couleur de boite Pastel green" loading="lazy">
                    <p>Pastel Green</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-6.webp" width="50px" height="60px" alt="Couleur de boite Pastel purple" loading="lazy">
                    <p>Pastel Purple</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-7.webp" width="50px" height="60px" alt="Couleur de boite Pastel beige" loading="lazy">
                    <p>White soft touch</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-8.webp" width="50px" height="60px" alt="Couleur de boite Pastel green" loading="lazy">
                    <p>Black</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-10.webp" width="50px" height="60px" alt="Couleur de boite Stressed grey" loading="lazy">
                    <p>Stressed Grey</p> 
                </div>
            </div>
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peleman Box A4 - 45mm');
            ?>
        </div>
</section>
<section class="section2" id="Coffret Prestige format A4 90mm">
    <div class="container">
        <h2 class="title-h3 centre-text">Coffret Prestige format A4 90mm personnalisée</h2>
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/boite-6.webp" width="400px" height="300px" alt="Boîte avec couvercle personnalisé" loading="lazy">
        </div>   
        <div class="colonne-6">
            <h3 class="title-h3 centre-text">Couleurs disponibles</h3>
            <div class="ligne" style="margin-left: 20%;">
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-1.webp" width="50px" height="60px" alt="Couleur de boite Pastel blue" loading="lazy">
                    <p>Pastel Blue</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-2.webp" width="50px" height="60px" alt="Couleur de boite Pastel pink" loading="lazy">
                    <p>Pastel Pink</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-3.webp" width="50px" height="60px" alt="Couleur de boite Pastel yellow" loading="lazy">
                    <p>Pastel Yellow</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-4.webp" width="50px" height="60px" alt="Couleur de boite Pastel beige" loading="lazy">
                    <p>Pastel Beige</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-5.webp" width="50px" height="60px" alt="Couleur de boite Pastel green" loading="lazy">
                    <p>Pastel Green</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-6.webp" width="50px" height="60px" alt="Couleur de boite Pastel purple" loading="lazy">
                    <p>Pastel Purple</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-7.webp" width="50px" height="60px" alt="Couleur de boite Pastel beige" loading="lazy">
                    <p>White soft touch</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-8.webp" width="50px" height="60px" alt="Couleur de boite Pastel green" loading="lazy">
                    <p>Black</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-10.webp" width="50px" height="60px" alt="Couleur de boite Stressed grey" loading="lazy">
                    <p>Stressed Grey</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-29.webp" width="50px" height="60px" alt="Couleur de boite photo Océan" loading="lazy">
                    <p>Ocean</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-211.webp" width="50px" height="60px" alt="Couleur de boite photo Fleur" loading="lazy">
                    <p>Flower</p> 
                </div>
            </div>
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peleman Box A4 - 90mm');
            ?>
        </div>
</section>
<section class="section1" id="Coffret Prestige Flexibox">
    <div class="container">
        <h2 class="title-h3 centre-text">Coffret Prestige Flexibox personnalisée</h2>
        <p class="paragraphe">Une solution écologique, entièrement en carton, offrant une flexibilité inégalée, vous permettant de choisir la taille exacte de la boîte.</p> 
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/boite-8-flexibox-1.webp" width="300px" height="400px" alt="Une boîte format A4 à recouvrir personnalisée" loading="lazy">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" style="margin-top: 120px;" src="../images/produits/boite-7-flexibox-1.webp" width="300px" height="250px" alt="Une boîte format A4 à recouvrir personnalisée avec un dessin festif" loading="lazy">
        </div>
    </div>
             

        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Flexibox');
            ?>
        </div>
</section>
<section class="section2" id="Sacs Cadeaux">
    <div class="container">
        <h2 class="title-h3 centre-text">Sacs Cadeaux personnalisés</h2>
        <p class="paragraphe">Pour vous démarquer, vous pouvez offrir à vos clients une expérience complète de votre marque ou de votre entreprise en remettant toutes vos solutions dans un beau sac de haute qualité.</p> 
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/sac-2.webp" width="300px" height="400px" alt="Sac cadeau personnalisée de couleur verte avec un logo">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" src="../images/produits/sac-1.webp" width="300px" height="250px" alt="Sac cadeau personnalisée de couleur noire avec un logo">
        </div>
    </div>
             

        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Carrier Bags');
            ?>
        </div>
</section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
