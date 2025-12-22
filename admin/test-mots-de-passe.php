<?php
require_once 'functions.php';
require_once '../includes/database.php';

// Vérifier l'authentification admin
checkAuth();

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Test de gestion des mots de passe clients</h2>";
    
    // Récupérer quelques clients pour le test
    $stmt = $db->prepare("
        SELECT id, email, prenom, nom, mot_de_passe,
               CASE 
                   WHEN LENGTH(mot_de_passe) = 60 AND SUBSTRING(mot_de_passe, 1, 7) = '$2y$10$' THEN 'Hashé (bcrypt)'
                   WHEN LENGTH(mot_de_passe) > 20 THEN 'Hashé (autre)'
                   ELSE 'Texte clair'
               END as type_password
        FROM clients 
        ORDER BY id DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($clients)) {
        echo "<p>Aucun client trouvé.</p>";
    } else {
        echo "<h3>Derniers clients :</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Type mot de passe</th><th>Hash (tronqué)</th><th>Action</th></tr>";
        
        foreach ($clients as $client) {
            $hash_tronque = strlen($client['mot_de_passe']) > 20 
                ? substr($client['mot_de_passe'], 0, 20) . '...' 
                : $client['mot_de_passe'];
            
            echo "<tr>";
            echo "<td>{$client['id']}</td>";
            echo "<td>" . htmlspecialchars($client['prenom'] . ' ' . $client['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($client['email']) . "</td>";
            echo "<td>{$client['type_password']}</td>";
            echo "<td style='font-family: monospace;'>" . htmlspecialchars($hash_tronque) . "</td>";
            echo "<td>";
            echo "<a href='client-details.php?id={$client['id']}' class='btn btn-info'>Voir détails</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test de hashage
    echo "<h3>Test de hashage :</h3>";
    $test_password = "motdepasse123";
    $hash = password_hash($test_password, PASSWORD_DEFAULT);
    $verify = password_verify($test_password, $hash);
    
    echo "<p><strong>Mot de passe test :</strong> $test_password</p>";
    echo "<p><strong>Hash généré :</strong> <code>$hash</code></p>";
    echo "<p><strong>Vérification :</strong> " . ($verify ? "✓ Succès" : "✗ Échec") . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>

<style>
.btn {
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 3px;
    color: white;
    background: #007bff;
    border: none;
    cursor: pointer;
}
.btn-info {
    background: #17a2b8;
}
table {
    width: 100%;
}
th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}
th {
    background: #f8f9fa;
}
</style>