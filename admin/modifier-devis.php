<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

$devis = null;
$devis_items = [];
$error = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $devis_id = $_GET['id'];
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupérer le devis avec les informations client
        $stmt = $db->prepare("
            SELECT d.*, c.nom as client_nom, c.prenom as client_prenom, 
                   c.email as client_email, c.societe as client_societe, 
                   c.adresse as client_adresse, c.code_postal as client_code_postal,
                   c.ville as client_ville, c.telephone as client_telephone
            FROM devis d
            LEFT JOIN clients c ON d.client_id = c.id
            WHERE d.id = ?
        ");
        $stmt->execute([$devis_id]);
        $devis = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($devis) {
            // Récupérer les articles du devis
            $stmt = $db->prepare("
                SELECT * FROM devis_items 
                WHERE devis_id = ? 
                ORDER BY id
            ");
            $stmt->execute([$devis_id]);
            $devis_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Devis non trouvé.";
        }
    } catch (Exception $e) {
        error_log("Erreur modification devis: " . $e->getMessage());
        $error = "Erreur lors du chargement du devis.";
    }
} else {
    $error = "ID de devis invalide.";
}

include 'header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-edit"></i> Modifier le Devis #<?php echo $devis ? htmlspecialchars($devis['numero']) : 'Inconnu'; ?></h2>
    <div>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php else: ?>

