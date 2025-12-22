<?php
require_once '../includes/database.php';
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

try {
    $db = Database::getInstance()->getConnection();
    
    // Action : corriger les frais de port
    if (isset($_POST['corriger_frais'])) {
        $stmt = $db->prepare("
            SELECT c.id, c.sous_total, c.frais_livraison, c.total
            FROM commandes c
            WHERE c.frais_livraison IS NULL OR c.frais_livraison = 0
        ");
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $corrections = 0;
        foreach ($commandes as $commande) {
            $frais_corrects = ($commande['sous_total'] > 200) ? 0 : 13.95;
            if ($commande['frais_livraison'] != $frais_corrects) {
                $nouveau_total = $commande['sous_total'] + ($commande['sous_total'] * 0.20) + $frais_corrects;
                
                $update = $db->prepare("
                    UPDATE commandes 
                    SET frais_livraison = ?, total = ? 
                    WHERE id = ?
                ");
                $update->execute([$frais_corrects, $nouveau_total, $commande['id']]);
                $corrections++;
            }
        }
        
        $message = "Correction effectuée sur $corrections commandes.";
    }
    
    // Analyse des frais de port actuels
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_commandes,
            COUNT(CASE WHEN frais_livraison = 0 THEN 1 END) as frais_gratuits,
            COUNT(CASE WHEN frais_livraison = 13.95 THEN 1 END) as frais_normaux,
            COUNT(CASE WHEN frais_livraison IS NULL THEN 1 END) as frais_null,
            COUNT(CASE WHEN frais_livraison NOT IN (0, 13.95) AND frais_livraison IS NOT NULL THEN 1 END) as frais_autres
        FROM commandes
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Commandes avec frais incorrects
    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.numero_commande,
            c.sous_total,
            c.frais_livraison,
            c.total,
            CASE 
                WHEN c.sous_total > 200 THEN 0 
                ELSE 13.95 
            END as frais_corrects,
            cl.prenom,
            cl.nom
        FROM commandes c
        LEFT JOIN clients cl ON c.client_id = cl.id
        WHERE c.frais_livraison != (CASE WHEN c.sous_total > 200 THEN 0 ELSE 13.95 END)
           OR c.frais_livraison IS NULL
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute();
    $commandes_incorrectes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur vérification frais port: " . $e->getMessage());
    $error = "Erreur lors de la vérification.";
}

include 'header.php';
?>

<div class="toolbar">
    <h2><i class="fas fa-shipping-fast"></i> Vérification des frais de port</h2>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<!-- Statistiques des frais de port -->
<div class="stats-card">
    <h3><i class="fas fa-chart-bar"></i> Analyse des frais de port</h3>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['total_commandes']; ?></div>
            <div class="stat-label">Total commandes</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['frais_gratuits']; ?></div>
            <div class="stat-label">Port gratuit</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['frais_normaux']; ?></div>
            <div class="stat-label">Port 13.95€</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['frais_null']; ?></div>
            <div class="stat-label">Port non défini</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['frais_autres']; ?></div>
            <div class="stat-label">Autres frais</div>
        </div>
    </div>
</div>

<!-- Commandes avec frais incorrects -->
<?php if (!empty($commandes_incorrectes)): ?>
<div class="table-card">
    <div class="card-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Commandes avec frais incorrects</h3>
        <form method="post" style="display: inline;">
            <button type="submit" name="corriger_frais" class="btn btn-warning" 
                    onclick="return confirm('Corriger automatiquement les frais de port pour toutes ces commandes ?')">
                <i class="fas fa-wrench"></i> Corriger automatiquement
            </button>
        </form>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Sous-total</th>
                    <th>Frais actuels</th>
                    <th>Frais corrects</th>
                    <th>Total actuel</th>
                    <th>Total correct</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes_incorrectes as $commande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($commande['numero_commande']); ?></td>
                        <td><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                        <td><?php echo number_format($commande['sous_total'], 2, ',', ' '); ?> €</td>
                        <td>
                            <?php 
                            if ($commande['frais_livraison'] === null) {
                                echo '<span class="badge badge-danger">Non défini</span>';
                            } else {
                                echo number_format($commande['frais_livraison'], 2, ',', ' ') . ' €';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($commande['frais_corrects'] > 0) {
                                echo number_format($commande['frais_corrects'], 2, ',', ' ') . ' €';
                            } else {
                                echo '<span class="badge badge-success">Gratuit</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</td>
                        <td>
                            <?php 
                            $total_correct = $commande['sous_total'] + ($commande['sous_total'] * 0.20) + $commande['frais_corrects'];
                            echo number_format($total_correct, 2, ',', ' '); ?> €
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Tous les frais de port sont corrects !
</div>
<?php endif; ?>

<style>
.stats-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    margin-top: 5px;
}

.table-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: #333;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-danger {
    background: #dc3545;
    color: white;
}

.badge-success {
    background: #28a745;
    color: white;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}
</style>

<?php include 'footer_simple.php'; ?>