<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

$client_id = $_GET['id'] ?? 0;

if (!$client_id) {
    header('Location: gestion-clients.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les infos du client
    $stmt = $db->prepare("SELECT prenom, nom, email FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Location: gestion-clients.php');
        exit;
    }
    
    // Récupérer les commandes avec détails complets
    $stmt = $db->prepare("
        SELECT c.*, 
               COUNT(ci.id) as nb_articles,
               SUM(ci.quantite) as total_articles,
               SUM(ci.quantite * ci.prix_unitaire) as total_ht,
               GROUP_CONCAT(
                   CONCAT(ci.quantite, 'x ', ci.designation, ' (', ci.prix_unitaire, '€)')
                   SEPARATOR '||'
               ) as produits_detail
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        WHERE c.client_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$client_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques des commandes
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as nb_commandes,
            COALESCE(SUM(total), 0) as total_ca,
            AVG(total) as panier_moyen,
            COUNT(CASE WHEN statut IN ('confirmee', 'en_preparation', 'en_cours', 'expediee') THEN 1 END) as en_cours,
            COUNT(CASE WHEN statut = 'livree' THEN 1 END) as livrees,
            COUNT(CASE WHEN statut = 'annulee' THEN 1 END) as annulees
        FROM commandes 
        WHERE client_id = ?
    ");
    $stmt->execute([$client_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur client commandes: " . $e->getMessage());
    header('Location: gestion-clients.php');
    exit;
}

include 'header.php';
?>

<div class="toolbar">
    <a href="gestion-clients.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
    <h2><i class="fas fa-shopping-cart"></i> Commandes de <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h2>
    <div style="flex: 1;"></div>
    <a href="client-details.php?id=<?php echo $client_id; ?>" class="btn btn-info">
        <i class="fas fa-user"></i> Voir le profil
    </a>
</div>

<!-- Statistiques des commandes -->
<div class="stats-grid-commandes">
    <div class="stat-card">
        <div class="stat-icon" style="background: #007bff;">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['nb_commandes']; ?></div>
            <div class="stat-label">Total Commandes</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #28a745;">
            <i class="fas fa-euro-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['total_ca'], 0, ',', ' '); ?>€</div>
            <div class="stat-label">Chiffre d'Affaires</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #ffc107;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['en_cours']; ?></div>
            <div class="stat-label">En Cours</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #17a2b8;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['livrees']; ?></div>
            <div class="stat-label">Livrées</div>
        </div>
    </div>
</div>

<!-- Liste des commandes -->
<div class="commandes-container">
    <?php if (empty($commandes)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart fa-3x"></i>
            <h3>Aucune commande</h3>
            <p>Ce client n'a encore passé aucune commande.</p>
        </div>
    <?php else: ?>
        <?php foreach ($commandes as $commande): ?>
            <div class="commande-card">
                <div class="commande-header">
                    <div class="commande-info">
                        <h3>Commande #<?php echo htmlspecialchars($commande['numero_commande']); ?></h3>
                        <div class="commande-meta">
                            <span class="date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?>
                            </span>
                            <span class="articles">
                                <i class="fas fa-box"></i>
                                <?php echo $commande['total_articles']; ?> article<?php echo $commande['total_articles'] > 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="commande-status-section">
                        <?php
                        $statuts = [
                            'en_attente' => ['En attente', 'badge-warning'],
                            'confirmee' => ['Confirmée', 'badge-info'],
                            'en_preparation' => ['En préparation', 'badge-primary'],
                            'en_cours' => ['En cours', 'badge-primary'],
                            'expediee' => ['Expédiée', 'badge-secondary'],
                            'livree' => ['Livrée', 'badge-success'],
                            'annulee' => ['Annulée', 'badge-danger']
                        ];
                        $statut_info = $statuts[$commande['statut']] ?? [$commande['statut'], 'badge-light'];
                        ?>
                        <div class="status-change">
                            <label>Statut:</label>
                            <select class="statut-select" data-commande-id="<?php echo $commande['id']; ?>">
                                <?php foreach ($statuts as $statut_key => $statut_data): ?>
                                    <option value="<?php echo $statut_key; ?>" 
                                            <?php echo $commande['statut'] === $statut_key ? 'selected' : ''; ?>>
                                        <?php echo $statut_data[0]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="commande-total">
                        <?php
                        // Calcul des frais de port selon la logique du panier
                        $totalHT = $commande['total_ht'] ? $commande['total_ht'] : 0;
                        $fraisPort = ($totalHT > 200) ? 0 : 13.95;
                        $fraisAffiches = $commande['frais_livraison'] !== null ? $commande['frais_livraison'] : $fraisPort;
                        ?>
                        <div class="total-breakdown">
                            <div class="total-ht">Sous-total HT: <?php echo number_format($totalHT, 2, ',', ' '); ?> €</div>
                            <div class="frais-port">
                                Frais de port: 
                                <?php 
                                if ($fraisAffiches > 0) {
                                    echo number_format($fraisAffiches, 2, ',', ' ') . ' €';
                                } else {
                                    echo '<span style="color: #28a745; font-weight: bold;">Gratuit</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="total-amount"><?php echo number_format($commande['total'], 2, ',', ' '); ?> € TTC</div>
                    </div>
                </div>
                
                <div class="commande-content">
                    <!-- Détail des produits -->
                    <div class="produits-section">
                        <h4><i class="fas fa-list"></i> Détail des produits</h4>
                        <div class="produits-list">
                            <?php 
                            if ($commande['produits_detail']) {
                                $produits = explode('||', $commande['produits_detail']);
                                foreach ($produits as $produit): 
                            ?>
                                <div class="produit-item">
                                    <?php echo htmlspecialchars($produit); ?>
                                </div>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="commande-actions">
                        <a href="../clients/confirmation-commande.php?numero=<?php echo urlencode($commande['numero_commande']); ?>" 
                           class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> Voir détail complet
                        </a>
                        
                        <?php if ($commande['statut'] === 'expediee' && $commande['numero_suivi']): ?>
                            <span class="suivi-info">
                                <i class="fas fa-truck"></i>
                                Suivi: <strong><?php echo htmlspecialchars($commande['numero_suivi']); ?></strong>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($commande['statut'] === 'livree'): ?>
                            <a href="generer-facture.php?commande=<?php echo $commande['id']; ?>" 
                               class="btn btn-success">
                                <i class="fas fa-file-invoice"></i> Générer facture
                            </a>
                        <?php endif; ?>                        
                        <!-- Bouton de suppression -->
                        <button class="btn btn-danger btn-sm" 
                                onclick="confirmerSuppressionCommande(<?php echo $commande['id']; ?>, '<?php echo htmlspecialchars($commande['numero_commande']); ?>')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.stats-grid-commandes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #333;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.commandes-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.commande-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    border-left: 4px solid #24256D;
}

.commande-header {
    background: #f8f9fa;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 20px;
    align-items: start;
}

.commande-info h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.commande-meta {
    display: flex;
    gap: 20px;
    color: #666;
    font-size: 0.9rem;
}

.commande-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-change label {
    display: block;
    font-weight: 600;
    color: #666;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.statut-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
    min-width: 140px;
    background: white;
}

.total-breakdown {
    font-size: 0.9rem;
    margin-bottom: 10px;
    color: #666;
}

.total-breakdown div {
    margin-bottom: 3px;
}

.total-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #28a745;
    text-align: right;
}

.commande-content {
    padding: 20px;
}

.produits-section h4 {
    margin: 0 0 15px 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.produits-list {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.produit-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    color: #555;
}

.produit-item:last-child {
    border-bottom: none;
}

.commande-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.suivi-info {
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9rem;
    color: #495057;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.empty-state i {
    color: #ddd;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .commande-header {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .total-amount {
        text-align: left;
    }
    
    .commande-actions {
        flex-direction: column;
        align-items: start;
    }
}

/* Bouton de suppression */
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
}

.btn-danger:active {
    transform: translateY(0);
}
</style>

<script>
// Gestion du changement de statut (même code que dans client-details.php)
document.querySelectorAll('.statut-select').forEach(select => {
    select.addEventListener('change', function() {
        const commandeId = this.dataset.commandeId;
        const nouveauStatut = this.value;
        
        if (confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
            fetch('update-statut-commande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    commande_id: commandeId,
                    statut: nouveauStatut
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour: ' + data.message);
                    this.value = this.dataset.oldValue;
                }
            })
            .catch(error => {
                alert('Erreur lors de la mise à jour');
                this.value = this.dataset.oldValue;
            });
        } else {
            this.value = this.dataset.oldValue;
        }
    });
    
    select.dataset.oldValue = select.value;
});

// Fonctions de suppression de commande
function confirmerSuppressionCommande(commandeId, numeroCommande) {
    if (confirm('Êtes-vous sûr de vouloir supprimer définitivement la commande #' + numeroCommande + ' ?\n\nCette action est irréversible et supprimera :\n- La commande et tous ses articles\n- L\'historique des modifications\n- Toutes les données associées')) {
        supprimerCommande(commandeId);
    }
}

function supprimerCommande(commandeId) {
    fetch('supprimer-commande.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            commande_id: commandeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Commande supprimée avec succès');
            location.reload();
        } else {
            alert('Erreur lors de la suppression: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression de la commande');
    });
}
</script>

<?php include 'footer_simple.php'; ?>