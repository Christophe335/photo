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
            <img class="centre-div" width="350" height="250" src="../images/produits/boite-10.webp" alt="Boîte avec couvercle personnalisé" loading="lazy">
        </div>
        <div class="colonne-6">
            <h3 class="title-h3 centre-text">Couleurs disponibles</h3>
            <div class="ligne" style="margin-left: 20%;">
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-1.webp" width="50" height="60" alt="Couleur de boite Pastel blue" loading="lazy">
                    <p>Bleu Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-2.webp" width="50" height="60" alt="Couleur de boite Pastel pink" loading="lazy">
                    <p>Rose Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-3.webp" width="50" height="60" alt="Couleur de boite Pastel yellow" loading="lazy">
                    <p>Jaune Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-4.webp" width="50" height="60" alt="Couleur de boite Pastel beige" loading="lazy">
                    <p>Beige Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-5.webp" width="50" height="60" alt="Couleur de boite Pastel green" loading="lazy">
                    <p>Vert Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-6.webp" width="50" height="60" alt="Couleur de boite Pastel purple" loading="lazy">
                    <p>Violet Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-7.webp" width="50" height="60" alt="Couleur de boite Blanc touché doux" loading="lazy">
                    <p>Blanc touché doux</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-8.webp" width="50" height="60" alt="Couleur de boite Noir" loading="lazy">
                    <p>Noir</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-10.webp" width="50" height="60" alt="Couleur de boite Gris stressé" loading="lazy">
                    <p>Gris stressé</p> 
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
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" width="250" height="200" style=" transform: scaleX(-1);" src="../images/produits/prestigeBox-1.webp" alt="Une boîte format A4 recouverte d'une personnalisation pour architecte représentant une serrure avec une clé dedans et le porte clé est en forme de petite maison" loading="lazy">
        </div>
        <div class="colonne-1">
            <img class="centre-div" width="320" height="250" style="margin-top: 50px;" src="../images/produits/prestigeBox-5.webp"  alt="Une boîte format A4 personnalisée avec un logo en dorure pour la boutique de luxe d'un hotel" loading="lazy">
        </div>
        <div class="colonne-1 onright">
            <img class="centre-div" width="250" height="200" src="../images/produits/prestigeBox-3.webp"  alt="Une boîte format A4 recouverte d'une personnalisation représentant une rose posée sur une planche" loading="lazy">
        </div>
    </div>
</section>
<section class="section2" id="Coffret Prestige format A4 90mm">
    <div class="container">
        <h2 class="title-h3 centre-text">Coffret Prestige format A4 90mm personnalisée</h2>
    </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" width="320" height="320" src="../images/produits/boite-6.webp" alt="Boîte avec couvercle personnalisé" loading="lazy">
        </div>   
        <div class="colonne-6">
            <h3 class="title-h3 centre-text">Couleurs disponibles</h3>
            <div class="ligne" style="margin-left: 20%;">
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-1.webp" width="50" height="60" alt="Couleur de boite Bleu pastel" loading="lazy">
                    <p>Bleu Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-2.webp" width="50" height="60" alt="Couleur de boite Rose pastel" loading="lazy">
                    <p>Rose Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-3.webp" width="50" height="60" alt="Couleur de boite Jaune pastel" loading="lazy">
                    <p>Jaune Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-4.webp" width="50" height="60" alt="Couleur de boite Beige pastel" loading="lazy">
                    <p>Beige Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-5.webp" width="50" height="60" alt="Couleur de boite Vert pastel" loading="lazy">
                    <p>Vert Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-6.webp" width="50" height="60" alt="Couleur de boite Violet pastel" loading="lazy">
                    <p>Violet Pastel</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-7.webp" width="50" height="60" alt="Couleur de boite Blanc touché doux" loading="lazy">
                    <p>Blanc touché doux</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-8.webp" width="50" height="60" alt="Couleur de boite Noir" loading="lazy">
                    <p>Noir</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-10.webp" width="50" height="60" alt="Couleur de boite Gris stressé" loading="lazy">
                    <p>Gris stressé</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-29.webp" width="50" height="60" alt="Couleur de boite photo Océan" loading="lazy">
                    <p>Océan</p> 
                </div>
                <div class="posCouleurBoite">
                    <img class="centre-div" src="../images/produits/boite-couleur-211.webp" width="50" height="60" alt="Couleur de boite photo Fleur" loading="lazy">
                    <p>Fleur</p> 
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
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" width="330" height="220" src="../images/produits/prestigeBox-4.webp" alt="Une boîte format A4 recouverte d'une personnalisation pour architecte représentant une serrure avec une clé dedans et le porte clé est en forme de petite maison" loading="lazy">
        </div>
        <div class="colonne-1">
            <img class="centre-div" width="290" height="200" src="../images/produits/prestigeBox-2.webp" alt="Une boîte format A4 personnalisée avec un logo en dorure pour la boutique de luxe d'un hotel" loading="lazy">
        </div>
        <div class="colonne-1 onright">
            <img class="centre-div" width="330" height="220" src="../images/produits/prestigeBox-6.webp" alt="Une boîte format A4 recouverte d'une personnalisation représentant une rose posée sur une planche" loading="lazy">
        </div>
    </div>
</section>
<section class="section1" id="Coffret Prestige Flexibox">
    <div class="container">
        <h2 class="title-h3 centre-text">Coffret Prestige Flexibox personnalisée</h2>
        <p class="paragraphe">Une solution écologique, entièrement en carton, offrant une flexibilité inégalée, vous permettant de choisir la taille exacte de la boîte.</p> 
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" width="300" height="200" src="../images/produits/boite-8-flexibox-1.webp" alt="Une boîte format A4 à recouvrir personnalisée" loading="lazy">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" width="300" height="200" style="margin-top: 120px;" src="../images/produits/boite-7-flexibox-1.webp" alt="Une boîte format A4 à recouvrir personnalisée avec un dessin festif" loading="lazy">
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
            <img class="centre-div" width="300" height="300" src="../images/produits/sac-2.webp"  alt="Sac cadeau personnalisée de couleur verte avec un logo">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" width="300" height="300" src="../images/produits/sac-1.webp" alt="Sac cadeau personnalisée de couleur noire avec un logo">
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
