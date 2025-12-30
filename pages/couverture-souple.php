<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Couverture Souple</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/couverture-souple-1.webp" alt="Un bandeau présentant des couvertures souples personnalisées">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Une option élégante et pratique avec couverture souple personnalisée.
        </p>
    </div>
    <section class="section1" id="Couverture Souple">
        <div class="container">
            
             <h2 class="title-h3 centre-text">COUVERTURE SOUPLE</h2>
                </br>
                <p class="paragraphe">La couverture Flex est la solution idéale pour toute présentation de documents. Elle dispose d'une feuille parfaitement transparente d'un côté et d'une finition mate givré de l'autre. C'est votre choix d'impression.</p>
                </br>
                <!-- tableau 1 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 270px; height: 400px; margin-top: 200px;" src="../images/produits/couverture-souple-1.webp" alt="Couverture souple transparente style crystal avec document à l'intérieur">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Crystal Flex Cover');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
                <!-- tableau 2 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 270px; height: 400px; margin-top: 200px;" src="../images/produits/unibackcover.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
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
                    <img class="centre-div" style="width: 135px; height: 200px; margin-top: 200px;" src="../images/produits/unibackcover.webp" alt="Couverture souple Uniback doté d'un guide en papier détachable">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Uniback Cover A5');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
            </br>   
            <p class="paragraphe">Les couvertures Mat Flex sont la solution idéale pour toute présentation de documents. Elles présentent un fini semi-transparent mat givré des deux côtés de la couverture. Avec les machines de reliure thermique, vous pouvez relier et sertir le dos pour un ajustement optimal.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 270px; height: 400px; margin-top: 200px;" src="../images/produits/couverture-souple-2.webp" alt="Couverture souple mat avec document à l'intérieur">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Mat Flex Cover');
                        ?>
                    </div>
                </div>
            </div> 
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
    
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
