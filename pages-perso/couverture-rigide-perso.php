<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 60px 0 0 0;">

        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/couverture-rigide-perso.webp" alt="Un bandeau présentant des couvertures rigides personnalisées" loading="lazy">
    <section class="section1" id="Couverture Rigide">
        <div class="container">
            <h1 class="title-h3 centre-text">COUVERTURE RIGIDE</h1>
                </br>
                <p class="paragraphe">Ces couvertures rigides permettent aux documents de se démarquer de la concurrence grâce à leur robustesse et leur finition haut de gamme.</p>
                </br>
                <!-- tableau 1 -->
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" width="320" height="340" style="margin-top: 200px;" src="../images/produits/CouvertureRigideA5Perso-2.webp" alt="Couverture rigide de couleur bleu et logo Carlton Cannes avec feuilles blanches à l'intérieur" loading="lazy">
                    <img class="centre-div" width="360" height="320" style="margin-top: 135px;" src="../images/produits/CouvertureRigideA5Perso-4.webp" alt="2 Couvertures rigide de couleur noir et bleu avec personnalisation" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                        
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
                    <img class="centre-div" width="250" height="300" style="margin-top: 2px;" src="../images/produits/CouvertureRigideA5Perso-3.webp" alt="Couverture rigide de couleur rose avec logo de personnalisation" loading="lazy">
                    <img class="centre-div" width="250" height="300" style="margin-top: -150px;" src="../images/produits/Hardcover-bleu-perso1.webp" alt="Couverture rigide de couleur bleu personnalisé avec feuilles blanches à l'intérieur" loading="lazy">
                </div>
                <div class="colonne-2">
                    
                    <div class="tableau-container">
                        <?php
                        // IMPORTANT: Ajuster le chemin selon votre structure
                        require_once __DIR__ . '/../includes/tableau-perso.php';
                        
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
        <h2 class="title-h3 centre-text">Carnet de notes personnalisé</h2>
        <p class="paragraphe">Ce carnet, avec une couverture rigide au toucher cachemire, est un plaisir à écrire. Le papier pointillé est agréablement lisse, de sorte que l'encre ne bave pas. Personnalisez votre couverture rigide avec votre propre logo d'entreprise et le nom de votre employé. Ajouter de belles pages en utilisant notre papier en 120 ou 160 g/m².</p>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Thermal Hard Cover Kashmir Touch');
            ?>
        </div>
        <p class="paragraphe">*** Choisissez bien votre couleur dans la colonne <span style="color: #ff5500;text-transform: uppercase;">couleur</span></p>
        <div class="ligne">
            <div class="colonne-1 onleft">
                <img class="centre-div" width="300" height="350" src="../images/produits/Hardcover-rouge-perso1.webp" alt="Présentation de la gamme de carnets de notes personnalisés à couverture rigide" loading="lazy">
            </div>
            <div class="colonne-1">
                <div class="ligne">
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-02.webp" alt="Couleur Charcoal (un noir)" loading="lazy">
                    <p>Charbon</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-06.webp" alt="Couleur Silver (argent)" loading="lazy">
                    <p>Argent</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-05.webp" alt="Couleur Red (rouge)" loading="lazy">
                    <p>Rouge</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-08.webp" alt="Couleur Ultra-marine (bleu marine)" loading="lazy">
                    <p>Ultra-marine</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-10.webp" alt="Couleur Marron" loading="lazy">
                    <p>Marron</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-09.webp" alt="Couleur Naranja (orange)" loading="lazy">
                    <p>Orange</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-01.webp" alt="Couleur Blossom (une variété de rose)" loading="lazy">
                    <p>Rose</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-07.webp" alt="Couleur Turqoise (bleu turquoise)" loading="lazy">
                    <p>Turqoise</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-03.webp" alt="Couleur Lime (vert citron)" loading="lazy">
                    <p>Citron Vert</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-04.webp" alt="Couleur Oyster (beige)" loading="lazy">
                    <p>Huître</p></div>
                </div>
            </div>
            <div class="colonne-1 onright">
                <img class="centre-div" width="350" height="200" src="../images/produits/carnet-de-note-personnalise-1.webp" alt="Présentation de la gamme de carnets de notes personnalisés à couverture rigide" loading="lazy">
            </div>
        </div>
        <p class="paragraphe">Les couvertures thermiques écologiques sont des livres à couverture rigide haut de gamme, respectueux de l'environnement. Idéales pour créer des matériaux de présentation personnalisés, durables et reliés de manière permanente. Ces couvertures avec du papier recyclé offrent un choix éco-responsable. Avec le système de reliure thermique, vous pouvez sertir le dos pour un ajustement parfait.</p>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img width="300" height="200" src="../images/produits/couverture-rigide-eco-1.webp" alt="Carnet de notes personnalisé à couverture rigide finition écologique" loading="lazy">
        </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Thermal Hard Cover A4 Eco');
            ?>
        </div>
    </div>    