<form id="form-devis" method="POST" action="process-modifier-devis.php" class="devis-form">
    <input type="hidden" name="devis_id" value="<?php echo $devis['id']; ?>">
    <input type="hidden" name="action" value="update">
    
    <!-- Informations client -->
    <div class="form-section">
        <h3><i class="fas fa-user"></i> Informations client</h3>
        
        <!-- Client existant -->
        <div id="client-nouveau">
            <div class="form-group">
                <label for="nouveau_societe">Société</label>
                <input type="text" id="nouveau_societe" name="nouveau_societe" 
                       value="<?php echo htmlspecialchars($devis['client_societe'] ?? ''); ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_prenom">Prénom *</label>
                    <input type="text" id="nouveau_prenom" name="nouveau_prenom" 
                           value="<?php echo htmlspecialchars($devis['client_prenom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="client_nom">Nom *</label>
                    <input type="text" id="nouveau_nom" name="nouveau_nom" 
                           value="<?php echo htmlspecialchars($devis['client_nom'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_email">Email *</label>
                    <input type="email" id="nouveau_email" name="nouveau_email" 
                           value="<?php echo htmlspecialchars($devis['client_email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="client_telephone">Téléphone</label>
                    <input type="tel" id="nouveau_telephone" name="nouveau_telephone" 
                           value="<?php echo htmlspecialchars($devis['client_telephone'] ?? ''); ?>">
                </div>
            </div>
            
            <h4>Adresse de facturation</h4>
            <div class="form-group">
                <label for="client_adresse">Adresse *</label>
                <textarea id="nouveau_adresse" name="nouveau_adresse" rows="3" required><?php echo htmlspecialchars($devis['client_adresse'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_code_postal">Code postal *</label>
                    <input type="text" id="nouveau_code_postal" name="nouveau_code_postal" 
                           value="<?php echo htmlspecialchars($devis['client_code_postal'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="client_ville">Ville *</label>
                    <input type="text" id="nouveau_ville" name="nouveau_ville" 
                           value="<?php echo htmlspecialchars($devis['client_ville'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="client_pays">Pays</label>
                <select id="client_pays" name="nouveau_pays">
                    <option value="France" <?php echo ($devis['client_pays'] ?? 'France') === 'France' ? 'selected' : ''; ?>>France</option>
                    <option value="Belgique" <?php echo ($devis['client_pays'] ?? '') === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                    <option value="Suisse" <?php echo ($devis['client_pays'] ?? '') === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                    <option value="Luxembourg" <?php echo ($devis['client_pays'] ?? '') === 'Luxembourg' ? 'selected' : ''; ?>>Luxembourg</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Articles -->
    <div class="form-section">
        <h3><i class="fas fa-shopping-cart"></i> Articles</h3>
        
        <div class="articles-toolbar">
            <button type="button" id="btn-ajouter-produit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un produit
            </button>
            <button type="button" id="btn-ajouter-libre" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Ligne libre
            </button>
        </div>
        
        <div class="articles-container">
            <div class="articles-header">
                <div>Désignation</div>
                <div>Quantité</div>
                <div>Prix unitaire</div>
                <div>Remise</div>
                <div>Total ligne</div>
                <div></div>
            </div>
            <!-- Articles existants du devis -->
            <?php if (!empty($devis_items)): ?>
                <?php foreach ($devis_items as $index => $item): ?>
                    <div class="article-item" data-index="<?php echo $index; ?>">
                        <div class="designation-col">
                            <input type="text" name="articles[<?php echo $index; ?>][designation]" 
                                   value="<?php echo htmlspecialchars($item['nom'] ?? $item['designation'] ?? ''); ?>" 
                                   placeholder="Nom de l'article" required>
                            <textarea name="articles[<?php echo $index; ?>][description]" 
                                      placeholder="Description détaillée"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <input type="number" name="articles[<?php echo $index; ?>][quantite]" 
                                   value="<?php echo $item['quantite'] ?? 1; ?>" min="1" step="1" required class="input-quantite">
                        </div>
                        <div>
                            <input type="number" name="articles[<?php echo $index; ?>][prix_unitaire]" 
                                   value="<?php echo $item['prix_unitaire'] ?? 0; ?>" min="0" step="0.01" required class="input-prix">
                        </div>
                        <div class="remise-group">
                            <input type="number" name="articles[<?php echo $index; ?>][remise_valeur]" 
                                   value="<?php echo $item['remise_valeur'] ?? 0; ?>" min="0" step="0.01" placeholder="0" class="input-remise">
                            <select name="articles[<?php echo $index; ?>][remise_type]" class="select-remise-type">
                                <option value="percent" <?php echo ($item['remise_type'] ?? 'percent') === 'percent' ? 'selected' : ''; ?>>%</option>
                                <option value="fixed" <?php echo ($item['remise_type'] ?? '') === 'fixed' ? 'selected' : ''; ?>>€</option>
                            </select>
                        </div>
                        <div>
                            <span class="total-ligne">
                                <?php 
                                $quantite = $item['quantite'] ?? 1;
                                $prix = $item['prix_unitaire'] ?? 0;
                                $remise = $item['remise_valeur'] ?? 0;
                                $type_remise = $item['remise_type'] ?? 'percent';
                                
                                $total_ligne = $quantite * $prix;
                                if ($remise > 0) {
                                    if ($type_remise === 'percent') {
                                        $total_ligne = $total_ligne * (1 - $remise / 100);
                                    } else {
                                        $total_ligne = $total_ligne - $remise;
                                    }
                                }
                                echo number_format(max(0, $total_ligne), 2, ',', ' ') . ' €';
                                ?>
                            </span>
                        </div>
                        <div>
                            <button type="button" class="btn-remove-article" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div id="articles-container">
                <!-- Les nouveaux articles seront ajoutés dynamiquement ici -->
            </div>
            
            <div class="totaux">
                <div class="totaux-row">
                    <span>Total HT :</span>
                    <span id="total-ht">0,00 €</span>
                </div>
                <div class="totaux-row">
                    <span>Frais de port :</span>
                    <span id="frais-port-display">8,50 €</span>
                    <input type="hidden" id="frais_port" name="frais_port" value="8.50">
                </div>
                <div class="totaux-row">
                    <span>Sous-total :</span>
                    <span id="sous-total">0,00 €</span>
                </div>
                <div class="totaux-row">
                    <span>TVA (20%) :</span>
                    <span id="tva-montant">0,00 €</span>
                </div>
                <div class="totaux-row total-final">
                    <span>Total TTC :</span>
                    <span id="total-ttc">0,00 €</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-section">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Sauvegarder les modifications
        </button>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>

<?php endif; ?>

<!-- Modal pour sélectionner un produit -->
<div id="modal-produits" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sélectionner un produit</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <input type="text" id="recherche-produit" placeholder="Rechercher un produit...">
            <div id="produits-liste">
                <!-- Les produits seront chargés dynamiquement via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script src="../js/devis.js"></script>

<?php include 'footer_simple.php'; ?>