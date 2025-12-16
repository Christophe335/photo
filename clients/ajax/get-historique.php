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
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Compter le total des commandes
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM commandes 
        WHERE client_id = ?
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $total_commandes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_commandes / $limit);
    
    // Récupérer l'historique des commandes
    $stmt = $db->prepare("
        SELECT c.*, 
               COUNT(ci.id) as nb_articles,
               DATE_FORMAT(c.date_commande, '%d/%m/%Y à %H:%i') as date_format,
               DATE_FORMAT(c.date_livraison, '%d/%m/%Y') as date_livraison_format
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        WHERE c.client_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$_SESSION['client_id'], $limit, $offset]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($commandes)) {
        echo '<div class="empty-state">';
        echo '<h3>Aucune commande</h3>';
        echo '<p>Vous n\'avez encore passé aucune commande.</p>';
        echo '<a href="../pages/catalogue.php" class="btn-primary">Commencer mes achats</a>';
        echo '</div>';
    } else {
        // Statistiques
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_commandes,
                COUNT(CASE WHEN statut = 'livree' THEN 1 END) as commandes_livrees,
                SUM(total) as total_depense,
                DATE_FORMAT(MIN(date_commande), '%d/%m/%Y') as premiere_commande
            FROM commandes 
            WHERE client_id = ?
        ");
        $stmt->execute([$_SESSION['client_id']]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo '<div class="historique-stats">';
        echo '<div class="stats-row">';
        echo '<div class="stat-item">';
        echo '<span class="stat-value">' . $stats['total_commandes'] . '</span>';
        echo '<span class="stat-label">Commandes total</span>';
        echo '</div>';
        echo '<div class="stat-item">';
        echo '<span class="stat-value">' . $stats['commandes_livrees'] . '</span>';
        echo '<span class="stat-label">Commandes livrées</span>';
        echo '</div>';
        echo '<div class="stat-item">';
        echo '<span class="stat-value">' . number_format($stats['total_depense'], 0, ',', ' ') . '€</span>';
        echo '<span class="stat-label">Total dépensé</span>';
        echo '</div>';
        echo '<div class="stat-item">';
        echo '<span class="stat-value">' . ($stats['premiere_commande'] ?: 'Récent') . '</span>';
        echo '<span class="stat-label">Client depuis</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Table des commandes
        echo '<div class="historique-table-container">';
        echo '<table class="historique-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Numéro</th>';
        echo '<th>Date</th>';
        echo '<th>Articles</th>';
        echo '<th>Statut</th>';
        echo '<th>Total</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $statuts = [
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'en_preparation' => 'En préparation',
            'en_cours' => 'En cours',
            'expediee' => 'Expédiée',
            'livree' => 'Livrée',
            'annulee' => 'Annulée'
        ];
        
        foreach ($commandes as $commande) {
            $statut_class = 'status-' . $commande['statut'];
            
            echo '<tr>';
            echo '<td><strong>#' . htmlspecialchars($commande['numero_commande']) . '</strong></td>';
            echo '<td>' . $commande['date_format'] . '</td>';
            echo '<td>' . $commande['nb_articles'] . '</td>';
            echo '<td><span class="statut-badge ' . $statut_class . '">' . ($statuts[$commande['statut']] ?? $commande['statut']) . '</span></td>';
            echo '<td><strong>' . number_format($commande['total'], 2, ',', ' ') . ' €</strong></td>';
            echo '<td>';
            echo '<a href="confirmation-commande.php?numero=' . urlencode($commande['numero_commande']) . '" class="btn-view">Détails</a>';
            if ($commande['statut'] === 'livree') {
                echo '<a href="#" onclick="reorderCommande(\'' . $commande['numero_commande'] . '\')" class="btn-reorder">Recommander</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        
        // Pagination
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            
            if ($page > 1) {
                echo '<a href="#" onclick="loadHistoriquePage(' . ($page - 1) . ')" class="page-btn">« Précédent</a>';
            }
            
            for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                $active = ($i == $page) ? ' active' : '';
                echo '<a href="#" onclick="loadHistoriquePage(' . $i . ')" class="page-btn' . $active . '">' . $i . '</a>';
            }
            
            if ($page < $total_pages) {
                echo '<a href="#" onclick="loadHistoriquePage(' . ($page + 1) . ')" class="page-btn">Suivant »</a>';
            }
            
            echo '</div>';
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur get-historique: " . $e->getMessage());
    echo '<p>Erreur lors du chargement de l\'historique.</p>';
}
?>

<style>
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.historique-stats {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.historique-table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.historique-table {
    width: 100%;
    border-collapse: collapse;
}

.historique-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.historique-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.historique-table tr:hover {
    background: #f8f9fa;
}

.statut-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-en_attente {
    background: #fff3cd;
    color: #856404;
}

.status-confirmee {
    background: #d4edda;
    color: #155724;
}

.status-en_preparation {
    background: #d1ecf1;
    color: #0c5460;
}

.status-en_cours {
    background: #d1ecf1;
    color: #0c5460;
}

.status-expediee {
    background: #e2e3e5;
    color: #383d41;
}

.status-livree {
    background: #d4edda;
    color: #155724;
}

.status-annulee {
    background: #f8d7da;
    color: #721c24;
}

.btn-view,
.btn-reorder {
    display: inline-block;
    padding: 6px 12px;
    margin-right: 5px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
}

.btn-view {
    background: #007bff;
    color: white;
}

.btn-view:hover {
    background: #0056b3;
}

.btn-reorder {
    background: #28a745;
    color: white;
    border: none;
}

.btn-reorder:hover {
    background: #1e7e34;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.page-btn {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-decoration: none;
    color: #007bff;
    background: white;
    cursor: pointer;
}

.page-btn:hover,
.page-btn.active {
    background: #007bff;
    color: white;
}

@media (max-width: 768px) {
    .historique-table {
        font-size: 0.9rem;
    }
    
    .historique-table th,
    .historique-table td {
        padding: 10px;
    }
    
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
function loadHistoriquePage(page) {
    fetch('ajax/get-historique.php?page=' + page)
        .then(response => response.text())
        .then(html => {
            document.getElementById('historique-content').innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

function reorderCommande(numeroCommande) {
    if (confirm('Voulez-vous vraiment recommander les mêmes articles ?')) {
        // Ici vous pouvez implémenter la logique de recommande
        alert('Fonctionnalité de recommande en cours de développement.');
    }
}
</script>