<?php include '../includes/header.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <div class="container">
        <h1 class="title-h1 bull">Livre Photo</h1>
        <img class="centre-div pose" src="../images/bandeaux/livre-photo-1.webp" alt="Un bandeau présentant des livres photo personnalisés">
        <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
            Transformez vos photos en livre photo professionnel de haute qualité.
        </p>
    </div>
</main>
<section class="section1">
    <div class="container">
        <h2 class="title-h3 centre-text">Service de personnalisation</h2>
        <h3 class="title-h2 centre-text">Peel & Stick Sheets</h3>
        <div class="ligne">
            <div class="colonne-1 onleft">
                <img src="../images/produits/peel-stick 1.webp" alt="Image en avant de feuilles Peel & Stick Sheets">
            </div>
            <div class="colonne-2 onright">
                <p>Cette solution prête à l'emploi dispose d'une zone auto-adhésive qui vous permet de coller n'importe quel type d'impression directement dans notre produit. Pas de situation collante avec de la colle renversée pour vous ! Une innovation intelligente et unique qui simplifiera votre vie. Imprimez vos photos et créez votre propre livre photo / panneau en un rien de temps. Le cadeau idéal pour quelqu'un de très spécial.Entièrement personnalisable avec l'œuvre de votre choix. Améliorez vos produits en leur donnant la touche finale que vos clients se souviendront. Ajoutez une touche personnelle pour en faire de puissants outils de marque avec votre propre design d'entreprise ou une œuvre en couleur pleine de paillettes. Sortez du lot. Boostez la notoriété de premier plan.</p>
            </div>
        </div>
        <div class="container">
            <img src="../images/produits/peel-stick-utilisation1.webp" alt="4 images montrant l'utilisation des Peel & Stick Sheets">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & Stick Sheets');
            ?>
        </div>
    
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2">
    <div class="container">
        <h2 class="title-h3 centre-text">Livres photo autocollants - Couverture couleur</h2>
        <p class="paragraphe">Produit à faire soi-même. Laissez vos clients personnaliser eux-mêmes leurs photos préférées, sans colle ni ruban adhésif !
 Étape 1: retirez le ﬁlm protecteur de la couche adhésive de la couverture.  Étape 2: collez votre photo.
En un rien de temps, les clients créent leur propre souvenir ou cadeau personnalisé.</p>
    </div>
    <div class="ligne">
        <div class="colonne-3 onleft">
            <img src="../images/produits/livrephotoautocollant-1.webp" alt="Livre photo autocollant de couleur vert pâle montrant le résultat de l'application des Peel & Stick Sheets sur la couverture">
        </div>
        <div class="colonne-3 onright">
            <img src="../images/produits/livrephotoautocollant-3.webp" alt="Livre photo autocollant ouvert, de couleur bleu pâle montrant le résultat de l'application des Peel & Stick Sheets sur l'intérieur de l'album">
        </div>
    </div>
    <div class="ligne centre-div" style="width:500px;">
        <img src="../images/produits/livrephotoautocollant-couleur1.webp" alt="Album photo peel & stick fermé de couleur bleu pâle">
        <img src="../images/produits/livrephotoautocollant-couleur2.webp" alt="Album photo peel & stick fermé de couleur vert pâle">
        <img src="../images/produits/livrephotoautocollant-couleur3.webp" alt="Album photo peel & stick fermé de couleur violet pâle">
        <img src="../images/produits/livrephotoautocollant-couleur4.webp" alt="Album photo peel & stick fermé de couleur jaune pâle">
        <img src="../images/produits/livrephotoautocollant-couleur5.webp" alt="Album photo peel & stick fermé de couleur rose pâle">
    </div>
</br>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & Stick Photo Book');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section1">
    <div class="container">
        <h2 class="title-h3 centre-text">Livres photo autocollants - Couverture Noir</h2>
        <p class="paragraphe">Créez facilement de magnifiques cadeaux photo en quelques secondes avec un pratique livre photo Peel & Stick. 
