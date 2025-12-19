// Gestion des uploads d'images pour les produits personnalisés

// Stockage temporaire des données de produits avec leurs images
let produitsAvecImages = {};

/**
 * Fonction pour rediriger vers le formulaire d'upload avec les informations du produit
 */
function uploaderImages(produitId, reference, designation, format, prix, conditionnement) {
    // Construire les paramètres URL
    const params = new URLSearchParams({
        produit_id: produitId,
        reference: reference,
        designation: designation,
        format: format || '',
        prix: prix,
        conditionnement: conditionnement || '',
        quantite: 1
    });
    
    // Détecter le chemin correct vers le formulaire
    const currentPath = window.location.pathname;
    let persoUrl;
    
    if (currentPath.includes('/pages-perso/') || 
        currentPath.includes('/pages/') || 
        currentPath.includes('/admin/') || 
        currentPath.includes('/clients/')) {
        persoUrl = '../formulaires/perso.php?' + params.toString();
    } else {
        persoUrl = 'formulaires/perso.php?' + params.toString();
    }
    
    // Redirection vers le formulaire d'upload
    window.location.href = persoUrl;
}

/**
 * Nouvelle fonction pour rediriger vers le formulaire d'upload en récupérant la quantité sélectionnée
 */
function uploaderImagesAvecQuantite(button, produitId, reference, designation, format, prix, conditionnement) {
    // Récupérer la quantité sélectionnée dans la ligne du produit
    const ligneProduit = button.closest('.ligne-produit');
    const quantiteInput = ligneProduit.querySelector('.input-quantite');
    const quantite = quantiteInput ? parseInt(quantiteInput.value) || 1 : 1;
    
    // Récupérer la couleur sélectionnée (active), sinon la première
    let couleur = "";
    let imageCouleur = "";
    const couleurItemActive = ligneProduit.querySelector(".couleur-item.active");
    if (couleurItemActive) {
        const couleurActive = couleurItemActive.querySelector(".couleur-nom");
        if (couleurActive) couleur = couleurActive.textContent.trim();
        
        // Récupérer l'image de couleur
        const couleurImg = couleurItemActive.querySelector(".couleur-image");
        if (couleurImg) {
            const imgSrc = couleurImg.src;
            // Convertir vers le chemin big relatif pour perso.php
            if (imgSrc.includes('/couleurs/')) {
                // Extraire le nom du fichier depuis l'URL
                const nomFichier = imgSrc.split('/').pop().replace('.webp', '');
                imageCouleur = `../images/couleurs/big/${nomFichier}-B.webp`;
            }
        }
    } else {
        // Si aucune couleur active, prendre la première disponible
        const couleurDefault = ligneProduit.querySelector(".couleur-nom");
        if (couleurDefault) couleur = couleurDefault.textContent.trim();
        
        const couleurImgDefault = ligneProduit.querySelector(".couleur-image");
        if (couleurImgDefault) {
            const imgSrc = couleurImgDefault.src;
            if (imgSrc.includes('/couleurs/')) {
                // Extraire le nom du fichier depuis l'URL
                const nomFichier = imgSrc.split('/').pop().replace('.webp', '');
                imageCouleur = `../images/couleurs/big/${nomFichier}-B.webp`;
            }
        }
    }
    
    // Construire les paramètres URL avec la quantité et la couleur
    const params = new URLSearchParams({
        produit_id: produitId,
        reference: reference,
        designation: designation,
        format: format || '',
        prix: prix,
        conditionnement: conditionnement || '',
        quantite: quantite,
        couleur: couleur,
        imageCouleur: imageCouleur
    });
    
    // Détecter le chemin correct vers le formulaire
    const currentPath = window.location.pathname;
    let persoUrl;
    
    if (currentPath.includes('/pages-perso/') || 
        currentPath.includes('/pages/') || 
        currentPath.includes('/admin/') || 
        currentPath.includes('/clients/')) {
        persoUrl = '../formulaires/perso.php?' + params.toString();
    } else {
        persoUrl = 'formulaires/perso.php?' + params.toString();
    }
    
    // Redirection vers le formulaire d'upload
    window.location.href = persoUrl;
}

/**
 * Fonction pour mettre à jour le statut d'upload d'un produit
 */
function mettreAJourStatutUpload(produitId, nombreImages) {
    const uploadStatus = document.getElementById(`upload-status-${produitId}`);
    const imagesCount = document.getElementById(`images-count-${produitId}`);
    const uploadButton = document.querySelector(`[onclick*="uploaderImagesAvecQuantite"][onclick*="${produitId}"]`);
    
    if (uploadStatus && imagesCount && uploadButton) {
        if (nombreImages > 0) {
            imagesCount.textContent = nombreImages;
            uploadStatus.style.display = 'block';
            uploadButton.style.display = 'none';
            
            // Stocker les informations du produit
            produitsAvecImages[produitId] = {
                nombreImages: nombreImages,
                uploaded: true
            };
        } else {
            uploadStatus.style.display = 'none';
            uploadButton.style.display = 'block';
            delete produitsAvecImages[produitId];
        }
    }
}

/**
 * Fonction modifiée pour ajouter au panier avec les images
 */
function ajouterAuPanier(button) {
    const ligneProduit = button.closest('.ligne-produit');
    const quantiteInput = ligneProduit.querySelector('.input-quantite');
    const quantite = quantiteInput ? parseInt(quantiteInput.value) || 1 : 1;
    
    // Récupérer les informations du produit depuis le bouton d'upload correspondant
    const uploadButton = ligneProduit.querySelector('[onclick*="uploaderImagesAvecQuantite"]');
    if (!uploadButton) {
        console.error('Bouton d\'upload introuvable');
        return;
    }
    
    // Extraire les informations du onclick
    const onclickText = uploadButton.getAttribute('onclick');
    const matches = onclickText.match(/uploaderImagesAvecQuantite\(this,\s*(\d+),\s*'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*([^,]+),\s*'([^']*)'\)/);
    
    if (!matches) {
        console.error('Impossible d\'extraire les informations du produit');
        return;
    }
    
    const [, produitId, reference, designation, format, prix, conditionnement] = matches;
    
    const produit = {
        id: parseInt(produitId),
        reference: reference,
        designation: designation,
        format: format,
        prix: parseFloat(prix),
        conditionnement: conditionnement,
        quantite: quantite,
        images: produitsAvecImages[produitId] ? produitsAvecImages[produitId].nombreImages : 0
    };
    
    // Vérifier si le produit a des images uploadées
    if (!produitsAvecImages[produitId] || produitsAvecImages[produitId].nombreImages === 0) {
        alert('Veuillez d\'abord ajouter vos personnalisations avant d\'ajouter au panier.');
        return;
    }
    
    // Appeler la fonction d'ajout au panier du panier.js
    if (typeof window.ajouterAuPanierProduit === 'function') {
        window.ajouterAuPanierProduit(produit);
        
        // Afficher un message de confirmation
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 10000;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
        toast.textContent = `${produit.designation} ajouté au panier avec ${produit.images} personnalisations`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
        
    } else {
        console.error('Fonction ajouterAuPanierProduit non trouvée');
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier s'il y a des données d'upload dans le localStorage
    const uploadData = localStorage.getItem('uploadData');
    if (uploadData) {
        try {
            const data = JSON.parse(uploadData);
            if (data.produit_id && data.nombreImages) {
                mettreAJourStatutUpload(data.produit_id, data.nombreImages);
                localStorage.removeItem('uploadData');
            }
        } catch (e) {
            console.error('Erreur lors du parsing des données d\'upload:', e);
        }
    }
});