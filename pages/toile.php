<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Impression sur Toile</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/photo-sur-toile-1.webp" alt="Un bandeau présentant des impressions sur toile personnalisées" loading="lazy">
    <section class="section1" id="Impression sur Toile">
        <div class="container">
            
            <h2 class="title-h3 centre-text">IMPRESSION SUR TOILE</h2>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/impression-toile-1.webp" alt="Impression sur toile d'un cheval" loading="lazy">
                    <img class="centre-div" src="../images/produits/impression-toile-3.webp" alt="Impression sur toile sur une plage une femme avec un voile rouge fait du cheval" loading="lazy">
                </div>
                <div class="colonne-2">
                    <p>La popularité d'une photo ou d'un dessin sur toile s'explique très simplement : elle transforme une photo ou une image personnelle en oeuvre d'art. C'est pourquoi nos cadres sur toile, faciles à monter, sont appréciés depuis des années comme panneaux muraux dans divers intérieurs, des salons ou cafés aux magasins, bureaux et lieux d'événements. Nos toiles sont disponibles en format 28x36 cm 11"x14".</p>
                    <div class="ligne">
                        <img class="centre-div" src="../images/produits/toile-1.webp" alt="Diverses impression sur toile sur une étagère et au mur" loading="lazy">
                        <img class="centre-div" src="../images/produits/impression-toile-4.webp" alt="Au dessus d'un canapé gris, le mur dispose de plein d'impressions sur toile" loading="lazy">
                        <img class="centre-div" src="../images/produits/toile-2.webp" alt="Détail d'un coin d'mpression sur toile montrant bien l'épaisseur de l'ouvrage" loading="lazy">
                    </div>
                    <p class="paragraphe">La dernière nouveauté de notre gamme d'art mural permet aux utilisateurs de créer leurs propres pièces de décoration en moins d'une minute. Idéal pour les détaillants, les boutiques de cadeaux, les photographes, les amateurs ainsi que les décorateurs professionnels, ces supports transformeront n'importe quelle photo, dessin ou œuvre d'art créative en impressions sur toile personnalisées. Contrairement à d'autres solutions à cadre en bois sur le marché qui nécessitent souvent l'aide d'un professionnel qualifié pour tendre et envelopper le matériau de la toile, le Canvas Frame peut être assemblé par n'importe quel utilisateur - sans stress.</p>
                    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Canvas');
            ?>
        </div>

        <?php include '../includes/bt-devis.php'; ?>
        
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
