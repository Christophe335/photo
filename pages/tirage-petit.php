<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <section class="section1" id="Petit format">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirages Petit Format</h2>
            <h3>(10 x 15 cm – 12 x 12 cm – 13 x 18 cm – 15 x 15 cm – 15 x 20 cm)</h3>
            </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/tirage-petit-1.webp" alt="Ensemble de tirages photo petit format disposés en éventail">
                    <img class="centre-div" src="../images/produits/tirage-petit-2.webp" alt="Ensemble de tirages photo petit format disposés en éventail">
                </div>
                <div class="colonne-2">
                    <p class="paragraphe">Les petits formats sont idéaux pour faire vivre vos souvenirs au quotidien. Discrets et élégants, ils se glissent parfaitement dans un album photo, un cadre ou une boîte à souvenirs. Chez Bindy Studio, chaque tirage petit format bénéficie d’une impression de haute qualité, avec des couleurs fidèles, des contrastes équilibrés et un papier soigneusement sélectionné pour sublimer vos images.</p>
                    <h1>à partir de 0.33 € HT l'unité</h1>
                    <p>Commande minimum 10 unités</p>
                
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau2.php';
                            
                    // Afficher les produits de reliure directement (quantité par défaut 10)
                    afficherTableauProduits('Tirage Photo Petit Format', false, 10);
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
