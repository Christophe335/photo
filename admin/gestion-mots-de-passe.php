<?php
require_once 'functions.php';
require_once '../includes/database.php';

// Vérifier l'authentification admin
checkAuth();
include 'header.php';
try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Gestion des mots de passe clients</h2>";
    
    // Action : générer des mots de passe pour les clients qui n'en ont pas
    if (isset($_POST['generer_mots_de_passe'])) {
        $stmt = $db->prepare("
            SELECT id, email, prenom, nom 
            FROM clients 
            WHERE mot_de_passe_clair IS NULL OR mot_de_passe_clair = ''
        ");
        $stmt->execute();
        $clients_sans_mdp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $generes = 0;
        foreach ($clients_sans_mdp as $client) {
            // Générer un mot de passe simple
            $nouveau_mdp = 'photo' . str_pad($client['id'], 3, '0', STR_PAD_LEFT);
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            
            $update = $db->prepare("UPDATE clients SET mot_de_passe = ?, mot_de_passe_clair = ? WHERE id = ?");
            $update->execute([$hash, $nouveau_mdp, $client['id']]);
            $generes++;
        }
        
        echo "<div class='alert alert-success'>$generes mots de passe générés automatiquement.</div>";
    }
    
    // Statistiques
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_clients,
            COUNT(CASE WHEN mot_de_passe_clair IS NOT NULL AND mot_de_passe_clair != '' THEN 1 END) as avec_mdp_clair,
            COUNT(CASE WHEN mot_de_passe_clair IS NULL OR mot_de_passe_clair = '' THEN 1 END) as sans_mdp_clair
        FROM clients
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<div class='stats-row'>";
    echo "<div class='stat-box'>";
    echo "<h3>{$stats['total_clients']}</h3>";
    echo "<p>Total clients</p>";
    echo "</div>";
    echo "<div class='stat-box'>";
    echo "<h3>{$stats['avec_mdp_clair']}</h3>";
    echo "<p>Avec mot de passe visible</p>";
    echo "</div>";
    echo "<div class='stat-box'>";
    echo "<h3>{$stats['sans_mdp_clair']}</h3>";
    echo "<p>Sans mot de passe visible</p>";
    echo "</div>";
    echo "</div>";
    
    if ($stats['sans_mdp_clair'] > 0) {
        echo "<form method='post' style='margin: 20px 0;'>";
        echo "<button type='submit' name='generer_mots_de_passe' class='btn btn-primary' onclick='return confirm(\"Générer des mots de passe automatiquement pour {$stats['sans_mdp_clair']} clients ?\")'>Générer mots de passe manquants</button>";
        echo "</form>";
    }
    
    // Liste des clients
    echo "<h3>Clients avec mots de passe :</h3>";
    $stmt = $db->prepare("
        SELECT id, email, prenom, nom, mot_de_passe_clair, date_creation
        FROM clients 
        WHERE mot_de_passe_clair IS NOT NULL AND mot_de_passe_clair != ''
        ORDER BY date_creation DESC
        LIMIT 20
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($clients)) {
        echo "<p>Aucun client avec mot de passe visible.</p>";
    } else {
        echo "<table class='clients-table'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Mot de passe</th><th>Date création</th><th>Action</th></tr>";
        
        foreach ($clients as $client) {
            echo "<tr>";
            echo "<td>{$client['id']}</td>";
            echo "<td>" . htmlspecialchars($client['prenom'] . ' ' . $client['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($client['email']) . "</td>";
            echo "<td class='password-cell'>" . htmlspecialchars($client['mot_de_passe_clair']) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($client['date_creation'])) . "</td>";
            echo "<td>";
            echo "<a href='client-details.php?id={$client['id']}' class='btn btn-sm'>Détails</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
}
?>

<style>
.alert {
    padding: 15px;
    margin: 15px 0;
    border-radius: 4px;
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
.stats-row {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}
.stat-box {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    flex: 1;
}
.stat-box h3 {
    margin: 0;
    color: #007bff;
    font-size: 2rem;
}
.stat-box p {
    margin: 5px 0 0 0;
    color: #666;
}
.clients-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}
.clients-table th,
.clients-table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}
.clients-table th {
    background: #f8f9fa;
    font-weight: 600;
}
.password-cell {
    font-family: monospace;
    background: #f8f9fa;
    font-weight: bold;
    color: #007bff;
}
.btn {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}
.btn:hover {
    background: #0056b3;
}
.btn-primary {
    background: #007bff;
}
.btn-sm {
    padding: 5px 10px;
    font-size: 0.9rem;
}
</style>