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
    $donnees = [
        'famille' => $_POST['famille'] ?? '',
        'nomDeLaFamille' => $_POST['nomDeLaFamille'] ?? '',
        'reference' => $_POST['reference'] ?? '',
        'designation' => $_POST['designation'] ?? '',
        'format' => $_POST['format'] ?? '',
            'ordre' => isset($_POST['ordre']) ? intval($_POST['ordre']) : 0,
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
</script>

<?php include 'footer_simple.php'; ?>