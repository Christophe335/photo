<?php
session_start();
require_once '../includes/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    header('Location: connexion.php');
    exit;
}

$numero_commande = $_GET['numero'] ?? '';
if (empty($numero_commande)) {
    header('Location: mon-compte.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les détails de la commande
    $stmt = $db->prepare("
        SELECT c.*, cl.prenom, cl.nom, cl.email
        FROM commandes c
        JOIN clients cl ON c.client_id = cl.id
        WHERE c.numero_commande = ? AND c.client_id = ?
    ");
    $stmt->execute([$numero_commande, $_SESSION['client_id']]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        $_SESSION['error_message'] = "Commande non trouvée.";
        header('Location: mon-compte.php');
        exit;
    }
    
    // Récupérer les items de la commande
    $stmt = $db->prepare("
        SELECT * FROM commande_items 
        WHERE commande_id = ? 
        ORDER BY id
    ");
    $stmt->execute([$commande['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur confirmation commande: " . $e->getMessage());
    $_SESSION['error_message'] = "Erreur lors de la récupération de la commande.";
    header('Location: mon-compte.php');
    exit;
}

include '../includes/header.php';
?>

<head>
    <link rel="stylesheet" href="../css/client.css">
    
</head>

<main>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1 class="confirmation-title">✓ Commande confirmée !</h1>
            <p class="order-number">Numéro de commande : <strong><?php echo htmlspecialchars($commande['numero_commande']); ?></strong></p>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="welcome-message">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="confirmation-card">
            <div class="order-details">
                <div class="detail-group">
                    <h3>Informations de commande</h3>
                    <div class="detail-item">
                        <span class="detail-label">Date :</span>
                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Statut :</span>
                        <span class="detail-value">
                            <?php
                            $statuts = [
                                'en_attente' => 'En attente de paiement',
                                'confirmee' => 'Confirmée',
                                'en_preparation' => 'En préparation',
                                'expediee' => 'Expédiée',
                                'livree' => 'Livrée'
                            ];
                            echo $statuts[$commande['statut']] ?? $commande['statut'];
                            ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Mode de paiement :</span>
                        <span class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $commande['mode_paiement'])); ?></span>
                    </div>
                </div>
                
                <div class="detail-group">
                    <h3>Adresse de livraison</h3>
                    <div class="detail-value">
                        <?php echo nl2br(htmlspecialchars($commande['adresse_livraison'])); ?><br>
                        <?php echo htmlspecialchars($commande['code_postal_livraison']); ?> 
                        <?php echo htmlspecialchars($commande['ville_livraison']); ?><br>
                        <?php echo htmlspecialchars($commande['pays_livraison']); ?>
                    </div>
                </div>
            </div>
            
            <h3>Détail de votre commande</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Détails</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['designation']); ?></td>
                            <td>
                                <?php if ($item['format']): ?>
                                    <small>Format: <?php echo htmlspecialchars($item['format']); ?></small><br>
                                <?php endif; ?>
                                <?php if ($item['couleur']): ?>
                                    <small>Couleur: <?php echo htmlspecialchars($item['couleur']); ?></small><br>
                                <?php endif; ?>
                                <?php if ($item['conditionnement']): ?>
                                    <small><?php echo htmlspecialchars($item['conditionnement']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $item['quantite']; ?></td>
                            <td><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?> €</td>
                            <td><?php echo number_format($item['total_ligne'], 2, ',', ' '); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-section">
                <div class="total-line">
                    <span>Sous-total :</span>
                    <span><?php echo number_format($commande['sous_total'], 2, ',', ' '); ?> €</span>
                </div>
                <div class="total-line">
                    <span>TVA (20%) :</span>
                    <span><?php echo number_format($commande['tva'], 2, ',', ' '); ?> €</span>
                </div>
                <div class="total-line">
                    <span>Frais de livraison :</span>
                    <span><?php echo number_format($commande['frais_livraison'], 2, ',', ' '); ?> €</span>
                </div>
                <div class="total-line total-final">
                    <span>Total :</span>
                    <span><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</span>
                </div>
            </div>
            
            <?php if ($commande['commentaire_client']): ?>
                <div style="margin-top: 20px;">
                    <h4>Votre commentaire :</h4>
                    <p style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
                        <?php echo nl2br(htmlspecialchars($commande['commentaire_client'])); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="actions">
            <a href="mon-compte.php" class="btn-primary">Voir mes commandes</a>
            <a href="../index.php" class="btn-cancel">Continuer mes achats</a>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>