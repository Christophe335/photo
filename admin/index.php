<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

// Traitement des actions
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $result = supprimerProduit($_GET['id']);
    
    if ($result) {
        $_SESSION['message'] = 'Produit supprimé avec succès';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Erreur lors de la suppression du produit';
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: index.php');
    exit;
}

// Paramètres de recherche et pagination
$recherche = $_GET['recherche'] ?? '';
$familleSelectionnee = $_GET['famille'] ?? '';
$pageActuelle = max(1, intval($_GET['page'] ?? 1));
$produitsParPage = 20;

// Récupérer les familles pour le filtre
$familles = getFamilles();

// Rechercher les produits pour la pagination
$totalProduitsRecherche = compterProduits($recherche, $familleSelectionnee);
$totalPages = ceil($totalProduitsRecherche / $produitsParPage);
$offset = ($pageActuelle - 1) * $produitsParPage;

$produits = rechercherProduits($recherche, $familleSelectionnee, $produitsParPage, $offset);

// Calculer les statistiques globales
$db = Database::getInstance()->getConnection();
$stats = $db->query("
    SELECT 
        COUNT(*) as total_produits,
        SUM(CASE WHEN prixAchat IS NOT NULL AND prixAchat > 0 THEN prixAchat ELSE 0 END) as valeur_stock,
        SUM(CASE WHEN prixVente IS NOT NULL AND prixVente > 0 THEN prixVente ELSE 0 END) as valeur_vente
    FROM produits
")->fetch(PDO::FETCH_ASSOC);

$totalProduits = $stats['total_produits'] ?? 0;
$valeurStock = $stats['valeur_stock'] ?? 0;
$valeurVente = $stats['valeur_vente'] ?? 0;

// Inclure le header
include 'header.php';
?>

    <div class="admin-content">
        <h1><i class="fas fa-boxes"></i> Gestion des Produits</h1>
        
        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-box">
                <h3><?= $totalProduits ?></h3>
                <p>Produits Total</p>
            </div>
            <div class="stat-box">
                <h3><?= count($familles) ?></h3>
                <p>Familles</p>
            </div>
            <div class="stat-box">
                <h3><?= number_format($valeurStock, 2, ',', ' ') ?> €</h3>
                <p>Valeur Stock (Prix d'achat)</p>
            </div>
            <div class="stat-box">
                <h3><?= number_format($valeurVente, 2, ',', ' ') ?> €</h3>
                <p>Valeur Potentielle (Prix de vente)</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-form">
                <div class="form-group">
                    <label for="searchInput">Recherche</label>
                    <input type="text" 
                           class="form-control" 
                           id="searchInput"
                           placeholder="Rechercher par nom, référence, famille..." 
                           value="<?= htmlspecialchars($recherche) ?>">
                </div>
                
                <div class="form-group">
                    <label for="familleFilter">Famille</label>
                    <select class="form-control" id="familleFilter">
                        <option value="">Toutes les familles</option>
                        <?php foreach ($familles as $famille): ?>
                            <option value="<?= htmlspecialchars($famille) ?>" 
                                    <?= $familleSelectionnee == $famille ? 'selected' : '' ?>>
                                <?= htmlspecialchars($famille) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="rechercherProduits()">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            
            <div style="margin-left: auto;">
                <a href="ajouter.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nouveau Produit
                </a>
            </div>
        </div>

        <!-- Tableau des produits -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-ordre">Ordre</th>
                        <th class="col-reference">Référence</th>
                        <th class="col-famille">Famille</th>
                        <th class="col-designation">Désignation</th>
                        <th class="col-prix">Prix d'achat</th>
                        <th class="col-prix">Prix de vente</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produits)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); font-style: italic;">
                                Aucun produit trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td class="col-id"><?= htmlspecialchars($produit['id']) ?></td>
                                <td class="col-ordre"><?= htmlspecialchars($produit['ordre'] ?? '') ?></td>
                                <td class="col-reference">
                                    <a href="modifier.php?id=<?= $produit['id'] ?>">
                                        <?= htmlspecialchars($produit['reference']) ?>
                                    </a>
                                </td>
                                <td class="col-famille"><?= htmlspecialchars($produit['famille']) ?></td>
                                <td class="col-designation">
                                    <div>
                                        <?php if ($produit['est_compose'] ?? false): ?>
                                            <i class="fas fa-layer-group" title="Article composé" style="color: #007bff; margin-right: 5px;"></i>
                                        <?php endif; ?>
                                        <strong><?= htmlspecialchars($produit['designation']) ?></strong>
                                        <?php if ($produit['est_compose'] ?? false): ?>
                                            <small style="color: #007bff; font-style: italic; margin-left: 5px;">(Composé)</small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($produit['format'])): ?>
                                        <small class="text-muted">Format: <?= htmlspecialchars($produit['format']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="col-prix">
                                    <?= number_format($produit['prixAchat'], 2, ',', ' ') ?> €
                                </td>
                                <td class="col-prix">
                                    <?= number_format($produit['prixVente'], 2, ',', ' ') ?> €
                                </td>
                                <td class="col-actions">
                                    <?php if ($produit['est_compose'] ?? false): ?>
                                        <a href="voir_composition.php?id=<?= $produit['id'] ?>" 
                                           class="btn btn-info btn-sm" 
                                           title="Voir la composition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="modifier.php?id=<?= $produit['id'] ?>" 
                                       class="btn btn-warning btn-sm"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?action=supprimer&id=<?= $produit['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($pageActuelle > 1): ?>
                    <a href="?page=<?= $pageActuelle - 1 ?>&recherche=<?= urlencode($recherche) ?>&famille=<?= urlencode($familleSelectionnee) ?>">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $pageActuelle - 2); $i <= min($totalPages, $pageActuelle + 2); $i++): ?>
                    <?php if ($i == $pageActuelle): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&recherche=<?= urlencode($recherche) ?>&famille=<?= urlencode($familleSelectionnee) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pageActuelle < $totalPages): ?>
                    <a href="?page=<?= $pageActuelle + 1 ?>&recherche=<?= urlencode($recherche) ?>&famille=<?= urlencode($familleSelectionnee) ?>">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 15px; color: var(--text-muted); font-size: 14px;">
                Page <?= $pageActuelle ?> sur <?= $totalPages ?> - 
                Total: <?= $totalProduitsRecherche ?> produits
                <?php if (!empty($recherche) || !empty($familleSelectionnee)): ?>
                    (filtré)
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<script>
function rechercherProduits() {
    const recherche = document.getElementById('searchInput').value;
    const famille = document.getElementById('familleFilter').value;
    
    const params = new URLSearchParams();
    if (recherche.trim()) params.append('recherche', recherche.trim());
    if (famille) params.append('famille', famille);
    params.append('page', '1');
    
    window.location.href = '?' + params.toString();
}

function resetFilters() {
    window.location.href = 'index.php';
}

// Recherche au clavier (Enter)
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        rechercherProduits();
    }
});

// Auto-recherche sur changement de famille
document.getElementById('familleFilter').addEventListener('change', function() {
    rechercherProduits();
});
</script>

<?php include 'footer_simple.php'; ?>