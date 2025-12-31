<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0 0 0;">
    <div class="container">
        <h1 class="title-h1 bull">Couverture Rigide</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/couverture-rigide-1.webp" alt="Un bandeau présentant des couvertures rigides personnalisées">
    <section class="section1" id="Couverture Rigide">
        <div class="container">
            <h2 class="title-h3 centre-text">COUVERTURE RIGIDE</h2>
                </br>
                <p class="paragraphe">Ces couvertures rigides permettent aux documents de se démarquer de la concurrence grâce à leur robustesse et leur finition haut de gamme.</p>
                </br>
                <!-- tableau 1 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 270px; height: 310px; margin-top: 200px;" src="../images/produits/hardcover-1.webp" alt="Couverture rigide de couleur noir avec feuilles blanches à l'intérieur">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Unihard Cover A4');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
                <!-- tableau 2 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" style="width: 135px; height: 170px; margin-top: 20px;" src="../images/produits/hardcover-3.webp" alt="Couverture rigide de couleur blanche avec feuilles blanches à l'intérieur">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau.php';
                        
                        // Afficher les produits de reliure directement
                        afficherTableauProduits('Unihard Cover A5');
                        ?>
                    </div>
                    <p>** Choisissez en cliquant la bonne couleur</p>
                </div>
            </div> 
        </div>
    </section>
    <section class="section2" id="Carnet de Notes">
    <div class="container">
        <h2 class="title-h3 centre-text">Carnet de notes</h2>
        <p class="paragraphe">Ce carnet, avec une couverture rigide au toucher cachemire, est un plaisir à écrire. Le papier pointillé est agréablement lisse, de sorte que l'encre ne bave pas. Personnalisez votre couverture rigide avec votre propre logo d'entreprise et le nom de votre employé. Ajouter de belles pages en utilisant <a href="#papier">notre papier</a> en 120 ou 160 g/m².</p>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Thermal Hard Cover Kashmir Touch');
            ?>
        </div>
        <p class="paragraphe">*** Choisissez bien votre couleur dans la colonne <span style="color: #ff5500;text-transform: uppercase;">couleur</span></p>
        <div class="ligne">
            <div class="colonne-3 onleft">
                <div class="ligne">
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-01.webp" alt="Couleur Blossom (une variété de rose)">
                    <p>Rose</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-02.webp" alt="Couleur Charcoal (un noir)">
                    <p>Charbon</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-03.webp" alt="Couleur Lime (vert citron)">
                    <p>Citron Vert</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-04.webp" alt="Couleur Oyster (beige)">
                    <p>Huître</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-05.webp" alt="Couleur Red (rouge)">
                    <p>Rouge</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-06.webp" alt="Couleur Silver (argent)">
                    <p>Argent</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-07.webp" alt="Couleur Turqoise (bleu turquoise)">
                    <p>Turqoise</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-08.webp" alt="Couleur Ultra-marine (bleu marine)">
                    <p>Ultra-marine</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-09.webp" alt="Couleur Naranja (orange)">
                    <p>Orange</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-10.webp" alt="Couleur Marron">
                    <p>Marron</p></div>
                </div>
            </div>
            <div class="colonne-3 onright">
                <img src="../images/produits/carnet-de-note-personnalise-1.webp" alt="Présentation de la gamme de carnets de notes personnalisés à couverture rigide">
            </div>
        </div>
        <p class="paragraphe">Les couvertures thermiques écologiques sont des livres à couverture rigide haut de gamme, respectueux de l'environnement. Idéales pour créer des matériaux de présentation personnalisés, durables et reliés de manière permanente. Ces couvertures avec du papier recyclé offrent un choix éco-responsable. Avec le système de reliure thermique, vous pouvez sertir le dos pour un ajustement parfait.</p>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img src="../images/produits/couverture-rigide-eco-1.webp" alt="Carnet de notes personnalisé à couverture rigide finition écologique">
        </div>
        <div class="tableau-container" id="papier">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Thermal Hard Cover A4 Eco');
            ?>
        
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('V-Paper');
            ?>
        </div>
    </div>    
    </div>
    <?php include '../includes/bt-devis.php'; ?>
</section>

<section class="section1" id="Couverture rigide 1 face">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide 1 face personnalisable</br>système Peel & Stick</h2>
        <p class="paragraphe">Les couvertures rigides Mono Hard sont des outils de personnalisation conviviales et rentables, créés en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures uniques préfabriquées vous permettent de personnaliser facilement le devant de votre HardCover. Son concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle. Les couvertures rigides sont la solution parfaite de fabrication de boîtiers pour toutes vos applications uniques ou en petite série.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/couverture-rigide-1.webp" alt="Exemple d'une couverture rigide personnalisée format paysage avec une photo représentant diverses personnes sur la face avant">
        </div>
        <div class="colonne-3 ligne onright">
            <img class="centre-div" src="../images/produits/peel-stick-hard-cover-black-1.webp" alt="Présentation de la mise en place du système autocollant pour couverture rigide personnalisée">
            <img class="centre-div" src="../images/produits/couverture-rigide-2.webp" alt="Couverture rigide personnalisée avec un dessin graphique coloré sur la face avant">
        </div>
        
    </div>
    
