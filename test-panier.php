<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/panier.css">

<h1>Test du Panier</h1>

<div style="padding: 20px;">
    <button onclick="ajouterArticleTest()">Ajouter un article test</button>
    <button onclick="afficherCompteur()">Afficher compteur</button>
    <button onclick="viderPanier()">Vider panier</button>
    
    <div id="debug" style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px;">
        <h3>Debug:</h3>
        <div id="debug-content"></div>
    </div>
</div>

<script src="js/panier.js"></script>
<script>
// Attendre que le panierManager soit chargé
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        console.log('panierManager disponible:', typeof panierManager !== 'undefined');
        if (typeof panierManager !== 'undefined') {
            console.log('Panier actuel:', panierManager.panier);
            console.log('Nombre d\'articles:', panierManager.panier.length);
        }
    }, 1000);
});

function ajouterArticleTest() {
    if (typeof panierManager !== 'undefined') {
        panierManager.ajouterProduit('TEST_001', 1, 10.50, {
            code: 'TEST_001',
            designation: 'Article de test',
            couleur: 'Rouge',
            imageCouleur: 'images/couleurs/rouge.jpg'
        });
        
        panierManager.afficherNotification(
            "Article de test ajouté !",
            "success",
            {
                code: 'TEST_001',
                designation: 'Article de test',
                couleur: 'Rouge',
                imageCouleur: 'images/couleurs/rouge.jpg',
                quantite: 1
            }
        );
    } else {
        alert('panierManager pas encore chargé');
    }
}

function afficherCompteur() {
    const compteurs = document.querySelectorAll('.cart-count, .compteur-panier');
    let debug = 'Compteurs trouvés: ' + compteurs.length + '<br>';
    compteurs.forEach((compteur, index) => {
        debug += `Compteur ${index + 1}: ${compteur.textContent} (visible: ${compteur.style.display})<br>`;
    });
    
    if (typeof panierManager !== 'undefined') {
        debug += 'Articles dans panier: ' + panierManager.panier.length + '<br>';
        panierManager.panier.forEach((item, index) => {
            debug += `- ${item.details.designation} (quantité: ${item.quantite})<br>`;
        });
    }
    
    document.getElementById('debug-content').innerHTML = debug;
}

function viderPanier() {
    if (typeof panierManager !== 'undefined') {
        panierManager.panier = [];
        panierManager.sauvegarderPanier();
        alert('Panier vidé');
    }
}
</script>

<?php include 'includes/footer.php'; ?>