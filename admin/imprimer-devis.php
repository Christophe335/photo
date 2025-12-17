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
        error_log("Erreur impression devis: " . $e->getMessage());
        $error = "Erreur lors du chargement du devis.";
    }
} else {
    $error = "ID de devis invalide.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis #<?php echo $devis ? htmlspecialchars($devis['numero']) : 'Inconnu'; ?> - Impression</title>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        
        .devis-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        
        .logo-section {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .company-info {
            color: #666;
            line-height: 1.4;
        }
        
        .devis-info {
            text-align: right;
            flex: 1;
        }
        
        .devis-title {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .devis-number {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .devis-date {
            color: #666;
        }
        
        .client-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .client-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            line-height: 1.6;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table th {
            background: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .items-table td:nth-child(3),
        .items-table td:nth-child(4),
        .items-table td:nth-child(5),
        .items-table td:nth-child(6) {
            text-align: right;
        }
        
        .totaux-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .totaux-table {
            min-width: 300px;
            border-collapse: collapse;
        }
        
        .totaux-table td {
            padding: 8px 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .totaux-table .total-label {
            text-align: left;
            font-weight: bold;
        }
        
        .totaux-table .total-value {
            text-align: right;
            font-weight: bold;
        }
        
        .totaux-table .total-final {
            background: #007bff;
            color: white;
            font-size: 18px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
            text-align: center;
        }
        
        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="devis-container">
            <!-- En-tête -->
            <div class="header">
                <div class="logo-section">
                    <div class="company-name">Mouillet Photos</div>
                    <div class="company-info">
                        Spécialiste en photographie et impression<br>
                        123 Avenue de la Photo<br>
                        75000 Paris<br>
                        Tél: 01 23 45 67 89<br>
                        Email: contact@mouillet-photos.fr
                    </div>
                </div>
                <div class="devis-info">
                    <div class="devis-title">DEVIS</div>
                    <div class="devis-number">N° <?php echo htmlspecialchars($devis['numero']); ?></div>
                    <div class="devis-date">
                        Date: <?php echo date('d/m/Y', strtotime($devis['date_creation'])); ?>
                    </div>
                    <div class="devis-date">
                        Validité: <?php echo date('d/m/Y', strtotime($devis['date_validite'])); ?>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="client-section">
                <div class="section-title">Client</div>
                <div class="client-info">
                    <?php if (!empty($devis['client_societe'])): ?>
                        <strong><?php echo htmlspecialchars($devis['client_societe']); ?></strong><br>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($devis['client_prenom'] . ' ' . $devis['client_nom']); ?><br>
                    <?php if (!empty($devis['client_adresse'])): ?>
                        <?php echo nl2br(htmlspecialchars($devis['client_adresse'])); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($devis['client_code_postal']) && !empty($devis['client_ville'])): ?>
                        <?php echo htmlspecialchars($devis['client_code_postal'] . ' ' . $devis['client_ville']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($devis['client_telephone'])): ?>
                        Tél: <?php echo htmlspecialchars($devis['client_telephone']); ?><br>
                    <?php endif; ?>
                    Email: <?php echo htmlspecialchars($devis['client_email']); ?>
                </div>
            </div>

            <!-- Articles -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Détails</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>TVA</th>
                        <th>Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_ht = 0;
                    $total_tva = 0;
                    foreach ($devis_items as $item): 
                        $ligne_ht = $item['quantite'] * $item['prix_unitaire'];
                        $ligne_tva = $ligne_ht * ($item['taux_tva'] / 100);
                        $total_ht += $ligne_ht;
                        $total_tva += $ligne_tva;
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['nom']); ?></strong></td>
                            <td><?php echo nl2br(htmlspecialchars($item['description'] ?? '')); ?></td>
                            <td><?php echo $item['quantite']; ?></td>
                            <td><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?> €</td>
                            <td><?php echo $item['taux_tva']; ?>%</td>
                            <td><?php echo number_format($ligne_ht, 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totaux -->
            <div class="totaux-section">
                <table class="totaux-table">
                    <tr>
                        <td class="total-label">Total HT :</td>
                        <td class="total-value"><?php echo number_format($total_ht, 2, ',', ' '); ?> €</td>
                    </tr>
                    <tr>
                        <td class="total-label">Total TVA :</td>
                        <td class="total-value"><?php echo number_format($total_tva, 2, ',', ' '); ?> €</td>
                    </tr>
                    <tr class="total-final">
                        <td class="total-label">Total TTC :</td>
                        <td class="total-value"><?php echo number_format($total_ht + $total_tva, 2, ',', ' '); ?> €</td>
                    </tr>
                </table>
            </div>

            <!-- Pied de page -->
            <div class="footer">
                <p>Ce devis est valable <?php echo $devis['duree_validite']; ?> jours à partir de la date d'émission.</p>
                <p>Conditions de paiement : <?php echo htmlspecialchars($devis['conditions_paiement'] ?? 'À réception de facture'); ?></p>
                <p>SIRET: 123 456 789 00012 - TVA: FR12345678901</p>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>