</section>
<section class="section1" id="Couverture rigide 1 face">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide 1 face personnalisable</br>système Peel & Stick</h2>
        <p class="paragraphe">Les couvertures rigides Mono Hard sont des outils de personnalisation conviviales et rentables, créés en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures uniques préfabriquées vous permettent de personnaliser facilement le devant de votre HardCover. Son concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle. Les couvertures rigides sont la solution parfaite de fabrication de boîtiers pour toutes vos applications uniques ou en petite série.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-rigide-1.webp" alt="Exemple d'une couverture rigide personnalisée format paysage avec une photo représentant diverses personnes sur la face avant" loading="lazy">
        </div>
        <div class="colonne-3 ligne onright">
            <img class="centre-div" width="400" height="300" src="../images/produits/peel-stick-hard-cover-black-1.webp" alt="Présentation de la mise en place du système autocollant pour couverture rigide personnalisée" loading="lazy">
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-rigide-2.webp" alt="Couverture rigide personnalisée avec un dessin graphique coloré sur la face avant" loading="lazy">
        </div> 
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Mono Hard Cover');
            ?>
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
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
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-rigide-double-1.webp" alt="Couverture rigide format paysage avec personnalisation recto verso représentant ici une photo d'immeubles modernes" loading="lazy">
        </div>
        <div class="colonne-3 onright">
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-rigide-double-2.webp" alt="Couverture rigide format portrait avec personnalisation recto verso représentant ici une photo de deux personnes en réunion d'affaires coupées en son centre mettant ainsi chaque personnage sur une face différente" loading="lazy">
        </div> 
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Duo Hard Cover');
            ?>
        </div>
</section>
<section class="section1" id="Couverture rigide Panorama">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama</h2>
        
    </div>
    <div class="ligne">
        <div class="colonne-5 onleft">
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-panorama-1.webp" alt="Couverture rigide de livre photo panoramique format paysage montrant 2 modèles différents" loading="lazy">  
        </div>
        <div class="colonne-6">
            <p>Personnalisez le recto, le dos et le verso de votre livre avec le Panorama HardCover Les Panorama Hard Covers sont un outil de personnalisation convivial et économique créé en réponse à la demande croissante pour des couvertures rigides personnalisées. Les couvertures rigides uniques préfabriquées vous permettent de personnaliser facilement le recto, le dos et le verso de votre HardCover. C'est un concept complètement sec ne nécessitant aucun liquide, produit chimique ou colle.Les Panorama Hard Covers sont la solution parfaite de fabrication de caisses pour toutes vos applications uniques ou en petite série.</p>
        </div>
        <div class="colonne-5 onright">
            <img class="centre-div" width="400" height="300" src="../images/produits/couverture-panorama-2.webp" alt="Couverture rigide de livre photo panoramique format portrait montrant bien que l'image couvre toute la surface" loading="lazy">
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
</section>
<section class="section2" id="Couverture rigide Panorama personnalisée">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide Panorama personnalisée</h2>
        <p class="paragraphe">Le Hard Cover Basic est une couverture préfabriquée avec un revêtement en papier et une fine couche de colle thermofusible sur ses bords extérieurs et intérieurs. Peleman a développé le Hard Cover Basic pour fabriquer un livre relié, un classeur à anneaux ou un présentoir parfaitement fini et personnalisé, sans utiliser de colles liquides, en petites ou grandes quantités.</p>
    </div>
    <img class="centre-div" src="../images/produits/hard-cover-basic-1.webp" alt="Présentation du système de couverture rigide panoramique personnalisée Hard Cover Basic" loading="lazy"> 
    
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
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
        <div class="colonne-1 onleft">
            <img class="centre-div" src="../images/produits/panorama-1.webp" width="400" height="300" style="margin-top: 40px;" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo d'un caméléon" loading="lazy">  
        </div>
        <div class="colonne-1">
            <img class="centre-div" src="../images/produits/CouvertureRigidePerso-1.webp" width="400" height="400" alt="Couverture rigide de livre photo panoramique format portrait montrant un montage photo de plusieurs images d'entreprises" loading="lazy">
        </div>
        <div class="colonne-1 onright">
            <img class="centre-div" src="../images/produits/panorama-2.webp" width="400" height="300" style="margin-top: 50px;" alt="Couverture rigide de livre photo panoramique format paysage montrant la photo de maisons sur pilotis aux Maldives" loading="lazy">
        </div>
    </div>
</section>

