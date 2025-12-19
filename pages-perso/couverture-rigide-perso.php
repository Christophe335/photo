<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Couverture Rigide Personnalisée</h1>
    </div>
        <img style="width: 100%;" class="centre-div pose" src="../images/bandeaux/couverture-rigide-1.webp" alt="Un bandeau présentant des couvertures rigides personnalisées">
    <div class="container">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            La durabilité et l'élégance avec nos couvertures rigides de qualité.
        </p>
    </div>
    <section class="section1" id="Carnet de Notes">
    <div class="container">
        <h2 class="title-h3 centre-text">Carnet de notes personnalisé</h2>
        <p class="paragraphe">Ce carnet, avec une couverture rigide au toucher cachemire, est un plaisir à écrire. Le papier pointillé est agréablement lisse, de sorte que l'encre ne bave pas. Personnalisez votre couverture rigide avec votre propre logo d'entreprise et le nom de votre employé.</p>
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
            <div class="colonne-3 onleft">
                <div class="ligne">
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-01.webp" alt="Couleur Blossom (une variété de rose)">
                    <p>Blossom</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-02.webp" alt="Couleur Charcoal (un noir)">
                    <p>Charcoal</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-03.webp" alt="Couleur Lime (vert citron)">
                    <p>Lime</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-04.webp" alt="Couleur Oyster (beige)">
                    <p>Oyster</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-05.webp" alt="Couleur Red (rouge)">
                    <p>Red</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-06.webp" alt="Couleur Silver (argent)">
                    <p>Silver</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-07.webp" alt="Couleur Turqoise (bleu turquoise)">
                    <p>Turqoise</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-08.webp" alt="Couleur Ultra-marine (bleu marine)">
                    <p>Ultra-marine</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-09.webp" alt="Couleur Naranja (orange)">
                    <p>Naranja</p></div>
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
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Thermal Hard Cover A4 Eco');
            ?>
        
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('V-Paper');
            ?>
        </div>
    </div>    
</section>
<section class="section2" id="Cahier de Réunion">
    <div class="container">
        <h2 class="title-h3 centre-text">Cahier de réunion personnalisé</h2>
        <div class="ligne">
            <div class="colonne-1 onleft">
                <img src="../images/produits/cahier-de-reunion-02.webp" alt="Présentation d'un cahier de réunion de couleur gris argent personnalisés à couverture rigide">
            </div>
            <p class="paragraphe">Les réunions n'ont jamais été aussi amusantes ! Ce nouveau concept combine un cahier avec des contenus préfabriqués ensemble avec une boîte unique. Les pages du cahier s'étendent parfaitement à plat et sont agréables à écrire. Tout pour un « Perfect Meeting » pour vos employés, partenaires, clients, clients, sponsors, …</br>Le cahier de réunion parfait est également disponible dans un pratique format A5. Ici aussi, la technique du plat ouvert est utilisée, vous permettant de créer une double page complète. En utilisant le V-Paper, vous obtenez le meilleur résultat d'impression possible, facile à lire et à écrire. La couverture rigide est facile à personnaliser avec votre logo ou votre nom.</p>
        </div>  
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau-perso.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Perfect Meeting Book');
            ?>
        </div>
        <p class="paragraphe">*** Choisissez bien votre couleur dans la colonne <span style="color: #ff5500;text-transform: uppercase;">couleur</span></p>
        <div class="ligne">
            <div class="colonne-3 onleft">
                <div class="ligne">
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-01.webp" alt="Couleur Blossom (une variété de rose)">
                    <p>Blossom</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-02.webp" alt="Couleur Charcoal (un noir)">
                    <p>Charcoal</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-03.webp" alt="Couleur Lime (vert citron)">
                    <p>Lime</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-04.webp" alt="Couleur Oyster (beige)">
                    <p>Oyster</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-05.webp" alt="Couleur Red (rouge)">
                    <p>Red</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-06.webp" alt="Couleur Silver (argent)">
                    <p>Silver</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-07.webp" alt="Couleur Turqoise (bleu turquoise)">
                    <p>Turqoise</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-08.webp" alt="Couleur Ultra-marine (bleu marine)">
                    <p>Ultra-marine</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-09.webp" alt="Couleur Naranja (orange)">
                    <p>Naranja</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/couverture-rigide-personnalise-couleur-10.webp" alt="Couleur Marron">
                    <p>Marron</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/cahier-de-reunion-couleur-01.webp" alt="Couleur Noir">
                    <p>Noir</p></div>
                    <div class="posCouleurRigide">
                    <p style="font-size: 14px; font-weight: bold;">+ seulement</br>format A4</p></div>
                    <div class="posCouleurRigide"><img src="../images/produits/cahier-de-reunion-couleur-02.webp" alt="Couleur Napura Eco Cumin">
                    <p>Napura</br>Eco Cumin</p></div>
                </div>
            </div>
            <div class="colonne-3 onright">
                <img src="../images/produits/cahier-de-reunion-01.webp" alt="Présentation à plat ouvert d'un cahier de réunion de couleur napura personnalisés à couverture rigide">
            </div>
        </div>
</section>

</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-perso.js"></script>

<?php include '../includes/footer.php'; ?>
