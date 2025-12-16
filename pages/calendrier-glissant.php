<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Calendrier Glissant</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/calendrier-glissant-1.webp" alt="Un bandeau présentant des calendriers de bureau personnalisés">
    <div class="container"> 
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Un calendrier glissant pratique et élégant pour votre bureau.
        </p>
    </div>
    <section class="section1" id="Calendrier à feuilles glissantes">
        <div class="container">
            
            <h2 class="title-h3 centre-text">CALENDRIER A FEUILLES GLISSANTES</h2>
            </br>
            <p class="paragraphe">Avec notre kit innovant de calendrier mural à feuilles glissantes, c'est aussi simple que 1-2-3. Imprimez 13 de vos photos préférées, glissez-les dans les cadres préformés, et voilà, vous avez un magnifique calendrier mural unique !</p>
            </br>
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau.php';
                        
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Slide-In Calendar 8” x 10”');
                    ?>
                </div>
            </br> 
            <div class="ligne">
                <div class="colonne-1 triangle">
                    <img class="centre-div" style="width: 325px; height: 285px;padding-top: 60px;" src="../images/produits/calendrier-glissant-2.webp" alt="Calendrier glissant montrant le mécanisme d'insertion des feuilles">
                </div>
                <div class="colonne-1 ligne triangle">
                    <img class="centre-div" style="width: 133px; height: 255px; padding-top: 60px; margin-left: 52px;" src="../images/produits/calendrier-glissant-3.webp" alt="Calendrier glissant dans son support">
                    <img class="centre-div" style="width: 115px; height: 250px; padding-top: 60px; margin-left: -75px;" src="../images/produits/calendrier-glissant-4.webp" alt="Calendrier glissant vue de coté">
                </div>
                <div class="colonne-1 triangle">
                    <img class="centre-div" src="../images/produits/calendrier-glissant-5.webp" alt="Calendrier glissant montrant le retrait des feuilles">
                </div>
            </div>  
            
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