<section class="section1" id="Couverture rigide personnalisables">
    <div class="container">
        <h2 class="title-h3 centre-text">Couverture rigide personnalisables</br>formats 10x15 cm - 20x20 cm - 30x30 cm</h2>
        <p class="paragraphe">Le Hard Cover Basic est une couverture préfabriquée avec un revêtement en papier dotée d'une fine couche de colle thermofusible sur son extérieur et ses bords intérieurs. Développé pour fabriquer un livre relié sans utilisation de colles liquides, en petites ou grandes quantités.</p>
    </div>
    <img class="centre-div" src="../images/produits/hard-cover-basic-1.webp" alt="Présentation de la conception des couvertures rigide personnalisables formats 10x15 cm - 20x20 cm - 30x30 cm" loading="lazy">
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic');
            ?>
        </div>
</section>
<section class="section2" id="Couverture rigide clamps">
    <div class="container">
        <h2 class="title-h3 centre-text">Porte Document Prestige Rigide Clamp</h2>
        <p class="paragraphe">Les couvertures Hard Cover Basic reliure clamps sont des couvertures de  reliure personnalisable à l'aide de la machine de personnalisation Hard  Cover Maker 650. Le système Clamps est une baguette plastique qui permet de relier des feuilles sans l'utilisation de relieure.</p>
    </div>
    <div class="ligne">
            <div class="colonne-3">
                <img class="centre-div" width="250" height="350" style="margin-top:40px; margin-left: 130px;" src="../images/produits/detail-clamp.webp" alt="Vue détaillée des couvertures rigide clamp" loading="lazy">
            </div>
            <div class="colonne-2">
                <div class="ligne">
                    <h3 class="posClamp1">Standard</h3>
                    <h3 class="posClamp2">Toile de Jute</h3>
                </div>
                <div class="ligne">
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-noir.webp" alt="Couleur Noir" loading="lazy">
                    <p>Noir</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-bordeaux.webp" alt="Couleur Bordeaux" loading="lazy">
                    <p>Bordeaux</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-bleu-foncé.webp" alt="Couleur Bleu Foncé" loading="lazy">
                    <p>Bleu Foncé</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-cuir-noir.webp" alt="Couleur Cuir Noir" loading="lazy">
                    <p>Cuir Noir</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-gris-anthracite.webp" alt="Couleur Gris Anthracite" loading="lazy">
                    <p>Gris</br>Anthracite</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-gris-ciment.webp" alt="Couleur Gris Ciment" loading="lazy">
                    <p>Gris</br>Ciment</p></div>
                </div>
                <div class="ligne">
                    <h3 class="posClamp1">Simili Cuir</h3>
                    <h3 class="posClamp2">Cuir Vegan</h3>
                </div>
                <div class="ligne">
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-marron.webp" alt="Couleur Marron" loading="lazy">
                    <p>Marron</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-petrol.webp" alt="Couleur Bleu Pétrol" loading="lazy">
                    <p>Bleu Pétrol</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-vert-foncé.webp" alt="Couleur Vert Foncé" loading="lazy">
                    <p>Vert Foncé</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-noir.webp" alt="Couleur Noir" loading="lazy">
                    <p>Noir</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-charme-nero.webp" alt="Couleur Gris Foncé" loading="lazy">
                    <p>Gris Foncé</p></div>
                    <div class="posCouleurClamp"><img src="../images/produits/clamp-stone-grey.webp" alt="Couleur Gris Clair" loading="lazy">
                    <p>Gris Clair</p></div>
                </div>
            </div>
        </div>
        <div class="container">
        <p class="paragraphe">Créez une couverture rigide élégante et professionnelle sans aucune machine. Avec cette couverture rigide à pince, vous recevez 25 feuilles de V-Papier haute qualité 160 grammes. Il est très facile à utiliser : regroupez vos impressions ou documents, insérez-les dans la pince puis glissez-la dans le dos. Le résultat : Une couverture rigide.</p>
    </div>
    <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Clamp Hard Cover');
            ?>
            
        </div>
    <div class="ligne">
        <div class="colonne-1 onleft">
            <img class="centre-div" width="500" height="400" src="../images/produits/hard_cover_basic_clamp-1.webp" alt="Présentation de la conception des couvertures rigide clamp personnalisables " loading="lazy">
        </div>
        <div class="colonne-1">
            <img class="centre-div" width="500" height="400" src="../images/produits/carte-vin-menu.webp" alt="Présentation de carte des vins et menus avec couvertures rigide clamp" loading="lazy">
        </div>
        <div class="colonne-1 onright">
            <img class="centre-div" width="500" height="400" src="../images/produits/clamp.webp" alt="Présentation de la conception des couvertures rigide clamp" loading="lazy">
        </div>
    </div>
        
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Hard Cover Basic Clamp Binding');
            ?>
            
        </div>
</section>

</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
