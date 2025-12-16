<?php
require_once 'functions.php';
require_once '../includes/database.php';

// Vérifier l'authentification admin
checkAuth();

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Test de suppression de commande</h2>";
    
    // Vérifier les tables existantes
    echo "<h3>Tables liées aux commandes :</h3>";
    $tables = ['commandes', 'commande_items', 'commande_historique'];
    
    foreach ($tables as $table) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $table");
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>$table : {$count['total']} enregistrements</p>";
    }
    
    // Afficher les dernières commandes
    echo "<h3>Dernières commandes :</h3>";
    $stmt = $db->prepare("
        SELECT c.id, c.numero_commande, c.statut, c.total, c.date_commande,
               COUNT(ci.id) as nb_items
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        GROUP BY c.id
        ORDER BY c.date_commande DESC
        LIMIT 5
    ");
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($commandes)) {
        echo "<p>Aucune commande trouvée.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>N° Commande</th><th>Statut</th><th>Total</th><th>Items</th><th>Date</th><th>Action</th></tr>";
        
        foreach ($commandes as $cmd) {
            echo "<tr>";
            echo "<td>{$cmd['id']}</td>";
            echo "<td>{$cmd['numero_commande']}</td>";
            echo "<td>{$cmd['statut']}</td>";
            echo "<td>" . number_format($cmd['total'], 2) . " €</td>";
            echo "<td>{$cmd['nb_items']}</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($cmd['date_commande'])) . "</td>";
            echo "<td>";
            echo "<button onclick='testerSuppression({$cmd['id']})' style='color: red;'>Test suppression</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>

<script>
function testerSuppression(commandeId) {
    if (confirm('Voulez-vous vraiment tester la suppression de cette commande ?')) {
        fetch('supprimer-commande.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                commande_id: commandeId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Commande supprimée avec succès !');
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}
</script>