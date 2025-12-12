<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

echo "<h1>Installation des colonnes pour articles composés</h1>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p>✅ Connexion à la base de données OK</p>";
    
    // Vérifier si les colonnes existent déjà
    $checkEstCompose = $db->query("SHOW COLUMNS FROM produits LIKE 'est_compose'");
    $checkCompositionAuto = $db->query("SHOW COLUMNS FROM produits LIKE 'composition_auto'");
    
    $hasEstCompose = $checkEstCompose->rowCount() > 0;
    $hasCompositionAuto = $checkCompositionAuto->rowCount() > 0;
    
    echo "<h2>État des colonnes :</h2>";
    echo "<p>est_compose : " . ($hasEstCompose ? "✅ Existe déjà" : "❌ Manquante") . "</p>";
    echo "<p>composition_auto : " . ($hasCompositionAuto ? "✅ Existe déjà" : "❌ Manquante") . "</p>";
    
    $modificationsFaites = false;
    
    // Ajouter la colonne est_compose si elle n'existe pas
    if (!$hasEstCompose) {
        echo "<p>🔧 Ajout de la colonne 'est_compose'...</p>";
        $db->exec("ALTER TABLE produits ADD COLUMN est_compose BOOLEAN DEFAULT FALSE");
        echo "<p>✅ Colonne 'est_compose' ajoutée</p>";
        $modificationsFaites = true;
    }
    
    // Ajouter la colonne composition_auto si elle n'existe pas
    if (!$hasCompositionAuto) {
        echo "<p>🔧 Ajout de la colonne 'composition_auto'...</p>";
        $db->exec("ALTER TABLE produits ADD COLUMN composition_auto BOOLEAN DEFAULT TRUE");
        echo "<p>✅ Colonne 'composition_auto' ajoutée</p>";
        $modificationsFaites = true;
    }
    
    // Vérifier si la table produit_compositions existe
    $checkTable = $db->query("SHOW TABLES LIKE 'produit_compositions'");
    $hasCompositionsTable = $checkTable->rowCount() > 0;
    
    echo "<p>Table produit_compositions : " . ($hasCompositionsTable ? "✅ Existe déjà" : "❌ Manquante") . "</p>";
    
    // Créer la table produit_compositions si elle n'existe pas
    if (!$hasCompositionsTable) {
        echo "<p>🔧 Création de la table 'produit_compositions'...</p>";
        
        $createTableSql = "
        CREATE TABLE produit_compositions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            produit_parent_id INT NOT NULL,
            produit_enfant_id INT NOT NULL,
            quantite INT DEFAULT 1,
            ordre_affichage INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (produit_parent_id) REFERENCES produits(id) ON DELETE CASCADE,
            FOREIGN KEY (produit_enfant_id) REFERENCES produits(id) ON DELETE CASCADE,
            
            UNIQUE KEY unique_composition (produit_parent_id, produit_enfant_id)
        )";
        
        $db->exec($createTableSql);
        echo "<p>✅ Table 'produit_compositions' créée</p>";
        
        // Créer les index
        $db->exec("CREATE INDEX idx_parent ON produit_compositions(produit_parent_id)");
        $db->exec("CREATE INDEX idx_enfant ON produit_compositions(produit_enfant_id)");
        echo "<p>✅ Index créés</p>";
        
        $modificationsFaites = true;
    }
    
    if ($modificationsFaites) {
        echo "<h2 style='color: green;'>🎉 Installation terminée avec succès !</h2>";
        echo "<p><strong>Vous pouvez maintenant utiliser les articles composés !</strong></p>";
    } else {
        echo "<h2 style='color: blue;'>ℹ️ Tout est déjà en place !</h2>";
        echo "<p><strong>Les articles composés sont prêts à être utilisés.</strong></p>";
    }
    
    // Tester la recherche
    echo "<h2>Test de la recherche :</h2>";
    $stmt = $db->query("SELECT COUNT(*) as total FROM produits");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Nombre total d'articles : {$total['total']}</p>";
    
    if ($total['total'] > 0) {
        echo "<p>✅ Vous avez des articles en base, la recherche devrait fonctionner !</p>";
        
        // Afficher quelques exemples
        $stmt = $db->query("SELECT reference, designation FROM produits LIMIT 3");
        $exemples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p><strong>Exemples d'articles que vous pouvez rechercher :</strong></p>";
        echo "<ul>";
        foreach ($exemples as $article) {
            echo "<li>{$article['reference']} - {$article['designation']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Vous n'avez aucun article en base. Ajoutez d'abord quelques articles avant de tester les articles composés.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
h1, h2 { color: #333; }
p { margin: 8px 0; }
</style>

<p><a href="ajouter.php" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Retour à l'ajout d'article</a></p>