<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../css/panier.css">

<?php
// Récupération des informations du produit depuis l'URL et la base de données
$produit_id = $_GET['produit_id'] ?? null;
$reference = $_GET['reference'] ?? '';
$designation = $_GET['designation'] ?? '';
$format = $_GET['format'] ?? '';
$prix = $_GET['prix'] ?? 0;
$conditionnement = $_GET['conditionnement'] ?? '';
$quantite_selectionnee = intval($_GET['quantite'] ?? 1);
$couleur = $_GET['couleur'] ?? '';
$imageCouleur = $_GET['imageCouleur'] ?? '';

// Si pas de conditionnement dans l'URL, récupération depuis la base de données
if (empty($conditionnement) && $produit_id) {
    require_once __DIR__ . '/../includes/database.php';
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT conditionnement FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();
        if ($produit) {
            $conditionnement = $produit['conditionnement'] ?? '';
        }
    } catch (Exception $e) {
        error_log("Erreur récupération produit: " . $e->getMessage());
    }
}
?>

<main class="cadre">
    <div class="container">
        <h2 class="title-h3">Ajoutez vos photos pour mettre au panier</h2>
        
        <!-- Layout 2 colonnes -->
        <div class="layout-2-colonnes" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
        
        <style>
        /* Layout responsif 2 colonnes */
        @media (max-width: 992px) {
            .layout-2-colonnes {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
        }
        
        @media (max-width: 768px) {
            .layout-2-colonnes {
                gap: 15px !important;
                padding: 0 10px !important;
            }
            
            .produit-selectionne-cadre {
                padding: 20px !important;
            }
        }
        </style>
            
            <!-- Colonne gauche : Produit sélectionné -->
            <?php if ($produit_id): ?>
            <div class="produit-selectionne-cadre">
                <!-- Titre avec icône (même style que perso.php, icône photo) -->
                <div style="
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 2px solid #f05124;
                ">
                    <div style="
                        background: linear-gradient(135deg, #f05124 0%, #ff6b47 100%);
                        color: white;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 18px;
                        margin-right: 15px;
                        box-shadow: 0 4px 10px rgba(240, 81, 36, 0.3);
                    ">📸</div>
                    <h3 style="
                        margin: 0;
                        color: #2a256d;
                        font-size: 20px;
                        font-weight: 600;
                    ">Produit sélectionné</h3>
                </div>

                <!-- Informations produit -->
                <div style="
                    padding: 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
                    border-left: 4px solid #f05124;
                " data-id="<?= htmlspecialchars($produit_id) ?>" data-prix="<?= htmlspecialchars($prix) ?>">

                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                        <div style="flex: 0 0 auto;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Code</div>
                            <div style="font-weight:600;color:#2a256d;font-size:14px;background:#fff5f3;padding:6px 10px;border-radius:4px;"><?= htmlspecialchars($reference) ?></div>
                        </div>

                        <div style="flex:1; min-width:200px;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Désignation</div>
                            <div style="font-weight:600;color:#2a256d;font-size:16px;line-height:1.3;"><?= htmlspecialchars($designation) ?></div>
                        </div>

                        <?php if ($format): ?>
                        <div style="flex:0 0 auto;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Format</div>
                            <div style="font-size:14px;font-weight:500;color:#f05124;background:#fff5f3;padding:6px 10px;border-radius:4px;"><?= htmlspecialchars($format) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px; flex-wrap:wrap;">
                        <?php if ($conditionnement): ?>
                        <div style="flex:0 0 auto;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Conditionnement</div>
                            <div style="font-size:14px;font-weight:500;color:#6f42c1;background:#f8f5ff;padding:6px 10px;border-radius:4px;">Pack de <?= htmlspecialchars($conditionnement) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($couleur): ?>
                        <div style="flex:0 0 auto;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Couleur</div>
                            <div style="font-weight:600;color:#2a256d;font-size:14px;background:#f0f0f0;padding:6px 10px;border-radius:4px;display:flex;align-items:center;gap:8px;"> 
                                <?= htmlspecialchars($couleur) ?>
                                <?php if ($imageCouleur): ?>
                                    <img src="<?= htmlspecialchars($imageCouleur) ?>" alt="<?= htmlspecialchars($couleur) ?>" style="width:20px;height:20px;border-radius:50%;border:1px solid #ddd;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div style="flex:0 0 auto;">
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Quantité</div>
                            <div style="font-weight:600;color:#2a256d;font-size:14px;background:#f0f0f0;padding:6px 10px;border-radius:4px;"><?= $quantite_selectionnee ?></div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top:20px; padding-top:20px; border-top:1px solid #eee;">
                        <div>
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Prix unitaire</div>
                            <div style="font-weight:600;color:#17a2b8;font-size:16px;background:#f0fbff;padding:8px 12px;border-radius:4px;border:1px solid #bee5eb;">
                                <?php $prixUnitaire = $conditionnement && intval($conditionnement) > 0 ? $prix / intval($conditionnement) : $prix; echo number_format($prixUnitaire,2,',',' '); ?> € HT
                            </div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#6c757d;text-transform:uppercase;font-weight:500;margin-bottom:5px;letter-spacing:0.5px;">Total</div>
                            <div style="font-weight:700;color:#28a745;font-size:18px;background:#f8fff9;padding:10px 14px;border-radius:6px;border:1px solid #d4edda;"><?= number_format($prix * $quantite_selectionnee,2,',',' ') ?> € HT</div>
                        </div>
                    </div>

                </div>

                <!-- Effet de décoration -->
                <div style="position:absolute; top:-50px; right:-50px; width:100px; height:100px; background: linear-gradient(135deg, rgba(240,81,36,0.1) 0%, rgba(255,107,71,0.05) 100%); border-radius:50%;"></div>
            </div>
            <?php endif; ?>
            
            <!-- Colonne droite : Module d'upload d'images -->
            <div class="upload-section">
                <div class="upload-header">
                    <h3>Vos images</h3>
                    <p>Ajoutez jusqu'à 30 images (JPG, PNG, WebP - max 5MB chacune)</p>
                </div>
                
                <div class="file-upload-area">
                    <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                    <div class="upload-dropzone" onclick="document.getElementById('imageUpload').click()">
                        <div class="upload-icon">📁</div>
                        <p>Cliquez ici ou glissez-déposez vos images</p>
                        <span>Formats acceptés : JPG, PNG, WebP</span>
                    </div>
                </div>
                
                <div class="images-counter">
                    <span id="imageCount">0</span> / 30 images
                </div>
                
                <div class="images-preview" id="imagesPreview">
                    <!-- Les images apparaîtront ici -->
                </div>
                
                <?php if ($produit_id): ?>
                <!-- Bouton d'action après upload -->
                <div class="upload-actions" id="uploadActions" style="display: none; margin-top: 20px; text-align: center;">
                    <button type="button" 
                            class="btn-ajouter-panier-photos" 
                            id="btnAjouterPanier"
                            onclick="ajouterAuPanierAvecPhotos(<?= $produit_id ?>, '<?= htmlspecialchars($reference, ENT_QUOTES) ?>', '<?= htmlspecialchars($designation, ENT_QUOTES) ?>', '<?= htmlspecialchars($format ?? '', ENT_QUOTES) ?>', <?= $prix ?>, '<?= htmlspecialchars($conditionnement ?? '', ENT_QUOTES) ?>')" 
                            style="
                                background: #28a745; 
                                color: white; 
                                border: none; 
                                padding: 12px 30px; 
                                border-radius: 6px; 
                                font-size: 16px; 
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                            ">
                        Ajouter au panier
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
        </div> <!-- Fin du layout 2 colonnes -->
    </div>
</main>

<!-- Modal pour le recadrage -->
<div id="cropModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Recadrer l'image</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="crop-layout">
                <!-- Aperçu sur le côté gauche -->
                <div class="crop-preview-sidebar">
                    <h4>Aperçu final</h4>
                    <div class="preview-wrapper">
                        <img id="cropPreviewImage" src="" alt="Aperçu du recadrage">
                        <div class="preview-info"></div>
                    </div>
                </div>
                
                <!-- Zone principale de recadrage -->
                <div class="crop-main">
                    <div class="crop-container">
                        <img id="cropImage" src="" alt="Image à recadrer">
                    </div>
                    
                    <!-- Contrôles en bas -->
                    <div class="crop-controls-layout">
                        <!-- Boutons principaux à gauche -->
                        <div class="crop-controls">
                            <button type="button" class="btn-crop-confirm">Confirmer</button>
                            <button type="button" class="btn-crop-cancel">Annuler</button>
                        </div>
                        
                        <!-- Contrôles de zoom à droite -->
                        <div class="zoom-controls-inline">
                            <button type="button" class="orientation-btn" id="orientationToggle" title="Basculer Portrait/Paysage">⟲</button>
                            <button type="button" class="zoom-btn-inline" id="zoomOutInline">-</button>
                            <span class="zoom-display-inline">100%</span>
                            <button type="button" class="zoom-btn-inline" id="zoomInInline">+</button>
                            <button type="button" class="zoom-reset-inline">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/panier.js"></script>
<script src="../js/simple-crop.js"></script>
<script src="../js/image-upload.js"></script>
<script src="../js/upload-produits.js"></script>

<script>
// Fonctions spécifiques au formulaire d'upload

/**
 * Récupérer les informations des images uploadées
 */
function obtenirInfosImages() {
    const images = [];
    const imageItems = document.querySelectorAll('#imagesPreview .image-item');
    
    imageItems.forEach((item, index) => {
        const img = item.querySelector('img');
        // Priorité : data-filename sur l'élément, sinon essayer window.fichiersUploades, sinon fallback généré
        let fileName = item.getAttribute('data-filename');
        if ((!fileName || fileName.trim() === '') && window.fichiersUploades && Array.isArray(window.fichiersUploades) && window.fichiersUploades[index] && window.fichiersUploades[index].name) {
            fileName = window.fichiersUploades[index].name;
        }
        if (!fileName || fileName.trim() === '') {
            fileName = `image_${index + 1}.jpg`;
        }

        images.push({
            nom: fileName,
            url: img ? img.src : '',
            index: index
        });
    });
    
    return images;
}

/**
 * Ajouter le produit au panier avec les photos
 */
function ajouterAuPanierAvecPhotos(produitId, reference, designation, format, prix, conditionnement) {
    const images = obtenirInfosImages();
    
    if (images.length === 0) {
        alert('Veuillez d\'abord ajouter des images avant d\'ajouter au panier.');
        return;
    }
    
    // Utiliser la quantité sélectionnée dans le tableau (pas de calcul automatique)
    const quantite = <?= $quantite_selectionnee ?>;
    const nombrePhotos = images.length;
    
    // Données du produit à ajouter au panier
    const produitPanier = {
        id: produitId,
        reference: reference,
        designation: designation,
        format: format,
        prix: prix,
        quantite: quantite,
        conditionnement: conditionnement,
        couleur: '<?= htmlspecialchars($couleur, ENT_QUOTES) ?>',
        imageCouleur: '<?= htmlspecialchars($imageCouleur, ENT_QUOTES) ?>',
        photos: images,
        nombrePhotos: nombrePhotos,
        source: 'photo'
    };
    
    // Animation de confirmation immédiate
    const btn = document.getElementById('btnAjouterPanier');
    btn.style.background = '#ffc107';
    btn.textContent = 'Ajout en cours...';
    btn.disabled = true;
    
    // Envoyer au panier PHP via AJAX
    const formData = new FormData();
    formData.append('action', 'ajouter_avec_photos');
    formData.append('produit', JSON.stringify(produitPanier));
    
    fetch('../pages/panier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animation de succès
            btn.style.background = '#28a745';
            btn.textContent = 'Ajouté au panier !';
            
            // Debug pour vérifier l'état du panierManager
            console.log('Debug popup - panierManager disponible:', typeof panierManager !== 'undefined');
            if (typeof panierManager !== 'undefined') {
                console.log('Debug popup - méthode afficherNotification:', typeof panierManager.afficherNotification);
                console.log('Debug popup - méthode synchroniserAvecSession:', typeof panierManager.synchroniserAvecSession);
                console.log('Debug popup - toutes les méthodes:', Object.getOwnPropertyNames(Object.getPrototypeOf(panierManager)));
            }
            
            // Fonction pour attendre que panierManager soit prêt
            function attendrePanierManager(callback, tentatives = 0) {
                if (typeof panierManager !== 'undefined' && panierManager.afficherNotification) {
                    callback();
                } else if (tentatives < 10) { // Max 1 seconde d'attente
                    setTimeout(() => attendrePanierManager(callback, tentatives + 1), 100);
                } else {
                    // Fallback si panierManager n'est toujours pas disponible
                    alert(`Produit ajouté au panier avec ${images.length} photo${images.length > 1 ? 's' : ''} !\nRéférence: ${reference}\nDésignation: ${designation}\nFormat: ${format || 'N/A'}\nCouleur: <?= htmlspecialchars($couleur) ? htmlspecialchars($couleur) : 'N/A' ?>\n\nVous pouvez consulter votre panier ou continuer vos achats.`);
                }
            }
            
            // Utiliser la popup de confirmation existante du site
            attendrePanierManager(() => {
                panierManager.afficherNotification(
                    "Votre article a bien été ajouté au panier !",
                    "success",
                    {
                        code: reference,
                        designation: designation,
                        format: format || '',
                        couleur: '<?= htmlspecialchars($couleur, ENT_QUOTES) ?>',
                        imageCouleur: '<?= htmlspecialchars($imageCouleur, ENT_QUOTES) ?>',
                        quantite: quantite + ' avec ' + nombrePhotos + ' photo' + (nombrePhotos > 1 ? 's' : '')
                    }
                );
            });
            
            // Réinitialiser le bouton après succès
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = 'Ajouter au panier';
                btn.style.background = '#28a745';
            }, 2000);
        } else {
            // Erreur
            btn.style.background = '#dc3545';
            btn.textContent = 'Erreur !';
            alert('Erreur lors de l\'ajout au panier: ' + data.message);
            
            // Réactiver le bouton après 2 secondes
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = 'Ajouter au panier';
                btn.style.background = '#28a745';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        btn.style.background = '#dc3545';
        btn.textContent = 'Erreur !';
        alert('Erreur de connexion lors de l\'ajout au panier.');
        
        // Réactiver le bouton après 2 secondes
        setTimeout(() => {
            btn.disabled = false;
            btn.textContent = 'Ajouter au panier';
            btn.style.background = '#28a745';
        }, 2000);
    });
}

