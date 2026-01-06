<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 60px 0 0 0;">

        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/luxe-perso.webp" alt="Un bandeau présentant des finitions luxe pour livres photo" loading="lazy">
    <section class="section1" id="Finition Luxe">
        <div class="container">
            
            <h1 class="title-h3 centre-text">FINITION LUXE Personnalisée</h1>
            </br>
            <p class="paragraphe">La présentation d’un certificat, diplôme ou tout document de congratulation mérite d’être protégée correctement et présentée dans un style élégant. Avec nos couvertures rigides possédant des coins renforcés et une personnalisation par film de dorure, vous aurez sous la main un produit d’excellente qualité.</p>
            </br>
            <div class="ligne">
                <div class="colonne-3 onleft">
                    <img class="centre-div" src="../images/produits/luxe-1.webp" alt="3 types de finitions luxe au format portrait couleur aluminium, blanc et noir" loading="lazy">
                </div>
                <div class="colonne-3 onright">
                    <img class="centre-div" src="../images/produits/luxe-2.webp" alt="Présentation de 2 dipômes version portrait avec une finition luxe" loading="lazy">
                </div>
            </div>  
            <div class="ligne">
                <div class="colonne-4 onleft">
                    <img class="centre-div" style="margin-top: 160px;" src="../images/produits/luxe-3.webp" alt="Support de certificat finition luxe au format portrait de couleur gris claire avec dorure en or" loading="lazy">
                </div>
                <div class="colonne-2">
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                            
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Certificate Holder');
                        ?>
                    </div>
                </div>
                <div class="colonne-4 onright">
                    <img class="centre-div" style="margin-top: 160px;" src="../images/produits/luxe-4.webp" alt="Support de certificat finition luxe sans coins de protection avec une finition écologique" loading="lazy">
                </div>
            </div>  
            </br> 
        </div>
    </section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
