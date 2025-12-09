<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Calendrier Mural</h1>
        <img class="centre-div pose" src="../images/bandeaux/calendrier-mural-1.webp" alt="Un bandeau présentant des calendriers muraux personnalisés">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Décorez vos murs avec un calendrier mural personnalisé avec vos photos.
        </p>
    </div>
    <section class="section1" id="Calendrier Mural pour la maison">
        <div class="container">
            
            <h2 class="title-h3 centre-text">CALENDRIER MURAL POUR LA MAISON</h2>
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 50%; height: auto;" src="../images/produits/calendrier-enfants-1.webp" alt="Présentation d'un calendrier à suspendre personnalisé avec des photos d'enfants">
                </div>
                <div class="colonne-2">
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Peel & Stick Calendar Home');
                        ?>
                    </div>
                    <p>** Ce calendrier mural est diponible dans plusieurs langues (NL, EN, DE, ES, FR, IT, PL, SK, CZ, DK, PT)</p>
                </div>
            </div> 
            </br>   
            <p class="paragraphe">Utilisez vos propres impressions photo pour créer un calendrier mural personnalisé à la maison. Un guide de calendrier amovible rend le positionnement des photographies sur le calendrier extrêmement facile. Résultats parfaits garantis.</p>
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
    <section class="section2" id="Calendrier Mural Professionnel">
        <div class="container">
            
            <h2 class="title-h3 centre-text">CALENDRIER MURAL PROFESSIONNEL</h2>
            <div class="ligne">
                <div class="colonne-1 onleft ligne">
                    <img class="centre-div" style="width: 170px; height: 272px; margin-left: 200px;" src="../images/produits/unicalendar-01.webp" alt="Présentation d'un calendrier professionnel à suspendre personnalisé avec photo représentant 1 femme qui marche dans un champ et portant une robe bleue">
                    <img class="centre-div" style="width: 245px; height: 346px; margin-left: -398px;" src="../images/produits/calendar-05.webp" alt="Présentation d'un calendrier professionnel à suspendre personnalisé avec photo représentant 3 femmes">
                    
                </div>
                <div class="colonne-2">
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Peel & Stick Calendar Pro - Wall Model');
                        ?>
                    </div>
                   
                </div>
            </div> 
            </br>   
            <p class="paragraphe">Utilisez vos propres tirages photo pour créer un calendrier mural personnalisé professionnel</p>
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>
    <section class="section1" id="Porte-affiche">
        <div class="container">
            
            <h2 class="title-h3 centre-text">PORTE-AFFICHE OU CALENDRIER EN BOIS</h2>
            <div class="ligne">
                <div class="colonne-1 onleft ligne">
                    <img class="centre-div" style="width: 204px; height: 375px; " src="../images/produits/calendrier-mural-3.webp" alt="Présentation d'un calendrier professionnel à suspendre personnalisé avec photo représentant 1 femme qui marche dans un champ et portant une robe bleue">
                    <img class="centre-div" style="width: 204px; height: 309px; margin-left: -138px; margin-top: 91px;" src="../images/produits/calendrier-mural-2.webp" alt="Présentation d'un calendrier professionnel à suspendre personnalisé avec photo représentant 3 femmes">
                    
                </div>
                <div class="colonne-2">
                    <p class="paragraphe">Ce porte-affiche stylé en bois de 8 pouces est une alternative sympa aux cadres photo classiques. Il est également très populaire pour les calendriers personnalisés. Après une petite perforation, les impressions sont maintenues par des chevilles en bois. Il ne faut que quelques secondes pour assembler le porte-affiche en bois.</p>
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Wood Hanger');
                        ?>
                    </div>
                   <p>La pince perforatrice est un outil pratique pour préparer vos impressions en les perforant avant de les insérer dans le porte-affiche en bois.</p>
                </div>
            </div> 
            </br>   
            
        </div>
        <?php include '../includes/bt-devis.php'; ?>
    </section>

</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
