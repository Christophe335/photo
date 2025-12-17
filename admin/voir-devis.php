<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

include 'header.php';

// Récupérer un devis spécifique pour affichage
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
        error_log("Erreur voir devis: " . $e->getMessage());
        $error = "Erreur lors du chargement du devis.";
    }
} else {
    $error = "ID de devis invalide.";
}
?>

<div class="page-header">
    <h2><i class="fas fa-eye"></i> Aperçu du Devis #<?php echo $devis ? htmlspecialchars($devis['numero']) : 'Inconnu'; ?></h2>
    <div>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <?php if ($devis): ?>
            <a href="modifier-devis.php?id=<?php echo $devis['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="imprimer-devis.php?id=<?php echo $devis['id']; ?>" class="btn btn-info" target="_blank">
                <i class="fas fa-print"></i> Imprimer
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php else: ?>
    <div class="content-card">
        <div class="devis-view">
            <!-- En-tête du devis -->
            <div class="form-section">
                <div class="devis-header-info">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Numéro de devis :</label>
                            <strong><?php echo htmlspecialchars($devis['numero']); ?></strong>
                        </div>
                        <div class="form-group">
                            <label>Date de création :</label>
                            <strong><?php echo date('d/m/Y', strtotime($devis['date_creation'])); ?></strong>
                        </div>
                        <div class="form-group">
                            <label>Statut :</label>
                            <span class="badge badge-<?php echo $devis['statut']; ?>">
                                <?php 
                                $statuts = [
                                    'brouillon' => 'Brouillon',
                                    'envoye' => 'Envoyé', 
                                    'accepte' => 'Accepté',
                                    'refuse' => 'Refusé',
                                    'expire' => 'Expiré'
                                ];
                                echo $statuts[$devis['statut']] ?? 'Inconnu'; 
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Informations Client</h3>
                <div class="client-info-grid">
                    <?php if (!empty($devis['client_societe'])): ?>
                        <div class="info-item">
                            <label>Société :</label>
                            <strong><?php echo htmlspecialchars($devis['client_societe']); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <label>Client :</label>
                        <strong><?php echo htmlspecialchars($devis['client_prenom'] . ' ' . $devis['client_nom']); ?></strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Email :</label>
                        <strong><?php echo htmlspecialchars($devis['client_email']); ?></strong>
                    </div>
                    
                    <?php if (!empty($devis['client_telephone'])): ?>
                        <div class="info-item">
                            <label>Téléphone :</label>
                            <strong><?php echo htmlspecialchars($devis['client_telephone']); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($devis['client_adresse'])): ?>
                        <div class="info-item full-width">
                            <label>Adresse :</label>
                            <div class="adresse-display">
                                <?php echo nl2br(htmlspecialchars($devis['client_adresse'])); ?>
                                <?php if (!empty($devis['client_code_postal']) && !empty($devis['client_ville'])): ?>
                                    <br><?php echo htmlspecialchars($devis['client_code_postal'] . ' ' . $devis['client_ville']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Articles -->
            <div class="form-section">
                <h3><i class="fas fa-box"></i> Articles</h3>
                <?php if (!empty($devis_items)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th style="text-align: center;">Quantité</th>
                                    <th style="text-align: right;">Prix unitaire</th>
                                    <th style="text-align: right;">Total HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_ht = 0;
                                $total_tva = 0;
                                foreach ($devis_items as $item): 
                                    $quantite = $item['quantite'] ?? 1;
                                    $prix_unitaire = $item['prix_unitaire'] ?? 0;
                                    $taux_tva = $item['taux_tva'] ?? 20; // Valeur par défaut 20%
                                    
                                    $ligne_ht = $quantite * $prix_unitaire;
                                    $ligne_tva = $ligne_ht * ($taux_tva / 100);
                                    $total_ht += $ligne_ht;
                                    $total_tva += $ligne_tva;
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['designation'] ?? $item['nom'] ?? 'Article sans nom'); ?></strong>
                                            <?php if (!empty($item['description'])): ?>
                                                <br><small class="text-muted"><?php echo nl2br(htmlspecialchars($item['description'])); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo number_format($quantite, 0); ?></td>
                                        <td style="text-align: right;"><?php echo number_format($prix_unitaire, 2, ',', ' '); ?> €</td>
                                        <td style="text-align: right;"><strong><?php echo number_format($ligne_ht, 2, ',', ' '); ?> €</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Aucun article dans ce devis.</p>
                <?php endif; ?>
            </div>

            <!-- Totaux -->
            <div class="form-section">
                <div class="totaux-section">
                    <div class="totaux-content">
                        <div class="total-row">
                            <span>Total HT :</span>
                            <span><?php echo number_format($devis['total_ht'] ?? $total_ht ?? 0, 2, ',', ' '); ?> €</span>
                        </div>
                        <div class="total-row">
                            <?php 
                            $montant_ht = $devis['total_ht'] ?? $total_ht ?? 0;
                            $frais_port = $devis['frais_port'] ?? 8.50; // Frais de port par défaut 8,50€
                            $frais_offerts = $montant_ht >= 200;
                            ?>
                            <span>Frais de port :</span>
                            <span>
                                <?php if ($frais_offerts): ?>
                                    <span style="color: #28a745; font-weight: bold;">Offert</span>
                                    <small style="color: #6c757d;"> (commande ≥ 200€ HT)</small>
                                <?php else: ?>
                                    <?php echo number_format($frais_port, 2, ',', ' '); ?> €
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="total-row">
                            <span>TVA 20% :</span>
                            <span><?php echo number_format($devis['total_tva'] ?? $total_tva ?? 0, 2, ',', ' '); ?> €</span>
                        </div>
                        <div class="total-row total-final">
                            <span>Total TTC :</span>
                            <span>
                                <?php 
                                $total_ttc_calcule = $montant_ht + ($devis['total_tva'] ?? $total_tva ?? 0);
                                if (!$frais_offerts) {
                                    $total_ttc_calcule += $frais_port;
                                }
                                echo number_format($devis['total_ttc'] ?? $total_ttc_calcule, 2, ',', ' '); 
                                ?> €
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Notes -->
        <?php if (!empty($devis['notes'])): ?>
            <div class="form-section">
                <h3><i class="fas fa-sticky-note"></i> Notes</h3>
                <div class="notes-display">
                    <?php echo nl2br(htmlspecialchars($devis['notes'])); ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
<?php endif; ?>

<style>
.client-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.adresse-display {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    white-space: pre-line;
}

.notes-display {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    white-space: pre-line;
}

.totaux-section {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.totaux-content {
    min-width: 300px;
    background: #f8f9fa;
    border-radius: 5px;
    padding: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #ddd;
}

.total-row.total-final {
    font-weight: bold;
    font-size: 1.1em;
    color: #007bff;
    border-bottom: 2px solid #007bff;
    margin-top: 10px;
    padding-top: 15px;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    border-radius: 12px;
    text-transform: uppercase;
}

.badge-brouillon { background: #6c757d; }
.badge-envoye { background: #17a2b8; }
.badge-accepte { background: #28a745; }
.badge-refuse { background: #dc3545; }
.badge-expire { background: #ffc107; color: #212529; }

.text-muted {
    color: #6c757d;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th,
.table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #007bff;
}

.table-responsive {
    overflow-x: auto;
}

.alert {
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.btn {
    display: inline-block;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    margin: 0 5px;
    border: none;
    cursor: pointer;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn:hover {
    opacity: 0.8;
}

.badge-brouillon { background-color: #6c757d; }
.badge-envoye { background-color: #007bff; }
.badge-accepte { background-color: #28a745; }
.badge-refuse { background-color: #dc3545; }
.badge-expire { background-color: #fd7e14; }
</style>

<?php include 'footer_simple.php'; ?>