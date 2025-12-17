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

// Récupérer le produit
$produit = getProduit($id);
if (!$produit) {
    $_SESSION['message'] = 'Produit non trouvé';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Vérifier que c'est un article composé
if (!($produit['est_compose'] ?? false)) {
    $_SESSION['message'] = 'Ce produit n\'est pas un article composé';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Récupérer les composants
$composants = getComposantsProduit($id);

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-layer-group"></i> Composition de l'article : <?= htmlspecialchars($produit['designation']) ?></h2>
        <div>
            <a href="modifier.php?id=<?= $produit['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="product-info">
        <div class="info-grid">
            <div class="info-section">
                <h4>Informations générales</h4>
                <div class="info-item">
                    <strong>Référence :</strong> <?= htmlspecialchars($produit['reference']) ?>
                </div>
                <div class="info-item">
                    <strong>Famille :</strong> <?= htmlspecialchars($produit['famille']) ?>
                </div>
                <div class="info-item">
                    <strong>Format :</strong> <?= htmlspecialchars($produit['format'] ?? 'Non spécifié') ?>
                </div>
            </div>
            
            <div class="info-section">
                <h4>Prix</h4>
                <div class="info-item">
                    <strong>Prix d'achat :</strong> <?= number_format($produit['prixAchat'], 2, ',', ' ') ?> €
                </div>
                <div class="info-item">
                    <strong>Prix de vente :</strong> <span class="prix-vente"><?= number_format($produit['prixVente'], 2, ',', ' ') ?> €</span>
                </div>
                <div class="info-item">
                    <strong>Composition automatique :</strong> 
                    <?= ($produit['composition_auto'] ?? true) ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-secondary">Non</span>' ?>
                </div>
            </div>
        </div>
    </div>

    <div class="composition-section">
        <h3><i class="fas fa-list"></i> Articles composants</h3>
        
        <?php if (empty($composants)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Aucun article composant défini pour cet article composé.
            </div>
        <?php else: ?>
            <div class="composants-table">
                <div class="table-header">
                    <div>Référence</div>
                    <div>Désignation</div>
                    <div>Prix unitaire</div>
                    <div>Quantité</div>
                    <div>Prix total</div>
                </div>
                
                <?php 
                $prixTotalCalcule = 0;
                foreach ($composants as $composant): 
                    $prixTotalItem = $composant['prix'] * $composant['quantite'];
                    $prixTotalCalcule += $prixTotalItem;
                ?>
                    <div class="table-row">
                        <div class="composant-reference">
                            <?= htmlspecialchars($composant['reference']) ?>
                        </div>
                        <div class="composant-designation">
                            <?= htmlspecialchars($composant['designation']) ?>
                        </div>
                        <div class="composant-prix">
                            <?= number_format($composant['prix'], 2, ',', ' ') ?> €
                        </div>
                        <div class="composant-quantite">
                            <?= $composant['quantite'] ?>
                        </div>
                        <div class="composant-total">
                            <?= number_format($prixTotalItem, 2, ',', ' ') ?> €
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="table-row total-row">
                    <div style="grid-column: 1 / -2; text-align: right; font-weight: bold;">
                        Total calculé :
                    </div>
                    <div style="font-weight: bold; font-size: 16px; color: var(--success-color);">
                        <?= number_format($prixTotalCalcule, 2, ',', ' ') ?> €
                    </div>
                </div>
                
                <?php if (abs($prixTotalCalcule - $produit['prixVente']) > 0.01): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Différence détectée :</strong> Le prix de vente du produit (<?= number_format($produit['prixVente'], 2, ',', ' ') ?> €) 
                        diffère du total calculé (<?= number_format($prixTotalCalcule, 2, ',', ' ') ?> €).
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>



<?php include 'footer_simple.php'; ?>