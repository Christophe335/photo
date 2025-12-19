<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Infinity Personnalisé</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/mariage-1.webp" alt="Un bandeau présentant des dépliants en accordéon personnalisés">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Notre collection premium aux possibilités infinies de personnalisation.
        </p>
    </div>    
     <section class="section1" id="Album Dépliant en accordéon">
        <div class="container">
            <h2 class="title-h3 centre-text">ALBUM DÉPLIANT EN ACCORÉON personnalisé</h2>
            <p class="paragraphe">Le Photobook Infinity Lay-Flat est un nouveau et unique produit dans le monde de la photographie. 
            Avec cette technologie conviviale, vous créez une expérience utilisateur fantastique en un rien de temps !
            Les photos peuvent être imprimées en continu sur 2 pages, sans fissure ni pli visible dans la ligne de séparation.</p>
            
        </div>
        <div class="container">
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/lay-flat-1.webp" alt="Album photo en accordéon présenté ouvert montrant des photos imprimées au format paysage">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-1.webp" alt="Album photo en accordéon présenté ouvert montrant des photos imprimées au format paysage">
                </div>
            </div>
            </br>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                // Afficher les produits de reliure directement
                afficherTableauProduits('Infinity Lay-Flat avec couverture rigide');
                 ?>
            </div>
        </div>
        <div>
                <div class="ligne">
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-3.webp" alt="Album photo en accordéon présentation des 2 versions fermées avec couvertures rigides">
                
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-2.webp" alt="Album photo en accordéon présenté ouvert montrant une photo imprimées au format paysage">
               
                    <img class="centre-div" src="../images/produits/infinity-lay-flat-4.webp" alt="Détail du rabat magnétique de l'album photo en accordéon">
                </div>
            </div>
    </section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
