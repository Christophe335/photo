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
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Location: gestion-clients.php');
        exit;
    }
    
    // Récupérer les commandes du client
    $stmt = $db->prepare("
        SELECT c.*, 
               COUNT(ci.id) as nb_articles,
               GROUP_CONCAT(CONCAT(ci.quantite, 'x ', ci.nom_produit) SEPARATOR '<br>') as produits
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        WHERE c.client_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$client_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques du client
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as nb_commandes,
            COALESCE(SUM(total), 0) as total_depense,
            AVG(total) as panier_moyen,
            COUNT(CASE WHEN statut IN ('confirmee', 'en_preparation', 'en_cours', 'expediee') THEN 1 END) as commandes_en_cours
        FROM commandes 
        WHERE client_id = ?
    ");
    $stmt->execute([$client_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur client details: " . $e->getMessage());
    header('Location: gestion-clients.php');
    exit;
}

include 'header.php';
?>

<div class="toolbar">
    <a href="gestion-clients.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
    <h2><i class="fas fa-user"></i> <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h2>
    <div style="flex: 1;"></div>
    <a href="client-edit.php?id=<?php echo $client['id']; ?>" class="btn btn-warning">
        <i class="fas fa-edit"></i> Modifier
    </a>
</div>

<div class="client-layout">
    <!-- Informations client -->
    <div class="client-info-card">
        <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Nom complet</label>
                <span><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></span>
            </div>
            
            <div class="info-item">
                <label>Email</label>
                <span><a href="mailto:<?php echo htmlspecialchars($client['email']); ?>"><?php echo htmlspecialchars($client['email']); ?></a></span>
            </div>
            
            <div class="info-item">
                <label>Téléphone</label>
                <span>
                    <?php if ($client['telephone']): ?>
                        <a href="tel:<?php echo htmlspecialchars($client['telephone']); ?>"><?php echo htmlspecialchars($client['telephone']); ?></a>
                    <?php else: ?>
                        <span class="text-muted">Non renseigné</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>Statut</label>
                <span>
                    <span class="badge <?php echo $client['actif'] ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $client['actif'] ? 'Actif' : 'Inactif'; ?>
                    </span>
                </span>
            </div>
            
            <div class="info-item">
                <label>Inscription</label>
                <span><?php echo date('d/m/Y à H:i', strtotime($client['date_creation'])); ?></span>
            </div>
            
            <div class="info-item">
                <label>Dernière connexion</label>
                <span>
                    <?php if ($client['derniere_connexion']): ?>
                        <?php echo date('d/m/Y à H:i', strtotime($client['derniere_connexion'])); ?>
                    <?php else: ?>
                        <span class="text-muted">Jamais connecté</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        
        <?php if ($client['adresse']): ?>
        <h4><i class="fas fa-map-marker-alt"></i> Adresse de facturation</h4>
        <div class="address-info">
            <?php echo nl2br(htmlspecialchars($client['adresse'])); ?><br>
            <?php echo htmlspecialchars($client['code_postal'] . ' ' . $client['ville']); ?><br>
            <?php echo htmlspecialchars($client['pays'] ?: 'France'); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-card">
        <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['nb_commandes']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo number_format($stats['total_depense'], 0, ',', ' '); ?> €</div>
                <div class="stat-label">Total dépensé</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo number_format($stats['panier_moyen'], 0, ',', ' '); ?> €</div>
                <div class="stat-label">Panier moyen</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['commandes_en_cours']; ?></div>
                <div class="stat-label">En cours</div>
            </div>
        </div>
    </div>
</div>

<!-- Commandes du client -->
<div class="commandes-card">
    <h3><i class="fas fa-shopping-cart"></i> Commandes (<?php echo count($commandes); ?>)</h3>
    
    <?php if (empty($commandes)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart fa-2x"></i>
            <p>Aucune commande pour ce client</p>
        </div>
    <?php else: ?>
        <div class="commandes-list">
            <?php foreach ($commandes as $commande): ?>
                <div class="commande-item">
                    <div class="commande-header">
                        <div class="commande-info">
                            <strong>Commande #<?php echo htmlspecialchars($commande['numero_commande']); ?></strong>
                            <span class="commande-date"><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></span>
                        </div>
                        
                        <div class="commande-status">
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
                            <span class="badge <?php echo $statut_info[1]; ?>"><?php echo $statut_info[0]; ?></span>
                        </div>
                        
                        <div class="commande-total">
                            <strong><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</strong>
                        </div>
                    </div>
                    
                    <div class="commande-details">
                        <div class="commande-produits">
                            <strong><?php echo $commande['nb_articles']; ?> article<?php echo $commande['nb_articles'] > 1 ? 's' : ''; ?></strong>
                            <div class="produits-list"><?php echo $commande['produits']; ?></div>
                        </div>
                        
                        <div class="commande-actions">
                            <!-- Changement de statut -->
                            <select class="statut-select" data-commande-id="<?php echo $commande['id']; ?>">
                                <?php foreach ($statuts as $statut_key => $statut_data): ?>
                                    <option value="<?php echo $statut_key; ?>" 
                                            <?php echo $commande['statut'] === $statut_key ? 'selected' : ''; ?>>
                                        <?php echo $statut_data[0]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <a href="../clients/confirmation-commande.php?numero=<?php echo urlencode($commande['numero_commande']); ?>" 
                               class="btn btn-sm btn-info" target="_blank" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($commande['statut'] === 'livree'): ?>
                                <a href="generer-facture.php?commande=<?php echo $commande['id']; ?>" 
                                   class="btn btn-sm btn-success" title="Générer facture">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.client-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.client-info-card,
.stats-card,
.commandes-card {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.client-info-card h3,
.stats-card h3,
.commandes-card h3 {
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-weight: 600;
    color: #666;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.info-item span {
    color: #333;
}

.address-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.commandes-list {
    space-y: 15px;
}

.commande-item {
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 15px;
    overflow: hidden;
}

.commande-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    gap: 20px;
}

.commande-info strong {
    display: block;
    color: #333;
}

.commande-date {
    color: #666;
    font-size: 0.9rem;
}

.commande-total {
    font-size: 1.1rem;
    color: #333;
}

.commande-details {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 20px;
}

.produits-list {
    margin-top: 5px;
    color: #666;
    font-size: 0.9rem;
}

.commande-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.statut-select {
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
    min-width: 130px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state i {
    color: #ddd;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .client-layout {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .commande-header {
        flex-direction: column;
        align-items: start;
        gap: 10px;
    }
    
    .commande-details {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<script>
// Gestion du changement de statut
document.querySelectorAll('.statut-select').forEach(select => {
    select.addEventListener('change', function() {
        const commandeId = this.dataset.commandeId;
        const nouveauStatut = this.value;
        
        if (confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
            // Envoyer la requête AJAX
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
                    // Recharger la page pour voir les changements
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour: ' + data.message);
                    // Remettre l'ancienne valeur
                    this.value = this.dataset.oldValue;
                }
            })
            .catch(error => {
                alert('Erreur lors de la mise à jour');
                this.value = this.dataset.oldValue;
            });
        } else {
            // Annuler le changement
            this.value = this.dataset.oldValue;
        }
    });
    
    // Stocker la valeur initiale
    select.dataset.oldValue = select.value;
});
</script>

<?php include 'footer.php'; ?>