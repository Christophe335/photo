<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../css/panier.css">
<link rel="stylesheet" href="../css/tableau.css">

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
        $stmt = $db->prepare("SELECT conditionnement, reference, designation, format, prixVente FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();
        if ($produit) {
            $conditionnement = $produit['conditionnement'] ?? '';
            if (empty($reference) && !empty($produit['reference'])) $reference = $produit['reference'];
            if (empty($designation) && !empty($produit['designation'])) $designation = $produit['designation'];
            if (empty($format) && !empty($produit['format'])) $format = $produit['format'];
            if (empty($prix) && isset($produit['prixVente'])) $prix = $produit['prixVente'];
        }
    } catch (Exception $e) {
        error_log("Erreur récupération produit: " . $e->getMessage());
    }
}
?>

<style>
.etapes-ascenseur {
    display: flex; 
    justify-content: center; 
    margin: 30px 0; 
    background: #fff; 
    border-radius: 10px; 
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.etape {
    display: flex; 
    align-items: center; 
    margin-right: 40px; 
    cursor: pointer;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.etape:hover {
    background: #f8f9fa;
}

.etape-numero {
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-weight: bold;
    margin-right: 10px;
    transition: all 0.3s ease;
}

.etape-numero.active {
    background: #f05124; 
    color: white;
}

.etape-numero:not(.active) {
    background: #ddd; 
    color: #666;
}

.etape-texte {
    font-weight: 600;
    transition: all 0.3s ease;
}

.etape.active .etape-texte {
    color: #2a256d;
}

.etape:not(.active) .etape-texte {
    color: #666;
}

.separateur {
    width: 30px; 
    height: 2px; 
    background: #ddd; 
    align-self: center; 
    margin: 0 20px;
}

.etape-contenu {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.etape-contenu.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.produit-selectionne-cadre {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.05);
    padding: 25px;
    position: relative;
    overflow: hidden;
    max-width: 800px;
    margin: 0 auto;
}

.personnalisation-options {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin: 40px 0;
}

.option-btn {
    background: linear-gradient(135deg, #f05124 0%, #ff6b47 100%);
    color: white;
    border: none;
    padding: 20px 40px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(240, 81, 36, 0.3);
    min-width: 200px;
}

.option-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 81, 36, 0.4);
}

.btn-suivant {
    background: #28a745;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin: 20px auto;
    display: block;
    transition: all 0.3s ease;
}

.btn-suivant:hover {
    background: #218838;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .etapes-ascenseur {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .etape {
        margin: 5px;
    }
    
    .separateur {
        display: none;
    }
    
    .personnalisation-options {
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
}
</style>

<main class="cadre">
    <div class="container">
        <h2 class="title-h3">Personnalisation de votre produit</h2>
        
        <!-- Ascenseur horizontal des étapes -->
        <div class="etapes-ascenseur">
            <div class="etape active" id="etape-1" onclick="afficherEtape(1)">
                <div class="etape-numero active">1</div>
                <span class="etape-texte">Produit</span>
            </div>
            
            <div class="separateur"></div>
            
            <div class="etape" id="etape-2" onclick="afficherEtape(2)">
                <div class="etape-numero">2</div>
                <span class="etape-texte">Personnalisation</span>
            </div>
            
            <div class="separateur"></div>
            
            <div class="etape" id="etape-3" onclick="afficherEtape(3)">
                <div class="etape-numero">3</div>
                <span class="etape-texte">Vos fichiers</span>
            </div>
        </div>
        
        <!-- Conteneur des étapes -->
        <div class="conteneur-etapes">
        
        <!-- ÉTAPE 1: PRODUIT -->
        <div class="etape-contenu active" id="contenu-etape-1">
            <?php if ($produit_id): ?>
            <div class="produit-selectionne-cadre">
                <!-- Titre avec icône -->
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
                    ">📦</div>
                    <h3 style="
                        margin: 0;
                        color: #2a256d;
                        font-size: 20px;
                        font-weight: 600;
                    ">Votre produit sélectionné</h3>
                </div>
                
                <!-- Informations produit -->
                <div style="
                    padding: 20px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
                    border-left: 4px solid #f05124;
                ">

                    
                    <!-- Ligne 1: Code - Désignation - Format -->
                    <div style="
                        display: flex; 
                        align-items: center; 
                        gap: 15px; 
                        margin-bottom: 15px;
                        flex-wrap: wrap;
                    ">
                        <div style="flex: 0 0 auto;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Code</div>
                            <div style="
                                font-weight: 600;
                                color: #2a256d;
                                font-size: 14px;
                                background: #fff5f3;
                                padding: 6px 10px;
                                border-radius: 4px;
                            "><?= htmlspecialchars($reference) ?></div>
                        </div>
                        
                        <div style="flex: 1; min-width: 200px;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Désignation</div>
                            <div style="
                                font-weight: 600;
                                color: #2a256d;
                                font-size: 16px;
                                line-height: 1.3;
                            "><?= htmlspecialchars($designation) ?></div>
                        </div>
                        
                        <?php if ($format): ?>
                        <div style="flex: 0 0 auto;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Format</div>
                            <div style="
                                font-size: 14px;
                                font-weight: 500;
                                color: #f05124;
                                background: #fff5f3;
                                padding: 6px 10px;
                                border-radius: 4px;
                            "><?= htmlspecialchars($format) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Ligne 2: Conditionnement - Couleur - Quantité -->
                    <div style="
                        display: flex; 
                        align-items: center; 
                        gap: 15px; 
                        margin-bottom: 15px;
                        flex-wrap: wrap;
                    ">
                        <?php if ($conditionnement): ?>
                        <div style="flex: 0 0 auto;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Conditionnement</div>
                            <div style="
                                font-size: 14px;
                                font-weight: 500;
                                color: #6f42c1;
                                background: #f8f5ff;
                                padding: 6px 10px;
                                border-radius: 4px;
                            ">Pack de <?= htmlspecialchars($conditionnement) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($couleur): ?>
                        <div style="flex: 0 0 auto;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Couleur</div>
                            <div style="
                                font-weight: 600;
                                color: #2a256d;
                                font-size: 14px;
                                background: #f0f0f0;
                                padding: 6px 10px;
                                border-radius: 4px;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <?= htmlspecialchars($couleur) ?>
                                <?php if ($imageCouleur): ?>
                                    <img src="<?= htmlspecialchars($imageCouleur) ?>" 
                                         alt="<?= htmlspecialchars($couleur) ?>" 
                                         style="width:20px;height:20px;border-radius:50%;border:1px solid #ddd;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div style="flex: 0 0 auto;">
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Quantité</div>
                            <div style="
                                font-weight: 600;
                                color: #2a256d;
                                font-size: 14px;
                                background: #f0f0f0;
                                padding: 6px 10px;
                                border-radius: 4px;
                            "><?= $quantite_selectionnee ?></div>
                        </div>
                    </div>
                    </div>
                    
                    <!-- Prix -->
                    <div style="
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                        margin-top: 20px;
                        padding-top: 20px;
                        border-top: 1px solid #eee;
                    ">
                        <!-- Prix unitaire -->
                        <div>
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Prix unitaire</div>
                            <div style="
                                font-weight: 600;
                                color: #17a2b8;
                                font-size: 16px;
                                background: #f0fbff;
                                padding: 8px 12px;
                                border-radius: 4px;
                                border: 1px solid #bee5eb;
                            ">
                                <?php 
                                $prixUnitaire = $conditionnement && intval($conditionnement) > 0 ? $prix / intval($conditionnement) : $prix;
                                echo number_format($prixUnitaire, 2, ',', ' ');
                                ?> € HT
                            </div>
                        </div>
                        
                        <!-- Prix total -->
                        <div>
                            <div style="
                                font-size: 11px;
                                color: #6c757d;
                                text-transform: uppercase;
                                font-weight: 500;
                                margin-bottom: 5px;
                                letter-spacing: 0.5px;
                            ">Total</div>
                            <div style="
                                font-weight: 700;
                                color: #28a745;
                                font-size: 18px;
                                background: #f8fff9;
                                padding: 10px 14px;
                                border-radius: 6px;
                                border: 1px solid #d4edda;
                            "><?= number_format($prix * $quantite_selectionnee, 2, ',', ' ') ?> € HT</div>
                        </div>
                    </div>
                </div>
                
                <!-- Bouton suivant -->
                <button type="button" class="btn-suivant" onclick="afficherEtape(2)">
                    Passer à la personnalisation →
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- ÉTAPE 2: PERSONNALISATION -->
        <div class="etape-contenu" id="contenu-etape-2">
            <div style="text-align: center; margin: 40px 0;">
                <h3 style="color: #2a256d; font-size: 24px; margin-bottom: 20px;">Type de personnalisation</h3>
                <p style="color: #666; font-size: 16px; margin-bottom: 40px;">Sélectionnez le type de personnalisation que vous souhaitez ajouter à votre produit</p>
                
                <?php
                // Déterminer quelles personnalisations sont disponibles pour ce produit via la table de liaisons
                $has_dorure = false;
                $has_imprime = false;
                $has_feuilles = false;
                if (!empty($reference)) {
                    require_once __DIR__ . '/../includes/database.php';
                    try {
                        $db = Database::getInstance()->getConnection();
                        $stmt = $db->prepare("SELECT ref_pre_encollage, ref_impression, ref_impression_2, ref_impression_3, enabled FROM personnalisation_liaisons WHERE produit_ref = ?");
                        $stmt->execute([trim($reference)]);
                        $liaisons = $stmt->fetchAll();
                        foreach ($liaisons as $l) {
                            $enabled = isset($l['enabled']) ? intval($l['enabled']) : 0;
                            if ($enabled) $has_dorure = true; // case 'Actif' = dorure
                            if (!empty($l['ref_impression']) || !empty($l['ref_impression_2']) || !empty($l['ref_impression_3'])) $has_imprime = true;
                            if (!empty($l['ref_pre_encollage'])) $has_feuilles = true;
                        }
                    } catch (Exception $e) {
                        error_log('Erreur liaison personnalisations: ' . $e->getMessage());
                    }
                }

                // Affichage des boutons selon disponibilités
                $countButtons = 0;
                ?>
                <div class="personnalisation-options">
                    <?php if ($has_dorure): $countButtons++; ?>
                        <button type="button" class="option-btn" onclick="afficherTableauPersonnalisation('dorure')">✨ Dorure</button>
                    <?php endif; ?>

                    <?php if ($has_imprime): $countButtons++; ?>
                        <button type="button" class="option-btn" onclick="afficherTableauPersonnalisation('imprime')">🎨 Impression couleur</button>
                    <?php endif; ?>

                    <?php if ($has_feuilles): $countButtons++; ?>
                        <button type="button" class="option-btn" onclick="afficherTableauPersonnalisation('feuilles')">📐 Feuilles pré-encollées</button>
                    <?php endif; ?>
                </div>

                <?php if ($countButtons === 0): ?>
                    <div style="text-align:center;padding:20px;background:#fff3f3;border:1px solid #ffd6d6;border-radius:8px;color:#8a1f1f;margin-top:20px;">Aucune personnalisation pour ce produit. Contactez-nous</div>
                <?php endif; ?>
                
                <!-- Zone pour afficher le tableau de personnalisation -->
                <div id="tableau-personnalisation" style="margin-top: 40px; display: none;">
                    <!-- Le tableau sera chargé ici via AJAX -->
                </div>
                
                <!-- Message de confirmation après ajout -->
                <div id="personnalisation-confirmee" style="display: none; margin: 20px 0;">
                    <div style="
                        background: #d4edda;
                        color: #155724;
                        padding: 15px;
                        border-radius: 8px;
                        border: 1px solid #c3e6cb;
                        margin: 20px 0;
                    ">
                        ✅ Personnalisation ajoutée au panier ! Vous pouvez maintenant passer aux fichiers.
                    </div>
                    <button type="button" class="btn-suivant" onclick="afficherEtape(3)">
                        Passer à l'ajout de fichiers →
                    </button>
                </div>
            </div>
        </div>
        
        <!-- ÉTAPE 3: VOS FICHIERS -->
        <div class="etape-contenu" id="contenu-etape-3">
            <div style="max-width: 600px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h3 style="color: #2a256d; font-size: 24px; margin-bottom: 10px;">Ajoutez vos fichiers</h3>
                    <p style="color: #666; font-size: 16px;">Uploadez les fichiers pour votre personnalisation (max 5MB chacun)</p>
                </div>
                
                <!-- Module d'upload -->
                <div class="upload-section">
                    <div class="file-upload-area">
                        <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                        <div class="upload-dropzone" onclick="document.getElementById('imageUpload').click()" style="
                            border: 3px dashed #ddd;
                            border-radius: 10px;
                            padding: 40px;
                            text-align: center;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            background: #f9f9f9;
                        ">
                            <div style="font-size: 48px; margin-bottom: 15px;">📁</div>
                            <p style="font-size: 18px; color: #333; margin-bottom: 10px;">Cliquez ici ou glissez-déposez vos fichiers</p>
                            <span style="color: #666; font-size: 14px;">Formats acceptés : JPG, PNG, WebP</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                        <div class="images-counter" style="color: #666;">
                            <span id="imageCount">0</span> / 30 fichiers
                        </div>
                    </div>
                    
                    <div class="images-preview" id="imagesPreview" style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                        gap: 15px;
                        margin: 20px 0;
                    ">
                        <!-- Les images apparaîtront ici -->
                    </div>

                    <!-- Champ message pour les détails de personnalisation -->
                    <div style="margin-top:16px;">
                        <label for="detailPersonnalisation" style="display:block;font-weight:600;margin-bottom:6px;color:#2a256d;">Détail de votre personnalisation</label>
                        <input type="text" id="detailPersonnalisation" name="detail_personnalisation" placeholder="dimentions et placement" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;" />
                    </div>
                    
                    <!-- Bouton final -->
                    <div class="upload-actions" id="uploadActions" style="display: none; text-align: center; margin-top: 30px;">
                        <button type="button" 
                                class="btn-ajouter-panier-photos" 
                                id="btnAjouterPanier"
                                onclick="finaliserCommande()" 
                                style="
                                    background: #28a745; 
                                    color: white; 
                                    border: none; 
                                    padding: 15px 40px; 
                                    border-radius: 8px; 
                                    font-size: 18px; 
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                ">
                            Finaliser et aller au panier
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        </div> <!-- Fin conteneur-etapes -->
    </div>
</main>

<script src="../js/panier.js"></script>
<script src="../js/simple-crop.js"></script>
<script src="../js/image-upload.js"></script>

<!-- Modal pour le recadrage (copié depuis formulaires/photo.php) -->
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

<script>
// Variables globales pour le processus de personnalisation
let etapeActuelle = 1;
let personnalisationChoisie = null;
let fichiersUploadés = [];
// Désactiver complètement l'affichage des popups panier sur cette page
window.disablePanierPopup = true;
// Référence et format du produit courant (disponibles côté serveur)
const produitRef = <?= json_encode($reference) ?>;
const produitFormat = <?= json_encode($format) ?>;

/**
 * Affiche une étape spécifique
 */
function afficherEtape(numeroEtape) {
    // Ne pas permettre d'aller à l'étape 3 sans avoir choisi une personnalisation
    if (numeroEtape === 3 && !personnalisationChoisie) {
        alert('Veuillez d\'abord choisir un type de personnalisation.');
        return;
    }
    
    // Cacher toutes les étapes
    document.querySelectorAll('.etape-contenu').forEach(contenu => {
        contenu.classList.remove('active');
    });
    
    // Retirer la classe active de toutes les étapes
    document.querySelectorAll('.etape').forEach(etape => {
        etape.classList.remove('active');
        etape.querySelector('.etape-numero').classList.remove('active');
    });
    
    // Afficher l'étape sélectionnée
    document.getElementById(`contenu-etape-${numeroEtape}`).classList.add('active');
    document.getElementById(`etape-${numeroEtape}`).classList.add('active');
    document.querySelector(`#etape-${numeroEtape} .etape-numero`).classList.add('active');
    
    etapeActuelle = numeroEtape;
}

/**
 * Affiche le tableau de personnalisation selon le type choisi
 */
function afficherTableauPersonnalisation(type) {
    personnalisationChoisie = type;
    
    const tableauDiv = document.getElementById('tableau-personnalisation');
    tableauDiv.style.display = 'block';
    
    // Simuler le chargement (vous pourrez remplacer par un vrai appel AJAX)
    tableauDiv.innerHTML = `
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 24px; margin-bottom: 15px;">⏳</div>
            <p>Chargement du tableau ${type}...</p>
        </div>
    `;
    
    // Simuler le chargement des produits de personnalisation
    setTimeout(() => {
        // Pour la dorure on charge la famille 'dorure', pour l'impression couleur on charge plusieurs sections
        if (type === 'dorure') {
            chargerTableauPersonnalisation('dorure', <?= intval($quantite_selectionnee) ?>, <?= intval($conditionnement ?: 1) ?>);
        } else if (type === 'imprime') {
            // Afficher un seul tableau centralisé pour l'impression couleur
            const section = { id: 'imprime-tirage', family: 'Tirage Photo', title: 'Tirage Photo' };

            tableauDiv.innerHTML = `
                <div class="imprime-section" style="margin-bottom:24px;">
                    <h4 style="margin:0 0 10px 0; color:#2a256d;">${section.title}</h4>
                    <div id="${section.id}" class="personnalisation-subtable" style="background:#fff; border:1px solid #eee; padding:12px; border-radius:6px;">Chargement...</div>
                </div>
            `;

            // Charger le tableau unique
            chargerTableauPersonnalisation(section.family, <?= intval($quantite_selectionnee) ?>, <?= intval($conditionnement ?: 1) ?>, section.id);
        } else if (type === 'feuilles') {
            // Nouvelle section 'Feuilles pré-encollées' contenant le tableau Panoramique
            const sections = [
                { id: 'feuilles-panoramique', family: 'Tirage Panoramique', title: 'Impression de Feuilles Pré-encollées' }
            ];

            tableauDiv.innerHTML = sections.map(s => `
                <div class="feuilles-section" style="margin-bottom:24px;">
                    <h4 style="margin:0 0 10px 0; color:#2a256d;">${s.title}</h4>
                    <div id="${s.id}" class="personnalisation-subtable" style="background:#fff; border:1px solid #eee; padding:12px; border-radius:6px;">Chargement...</div>
                </div>
            `).join('');

            sections.forEach(s => {
                chargerTableauPersonnalisation(s.family, <?= intval($quantite_selectionnee) ?>, <?= intval($conditionnement ?: 1) ?>, s.id);
            });
        } else {
            // Comportement par défaut: charger la famille demandée
            chargerTableauPersonnalisation(type, <?= intval($quantite_selectionnee) ?>, <?= intval($conditionnement ?: 1) ?>);
        }
    }, 1000);
}

/**
 * Charge le tableau de personnalisation via AJAX
 */
async function chargerTableauPersonnalisation(famille, quantite = 1, conditionnement = 1) {
    // If a target container id is passed as 4th argument, use it; otherwise use main tableau element
    const args = Array.prototype.slice.call(arguments);
    const targetId = args.length >= 4 ? args[3] : null;
    const tableauDiv = targetId ? document.getElementById(targetId) : document.getElementById('tableau-personnalisation');

    // Debug log pour surveiller les appels depuis l'UI
    console.log('chargerTableauPersonnalisation called', {famille, quantite, conditionnement, produitRef, produitFormat, targetId});

    try {
        // Ajouter référence produit et format si disponibles pour filtrage via table de liaison
        const params = new URLSearchParams();
        params.set('type', famille);
        params.set('quantite', quantite);
        params.set('conditionnement', conditionnement);
        if (typeof produitRef !== 'undefined' && produitRef) params.set('produit_ref', produitRef);
        if (typeof produitFormat !== 'undefined' && produitFormat) params.set('produit_format', produitFormat);

        const response = await fetch(`../ajax/charger-personnalisation.php?${params.toString()}`);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const html = await response.text();
        if (tableauDiv) tableauDiv.innerHTML = html;

    } catch (error) {
        console.error('Erreur lors du chargement:', error);
        if (tableauDiv) {
            tableauDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb; text-align: center;">
                    <strong>Erreur de chargement :</strong> ${error.message}
                </div>
            `;
        }
    }
}

/**
 * Variables globales pour les quantités et couleurs
 */
const quantitesPersonnalisation = {};
const couleursPersonnalisation = {};

/**
 * Modifie la quantité d'un produit de personnalisation (système tableau existant)
 */
function modifierQuantitePersonnalisation(bouton, delta) {
    const ligne = bouton.closest('.ligne-produit');
    const input = ligne.querySelector('.input-quantite');
    let nouvelleQuantite = parseInt(input.value) + delta;
    
    // Minimum 1, maximum 99
    if (nouvelleQuantite < 1) nouvelleQuantite = 1;
    if (nouvelleQuantite > 99) nouvelleQuantite = 99;
    
    input.value = nouvelleQuantite;
}

/**
 * Gère la sélection de couleur pour les produits de dorure
 */
function selectionnerCouleurPersonnalisation(selectElement, produitId) {
    const couleurNom = selectElement.value;
    const rawImage = selectElement.selectedOptions[0]?.dataset.image || '';

    // Résoudre le chemin de l'image : si la valeur contient déjà 'images' ou commence par '/' ou 'http', l'utiliser tel quel
    function resolveImagePath(img) {
        if (!img) return '';
        if (img.startsWith('http') || img.startsWith('/') || img.includes('images/')) return img;
        return `../images/couleurs/${img}`;
    }

    const couleurImage = resolveImagePath(rawImage);

    // Stocker la couleur sélectionnée
    couleursPersonnalisation[produitId] = {
        nom: couleurNom,
        image: couleurImage
    };

    // Afficher l'aperçu de couleur
    const preview = document.getElementById(`couleur-preview-${produitId}`);
    if (preview) {
        if (couleurNom) {
            const img = preview.querySelector('.couleur-image-preview');
            const span = preview.querySelector('.couleur-nom-preview');

            if (couleurImage) {
                img.src = couleurImage;
                img.alt = couleurNom;
                img.style.display = 'inline-block';
            } else {
                img.style.display = 'none';
            }

            span.textContent = couleurNom;
            preview.style.display = 'flex';
            preview.style.alignItems = 'center';
            preview.style.gap = '8px';
        } else {
            preview.style.display = 'none';
        }
    }
}

/**
 * Ajoute un produit de personnalisation au panier depuis le tableau
 */
function ajouterPersonnalisationAuPanierDepuisTableau(bouton) {
    const ligne = bouton.closest('.ligne-produit');
    const produitId = ligne.dataset.id;
    const reference = ligne.dataset.reference;
    const designation = ligne.dataset.designation;
    const format = ligne.dataset.format;
    const prix = parseFloat(ligne.dataset.prix);
    const quantite = parseInt(ligne.querySelector('.input-quantite').value);
    
    // Récupérer la couleur sélectionnée (pour dorure)
    let couleur = '';
    let imageCouleur = '';
    const couleurSelect = ligne.querySelector('.couleur-select');
    if (couleurSelect && couleurSelect.value) {
        couleur = couleurSelect.value;

        // Priorité : couleur stockée via selectionnerCouleurPersonnalisation
        if (couleursPersonnalisation[produitId] && couleursPersonnalisation[produitId].image) {
            imageCouleur = couleursPersonnalisation[produitId].image;
        } else {
            // Fallback : résoudre le chemin depuis l'attribut data-image
            const optionSelected = couleurSelect.selectedOptions[0];
            const rawImg = optionSelected?.dataset.image || '';
            if (rawImg) {
                if (rawImg.startsWith('http') || rawImg.startsWith('/') || rawImg.includes('images/')) {
                    imageCouleur = rawImg;
                } else {
                    imageCouleur = `../images/couleurs/${rawImg}`;
                }
            }
        }

        // Vérifier que la couleur est bien sélectionnée pour la dorure
        if (couleurSelect.closest('.col-couleur') && !couleur) {
            alert('Veuillez sélectionner une couleur avant d\'ajouter au panier.');
            return;
        }
    }
    
    console.log(`Ajout de ${quantite}x ${reference} (${couleur}) au prix unitaire de ${prix}€`);
    
    // Ajouter au panier
    if (typeof panierManager !== 'undefined') {
        const details = {
            code: reference,
            designation: designation,
            format: format || '',
            conditionnement: '',
            matiere: '',
            couleur: couleur,
            imageCouleur: imageCouleur || ''
        };
        
        panierManager.ajouterProduit(`pers_${reference}_${Date.now()}`, quantite, prix, details);
        
        // Afficher notification de succès
        panierManager.afficherNotification(
            `${quantite}x ${designation} ${couleur ? '(' + couleur + ')' : ''} ajouté au panier !`,
            "success",
            {
                code: reference,
                designation: designation,
                format: format || '',
                couleur: couleur,
                imageCouleur: imageCouleur ? `../images/couleurs/${imageCouleur}` : '',
                quantite: quantite
            }
        );
        
        // Masquer le tableau et afficher la confirmation
        document.getElementById('tableau-personnalisation').style.display = 'none';
        document.getElementById('personnalisation-confirmee').style.display = 'block';
    }
}

/**
 * Ajoute l'article de personnalisation au panier avec quantité
 */
function ajouterPersonnalisationAuPanierAvecQte(reference, designation, prix, format, produitId) {
    const quantite = quantitesPersonnalisation[produitId] || 1;
    console.log(`Ajout de ${quantite}x ${reference} au prix unitaire de ${prix}€`);
    
    // Ajouter réellement l'article au panier
    if (typeof panierManager !== 'undefined') {
        const details = {
            code: reference,
            designation: designation,
            format: format || '',
            conditionnement: '',
            matiere: '',
            couleur: '',
            imageCouleur: ''
        };
        
        panierManager.ajouterProduit(`pers_${reference}_${Date.now()}`, quantite, parseFloat(prix), details);
        
        // Afficher notification de succès
        panierManager.afficherNotification(
            `${quantite}x Personnalisation ajoutée au panier !`,
            "success",
            {
                code: reference,
                designation: designation,
                format: format || '',
                couleur: '',
                imageCouleur: '',
                quantite: quantite
            }
        );
    }
    
    // Masquer le tableau et afficher la confirmation
    document.getElementById('tableau-personnalisation').style.display = 'none';
    document.getElementById('personnalisation-confirmee').style.display = 'block';
}

/**
 * Ajoute l'article de personnalisation au panier (ancienne fonction maintenue pour compatibilité)
 */
function ajouterPersonnalisationAuPanier(reference, designation, prix, format) {
    console.log(`Ajout de la personnalisation ${reference} au prix de ${prix}€`);
    
    // Ajouter réellement l'article au panier
    if (typeof panierManager !== 'undefined') {
        const details = {
            code: reference,
            designation: designation,
            format: format || '',
            conditionnement: '',
            matiere: '',
            couleur: '',
            imageCouleur: ''
        };
        
        panierManager.ajouterProduit(`pers_${reference}_${Date.now()}`, 1, parseFloat(prix), details);
        
        // Afficher notification de succès
        panierManager.afficherNotification(
            "Personnalisation ajoutée au panier !",
            "success",
            {
                code: reference,
                designation: designation,
                format: format || '',
                couleur: '',
                imageCouleur: '',
                quantite: 1
            }
        );
    }
    
    // Masquer le tableau et afficher la confirmation
    document.getElementById('tableau-personnalisation').style.display = 'none';
    document.getElementById('personnalisation-confirmee').style.display = 'block';
}

/**
 * Finalise la commande et redirige vers le panier
 */
function finaliserCommande() {
    // Récupérer les infos du produit principal (étape 1)
    const reference = "<?= addslashes($reference) ?>";
    const designation = "<?= addslashes($designation) ?>";
    const format = "<?= addslashes($format) ?>";
    const prix = parseFloat("<?= addslashes($prix) ?>");
    const conditionnement = "<?= addslashes($conditionnement) ?>";
    const quantite = <?= $quantite_selectionnee ?>;
    const couleur = "<?= addslashes($couleur) ?>";
    const imageCouleur = "<?= addslashes($imageCouleur) ?>";

    // Récupérer les fichiers uploadés (exposés par le script d'upload)
    // On supporte deux sources : `window.fichiersUploades` (script image-upload.js)
    // ou la variable locale `fichiersUploadés` si présente.
    let fichiers = [];
    const globalFiles = (typeof window !== 'undefined' && window.fichiersUploades) ? window.fichiersUploades : (typeof fichiersUploadés !== 'undefined' ? fichiersUploadés : null);
    if (Array.isArray(globalFiles)) {
        fichiers = globalFiles.map(f => {
            if (!f) return f;
            if (typeof f === 'string') return f;

            const obj = {};
            if (f.name) obj.name = f.name;
            if (f.dataUrl) obj.dataUrl = f.dataUrl;
            if (f.originalDataUrl) obj.originalDataUrl = f.originalDataUrl;
            if (f.size) obj.size = f.size;
            if (f.type) obj.type = f.type;
            if (f.url) obj.url = f.url;
            if (f.src) obj.src = f.src;
            if (f.file && f.file.name && !obj.name) obj.name = f.file.name;

            // If we only have a name, keep the string for backward compatibility
            if (Object.keys(obj).length === 1 && obj.name) return obj.name;
            return obj;
        });
    }

    // Ajouter l'article principal au panier en y joignant les fichiers de personnalisation
    if (typeof panierManager !== 'undefined') {
        // Utiliser l'ID produit si disponible, sinon fallback sur la référence
        const produitId = "<?= addslashes($produit_id ?: 'prod_' . $reference) ?>";

        const detailsProduit = {
            code: reference,
            designation: designation,
            format: format,
            conditionnement: conditionnement,
            couleur: couleur,
            imageCouleur: imageCouleur,
            photos: fichiers,
            nombrePhotos: fichiers.length,
            source: 'perso'
        };

        // Récupérer le détail de personnalisation saisi par l'utilisateur (dimensions / placement)
        try {
            const detail = document.getElementById('detailPersonnalisation') ? document.getElementById('detailPersonnalisation').value.trim() : '';
            if (detail) detailsProduit.personnalisation_detail = detail;
        } catch (e) {
            console.warn('Impossible de lire detailPersonnalisation:', e);
        }

        // On ajoute l'article principal avec les photos attachées — ainsi le produit original n'est pas perdu
        panierManager.ajouterProduit(produitId, quantite, prix, detailsProduit);
        panierManager.afficherNotification(
            `Produit personnalisé ajouté au panier !`,
            "success",
            Object.assign({ quantite: quantite }, detailsProduit)
        );

        // Debug: log du panier local après ajout
        try {
            console.log('[perso] Panier local après ajout :', panierManager.panier);
        } catch (e) {
            console.warn('[perso] Impossible de logger panierManager', e);
        }

        // Forcer la synchronisation client -> session avant de rediriger pour éviter la perte lors du rendu serveur
        try {
            fetch('/pages/sync_panier.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(panierManager.panier)
            }).then(resp => resp.json()).then(data => {
                console.log('[perso] Synchronisation vers session terminée:', data);
                // Redirection après confirmation de sync
                window.location.href = '/pages/panier.php';
            }).catch(err => {
                console.warn('[perso] Erreur sync, redirection quand même:', err);
                window.location.href = '/pages/panier.php';
            });
            return; // empêcher la redirection immédiate ci-dessous
        } catch (e) {
            console.warn('[perso] Exception lors de la sync:', e);
        }
    }

    // Rediriger vers le panier (fallback si la sync asynchrone n'a pas pu être lancée)
    window.location.href = '/pages/panier.php';
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // S'assurer que l'étape 1 est affichée par défaut
    afficherEtape(1);
});

// Fallback: observer pour afficher #uploadActions si des vignettes sont ajoutées
window.addEventListener('load', function() {
    try {
        const imagesPreview = document.getElementById('imagesPreview');
        const uploadActions = document.getElementById('uploadActions');
        if (!imagesPreview || !uploadActions) return;

        const obs = new MutationObserver(() => {
            uploadActions.style.display = imagesPreview.children.length > 0 ? 'block' : 'none';
        });
        obs.observe(imagesPreview, { childList: true });

        // Vérification initiale
        uploadActions.style.display = imagesPreview.children.length > 0 ? 'block' : 'none';
    } catch (e) {
        console.error('Observer fallback error:', e);
    }
});
</script>

<style>
.upload-dropzone:hover {
    border-color: #f05124 !important;
    background: #fff5f3 !important;
}

.btn-ajouter-panier-photos:hover {
    background: #218838 !important;
    transform: translateY(-1px);
}

.images-preview img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #ddd;
}

.image-preview-item {
    position: relative;
}

.image-preview-item button {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255, 0, 0, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-size: 12px;
}
</style>

<?php include '../includes/footer.php'; ?>