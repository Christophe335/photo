<?php
require_once 'includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connexion OK\n";
    
    // Vérifier les familles existantes
    $stmt = $db->query('SELECT DISTINCT famille, nomDeLaFamille, COUNT(*) as nb FROM produits GROUP BY famille, nomDeLaFamille');
    $familles = $stmt->fetchAll();
    
    echo "Familles trouvées:\n";
    foreach($familles as $f) {
        echo "- {$f['famille']} ({$f['nomDeLaFamille']}) : {$f['nb']} produits\n";
    }
    
    // Test avec RELI
    echo "\nTest RELI:\n";
    $stmt = $db->prepare('SELECT * FROM produits WHERE famille = ?');
    $stmt->execute(['RELI']);
    $produits = $stmt->fetchAll();
    echo "Produits RELI trouvés: " . count($produits) . "\n";
    
    if (count($produits) > 0) {
        echo "Premier produit RELI:\n";
        print_r($produits[0]);
    }
    
    // Vérifier la table complète
    echo "\nTous les produits:\n";
    $stmt = $db->query('SELECT famille, reference, designation FROM produits ORDER BY famille, reference');
    $tous = $stmt->fetchAll();
    foreach($tous as $p) {
        echo "- {$p['famille']} | {$p['reference']} | {$p['designation']}\n";
    }
    
} catch(Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>