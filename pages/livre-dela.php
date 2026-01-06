<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Livre Photo Dela</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/dela-1.webp" alt="Un bandeau présentant des livres photo delà personnalisés" loading="lazy">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Notre collection premium de livres photo avec finitions luxueuses.
        </p>
    </div>   
    <section class="section1" id="Livre photo Dela">
        <div class="container">
            <h2 class="title-h3 centre-text">Livre photo Dela commémoratif A4</h2>
            <p class="paragraphe">Dans le monde numérique d’aujourd’hui, les images jouent un rôle  crucial, tant pour les entreprises que pour les particuliers. Nous  communiquons avec des images. 
            Avec nos smartphones, nous capturons sans  effort des moments et les partageons sur les réseaux sociaux, créant  ainsi une mosaïque numérique de nos activités. 
            Pourtant, il existe un  attrait unique et durable pour les images tangibles qui ne peut être  ignoré. 
            Ressentez son poids et sa texture, ou accrochez-le fièrement  dans votre maison ou votre bureau.</p>
            <p class="paragraphe">Créez vous-même ce livre photo haut de gamme grâce à l’éditeur de  photos Peleman, qui propose de nombreux modèles prêts à l’emploi. 
            Il  suffit de télécharger les photos, de commander et le tour est joué. Un  souvenir tangible d’une grande valeur émotionnelle pour les proches.  
            CONSEIL : commandez plusieurs livres photo, par exemple un pour chaque  être cher.
            90 photos maximum</p>
        </div>
        <div class="container">
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/dela-1.webp" alt="Livre photo Dela fermé présentant la face avant avec sa personnalisation" loading="lazy">
                </div>
                <div class="colonne-1">
                    <img class="centre-div" src="../images/produits/dela-open-1.webp" alt="Livre photo Dela, positionné ouvert montrant une double page avec des photos de paysages" loading="lazy">
                </div>
                <div class="colonne-1 onright">
                    <img class="centre-div" src="../images/produits/dela-2.webp" alt="Livre photo Dela entre-ouvertprésentant la face avant avec sa personnalisation" loading="lazy">
                </div>
            </div>
            </br>
            <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau.php';
                
                // Afficher les produits de reliure directement
                afficherTableauProduits('PhotoBookDela');
                ?>
            </div>
        </div>
        </br>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
