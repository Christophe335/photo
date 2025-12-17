<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

$numero_commande = $_GET['numero'] ?? '';
if (empty($numero_commande)) {
    header('Location: index.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les détails de la commande
    $stmt = $db->prepare("
        SELECT c.*, cl.prenom, cl.nom, cl.email, cl.telephone, cl.adresse, cl.code_postal, cl.ville,
               cl.adresse_livraison_differente, cl.adresse_livraison, cl.code_postal_livraison, 
               cl.ville_livraison, cl.pays_livraison
        FROM commandes c
        JOIN clients cl ON c.client_id = cl.id
        WHERE c.numero_commande = ?
    ");
    $stmt->execute([$numero_commande]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        $_SESSION['message'] = "Commande non trouvée.";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
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
    error_log("Erreur détail commande admin: " . $e->getMessage());
    $_SESSION['message'] = "Erreur lors de la récupération de la commande.";
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Fonction pour afficher le statut avec couleur
function getStatutBadge($statut) {
    $statuts = [
        'en_attente' => ['label' => 'En attente', 'class' => 'warning'],
        'confirmee' => ['label' => 'Confirmée', 'class' => 'info'],
        'en_preparation' => ['label' => 'En préparation', 'class' => 'primary'],
        'en_cours' => ['label' => 'En cours', 'class' => 'primary'],
        'expediee' => ['label' => 'Expédiée', 'class' => 'success'],
        'livree' => ['label' => 'Livrée', 'class' => 'success'],
        'annulee' => ['label' => 'Annulée', 'class' => 'danger']
    ];
    
    $info = $statuts[$statut] ?? ['label' => $statut, 'class' => 'secondary'];
    return '<span class="badge badge-' . $info['class'] . '">' . $info['label'] . '</span>';
}

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<div class="page-header">
    <div>
        <h2><i class="fas fa-receipt"></i> Détail de la commande #<?php echo htmlspecialchars($commande['numero_commande']); ?></h2>
        <p class="page-subtitle">Commande passée le <?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></p>
    </div>
    <div>
        <a href="client-commandes.php?id=<?php echo $commande['client_id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux commandes
        </a>
        <a href="client-details.php?id=<?php echo $commande['client_id']; ?>" class="btn btn-info">
            <i class="fas fa-user"></i> Voir le client
        </a>
    </div>
</div>

<div class="commande-detail">
    <!-- Informations générales -->
    <div class="info-section">
        <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Numéro de commande</label>
                <span><?php echo htmlspecialchars($commande['numero_commande']); ?></span>
            </div>
            <div class="info-item">
                <label>Date de commande</label>
                <span><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></span>
            </div>
            <div class="info-item">
                <label>Statut</label>
                <span><?php echo getStatutBadge($commande['statut']); ?></span>
            </div>
            <div class="info-item">
                <label>Total</label>
                <span class="price"><?php echo number_format($commande['total'], 2, ',', ' '); ?>€</span>
            </div>
            <?php if ($commande['numero_suivi']): ?>
            <div class="info-item">
                <label>Numéro de suivi</label>
                <span><?php echo htmlspecialchars($commande['numero_suivi']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informations client -->
    <div class="info-section">
        <h3><i class="fas fa-user"></i> Informations client</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Nom complet</label>
                <span><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></span>
            </div>
            <div class="info-item">
                <label>Email</label>
                <span><a href="mailto:<?php echo htmlspecialchars($commande['email']); ?>"><?php echo htmlspecialchars($commande['email']); ?></a></span>
            </div>
            <?php if ($commande['telephone']): ?>
            <div class="info-item">
                <label>Téléphone</label>
                <span><?php echo htmlspecialchars($commande['telephone']); ?></span>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Adresses -->
    <div class="addresses-section">
        <div class="address-card">
            <h4><i class="fas fa-file-invoice"></i> Adresse de facturation</h4>
            <div class="address-content">
                <?php echo htmlspecialchars($commande['adresse_facturation']); ?><br>
                <?php echo htmlspecialchars($commande['code_postal_facturation'] . ' ' . $commande['ville_facturation']); ?><br>
                <?php echo htmlspecialchars($commande['pays_facturation']); ?>
            </div>
        </div>
        
        <div class="address-card">
            <h4><i class="fas fa-truck"></i> Adresse de livraison</h4>
            <div class="address-content">
                <?php 
                // Priorité 1: Adresse de livraison spécifique de la commande
                if (!empty($commande['adresse_livraison'])): ?>
                    <?php echo htmlspecialchars($commande['adresse_livraison']); ?><br>
                    <?php echo htmlspecialchars($commande['code_postal_livraison'] . ' ' . $commande['ville_livraison']); ?><br>
                    <?php echo htmlspecialchars($commande['pays_livraison']); ?>
                <?php 
                // Priorité 2: Adresse de livraison du profil client si différente
                elseif ($commande['adresse_livraison_differente'] && !empty($commande['adresse_livraison'])): ?>
                    <?php echo nl2br(htmlspecialchars($commande['adresse_livraison'])); ?><br>
                    <?php echo htmlspecialchars($commande['code_postal_livraison'] . ' ' . $commande['ville_livraison']); ?><br>
                    <?php echo htmlspecialchars($commande['pays_livraison'] ?: 'France'); ?>
                <?php 
                // Priorité 3: Adresse principale du client
                else: ?>
                    <?php echo htmlspecialchars($commande['adresse']); ?><br>
                    <?php echo htmlspecialchars($commande['code_postal'] . ' ' . $commande['ville']); ?><br>
                    France
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Articles commandés -->
    <div class="info-section">
        <h3><i class="fas fa-shopping-cart"></i> Articles commandés</h3>
        <div class="items-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Référence</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total_ht = 0; ?>
                    <?php foreach ($items as $item): ?>
                        <?php $sous_total = $item['prix_unitaire'] * $item['quantite']; ?>
                        <?php $total_ht += $sous_total; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['designation']); ?></td>
                            <td><?php echo htmlspecialchars($item['produit_code']); ?></td>
                            <td class="price"><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?>€</td>
                            <td class="text-center"><?php echo $item['quantite']; ?></td>
                            <td class="price"><?php echo number_format($sous_total, 2, ',', ' '); ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total HT:</strong></td>
                        <td class="price"><strong><?php echo number_format($total_ht, 2, ',', ' '); ?>€</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Frais de port:</strong></td>
                        <td class="price"><strong><?php echo number_format($commande['frais_livraison'], 2, ',', ' '); ?>€</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total TTC:</strong></td>
                        <td class="price total-final"><strong><?php echo number_format($commande['total'], 2, ',', ' '); ?>€</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if (!empty($commande['notes'])): ?>
    <!-- Notes -->
    <div class="info-section">
        <h3><i class="fas fa-sticky-note"></i> Notes</h3>
        <div class="notes-content">
            <?php echo nl2br(htmlspecialchars($commande['notes'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="actions-section">
        <h3><i class="fas fa-tools"></i> Actions</h3>
        <div class="action-buttons">
            <a href="update-statut-commande.php?id=<?php echo $commande['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier le statut
            </a>
            <?php if ($commande['statut'] === 'livree'): ?>
            <a href="generer-facture.php?commande=<?php echo $commande['id']; ?>" class="btn btn-success">
                <i class="fas fa-file-invoice"></i> Générer facture
            </a>
            <?php endif; ?>
            <a href="javascript:window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> Imprimer une capture écran de cette page
            </a>
        </div>
    </div>
</div>



<?php include 'footer_simple.php'; ?>