<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

// Vérifier l'ID du produit
if (!isset($_GET['id'])) {
    $_SESSION['message'] = 'Produit non trouvé';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Récupérer le produit existant
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    $_SESSION['message'] = 'Produit non trouvé';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Traitement du formulaire
if ($_POST) {
    // Préparer les données selon la vraie structure de la table
    $estCompose = isset($_POST['est_compose']) && $_POST['est_compose'] == '1';
    $compositionAuto = isset($_POST['composition_auto']) && $_POST['composition_auto'] == '1';
    
    $donnees = [
        'famille' => $_POST['famille'] ?? '',
        'nomDeLaFamille' => $_POST['nomDeLaFamille'] ?? '',
        'reference' => $_POST['reference'] ?? '',
        'designation' => $_POST['designation'] ?? '',
        'format' => $_POST['format'] ?? '',
        'ordre' => isset($_POST['ordre']) ? intval($_POST['ordre']) : 0,
        'est_compose' => $estCompose ? 1 : 0,
        'composition_auto' => $compositionAuto ? 1 : 0,
        'prixAchat' => floatval($_POST['prixAchat'] ?? 0),
        'prixVente' => floatval($_POST['prixVente'] ?? 0),
        'conditionnement' => $_POST['conditionnement'] ?? '',
        'matiere' => $_POST['matiere'] ?? '',
        'couleur_interieur' => $_POST['couleur_interieur'] ?? ''
    ];
    
    // Traiter les 13 couleurs extérieures selon la vraie structure
    for ($i = 1; $i <= 13; $i++) {
        $donnees["couleur_ext$i"] = $_POST["couleur$i"] ?? '';
        $donnees["imageCoul$i"] = $_POST["imageCoul$i"] ?? ''; // Chemin de l'image
    }
    
    // Modifier le produit
    $result = modifierProduit($id, $donnees);
    
    if ($result) {
        // Si c'est un article composé, traiter les composants
        if ($estCompose) {
            $composantsData = $_POST['composants_data'] ?? '';
            if (!empty($composantsData)) {
                $composants = json_decode($composantsData, true);
                if ($composants && is_array($composants)) {
                    ajouterComposants($id, $composants);
                }
            } else {
                // Si pas de données de composants, supprimer les compositions existantes
                ajouterComposants($id, []);
            }
        } else {
            // Si ce n'est plus un article composé, supprimer les compositions existantes
            ajouterComposants($id, []);
        }
        
        $_SESSION['message'] = 'Produit modifié avec succès';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['message'] = 'Erreur lors de la modification du produit';
        $_SESSION['message_type'] = 'error';
    }
}

// Récupérer les familles existantes pour l'aide à la saisie
$familles = getFamilles();

include 'header.php';
?>

    <div class="page-header">
        <h2><i class="fas fa-edit"></i> Modifier le produit #<?= $produit['id'] ?></h2>
        <div>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <form method="POST" class="product-form">
        <!-- Informations générales -->
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
            
            <div class="form-row admin-form-row">
                <div class="form-group admin-col-ordre">
                    <label for="ordre">Ordre d'affichage</label>
                    <input type="number" id="ordre" name="ordre" min="0" step="1"
                           value="<?= htmlspecialchars($_POST['ordre'] ?? $produit['ordre'] ?? '0') ?>"
                           placeholder="0" class="admin-input-full">
                </div>

                <div class="form-group admin-col-reference">
                    <label for="reference">Référence *</label>
                    <input type="text" id="reference" name="reference" required 
                           value="<?= htmlspecialchars($_POST['reference'] ?? $produit['reference'] ?? '') ?>"
                           placeholder="Ex: REL-A4-001" class="admin-input-full">
                </div>

                <div class="form-group admin-col-designation">
                    <label for="designation">Désignation *</label>
                    <input type="text" id="designation" name="designation" required 
                           value="<?= htmlspecialchars($_POST['designation'] ?? $produit['designation'] ?? '') ?>"
                           placeholder="Ex: Reliure spirale A4" class="admin-input-full">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="famille">Famille *</label>
                    <input type="text" id="famille" name="famille" required list="famillesList"
                           value="<?= htmlspecialchars($_POST['famille'] ?? $produit['famille'] ?? '') ?>"
                           placeholder="Ex: Reliures, Couvertures...">
                    <datalist id="famillesList">
                        <?php foreach ($familles as $famille): ?>
                            <option value="<?= htmlspecialchars($famille) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                
                <div class="form-group">
                    <label for="nomDeLaFamille">Nom de la famille</label>
                    <input type="text" id="nomDeLaFamille" name="nomDeLaFamille" 
                           value="<?= htmlspecialchars($_POST['nomDeLaFamille'] ?? $produit['nomDeLaFamille'] ?? '') ?>"
                           placeholder="Ex: Reliures personnalisées">
                    <small class="form-help">Nom descriptif complet de la famille</small>
                </div>
            </div>
            
            
            <div class="form-row">
                <div class="form-group">
                    <label for="format">Format</label>
                    <input type="text" id="format" name="format" 
                           value="<?= htmlspecialchars($_POST['format'] ?? $produit['format'] ?? '') ?>"
                           placeholder="Ex: A4, A5, 21x29.7cm">
                </div>
            </div>
        </div>

        <!-- Caractéristiques techniques -->
        <div class="form-section">
            <h3><i class="fas fa-cog"></i> Caractéristiques techniques</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="conditionnement">Conditionnement</label>
                    <input type="text" id="conditionnement" name="conditionnement" 
                           value="<?= htmlspecialchars($_POST['conditionnement'] ?? $produit['conditionnement'] ?? '') ?>"
                           placeholder="Ex: Par unité, Par lot de 10...">
                </div>
                
                <div class="form-group">
                    <label for="matiere">Matière</label>
                    <input type="text" id="matiere" name="matiere" 
                           value="<?= htmlspecialchars($_POST['matiere'] ?? $produit['matiere'] ?? '') ?>"
                           placeholder="Ex: Papier, Carton, Plastique">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="couleur_interieur">Couleur intérieure</label>
                    <input type="text" id="couleur_interieur" name="couleur_interieur" 
                           value="<?= htmlspecialchars($_POST['couleur_interieur'] ?? $produit['couleur_interieur'] ?? '') ?>"
                           placeholder="Ex: Blanc, Ivoire, Couleur">
                </div>
            </div>
        </div>

        <!-- Prix et marge - Section importante -->
        <div class="form-section highlight-section">
            <h3><i class="fas fa-euro-sign"></i> Prix et Marge (Important pour les étapes à venir)</h3>

            <div class="form-row admin-form-row center">
                <div class="form-group admin-col-25">
                    <label for="prixAchat">Prix d'achat * (€)</label>
                    <input type="number" id="prixAchat" name="prixAchat" required 
                           min="0" step="0.01" 
                           value="<?= htmlspecialchars($_POST['prixAchat'] ?? $produit['prixAchat'] ?? '0') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Coût d'achat unitaire HT</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="prixVente">Prix de vente * (€)</label>
                    <input type="number" id="prixVente" name="prixVente" required 
                           min="0" step="0.01" 
                           value="<?= htmlspecialchars($_POST['prixVente'] ?? $produit['prixVente'] ?? '0') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Prix de vente unitaire HT</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="marge">Marge calculée (%)</label>
                    <input type="number" id="marge" name="marge" readonly
                           value="<?= htmlspecialchars($_POST['marge'] ?? $produit['marge'] ?? '0') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Calculée automatiquement</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="prixUnitaire" class="admin-prixunitaire-label">Prix unitaire de vente (€)</label>
                    <input type="number" id="prixUnitaire" name="prixUnitaire" readonly
                           class="admin-prixunitaire-input"
                           placeholder="0.00">
                    <small class="form-help admin-form-help-green">Prix de vente ÷ conditionnement</small>
                </div>
            </div>
        </div>

        <!-- Article composé -->
        <div class="form-section">
            <h3><i class="fas fa-layer-group"></i> Article composé</h3>
            <p class="section-description">Créer un article constitué de plusieurs autres articles</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="est_compose" name="est_compose" value="1" 
                               <?= isset($_POST['est_compose']) ? (($_POST['est_compose'] == '1') ? 'checked' : '') : (($produit['est_compose'] ?? false) ? 'checked' : '') ?>>
                        <span>Cet article est un article composé</span>
                    </label>
                    <small class="form-help">Cochez cette case pour créer un article composé de plusieurs autres articles</small>
                </div>
            </div>
            
            <!-- Section des composants -->
            <div id="section-composants" style="display: <?= ($produit['est_compose'] ?? false) ? 'block' : 'none' ?>;">
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="composition_auto" name="composition_auto" value="1" 
                                   <?= isset($_POST['composition_auto']) ? (($_POST['composition_auto'] == '1') ? 'checked' : '') : (($produit['composition_auto'] ?? true) ? 'checked' : '') ?>>
                            Calcul automatique de la désignation et du prix
                        </label>
                        <small class="form-help">Si coché, la désignation et le prix seront automatiquement calculés à partir des articles composants</small>
                    </div>
                </div>
                
                <div class="composants-container">
                    <h4>Articles composants</h4>
                    <div class="search-article-container">
                        <div class="form-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="recherche-article">Rechercher un article</label>
                                <input type="text" id="recherche-article" placeholder="Tapez une référence ou désignation...">
                            </div>
                            <div class="form-group" style="flex: 0 0 100px;">
                                <label for="quantite-article">Quantité</label>
                                <input type="number" id="quantite-article" min="1" value="1" style="width: 100%;">
                            </div>
                            <div class="form-group" style="flex: 0 0 auto; align-self: end;">
                                <button type="button" id="btn-ajouter-composant" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        
                        <div id="resultats-recherche" class="resultats-recherche" style="display: none;"></div>
                    </div>
                    
                    <div id="liste-composants" class="liste-composants">
                        <div class="composants-header">
                            <div>Référence</div>
                            <div>Désignation</div>
                            <div>Prix unitaire</div>
                            <div>Quantité</div>
                            <div>Prix total</div>
                            <div>Actions</div>
                        </div>
                        <div id="composants-vides" class="composants-vide">
                            Aucun article ajouté
                        </div>
                    </div>
                    
                    <div class="composants-total">
                        <strong>Prix total calculé : <span id="prix-total-calcule">0.00</span> €</strong>
                    </div>
                </div>
                
                <!-- Champs cachés pour stocker les données des composants -->
                <input type="hidden" id="composants-data" name="composants_data" value="">
            </div>
        </div>

        <!-- Gestion des couleurs -->
        <div class="form-section">
            <h3><i class="fas fa-palette"></i> Couleurs disponibles</h3>
            <p class="section-description">Saisissez les couleurs disponibles pour ce produit et leurs images (jusqu'à 13 couleurs)</p>
            
            <div class="couleurs-container">
                <?php for ($i = 1; $i <= 13; $i++): ?>
                    <div class="couleur-item">
                        <h4>Couleur <?= $i ?></h4>
                        <div class="couleur-fields">
                            <div class="form-group">
                                <label for="couleur<?= $i ?>">Nom de la couleur</label>
                                <input type="text" id="couleur<?= $i ?>" name="couleur<?= $i ?>" 
                                       value="<?= htmlspecialchars($_POST["couleur$i"] ?? $produit["couleur_ext$i"] ?? '') ?>"
                                       placeholder="Exemple : Red">
                            </div>
                            <div class="form-group">
                                <label for="imageCoul<?= $i ?>">Chemin de l'image</label>
                                <input type="text" id="imageCoul<?= $i ?>" name="imageCoul<?= $i ?>" 
                                       value="<?= htmlspecialchars($_POST["imageCoul$i"] ?? $produit["imageCoul$i"] ?? '') ?>"
                                       placeholder="Exemple : mini/red.webp">
                                <small class="form-help">Chemin relatif vers le fichier image de la couleur : taille 16x16px</small>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>







        <!-- Informations de suivi -->
        <div class="form-section">
            <h3><i class="fas fa-info"></i> Informations de suivi</h3>
            
            <div class="info-grid">
                <div class="info-item">
                    <strong>Créé le :</strong> 
                    <?= date('d/m/Y H:i', strtotime($produit['dateCreation'] ?? 'now')) ?>
                </div>
                <?php if (!empty($produit['dateModification'])): ?>
                    <div class="info-item">
                        <strong>Modifié le :</strong> 
                        <?= date('d/m/Y H:i', strtotime($produit['dateModification'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Sauvegarder les modifications
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
            <a href="?action=supprimer&id=<?= $produit['id'] ?>" 
               class="btn btn-danger"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
               style="margin-left: auto;">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
    </form>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-dark);
        position: relative;
        z-index: 10;
    }

    .page-header h2 {
        color: var(--primary-dark);
        font-weight: 500;
        margin: 0;
    }

    .product-form {
        background: #d2d2d2;
        border-radius: 8px;
        padding: 30px;
        box-shadow: var(--shadow);
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-section {
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 20px;
    }

    .form-section h3 {
        color: var(--primary-dark);
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .highlight-section {
        background: #fff8e1;
        padding: 25px;
        border-radius: 8px;
        border: 2px solid var(--primary-orange);
    }

    .section-description {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 20px;
        font-style: italic;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 0 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        transition: border-color 0.2s ease;
        background: white !important;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 3px rgba(42, 37, 109, 0.1);
    }

    .form-help {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        font-style: italic;
    }

    .couleurs-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
    }
    
    .couleur-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
    
    .couleur-item h4 {
        color: var(--primary-dark);
        margin-bottom: 15px;
        font-size: 16px;
        font-weight: 500;
    }
    
    .couleur-fields {
        display: grid;
        gap: 15px;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 400;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        background: var(--background-light);
        padding: 15px;
        border-radius: 6px;
    }

    .info-item {
        font-size: 14px;
        color: var(--text-muted);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        align-items: center;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Styles pour les articles composés */
    .search-article-container {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .resultats-recherche {
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-top: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .resultat-item {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
        line-height: 1.4;
    }

    .resultat-item:hover {
        background-color: #f8f9fa;
    }

    .resultat-item:last-child {
        border-bottom: none;
    }

    .liste-composants {
        background: white;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }

    .composants-header {
        display: grid;
        grid-template-columns: 1fr 2fr 120px 80px 120px 80px;
        gap: 10px;
        padding: 15px;
        background: var(--primary-dark);
        color: white;
        font-weight: 500;
        font-size: 14px;
    }

    .composant-item {
        display: grid;
        grid-template-columns: 1fr 2fr 120px 80px 120px 80px;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #eee;
        align-items: center;
    }

    .composant-item:last-child {
        border-bottom: none;
    }

    .composants-vide {
        padding: 30px;
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
    }

    .composants-total {
        padding: 15px;
        background: #e8f5e8;
        color: var(--success-color);
        text-align: right;
        border-radius: 6px;
        font-size: 16px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .couleurs-container {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<script>
    // Calcul automatique de la marge et du prix unitaire
    function calculerMarge() {
        const prixAchat = parseFloat(document.getElementById('prixAchat').value) || 0;
        const prixVente = parseFloat(document.getElementById('prixVente').value) || 0;
        const conditionnement = parseFloat(document.getElementById('conditionnement').value) || 1;
        
        // Calcul de la marge
        if (prixAchat > 0 && prixVente > prixAchat) {
            const marge = ((prixVente - prixAchat) / prixVente) * 100;
            document.getElementById('marge').value = marge.toFixed(2);
        } else {
            document.getElementById('marge').value = '';
        }
        
        // Calcul du prix unitaire (arrondi au centime supérieur)
        if (prixVente > 0 && conditionnement > 0) {
            const prixUnitaire = prixVente / conditionnement;
            const prixUnitaireArrondi = Math.ceil(prixUnitaire * 100) / 100; // centime supérieur
            document.getElementById('prixUnitaire').value = prixUnitaireArrondi.toFixed(2);
        } else {
            document.getElementById('prixUnitaire').value = '';
        }
    }

    // Attacher les événements
    document.getElementById('prixAchat').addEventListener('input', calculerMarge);
    document.getElementById('prixVente').addEventListener('input', calculerMarge);
    document.getElementById('conditionnement').addEventListener('input', calculerMarge);

    // Calcul initial
    calculerMarge();

    // ========== GESTION DES ARTICLES COMPOSÉS (MODIFICATION) ==========
    let composants = [];
    let rechercheTimeout = null;
    
    // Charger les composants existants lors du chargement de la page
    <?php if ($produit['est_compose'] ?? false): ?>
        <?php 
        $composantsExistants = getComposantsProduit($id);
        if (!empty($composantsExistants)): 
        ?>
            composants = <?= json_encode($composantsExistants) ?>;
            mettreAJourAffichageComposants();
        <?php endif; ?>
    <?php endif; ?>

    // Attendre que le DOM soit complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle de la section composants
        const estComposeCheckbox = document.getElementById('est_compose');
        if (estComposeCheckbox) {
            estComposeCheckbox.addEventListener('change', function() {
                const sectionComposants = document.getElementById('section-composants');
                const sectionPrix = document.querySelector('.highlight-section');
                
                if (this.checked) {
                    if (sectionComposants) sectionComposants.style.display = 'block';
                    toggleSectionPrix();
                } else {
                    if (sectionComposants) sectionComposants.style.display = 'none';
                    if (sectionPrix) sectionPrix.style.display = 'block';
                    composants = [];
                    mettreAJourAffichageComposants();
                }
            });
        }

        // Toggle de la section prix selon le mode de composition
        const compositionAutoCheckbox = document.getElementById('composition_auto');
        if (compositionAutoCheckbox) {
            compositionAutoCheckbox.addEventListener('change', toggleSectionPrix);
        }

    function toggleSectionPrix() {
        const sectionPrix = document.querySelector('.highlight-section');
        const compositionAuto = document.getElementById('composition_auto').checked;
        const estCompose = document.getElementById('est_compose').checked;
        
        if (estCompose && compositionAuto) {
            sectionPrix.style.display = 'none';
        } else {
            sectionPrix.style.display = 'block';
        }
    }

        // Recherche d'articles
        const rechercheArticleInput = document.getElementById('recherche-article');
        if (rechercheArticleInput) {
            rechercheArticleInput.addEventListener('input', function() {
                const terme = this.value.trim();
                
                clearTimeout(rechercheTimeout);
                
                if (terme.length < 2) {
                    masquerResultatsRecherche();
                    return;
                }
                
                rechercheTimeout = setTimeout(() => {
                    rechercherArticles(terme);
                }, 300);
            });
        }

    async function rechercherArticles(terme) {
        console.log('Recherche lancée pour:', terme);
        try {
            const response = await fetch('recherche_articles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `terme=${encodeURIComponent(terme)}`
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const articles = await response.json();
            console.log('Articles trouvés:', articles);
            afficherResultatsRecherche(articles);
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            masquerResultatsRecherche();
        }
    }

    function afficherResultatsRecherche(articles) {
        const conteneur = document.getElementById('resultats-recherche');
        
        if (articles.length === 0) {
            masquerResultatsRecherche();
            return;
        }
        
        const html = articles.map(article => {
            const designationEchappee = article.designation.replace(/'/g, "\\'");
            
            // Construire les informations supplémentaires
            let infosSupp = [];
            if (article.format) {
                infosSupp.push(`Format: ${article.format}`);
            }
            if (article.matiere) {
                infosSupp.push(`Matière: ${article.matiere}`);
            }
            
            let couleursHtml = '';
            if (article.couleurs && article.couleurs.length > 0) {
                couleursHtml = `<div style="font-size: 0.85em; color: #666; margin-top: 2px;">
                    ${article.couleurs.join(' • ')}
                </div>`;
            }
            
            let infosSupplementaires = '';
            if (infosSupp.length > 0) {
                infosSupplementaires = `<div style="font-size: 0.85em; color: #666; font-style: italic;">
                    ${infosSupp.join(' • ')}
                </div>`;
            }
            
            return `
                <div class="resultat-item" onclick="selectionnerArticle(${article.id}, '${article.reference}', '${designationEchappee}', ${article.prixVente})">
                    <div><strong>${article.reference}</strong></div>
                    <div>${article.designation}</div>
                    ${infosSupplementaires}
                    ${couleursHtml}
                    <div style="color: var(--success-color); font-weight: 500; margin-top: 4px;">${parseFloat(article.prixVente).toFixed(2)} €</div>
                </div>
            `;
        }).join('');
        
        conteneur.innerHTML = html;
        conteneur.style.display = 'block';
    }

    function masquerResultatsRecherche() {
        document.getElementById('resultats-recherche').style.display = 'none';
    }

    function selectionnerArticle(id, reference, designation, prix) {
        const quantite = parseInt(document.getElementById('quantite-article').value) || 1;
        
        // Vérifier si l'article n'est pas déjà présent
        if (composants.find(c => c.id == id)) {
            alert('Cet article est déjà dans la composition');
            return;
        }
        
        // Ajouter l'article à la liste
        composants.push({
            id: parseInt(id),
            reference: reference,
            designation: designation,
            prix: parseFloat(prix),
            quantite: quantite
        });
        
        // Mettre à jour l'affichage
        mettreAJourAffichageComposants();
        
        // Vider les champs de recherche
        document.getElementById('recherche-article').value = '';
        document.getElementById('quantite-article').value = 1;
        masquerResultatsRecherche();
        
        // Mettre à jour la désignation et le prix si mode automatique
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function supprimerComposant(index) {
        composants.splice(index, 1);
        mettreAJourAffichageComposants();
        
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function modifierQuantiteComposant(index, nouvelleQuantite) {
        if (nouvelleQuantite < 1) return;
        
        composants[index].quantite = nouvelleQuantite;
        mettreAJourAffichageComposants();
        
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function mettreAJourAffichageComposants() {
        const conteneur = document.getElementById('liste-composants');
        const vide = document.getElementById('composants-vides');
        
        if (composants.length === 0) {
            vide.style.display = 'block';
            document.getElementById('prix-total-calcule').textContent = '0.00';
        } else {
            vide.style.display = 'none';
            
            const html = composants.map((composant, index) => {
                const prixTotal = composant.prix * composant.quantite;
                return `
                    <div class="composant-item">
                        <div>${composant.reference}</div>
                        <div>${composant.designation}</div>
                        <div style="text-align: right;">${composant.prix.toFixed(2)} €</div>
                        <div style="text-align: center;">
                            <input type="number" min="1" value="${composant.quantite}" 
                                   onchange="modifierQuantiteComposant(${index}, parseInt(this.value))"
                                   style="width: 60px; text-align: center;">
                        </div>
                        <div style="text-align: right; font-weight: 500; color: var(--success-color);">
                            ${prixTotal.toFixed(2)} €
                        </div>
                        <div style="text-align: center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="supprimerComposant(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            
            // Insérer après l'en-tête
            const header = conteneur.querySelector('.composants-header');
            const items = conteneur.querySelectorAll('.composant-item');
            items.forEach(item => item.remove());
            
            header.insertAdjacentHTML('afterend', html);
            
            // Calculer le prix total
            const prixTotal = composants.reduce((total, composant) => {
                return total + (composant.prix * composant.quantite);
            }, 0);
            
            document.getElementById('prix-total-calcule').textContent = prixTotal.toFixed(2);
        }
        
        // Mettre à jour le champ caché
        document.getElementById('composants-data').value = JSON.stringify(composants);
    }

    function mettreAJourCompositionAutomatique() {
        if (!document.getElementById('composition_auto').checked) return;
        
        // Générer la désignation automatique
        const designations = composants.map(c => c.designation);
        const designationComplete = designations.join(' + ');
        document.getElementById('designation').value = designationComplete;
        
        // Calculer le prix total
        const prixTotal = composants.reduce((total, composant) => {
            return total + (composant.prix * composant.quantite);
        }, 0);
        
        document.getElementById('prixVente').value = prixTotal.toFixed(2);
        document.getElementById('prixAchat').value = (prixTotal * 0.7).toFixed(2);
    }

        // Masquer les résultats si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-article-container')) {
                masquerResultatsRecherche();
            }
        });

        // Initialiser l'affichage selon l'état actuel
        toggleSectionPrix();
    }); // Fermeture du DOMContentLoaded
</script>

<?php include 'footer_simple.php'; ?>