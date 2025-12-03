<?php include '../includes/header.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Calendrier de Bureau</h1>
        <img class="centre-div pose" src="../images/bandeaux/calendrier-bureau-1.webp" alt="Un bandeau présentant des calendriers de bureau personnalisés">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Personnalisez votre espace de travail avec un calendrier de bureau unique.
        </p>
    </div>
    <section class="section1">
        <div class="container">
            
            <h2 class="title-h3 centre-text">CALENDRIER DE BUREAU</h2>
            <div class="ligne">
                <div class="colonne-1 onleft ligne">
                    <img class="centre-div" style="width: 50%; height: auto;" src="../images/produits/calendrier-bureau-1.webp" alt="Présentation d'un calendrier de bureau personnalisé avec des photos">
                    <img class="centre-div" style="width: 50%; height: auto;" src="../images/produits/calendrier-bureau-2.webp" alt="Présentation du support d'un calendrier de bureau">
                </div>
                <div class="colonne-2">
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Peel & Stick Calendar Desktop');
                        ?>
                    </div>
                    <p>** Ce calendrier de bureau est diponible dans plusieurs langues (NL, EN, DE, ES, FR, IT, PL, SK, CZ, DK, PT)</p>
                </div>
            </div> 
            </br>   
            <p class="paragraphe">Nos calendriers Peel & Stick convivial pour l'utilisateur sont déjà pré-assemblés, de sorte que vous n'avez pas besoin d'une machine de reliure. 
                        Il suffit de collecter vos impressions et de les coller sur les bandes auto-adhésives de chaque page du calendrier.</p>
            <div class="ligne">
                <div class="colonne-2">
                    <div>
                        
                        <div class="ligne">
                            <div class="tableau-container">
                                <?php
                                // IMPORTANT: Ajuster le chemin selon votre structure
                                require_once __DIR__ . '/../includes/tableau.php';
                                
                                // Afficher les produits de reliure directement
                                afficherTableauProduits('Peel & Stick Calendar Cardboard Desktop');
                                ?>
                            </div>
                            
                        </div>
                        
                    </div> 
                </div>
                <div class="colonne-1 onright">
                    <img style="width: 74%; height: auto; padding-top: 27% !important; padding-left: 15%;" class="centre-div" src="../images/produits/calendrier-bureau-3.webp" alt="Ensemble de calendriers de bureau personnalisés montrant le support seul et le calendrier en place">
                </div>
            </div>
            <div>
            <div class="onbottom">
                <img class="centre-div" src="../images/produits/unicalendar-02.webp" alt="Présentation d'un calendrier de bureau personnalisé avec des photos">
            </div>
        </div>
        <?php include '../includes/bt-devis.php'; ?>
        
    </section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
