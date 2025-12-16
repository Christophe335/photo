<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la structure de la table
    echo "<h2>Structure de la table commandes</h2>";
    $stmt = $db->prepare("SHOW COLUMNS FROM commandes");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Défaut</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Compter les commandes
    echo "<h2>Nombre de commandes</h2>";
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM commandes");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total commandes: {$count['total']}</p>";
    
    if ($count['total'] > 0) {
        // Afficher les dernières commandes
        echo "<h2>Dernières commandes</h2>";
        $stmt = $db->prepare("SELECT id, numero_commande, statut, total, date_commande FROM commandes ORDER BY id DESC LIMIT 5");
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Numéro</th><th>Statut</th><th>Total</th><th>Date</th></tr>";
        foreach ($commandes as $cmd) {
            echo "<tr>";
            echo "<td>{$cmd['id']}</td>";
            echo "<td>{$cmd['numero_commande']}</td>";
            echo "<td>{$cmd['statut']}</td>";
            echo "<td>{$cmd['total']} €</td>";
            echo "<td>{$cmd['date_commande']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>