/**
 * Mettre à jour l'affichage de la quantité et du prix total
 */
function mettreAJourQuantiteEtPrix() {
    const quantiteAffichee = document.getElementById('quantite-affichee');
    const prixTotalAffiche = document.getElementById('prix-total-affiche');
    
    if (quantiteAffichee && prixTotalAffiche) {
        const quantiteSelectionnee = <?= $quantite_selectionnee ?>; // Quantité choisie dans le tableau
        const prixHT = <?= $prix ?>; // Prix du pack complet
        
        // La quantité reste celle sélectionnée dans le tableau
        // Elle ne change pas selon le nombre d'images
        quantiteAffichee.textContent = quantiteSelectionnee;
        
        // Calcul du prix total : quantité sélectionnée × prix HT du pack
        const prixTotal = prixHT * quantiteSelectionnee;
        prixTotalAffiche.textContent = new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(prixTotal).replace(',', ',') + ' € HT';
        
        // Changer la couleur selon la quantité
        if (quantiteSelectionnee > 1) {
            quantiteAffichee.style.background = '#fff3cd';
            quantiteAffichee.style.color = '#856404';
            prixTotalAffiche.style.background = '#fff3cd';
            prixTotalAffiche.style.borderColor = '#ffeaa7';
        } else {
            quantiteAffichee.style.background = '#f0f0f0';
            quantiteAffichee.style.color = '#2a256d';
            prixTotalAffiche.style.background = '#f8fff9';
            prixTotalAffiche.style.borderColor = '#d4edda';
        }
    }
}

// Observer les changements dans la zone d'aperçu pour afficher le bouton
const observer = new MutationObserver(function(mutations) {
    const imagesPreview = document.getElementById('imagesPreview');
    const uploadActions = document.getElementById('uploadActions');
    
    if (imagesPreview && uploadActions) {
        const hasImages = imagesPreview.children.length > 0;
        uploadActions.style.display = hasImages ? 'block' : 'none';
        
        // Mettre à jour la quantité et le prix total
        mettreAJourQuantiteEtPrix();
        
        // Réactiver le bouton si des images sont supprimées puis rajoutées
        if (hasImages) {
            const btn = document.getElementById('btnAjouterPanier');
            if (btn && btn.disabled) {
                btn.disabled = false;
                btn.textContent = 'Ajouter au panier';
                btn.style.background = '#28a745';
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const imagesPreview = document.getElementById('imagesPreview');
    if (imagesPreview) {
        observer.observe(imagesPreview, { childList: true });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
