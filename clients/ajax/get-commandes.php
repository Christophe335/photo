<?php
session_start();
require_once '../../includes/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    http_response_code(401);
    echo '<div class="alert alert-error">Vous devez être connecté pour accéder à cette page.</div>';
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les commandes en cours
    $stmt = $db->prepare("
        SELECT c.*, 
               COUNT(ci.id) as nb_articles,
               DATE_FORMAT(c.date_commande, '%d/%m/%Y à %H:%i') as date_format,
               DATE_FORMAT(c.date_expedition, '%d/%m/%Y') as date_expedition_format
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        WHERE c.client_id = ? 
        AND c.statut IN ('confirmee', 'en_preparation', 'en_cours', 'expediee')
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $commandes_en_cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo '<div class="debug-info">Client ID: ' . $_SESSION['client_id'] . ' - Nombre de commandes trouvées: ' . count($commandes_en_cours) . '</div>';
        if (empty($commandes_en_cours)) {
        echo '<div class="empty-state">';
        echo '<h3>Aucune commande en cours</h3>';
        echo '<p>Vous n\'avez actuellement aucune commande en cours de traitement.</p>';
        echo '<a href="../pages/catalogue.php" class="btn-primary">Découvrir nos produits</a>';
        echo '</div>';
    } else {
        echo '<div class="commandes-grid">';
        
        foreach ($commandes_en_cours as $commande) {
            $statut_class = 'status-' . $commande['statut'];
            $statuts = [
                'confirmee' => 'Confirmée',
                'en_preparation' => 'En préparation',
                'en_cours' => 'En cours de traitement',
                'expediee' => 'Expédiée'
            ];
            
            echo '<div class="commande-card">';
            echo '<div class="commande-header">';
            echo '<div class="commande-numero">';
            echo '<strong>Commande #' . htmlspecialchars($commande['numero_commande']) . '</strong>';
            echo '<span class="commande-date">Passée le ' . $commande['date_format'] . '</span>';
            echo '</div>';
            echo '<div class="commande-statut">';
            echo '<span class="statut-badge ' . $statut_class . '">' . $statuts[$commande['statut']] . '</span>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="commande-details">';
            echo '<div class="detail-row">';
            echo '<span class="detail-label">Articles :</span>';
            echo '<span class="detail-value">' . $commande['nb_articles'] . '</span>';
            echo '</div>';
            echo '<div class="detail-row">';
            echo '<span class="detail-label">Total :</span>';
            echo '<span class="detail-value"><strong>' . number_format($commande['total'], 2, ',', ' ') . ' €</strong></span>';
            echo '</div>';
            
            if ($commande['statut'] === 'expediee' && $commande['numero_suivi']) {
                echo '<div class="detail-row">';
                echo '<span class="detail-label">Numéro de suivi :</span>';
                echo '<span class="detail-value"><code>' . htmlspecialchars($commande['numero_suivi']) . '</code></span>';
                echo '</div>';
                
                if ($commande['transporteur']) {
                    echo '<div class="detail-row">';
                    echo '<span class="detail-label">Transporteur :</span>';
                    echo '<span class="detail-value">' . htmlspecialchars($commande['transporteur']) . '</span>';
                    echo '</div>';
                }
                
                if ($commande['date_expedition']) {
                    echo '<div class="detail-row">';
                    echo '<span class="detail-label">Expédiée le :</span>';
                    echo '<span class="detail-value">' . $commande['date_expedition_format'] . '</span>';
                    echo '</div>';
                }
            }
            echo '</div>';
            
            echo '<div class="commande-actions">';
            echo '<a href="confirmation-commande.php?numero=' . urlencode($commande['numero_commande']) . '" class="btn-details">Voir les détails</a>';
            if ($commande['statut'] === 'expediee' && $commande['numero_suivi']) {
                echo '<a href="#" onclick="trackPackage(\'' . htmlspecialchars($commande['numero_suivi']) . '\')" class="btn-track">Suivre le colis</a>';
            }
            echo '</div>';
            
            echo '</div>'; // .commande-card
        }
        
        echo '</div>'; // .commandes-grid
    }
    
} catch (Exception $e) {
    error_log("Erreur get-commandes: " . $e->getMessage());
    echo '<div class="alert alert-error">Erreur lors du chargement des commandes: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>