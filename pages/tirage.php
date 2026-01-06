<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Tirage Photo</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/tirage-1.webp" alt="Un bandeau présentant des photos imprimées de différents formats disposées en éventail" loading="lazy">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Tirages photo RÉSERVÉ au clients qui commandent un album photo chez Bindy Studio.
        </p>
    </div>
    <section class="section1" id="Petit format">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirages Petit Format</h2>
            <h3>(10x15cm – 12x12cm – 13x18cm – 15x15cm – 15x20cm)</h3>
            </br>
            <p class="paragraphe">Les petits formats sont idéaux pour faire vivre vos souvenirs au quotidien. Discrets et élégants, ils se glissent parfaitement dans un album photo, un cadre ou une boîte à souvenirs. Chez Bindy Studio, chaque tirage petit format bénéficie d’une impression de haute qualité, avec des couleurs fidèles, des contrastes équilibrés et un papier soigneusement sélectionné pour sublimer vos images.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/tirage-petit-1.webp" alt="Ensemble de tirages photo petit format disposés en éventail" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/tirage-petit-2.webp" alt="Ensemble de tirages photo petit format disposés en éventail" loading="lazy">
                </div>
            </div>  
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau.php';
                            
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Tirage Photo Petit Format', false);
                    ?>
                </div> 
            </br> 
        </div>
    </section>
    <section class="section2" id="Grand format">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirage Grand Format</h2>
            <h3>(20x20cm – 20x25cm – 20x30cm – 21x21cm – 25x25cm)</h3>
            </br>
            <p class="paragraphe">Donnez plus d’impact à vos photos avec nos tirages grand format. Ils offrent une présence visuelle forte tout en conservant une grande finesse de détail. Pensés pour être exposés, ces formats mettent en valeur vos plus beaux clichés et transforment vos images en véritables éléments de décoration.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/tirage-grand-1.webp" alt="un album photo ouvert présentant des tirages photo grand format" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/tirage-grand-2.webp" alt="des tirages photo grand format d'un couple et de photos de la ville de Paris" loading="lazy">
                </div>
            </div>  
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau.php';
                            
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Tirage Photo Grand Format', false);
                    ?>
                </div> 
            </br> 
        </div>
    </section>
   <section class="section1" id="Format A4 et A3">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirage Format A4 et A3</h2>
            <h3>(21x29,7cm – 29,7x42cm)</h3>
            </br>
            <p class="paragraphe">Les formats A4 et A3 sont parfaits pour ceux qui souhaitent un rendu spectaculaire. Grâce à leur grande surface d’impression, chaque détail prend vie et chaque photo raconte pleinement son histoire. Imprimés avec le savoir-faire Bindy Studio, ces tirages offrent une profondeur exceptionnelle et un rendu professionnel, digne d’une exposition.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/impression-toile-1.webp" width="200" height="auto" alt="Photo d'un cheval imprimé sur une toile de format A3" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/panneau photo-3.webp"  width="200" height="auto" alt="Photo d'un hôtel de luxe dans les tropiques imprimée sur un panneau rigide de format A3" loading="lazy">
                </div>
            </div>  
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau.php';
                            
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Tirage Photo A4 et A3', false);
                    ?>
                </div> 
            </br> 
        </div>
    </section>
    <section class="section2" id="Sur mesure">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirage Format sur mesure</h2>
            <h3>Feuille pré-encollée pour couverture panoramique</h3>
            </br>
            <p class="paragraphe">Parce que certaines images méritent un format unique, Bindy Studio vous propose des tirages entièrement sur mesure. Ce service est spécialement conçu pour les couvertures panoramiques d’albums photo ou les projets créatifs nécessitant des dimensions spécifiques. Nous adaptons le format à votre image afin de garantir un rendu harmonieux, sans compromis sur la qualité ou l’émotion.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/hardcoverbasic-1.webp" width="300" height="auto" alt="Présentation de la feuille à imprimer pour une couverture panoramique" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/hard_cover_basic_clamp-1.webp" width="220" height="auto" alt="Présentation de la feuille à imprimer pour une couverture panoramique avec pince" loading="lazy">
                </div>
            </div>  
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau.php';
                            
                    // Afficher les produits de reliure directement
                    afficherTableauProduits('Tirage panoramique', false);
                    ?>
                </div> 
            </br> 
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
