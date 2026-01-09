<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Portfolio Prestige</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/ban-portfolio.webp" alt="Un bandeau présentant des finitions luxe pour livres photo" loading="lazy">
    <section class="section2" id="Portfolio et Classeur">
        <div class="container"> 
            <h2 class="title-h3 centre-text">PORTFOLIO PRESTIGE / CLASSEUR À 2 ANNEAUX</h2>
                </br>
                <p class="paragraphe">Cette boîte de rangement professionnelle présente et stocke vos documents et livres reliés de manière pratique et élégante. Disponible en 2 hauteurs de clip standard 15-30 mm/espace de rangement.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" width="280" height="280" src="../images/produits/luxe-5.webp" alt="Portfolio à 2 anneaux de couleur blanc perle" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-choix.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('PortFolio A4');
                        ?>
                    </div>
                </div>
            </div> 
            </br>  
             
            <p class="paragraphe">Le PortFolio Prestige DuoBinder, un classeur à anneaux et une boîte de rangement en un seul, vous offre le meilleur des deux mondes. Vos présentations n'ont jamais été aussi belles. Parlons-en du professionnel : le DuoBinder peut être personnalisé avec n'importe quel logo ou œuvre d'art.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" width="250" height="250" src="../images/produits/luxe-6.webp" alt="Classeur à 2 anneaux et en même temps boite de rangement" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('DuoBinder A4');
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
