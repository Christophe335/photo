<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Filtres de recherche
    $search = $_GET['search'] ?? '';
    $statut_filter = $_GET['statut'] ?? 'tous';
    
    // Requête pour récupérer les clients
    $whereConditions = [];
    $params = [];
    
    if ($search) {
        $whereConditions[] = "(prenom LIKE ? OR nom LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($statut_filter !== 'tous') {
        $whereConditions[] = "actif = ?";
        $params[] = ($statut_filter === 'actifs') ? 1 : 0;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Statistiques
    $stats_query = "
        SELECT 
            COUNT(*) as total_clients,
            COUNT(CASE WHEN actif = 1 THEN 1 END) as clients_actifs,
            COUNT(CASE WHEN DATE(date_creation) = CURDATE() THEN 1 END) as nouveaux_aujourd_hui
        FROM clients
    ";
    $stats = $db->query($stats_query)->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer les clients avec nombre de commandes
    $query = "
        SELECT c.*, 
               COUNT(cmd.id) as nb_commandes,
               COALESCE(SUM(cmd.total), 0) as total_depense,
               MAX(cmd.date_commande) as derniere_commande
        FROM clients c
        LEFT JOIN commandes cmd ON c.id = cmd.client_id
        $whereClause
        GROUP BY c.id
        ORDER BY c.date_creation DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur gestion clients: " . $e->getMessage());
    $clients = [];
    $stats = ['total_clients' => 0, 'clients_actifs' => 0, 'nouveaux_aujourd_hui' => 0];
}

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/client.css">
</head>
<div class="toolbar">
    <h2><i class="fas fa-users"></i> Gestion des Clients</h2>
    <div style="flex: 1;"></div>
    <a href="export-clients.php" class="btn btn-success">
        <i class="fas fa-download"></i> Exporter
    </a>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #007bff;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['total_clients']); ?></div>
            <div class="stat-label">Total Clients</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #28a745;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['clients_actifs']); ?></div>
            <div class="stat-label">Clients Actifs</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #17a2b8;">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo number_format($stats['nouveaux_aujourd_hui']); ?></div>
            <div class="stat-label">Nouveaux Aujourd'hui</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-card">
    <form method="GET" class="filters-form">
        <div class="filter-group">
            <label for="search">Rechercher :</label>
            <input type="text" id="search" name="search" placeholder="Nom, prénom ou email..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="filter-group">
            <label for="statut">Statut :</label>
            <select id="statut" name="statut">
                <option value="tous" <?php echo $statut_filter === 'tous' ? 'selected' : ''; ?>>Tous</option>
                <option value="actifs" <?php echo $statut_filter === 'actifs' ? 'selected' : ''; ?>>Actifs</option>
                <option value="inactifs" <?php echo $statut_filter === 'inactifs' ? 'selected' : ''; ?>>Inactifs</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Rechercher
        </button>
        
        <?php if ($search || $statut_filter !== 'tous'): ?>
        <a href="?" class="btn btn-secondary">
            <i class="fas fa-times"></i> Effacer
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Liste des clients -->
<div class="content-card">
    <?php if (empty($clients)): ?>
        <div class="empty-state">
            <i class="fas fa-users fa-3x"></i>
            <h3>Aucun client trouvé</h3>
            <p>Aucun client ne correspond aux critères de recherche.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Commandes</th>
                        <th>Total dépensé</th>
                        <th>Inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td>
                            <div class="client-info">
                                <strong><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></strong>
                                <small>ID: #<?php echo $client['id']; ?></small>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($client['email']); ?>">
                                <?php echo htmlspecialchars($client['email']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($client['telephone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($client['telephone']); ?>">
                                    <?php echo htmlspecialchars($client['telephone']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $client['nb_commandes'] > 0 ? 'badge-info' : 'badge-light'; ?>">
                                <?php echo $client['nb_commandes']; ?> commande<?php echo $client['nb_commandes'] > 1 ? 's' : ''; ?>
                            </span>
                        </td>
                        <td>
                            <strong><?php echo number_format($client['total_depense'], 2, ',', ' '); ?> €</strong>
                        </td>
                        <td>
                            <div class="date-info">
                                <?php echo date('d/m/Y', strtotime($client['date_creation'])); ?>
                                <small><?php echo date('H:i', strtotime($client['date_creation'])); ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $client['actif'] ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $client['actif'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="client-details.php?id=<?php echo $client['id']; ?>" 
                                   class="btn btn-sm btn-info" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="client-edit.php?id=<?php echo $client['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="client-commandes.php?id=<?php echo $client['id']; ?>" 
                                   class="btn btn-sm btn-primary" title="Voir les commandes">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>



<?php //include 'footer.php'; ?>