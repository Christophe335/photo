// Gestion des uploads d'images pour les produits

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
    let photoUrl;
    
    if (currentPath.includes('/pages/') || 
        currentPath.includes('/admin/') || 
        currentPath.includes('/clients/')) {
        photoUrl = '../formulaires/photo.php?' + params.toString();
    } else {
        photoUrl = 'formulaires/photo.php?' + params.toString();
    }
    
    // Redirection vers le formulaire d'upload
    window.location.href = photoUrl;
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
            // Convertir vers le chemin big
            if (imgSrc.includes('/couleurs/')) {
                imageCouleur = imgSrc.replace('/couleurs/', '/couleurs/big/').replace('.webp', '-B.webp');
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
                imageCouleur = imgSrc.replace('/couleurs/', '/couleurs/big/').replace('.webp', '-B.webp');
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
    let photoUrl;
    
    if (currentPath.includes('/pages/') || 
        currentPath.includes('/admin/') || 
        currentPath.includes('/clients/')) {
        photoUrl = '../formulaires/photo.php?' + params.toString();
    } else {
        photoUrl = 'formulaires/photo.php?' + params.toString();
    }
    
    // Redirection vers le formulaire d'upload
    window.location.href = photoUrl;
}

/**
 * Fonction pour mettre à jour le statut d'upload d'un produit
 */
function mettreAJourStatutUpload(produitId, nombreImages) {
    // Stocker les informations
    produitsAvecImages[produitId] = nombreImages;
    
    // Mettre à jour l'affichage
    const uploadStatus = document.getElementById(`upload-status-${produitId}`);
    const btnUploader = document.querySelector(`[onclick*="uploaderImages(${produitId}"]`);
    const imagesCount = document.getElementById(`images-count-${produitId}`);
    
    if (uploadStatus && btnUploader && imagesCount) {
        if (nombreImages > 0) {
            // Afficher le statut et masquer le bouton upload
            imagesCount.textContent = nombreImages;
            uploadStatus.style.display = 'block';
            btnUploader.style.display = 'none';
            
            // Mettre à jour la description dans la ligne produit
            const ligneProduit = uploadStatus.closest('.ligne-produit');
            if (ligneProduit) {
                const description = ligneProduit.querySelector('.col-description .designation');
                if (description && !description.innerHTML.includes('vous avez uploadé')) {
                    description.innerHTML += `<br><small style="color: #28a745; font-weight: 500;">Vous avez uploadé ${nombreImages} photo${nombreImages > 1 ? 's' : ''}</small>`;
                }
            }
        }
    }
}

/**
 * Fonction modifiée pour ajouter au panier avec les images
 */
function ajouterAuPanier(button) {
    const ligneProduit = button.closest('.ligne-produit');
    const produitId = ligneProduit.getAttribute('data-id');
    const prix = ligneProduit.getAttribute('data-prix');
    const quantite = ligneProduit.querySelector('.input-quantite').value;
    
    // Vérifier si le produit a des images uploadées
    const hasUploaded = button.getAttribute('data-uploaded') === 'true';
    const nombreImages = produitsAvecImages[produitId] || 0;
    
    if (hasUploaded && nombreImages > 0) {
        console.log(`Ajout au panier - Produit ${produitId}, Quantité: ${quantite}, Images: ${nombreImages}`);
        
        // Ici vous pouvez ajouter votre logique d'ajout au panier
        // en incluant les informations sur les images uploadées
        
        // Animation de confirmation
        button.classList.add('adding');
        button.textContent = 'Ajouté !';
        
        setTimeout(() => {
            button.classList.remove('adding');
            button.textContent = 'Ajouter au panier';
        }, 2000);
        
    } else {
        alert('Veuillez d\'abord uploader vos images pour ce produit.');
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script upload-produits.js chargé');
    
    // Vérifier s'il y a des données de retour du formulaire d'upload
    const urlParams = new URLSearchParams(window.location.search);
    const produitId = urlParams.get('produit_retour');
    const nombreImages = urlParams.get('nb_images');
    
    if (produitId && nombreImages) {
        // Mettre à jour le statut si on revient du formulaire d'upload
        mettreAJourStatutUpload(produitId, parseInt(nombreImages));
        
        // Nettoyer l'URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});