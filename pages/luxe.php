<?php include '../includes/header.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Finition Luxe</h1>
        <img class="centre-div pose" src="../images/bandeaux/luxe-1.webp" alt="Un bandeau présentant des finitions luxe pour livres photo">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Optez pour nos finitions luxe pour un rendu exceptionnel et premium.
        </p>
    </div>
    <section class="section1">
        <div class="container">
            
            <h2 class="title-h3 centre-text">FINITION LUXE</h2>
            </br>
            <p class="paragraphe">La présentation d’un certificat, diplôme ou tout document de congratulation mérite d’être protégée correctement et présentée dans un style élégant. Avec nos couvertures rigides possédant des coins renforcés et une personnalisation par film de dorure, vous aurez sous la main un produit d’excellente qualité.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/luxe-1.webp" alt="3 types de finitions luxe au format portrait couleur aluminium, blanc et noir">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/luxe-2.webp" alt="Présentation de 2 dipômes version portrait avec une finition luxe">
                </div>
            </div>  
            <div class="ligne">
                <div class="colonne-4 onleft">
                    <img class="centre-div" style="margin-top: 160px;" src="../images/produits/luxe-3.webp" alt="Support de certificat finition luxe au format portrait de couleur gris claire avec dorure en or">
                </div>
                <div class="colonne-2">
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                            
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Certificate Holder');
                        ?>
                    </div>
                </div>
                <div class="colonne-4 onright">
                    <img class="centre-div" style="margin-top: 160px;" src="../images/produits/luxe-4.webp" alt="Support de certificat finition luxe sans coins de protection avec une finition écologique">
                </div>
            </div>  
            </br> 
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
    <section class="section2">
        <div class="container">
            
            <h2 class="title-h3 centre-text">PORTFOLIO / CLASSEUR À 2 ANNEAUX</h2>
                </br>
                <p class="paragraphe">Cette boîte de rangement professionnelle présente et stocke vos documents et livres reliés de manière pratique et élégante. Disponible en 2 hauteurs de clip standard 15-30 mm/espace de rangement.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/luxe-5.webp" alt="Portfolio à 2 anneaux de couleur blanc perle">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('PortFolio A4');
                        ?>
                    </div>
                </div>
            </div> 
            </br>   
            <p class="paragraphe">Le DuoBinder, un classeur à anneaux et une boîte de rangement en un seul, vous offre le meilleur des deux mondes. Vos présentations n'ont jamais été aussi belles. Parlons-en du professionnel : le DuoBinder peut être personnalisé avec n'importe quel logo ou œuvre d'art.</p>
                </br>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/luxe-6.webp" alt="Classeur à 2 anneaux et en même temps boite de rangement">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
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
