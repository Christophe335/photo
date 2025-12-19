<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <section class="section1" id="Sure mesure">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirage Format sur mesure</h2>
            <h3>Feuille pré-encollée pour couverture panoramique</h3>
            
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/hardcoverbasic-1.webp" width="300" height="auto" alt="Présentation de la feuille à imprimer pour une couverture panoramique">
                    <img class="centre-div" src="../images/produits/hard_cover_basic_clamp-1.webp" width="220" height="auto" alt="Présentation de la feuille à imprimer pour une couverture panoramique avec pince">
                </div>
                <div class="colonne-2">
                    <p class="paragraphe">Parce que certaines images méritent un format unique, Bindy Studio vous propose des tirages entièrement sur mesure. Ce service est spécialement conçu pour les couvertures panoramiques d’albums photo ou les projets créatifs nécessitant des dimensions spécifiques. Nous adaptons le format à votre image afin de garantir un rendu harmonieux, sans compromis sur la qualité ou l’émotion.</h1>
                    <h1>à partir de 1.90 € HT l'unité</h1>
                    <p>Commande minimum 25 unités</p>
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau2.php';
                            
                    // Afficher les produits de reliure directement (quantité par défaut 25)
                    afficherTableauProduits('Tirage panoramique', false, 25);
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
