<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 60px 0 0 0;">

        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/couverture-souple-perso.webp" alt="Un bandeau présentant des couvertures souples personnalisées" loading="lazy">
    <section class="section1" id="Couverture Souple Personnalisée">
        <div class="container">
            <h1 class="title-h3 centre-text">COUVERTURE SOUPLE PERSONNALISÉE</h1>
                </br>

                            <!-- tableau 2 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 180px; height: 260px; margin-top: 150px;" src="../images/produits/PlusFlexCover-Red-perso-1.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable" loading="lazy">
                    <img class="centre-div" style="width: 420px; height: 300px; margin-top: 20px;" src="../images/produits/unibackcover-2.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Uniback Cover A4');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
                <!-- tableau 3 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <div class="ligne">
                    <img class="centre-div" width="135" height="200" style="margin-top: 200px;" src="../images/produits/PlusFlexCover XS DUITS.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable" loading="lazy">
                    <img class="centre-div"  width="135" height="200" style="margin-top: 200px;" src="../images/produits/unibackcover-3.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable" loading="lazy">
                    </div>
                    
                    <img class="centre-div"  width="200" height="300" style="margin-top: 20px;" src="../images/produits/PlusFlexCover-Vert-perso-1.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Uniback Cover A5');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
                <p class="paragraphe">Parfois, nous devons aller vite et voyager léger. Notre couverture souple est votre compagnon idéal pour des couvertures professionnelles, élégantes et pratiques. Créez de puissants outils de marque avec votre propre design corporate ou votre œuvre d'art de choix. Un aspect premium sans le poids d'une couverture rigide. Disponible avec finition brillante, mate et soft-touch pour laisser une impression durable sur vos clients et prospects.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/couverture-souple-4.webp" alt="Couverture souple transparente style crystal avec document à l'intérieur" loading="lazy">
                </div>
                <div class="colonne-1">
                    <div class="ligne">
                        <img class="centre-div" style="width: 80px; height: 80px; margin: 10px;" src="../images/produits/couverture-souple-detail-1.webp" alt="Couverture souple mat avec document à l'intérieur" loading="lazy">
                        <div>
                            <h3 class="titre-h3">Flexible</h3>
                            <p class="interp">Le carnet flexible parfait, fabriqué dans un matériau souple, flexible et robuste.</p>
                        </div>
                    </div>
                    <div class="ligne">
                        <img class="centre-div" style="width: 80px; height: 80px; margin: 10px;" src="../images/produits/couverture-souple-detail-2.webp" alt="Couverture souple mat avec document à l'intérieur" loading="lazy">
                        <div>
                            <h3 class="titre-h3">Couleurs</h3>
                            <p class="interp">Trois couleurs professionnelles pour s’adapter à tout profil d’entreprise.
</p>
                        </div>
                    </div>
                    <div class="ligne">
                        <img class="centre-div" style="width: 80px; height: 80px; margin: 10px;" src="../images/produits/couverture-souple-detail-3.webp" alt="Couverture souple mat avec document à l'intérieur" loading="lazy">
                        <div>
                            <h3 class="titre-h3">Personnalisation</h3>
                            <p class="interp">Personnalisez la couverture avec l’imprimante à plat. Choix de nombreuses couleurs.</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="titre-h2">Couleurs</h3>
                        <div class="ligne" style="margin-top: -20px;">
                            <div class="petiteVignette">
                                <p class="interp"></br>Black</p>
                                <img class="centre-div" src="../images/produits/couverture-souple-couleur-1.webp" alt="Couleur Noir" loading="lazy">
                            </div>
                            <div class="petiteVignette">
                                <p class="interp">White</br> soft touch</p>
                                <img class="centre-div" src="../images/produits/couverture-souple-couleur-2.webp" alt="Couleur Noir" loading="lazy">
                            </div>
                            <div class="petiteVignette">
                                <p class="interp"></br>Dark Blue</p>
                                <img class="centre-div" src="../images/produits/couverture-souple-couleur-3.webp" alt="Couleur Noir" loading="lazy">
                            </div>
                            <div class="petiteVignette">
                                <p class="interp"></br>Full colour</p>
                                <img class="centre-div" src="../images/produits/couverture-souple-couleur-4.webp" alt="Couleur Noir" loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>
                
                 <div class="colonne-1 onright">
                    <img class="centre-div" src="../images/produits/couverture-souple-personnalise-1.webp" alt="Couverture souple personnalisé" loading="lazy">
                    <h3 class="titre-h3">Personnalisez votre propre création avec la couverture souple en couleur.</h3>
                </div>
            </div> 
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('LAY-FLAT FLEX COVERS');
                        ?>
                    </div>
    </section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
