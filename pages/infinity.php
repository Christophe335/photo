<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Photo Book Infinity</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/mariage-1.webp" alt="Un bandeau présentant des dépliants en accordéon personnalisés" loading="lazy">  
     <section class="section1" id="Photo Book Infinity">
        <div class="container">
            <h2 class="title-h3 centre-text">Photo Book Infinity Lay-Flat</h2>
            <p class="paragraphe">Le Photo Book Infinity Lay-Flat est un nouveau et unique produit dans le monde de la photographie. 
            Avec cette technologie conviviale, vous créez une expérience utilisateur fantastique en un rien de temps !
            Les photos peuvent être imprimées en continu sur 2 pages, sans fissure ni pli visible dans la ligne de séparation.</p>
            
        </div>
        <div class="container">
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/lay-flat-1.webp" alt="Album photo en accordéon présenté ouvert montrant des photos imprimées au format paysage" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-1.webp" alt="Album photo en accordéon présenté ouvert montrant des photos imprimées au format paysage" loading="lazy">
                </div>
            </div>
            </br>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-choix.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Infinity Lay-Flat sans couverture');
                 ?>
            </div>
            <div class="tableau-container">
                <?php
                // Afficher les produits de reliure directement
                afficherTableauProduits('Infinity Lay-Flat avec couverture rigide');
                 ?>
            </div>
            <div class="tableau-container">
                <?php    
                // Afficher les produits de reliure directement
                afficherTableauProduits('Infinity Lay Flat Hard Cover Basic 21,6x21,6 cm');
                 ?>
            </div>
            
        </div>
        <div>
                <div class="ligne">
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-3.webp" alt="Album photo en accordéon présentation des 2 versions fermées avec couvertures rigides" loading="lazy">
                
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-2.webp" alt="Album photo en accordéon présenté ouvert montrant une photo imprimées au format paysage" loading="lazy">
               
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-4.webp" alt="Détail du rabat magnétique de l'album photo en accordéon" loading="lazy">
                </div>
            </div>
        </br>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
