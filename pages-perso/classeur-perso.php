<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 60px 0 0 0;">

        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/classeur-perso.webp" alt="Un bandeau présentant des finitions luxe pour livres photo" loading="lazy">
   
    <section class="section1" id="Classeur">
    <div class="container">
        <h1 class="title-h3 centre-text">Classeur personnalisé - 2 ou 4 anneaux</h1>
        <p class="paragraphe">Présentez vos documents de manière élégante et professionnelle. Le Classeur personnalisé est parfait pour organiser vos documents. Le Classeur peut être personnalisé avec n'importe quel logo ou œuvre d'art, et nous imprimons pour vous la feuille de personnalisation.</br>Choisissez entre 2 anneaux ou 4 anneaux.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/hard_cover_basic_classeur-1.webp" alt="Présentation de la conception des couvertures rigide pour classeur personnalisables" loading="lazy">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" src="../images/produits/accessoires classeur-1.webp" alt="Présentation des accessoires nécessaires à la conception des couvertures rigide pour classeur, tels que les anneaux et les rivets" loading="lazy">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic Ringbinder');
            ?>
        </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/classeur-1.webp" alt="Classeur au format A4 blanc avec des dessins d'ordinateurs et de bonhommes bleus avec couverture rigide personnalisée et système de reliure à anneaux" loading="lazy">
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/classeur-2.webp" alt="Classeur au format A4 blanc ouvert montrant le système de reliure à 2 anneaux et un document d'architecte" loading="lazy">
        </div>
        <div class="colonne-5">
            <img class="centre-div" src="../images/produits/classeur-4.webp" alt="Classeur au format A4 noir ouvert montrant le système de reliure à 2 anneaux avec des  documents déjà en place" loading="lazy">
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" src="../images/produits/classeur-3.webp" alt="Classeur au format A4 noir entre ouvert avec un logo vert personnalisé sur la couverture rigide et système de reliure à anneaux">
        </div>
    </div>

</section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
