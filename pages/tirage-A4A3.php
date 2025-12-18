<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <section class="section1" id="Grand format">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirage Format A4 et A3</h2>
            <h3>(21 x 29,7 cm – 29,7 x 42 cm)</h3>
            </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/impression-toile-1.webp" width="150" height="auto" alt="Photo d'un cheval imprimé sur une toile de format A3">
                    <img class="centre-div" src="../images/produits/panneau photo-3.webp"  width="150" height="auto" alt="Photo d'un hôtel de luxe dans les tropiques imprimée sur un panneau rigide de format A3">
                </div>
                <div class="colonne-2">
                    <p class="paragraphe">Les formats A4 et A3 sont parfaits pour ceux qui souhaitent un rendu spectaculaire. Grâce à leur grande surface d’impression, chaque détail prend vie et chaque photo raconte pleinement son histoire. Imprimés avec le savoir-faire Bindy Studio, ces tirages offrent une profondeur exceptionnelle et un rendu professionnel, digne d’une exposition.</p>
                    <h1>à partir de 6.99 € l'unité</h1>
                
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau2.php';
                            
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Tirage Photo A4 et A3', false);
                    ?>
                </div> 
                </div>
            </div>  
                
            </br> 
        </div>
    </section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-produits.js"></script>

<?php include '../includes/footer.php'; ?>
