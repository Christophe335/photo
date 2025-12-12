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

<style>
    .product-info {
        background: white;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .info-section h4 {
        color: var(--primary-dark);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary-orange);
    }

    .info-item {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .prix-vente {
        color: var(--success-color);
        font-weight: bold;
        font-size: 18px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background: var(--success-color);
        color: white;
    }

    .badge-secondary {
        background: #6c757d;
        color: white;
    }

    .composition-section {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .composition-section h3 {
        color: var(--primary-dark);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-orange);
    }

    .composants-table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .composants-table .table-header {
        display: grid;
        grid-template-columns: 1fr 2fr 120px 80px 120px;
        gap: 15px;
        padding: 15px 20px;
        background: var(--primary-dark);
        color: white;
        font-weight: 500;
        font-size: 14px;
    }

    .composants-table .table-row {
        display: grid;
        grid-template-columns: 1fr 2fr 120px 80px 120px;
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        align-items: center;
    }

    .composants-table .table-row:last-child {
        border-bottom: none;
    }

    .composants-table .table-row:nth-child(even) {
        background-color: #f8f9fa;
    }

    .total-row {
        background: #e8f5e8 !important;
        font-weight: bold;
        border-top: 2px solid var(--success-color);
    }

    .composant-reference {
        font-weight: 500;
        color: var(--primary-dark);
    }

    .composant-prix, .composant-total {
        text-align: right;
        font-weight: 500;
    }

    .composant-quantite {
        text-align: center;
        font-weight: 500;
    }

    .alert {
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-warning {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }

    .alert-info {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .composants-table .table-header,
        .composants-table .table-row {
            grid-template-columns: 1fr;
            gap: 5px;
        }
        
        .composants-table .table-header div,
        .composants-table .table-row div {
            padding: 5px 0;
        }
    }
</style>

<?php include 'footer_simple.php'; ?>