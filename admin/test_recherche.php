<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

echo "<h1>Test de la recherche d'articles</h1>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p>✅ Connexion à la base de données OK</p>";
    
    // Vérifier la structure de la table
    echo "<h2>Structure de la table produits :</h2>";
    $stmt = $db->query("DESCRIBE produits");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Compter les produits
    $stmt = $db->query("SELECT COUNT(*) as total FROM produits");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Total produits : {$total['total']}</h2>";
    
    // Afficher quelques exemples
    if ($total['total'] > 0) {
        echo "<h2>Exemples de produits :</h2>";
        $stmt = $db->query("SELECT id, reference, designation, prixVente FROM produits LIMIT 5");
        $exemples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Référence</th><th>Désignation</th><th>Prix</th></tr>";
        foreach ($exemples as $produit) {
            echo "<tr>";
            echo "<td>{$produit['id']}</td>";
            echo "<td>{$produit['reference']}</td>";
            echo "<td>{$produit['designation']}</td>";
            echo "<td>{$produit['prixVente']} €</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Aucun produit en base ! Vous devez d'abord ajouter des produits.</p>";
    }
    
    // Test de recherche
    echo "<h2>Test de recherche AJAX :</h2>";
    echo "<input type='text' id='test-search' placeholder='Tapez pour tester la recherche...'>";
    echo "<div id='test-results'></div>";
    
} catch (Exception $e) {
    echo "<p>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>

<script>
document.getElementById('test-search').addEventListener('input', async function() {
    const terme = this.value.trim();
    const resultsDiv = document.getElementById('test-results');
    
    if (terme.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    try {
        const response = await fetch('recherche_articles.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `terme=${encodeURIComponent(terme)}`
        });
        
        const articles = await response.json();
        
        if (articles.length > 0) {
            let html = '<h3>Résultats :</h3><ul>';
            articles.forEach(article => {
                html += `<li>${article.reference} - ${article.designation} (${article.prixVente}€)</li>`;
            });
            html += '</ul>';
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = '<p>Aucun résultat trouvé</p>';
        }
    } catch (error) {
        console.error('Erreur:', error);
        resultsDiv.innerHTML = '<p style="color: red;">Erreur lors de la recherche</p>';
    }
});
</script>

<style>
table { border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
#test-search { width: 300px; padding: 8px; margin: 10px 0; }
#test-results { margin: 10px 0; padding: 10px; border: 1px solid #ccc; min-height: 50px; }
</style>