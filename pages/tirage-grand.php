<?php include '../includes/header.php'; ?>
<?php include '../includes/menu-flottant.php'; ?>

<!-- Styles nécessaires pour le tableau des produits -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/tableau.css">
<link rel="stylesheet" href="../css/panier.css">

<main style="padding: 40px 0;">
    <section class="section1" id="Grand format">
        <div class="container">
            
            <h2 class="title-h3 centre-text">Tirages Grand Format</h2>
            <h3>(20 x 20 cm – 20 x 25 cm – 20 x 30 cm – 21 x 21 cm – 25 x 25 cm)</h3>
            
            <div class="ligne">
                <div class="colonne-1 onleft">
                    <img class="centre-div" src="../images/produits/tirage-grand-1.webp" alt="un album photo ouvert présentant des tirages photo grand format">
                    <img class="centre-div" src="../images/produits/tirage-grand-2.webp" alt="des tirages photo grand format d'un couple et de photos de la ville de Paris">
                </div>
                <div class="colonne-2">
                    <p class="paragraphe">Donnez plus d’impact à vos photos avec nos tirages grand format. Ils offrent une présence visuelle forte tout en conservant une grande finesse de détail. Pensés pour être exposés, ces formats mettent en valeur vos plus beaux clichés et transforment vos images en véritables éléments de décoration.</p>
                    <h1>à partir de 1.40 € HT l'unité</h1>
                    <p>Commande minimum 10 unités</p>
                <div class="tableau-container">
                    <?php
                    // IMPORTANT: Ajuster le chemin selon votre structure
                    require_once __DIR__ . '/../includes/tableau2.php';
                            
                    // Afficher les produits de reliure directement (quantité par défaut 10)
                    afficherTableauProduits('Tirage Photo Grand Format', false, 10);
                    ?>
                </div> 
                <p class="defile">Toute commande de tirage photo doit être en rapport avec un produit commandé. Pas de commande individuelle de tirage photo.</p>
                </div>
            </div>  
                
            </br> 
        </div>
    </section>
</main>

<script src="/js/panier.js"></script>
<script src="../js/upload-produits.js"></script>

<?php include '../includes/footer.php'; ?>