</br>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Mono Hard Cover');
            ?>
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & Stick Hard Cover');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Couverture rigide 2 faces">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide 2 faces personnalisables</br>système Peel & Stick</h2>
        <p class="paragraphe">Les couvertures rigides Duo sont un outil de personnalisation convivial et économique créé en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures rigides Duo uniques préfabriquées vous permettent de personnaliser facilement la police et le dos de votre HardCover. C'est un concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle.  Les couvertures rigides Duo sont la solution parfaite de fabrication de boîtiers pour toutes vos applications uniques ou en petite série.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/couverture-rigide-double-1.webp" alt="Couverture rigide format paysage avec personnalisation recto verso représentant ici une photo d'immeubles modernes">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" src="../images/produits/couverture-rigide-double-2.webp" alt="Couverture rigide format portrait avec personnalisation recto verso représentant ici une photo de deux personnes en réunion d'affaires coupées en son centre mettant ainsi chaque personnage sur une face différente">
        </div>
        
    </div>
    
</br>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Duo Hard Cover');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Couverture rigide Panorama">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama</h2>
        
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" src="../images/produits/couverture-panorama-1.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant 2 modèles différents">  
        </div>
        <div class="colonne-6">
            <p>Personnalisez le recto, le dos et le verso de votre livre avec le Panorama HardCover Les Panorama Hard Covers sont un outil de personnalisation convivial et économique créé en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures rigides uniques préfabriquées vous permettent de personnaliser facilement le recto, le dos et le verso de votre HardCover. C'est un concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle.Les Panorama Hard Covers sont la solution parfaite de fabrication de caisses pour toutes vos applications uniques ou en petite série.</p>
        </div>
        <div class="colonne-5 onright">
            <img src="../images/produits/couverture-panorama-2.webp" alt="Couverture rigide de livre photo panoramique format portrait montrant bien que l'image couvre toute la surface">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Panorama Hard Cover');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Couverture rigide Panorama personnalisée">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama personnalisée</h2>
        <p class="paragraphe">Le Hard Cover Basic est une couverture préfabriquée avec un revêtement en papier et une fine couche de colle thermofusible sur ses bords extérieurs et intérieurs. Peleman a développé le Hard Cover Basic pour fabriquer un livre relié, un classeur à anneaux ou un présentoir parfaitement fini et personnalisé, sans utiliser de colles liquides, en petites ou grandes quantités.</p>
    </div>
    <img class="centre-div" src="../images/produits/hard-cover-basic-1.webp" alt="Présentation du système de couverture rigide panoramique personnalisée Hard Cover Basic"> 
    
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic A4 Portrait');
            afficherTableauProduits('Hard Cover Basic A4 Paysage');
            afficherTableauProduits('Hard Cover Basic A5 Portrait');
            afficherTableauProduits('Hard Cover Basic A5 Paysage');
            afficherTableauProduits('Hard Cover Basic Lettersize Portrait');
            ?>
        </div>
        </br>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/panorama-1.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo d'un caméléon">  
        </div>
        
        <div class="colonne-3 onright">
            <img src="../images/produits/panorama-2.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo de maisons sur pilotis aux Maldives">
        </div>
    </div>
        
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1" id="Couverture rigide personnalisables">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide personnalisables</br>formats 10x15 cm - 20x20 cm - 30x30 cm</h2>
        <p class="paragraphe">Le Hard Cover Basic est une couverture préfabriquée avec un revêtement en papier dotée d'une fine couche de colle thermofusible sur son extérieur et ses bords intérieurs. Développé pour fabriquer un livre relié sans utilisation de colles liquides, en petites ou grandes quantités.</p>
    </div>
    <img class="centre-div" src="../images/produits/hard-cover-basic-1.webp" alt="Présentation de la conception des couvertures rigide personnalisables formats 10x15 cm - 20x20 cm - 30x30 cm">
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic');
            ?>

        </div>
 
</br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2" id="Couverture rigide clamps">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide clamps personnalisables</h2>
        <p class="paragraphe">Les couvertures Hard Cover Basic reliure clamps sont des couvertures de  reliure personnalisable à l'aide de la machine de personnalisation Hard  Cover Maker 650. Le système Clamps est une baguette plastique qui permet de relier des feuilles sans l'utilisation de relieure.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" src="../images/produits/hard_cover_basic_clamp-1.webp" alt="Présentation de la conception des couvertures rigide clamp personnalisables ">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" src="../images/produits/clamp.webp" alt="Présentation de la conception des couvertures rigide clamp">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic Clamp Binding');
            ?>
            
        </div>
    
</br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
</main>

<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