Imprimez simplement vos photos et créez votre propre livre photo en un rien de temps. 
Ces livres photo lay-flat sont sans aucun doute faciles à assembler et offrent une tenue solide et robuste. 
Le cadeau idéal pour une personne très spéciale.</p>
    </div>
    <div class="ligne">
        <div class="colonne-2">
            <img src="../images/produits/album-photo-autocollant-1.webp" alt="Livre photo autocollant ouvert, de couleur noir montrant le résultat de l'application des Peel & Stick Sheets sur l'intérieur de l'album">  
        </div>
        <div class="colonne-1 onright">
            <img src="../images/produits/album-photo-autocollant-2.webp" alt="Livre photo autocollant de couleur noir montrant le résultat de l'application des Peel & Stick Sheets sur la couverture">
        </div>
    </div>
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/../includes/tableau.php';
            
            // Afficher les produits de reliure directement
            afficherTableauProduits('Peel & Stick Photo BookB');
            ?>
        </div>
        </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>
<section class="section2">
    <div class="container">
        <h2 class="title-h3 centre-text">Livre Photo Créatif</h2>
    </div>
    <div class="ligne">
        <div>
            <p class="paragraphe">Une image en dit plus que mille mots. Dans ces livres photo
            instantanés colorés, il y a de la place pour 10 images de ce type (soit
            plus de 10 000 mots) et pour ajouter un message spécial. Combien
            de mots cela représente-t-il ? Nous avons perdu le compte ...</p>
            <p class="paragraphe">Indispensable pour les créateurs de scrapbooking
            Les livres photo instantanés constituent la base idéale pour un
            scrapbook, une sorte de livre photo à l'ancienne, mais un peu plus
            personnel. Ils comprennent des autocollants double face pour fixer
            des photos et d'autres souvenirs, ainsi qu'un marqueur pour ajouter
            des noms, des dates, des citations ou d'autres éléments d'écriture</p>
        </div>
        <div class="onright">
            <img src="../images/produits/livrephotocreatif-1.webp" alt="Livre photo créatif fermé montrant une couverture personnalisée avec des photos collées dessus">
        </div>
    </div>
    <div class="ligne">
        <div class="colonne-1">
            <img src="../images/produits/instant-photo-book-1.webp" alt="Présentation du livre photo créatif sous la forme de sa pochette contenant tout le néccessaire pour créer le livre photo">
        </div>
        <div>
        <div class="colonne-2 ligne">
            <div><img style="width: 80px; height: 80px; margin-right: 25px;" src="../images/produits/livrephotocreatif-couleur1.webp" alt="Exmple de la couleur noire disponible pour le livre créatif"></br>Black</div>
            <div><img style="width: 80px; height: 80px; margin-right: 25px;" src="../images/produits/livrephotocreatif-couleur2.webp" alt="Exmple de la couleur bleue disponible pour le livre créatif"></br>Blue</div>
            <div><img style="width: 80px; height: 80px; margin-right: 25px;" src="../images/produits/livrephotocreatif-couleur3.webp" alt="Exmple de la couleur rose disponible pour le livre créatif"></br>Pink</div>
            <div><img style="width: 80px; height: 80px; margin-right: 25px;" src="../images/produits/livrephotocreatif-couleur4.webp" alt="Exmple de la couleur violette disponible pour le livre créatif"></br>Purple</div>
            <div><img style="width: 80px; height: 80px; margin-right: 25px;" src="../images/produits/livrephotocreatif-couleur5.webp" alt="Exmple de la couleur blanche disponible pour le livre créatif"></br>White</div>
        </div>
            
        </div>
    </div>
    <div class="tableau-container">
                <?php
                // IMPORTANT: Ajuster le chemin selon votre structure
                require_once __DIR__ . '/../includes/tableau.php';
                
                // Afficher les produits de reliure directement
                afficherTableauProduits('Instant Photobooks');
                ?>
            </div>
    </br>
    <?php include '../includes/bt-devis.php'; ?>
</section>

<!-- Script nécessaire pour le panier -->
<script src="/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
