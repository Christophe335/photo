<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les devis avec informations client
    $stmt = $db->prepare("
        SELECT d.*, c.nom as client_nom, c.prenom as client_prenom, c.email as client_email,
               COUNT(di.id) as nb_items
        FROM devis d
        LEFT JOIN clients c ON d.client_id = c.id
        LEFT JOIN devis_items di ON d.id = di.devis_id
        GROUP BY d.id
        ORDER BY d.date_creation DESC
    ");
    $stmt->execute();
    $devis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques
    $stats = $db->query("
        SELECT 
            COUNT(*) as total_devis,
            COUNT(CASE WHEN statut = 'brouillon' THEN 1 END) as brouillons,
            COUNT(CASE WHEN statut = 'envoye' THEN 1 END) as envoyes,
            COUNT(CASE WHEN statut = 'accepte' THEN 1 END) as acceptes,
            COALESCE(SUM(CASE WHEN statut = 'accepte' THEN total_ttc ELSE 0 END), 0) as ca_accepte
        FROM devis
    ")->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur gestion devis: " . $e->getMessage());
    $error = "Erreur lors du chargement des données.";
}

include 'header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-file-invoice"></i> Gestion des Devis</h2>
    <div>
        <a href="creer-devis.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nouveau Devis
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #007bff;">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['total_devis']; ?></div>
            <div class="stat-label">Total Devis</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #ffc107;">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['brouillons']; ?></div>
            <div class="stat-label">Brouillons</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #17a2b8;">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['envoyes']; ?></div>
            <div class="stat-label">Envoyés</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #28a745;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $stats['acceptes']; ?></div>
            <div class="stat-label">Acceptés</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #28a745;">
            <i class="fas fa-euro-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['ca_accepte'], 0, ',', ' '); ?>€</div>
            <div class="stat-label">CA Accepté</div>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Liste des devis -->
<div class="content-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>N° Devis</th>
                    <th>Client</th>
                    <th>Date création</th>
                    <th>Statut</th>
                    <th>Articles</th>
                    <th>Total TTC</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devis)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice fa-3x text-muted"></i>
                                <p>Aucun devis trouvé</p>
                                <a href="creer-devis.php" class="btn btn-primary">Créer le premier devis</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($devis as $d): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($d['numero']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($d['client_prenom'] . ' ' . $d['client_nom']); ?>
                                <?php if ($d['client_id']): ?>
                                    <br><small class="text-muted">
                                        <a href="client-details.php?id=<?php echo $d['client_id']; ?>">
                                            Voir profil client
                                        </a>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($d['date_creation'])); ?></td>
                            <td>
                                <?php 
                                $badges = [
                                    'brouillon' => 'secondary',
                                    'envoye' => 'info', 
                                    'accepte' => 'success',
                                    'refuse' => 'danger',
                                    'expire' => 'warning'
                                ];
                                $labels = [
                                    'brouillon' => 'Brouillon',
                                    'envoye' => 'Envoyé',
                                    'accepte' => 'Accepté', 
                                    'refuse' => 'Refusé',
                                    'expire' => 'Expiré'
                                ];
                                ?>
                                <span class="badge badge-<?php echo $badges[$d['statut']]; ?>">
                                    <?php echo $labels[$d['statut']]; ?>
                                </span>
                            </td>
                            <td><?php echo $d['nb_items']; ?> article(s)</td>
                            <td class="text-right">
                                <strong><?php echo number_format($d['total_ttc'], 2, ',', ' '); ?>€</strong>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="voir-devis.php?id=<?php echo $d['id']; ?>" 
                                       class="btn btn-sm btn-info" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="modifier-devis.php?id=<?php echo $d['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="imprimer-devis.php?id=<?php echo $d['id']; ?>" 
                                       class="btn btn-sm btn-secondary" title="Imprimer" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php if ($d['statut'] === 'brouillon'): ?>
                                        <a href="javascript:void(0)" 
                                           onclick="supprimerDevis(<?php echo $d['id']; ?>)"
                                           class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function supprimerDevis(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')) {
        window.location.href = 'supprimer-devis.php?id=' + id;
    }
}
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5em;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.8em;
    font-weight: bold;
    color: #333;
    line-height: 1;
}

.stat-label {
    font-size: 0.9em;
    color: #666;
    margin-top: 5px;
}

.empty-state {
    padding: 40px;
    text-align: center;
}

.empty-state i {
    margin-bottom: 20px;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 500;
}

.badge-secondary { background: #6c757d; color: white; }
.badge-info { background: #17a2b8; color: white; }
.badge-success { background: #28a745; color: white; }
.badge-danger { background: #dc3545; color: white; }
.badge-warning { background: #ffc107; color: #212529; }

.btn-group {
    display: flex;
    gap: 5px;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #6c757d;
}
</style>

<?php include 'footer_simple.php'; ?>