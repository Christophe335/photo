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
            // Récupérer les articles du devis et certaines informations produit (référence, format, conditionnement, couleur)
            $stmt = $db->prepare(
                "SELECT di.*, p.reference AS produit_reference_full, p.designation AS produit_designation, p.format AS produit_format, p.conditionnement AS produit_conditionnement, p.couleur_interieur AS produit_couleur
                 FROM devis_items di
                 LEFT JOIN produits p ON di.produit_id = p.id
                 WHERE di.devis_id = ?
                 ORDER BY di.id"
            );
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
                        <img src="../images/logo-icon/logo.svg" alt="Bindy Studio" style="max-height:80px; display:block; margin-bottom:10px;">
                        <div class="company-name">Bindy Studio</div>
                        <div class="company-info">
                            by General Cover<br>
                            9 rue de la gare<br>
                            70000 Vallerois-le-Boiss<br>
                            Tél: 03 84 78 38 39<br>
                            Email: contact@bindy-studio.fr
                        </div>
                    </div>
                <div class="devis-info">
                    <div class="devis-title">DEVIS</div>
                    <div class="devis-number">N° <?php echo htmlspecialchars($devis['numero']); ?></div>
                    <div class="devis-date">
                        Date: <?php echo date('d/m/Y', strtotime($devis['date_creation'])); ?>
                    </div>
                    <?php
                    $duree_validite = isset($devis['duree_validite']) && $devis['duree_validite'] !== '' ? (int)$devis['duree_validite'] : 30;
                    $validite_date = isset($devis['date_creation']) ? date('d/m/Y', strtotime($devis['date_creation'] . " +{$duree_validite} days")) : '—';
                    ?>
                    <div class="devis-date">
                        Validité: <?php echo $validite_date; ?>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="client-section">
                <div class="section-title">Client</div>
                <?php
                // Colonne 1 : nom & email
                $client_name = trim((string)($devis['client_prenom'] ?? '') . ' ' . (string)($devis['client_nom'] ?? ''));
                $client_email = $devis['client_email'] ?? '';

                // Adresse de facturation : utiliser les informations de la fiche client
                $billing_parts = [];
                if (!empty($devis['client_societe'])) $billing_parts[] = $devis['client_societe'];
                if ($client_name) $billing_parts[] = $client_name;
                if (!empty($devis['client_adresse'])) $billing_parts[] = $devis['client_adresse'];
                $pc = trim((string)($devis['client_code_postal'] ?? '') . ' ' . (string)($devis['client_ville'] ?? ''));
                if ($pc) $billing_parts[] = $pc;
                if (!empty($devis['client_telephone'])) $billing_parts[] = 'Tél: ' . $devis['client_telephone'];

                // Adresse de livraison : priorité champs devis, sinon client, sinon même que facturation
                $shipping_parts = [];
                if (!empty($devis['adresse_livraison'])) {
                    $shipping_parts[] = $devis['adresse_livraison'];
                    $pcl = trim((string)($devis['code_postal_livraison'] ?? '') . ' ' . (string)($devis['ville_livraison'] ?? ''));
                    if ($pcl) $shipping_parts[] = $pcl;
                } elseif (!empty($devis['client_adresse_livraison'])) {
                    $shipping_parts[] = $devis['client_adresse_livraison'];
                    $pcl = trim((string)($devis['client_code_postal_livraison'] ?? '') . ' ' . (string)($devis['client_ville_livraison'] ?? ''));
                    if ($pcl) $shipping_parts[] = $pcl;
                }

                $col1 = [];
                if ($client_name) $col1[] = htmlspecialchars($client_name);
                if ($client_email) $col1[] = 'Email: ' . htmlspecialchars($client_email);

                $billing_html = '';
                if (!empty($billing_parts)) {
                    $billing_html = implode("<br>", array_map(function($v){ return htmlspecialchars((string)$v); }, $billing_parts));
                }

                $shipping_html = '';
                if (!empty($shipping_parts)) {
                    $shipping_html = implode("<br>", array_map(function($v){ return htmlspecialchars((string)$v); }, $shipping_parts));
                } else {
                    $shipping_html = '<em>Même que facturation</em>';
                }
                ?>

                <div style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <div class="client-info">
                            <?php if (!empty($col1)): ?>
                                <?php echo implode('<br>', $col1); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="client-info">
                            <strong>Adresse de facturation</strong><br>
                            <?php echo $billing_html; ?>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div class="client-info">
                            <strong>Adresse de livraison</strong><br>
                            <?php echo $shipping_html; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles -->
            <table class="items-table">
                <thead>
                    <tr>
                                        <th>Ref</th>
                                        <th>Description</th>
                                        <th>Cdt</th>
                                        <th>Qte</th>
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
                        // référence : priorité champ dans la ligne puis référence produit liée
                        $ref = !empty($item['produit_reference']) ? $item['produit_reference'] : ($item['produit_reference_full'] ?? '');
                        // désignation (ligne de devis) ou fallback sur produit
                        $designation = $item['designation'] ?? ($item['produit_designation'] ?? '');
                        $format = $item['produit_format'] ?? '';
                        $couleur = $item['produit_couleur'] ?? '';
                        $conditionnement = $item['produit_conditionnement'] ?? '';
                        $quantite = isset($item['quantite']) ? $item['quantite'] : 0;
                        $prix_unitaire = isset($item['prix_unitaire']) ? $item['prix_unitaire'] : 0;
                        $taux_tva = isset($item['taux_tva']) && $item['taux_tva'] !== '' ? $item['taux_tva'] : 20;
                        $ligne_ht = $quantite * $prix_unitaire;
                        $ligne_tva = $ligne_ht * ($taux_tva / 100);
                        $total_ht += $ligne_ht;
                        $total_tva += $ligne_tva;

                        $desc_parts = [];
                        if ($designation !== '') $desc_parts[] = $designation;
                        if ($format !== '') $desc_parts[] = $format;
                        if ($couleur !== '') $desc_parts[] = $couleur;
                        $description_display = implode(' — ', $desc_parts);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$ref); ?></td>
                            <td><?php echo nl2br(htmlspecialchars((string)$description_display)); ?></td>
                            <td><?php echo htmlspecialchars((string)$conditionnement); ?></td>
                            <td><?php echo (int)$quantite; ?></td>
                            <td><?php echo number_format((float)$prix_unitaire, 2, ',', ' '); ?> €</td>
                            <td><?php echo htmlspecialchars((string)$taux_tva); ?>%</td>
                            <td><?php echo number_format($ligne_ht, 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php
            // Calcul des frais de port : 13,95€ HT si total HT > 0 et < 200€, sinon offerts
            $frais_port_ht = ($total_ht > 0 && $total_ht < 200) ? 13.95 : 0;
            $frais_port_tva = $frais_port_ht * 0.20; // TVA 20% sur les frais de port
            $total_ht_with_shipping = $total_ht + $frais_port_ht;
            $total_tva_with_shipping = $total_tva + $frais_port_tva;
            $total_ttc_final = $total_ht_with_shipping + $total_tva_with_shipping;
            ?>

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
                    <tr>
                        <td class="total-label">Frais de port HT :</td>
                        <td class="total-value">
                            <?php if ($frais_port_ht > 0): ?>
                                <?php echo number_format($frais_port_ht, 2, ',', ' '); ?> €
                            <?php else: ?>
                                <span style="color:green; font-weight:bold">Frais de port Offerts</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="total-label">Total TTC :</td>
                        <td class="total-value"><?php echo number_format($total_ttc_final, 2, ',', ' '); ?> €</td>
                    </tr>
                </table>
            </div>

            <!-- Pied de page -->
            <div class="footer">
                <?php $duree_validite = isset($devis['duree_validite']) && $devis['duree_validite'] !== '' ? (int)$devis['duree_validite'] : 30; ?>
                <p>Ce devis est valable <?php echo $duree_validite; ?> jours à partir de la date d'émission.</p>
                <p>Conditions de paiement : <?php echo htmlspecialchars($devis['conditions_paiement'] ?? 'À réception de facture'); ?></p>
                <p>SIRET: 423 249 879 00010 - TVA: FR55423249879000010</p>
                <p>Téléphone: 03 84 78 38 39 - Email: contact@bindy-studio.fr</p>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>