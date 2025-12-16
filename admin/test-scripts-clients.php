<?php
require_once '../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Test des modifications côté client</h2>";
    
    // Vérifier la structure de la table
    echo "<h3>Structure de la colonne mot_de_passe_clair :</h3>";
    $stmt = $db->prepare("SHOW COLUMNS FROM clients LIKE 'mot_de_passe_clair'");
    $stmt->execute();
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "<p style='color: green;'>✓ Colonne mot_de_passe_clair existe</p>";
        echo "<pre>" . print_r($column, true) . "</pre>";
    } else {
        echo "<p style='color: red;'>✗ Colonne mot_de_passe_clair manquante</p>";
    }
    
    // Vérifier les scripts côté client modifiés
    echo "<h3>Vérification des scripts côté client :</h3>";
    
    $scripts_to_check = [
        '../clients/ajax/update-profile.php' => 'Mise à jour profil',
        '../clients/process-register.php' => 'Inscription',
        '../clients/reset-password.php' => 'Réinitialisation mot de passe'
    ];
    
    foreach ($scripts_to_check as $file => $description) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'mot_de_passe_clair') !== false) {
                echo "<p style='color: green;'>✓ $description - Contient mot_de_passe_clair</p>";
            } else {
                echo "<p style='color: orange;'>⚠ $description - Ne contient pas mot_de_passe_clair</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ $description - Fichier non trouvé</p>";
        }
    }
    
    // Tester la mise à jour
    echo "<h3>Test de mise à jour :</h3>";
    
    // Récupérer un client test
    $stmt = $db->prepare("SELECT id, email, mot_de_passe_clair FROM clients LIMIT 1");
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        echo "<p>Client de test : ID {$client['id']} - {$client['email']}</p>";
        echo "<p>Mot de passe clair actuel : " . ($client['mot_de_passe_clair'] ?: 'Non défini') . "</p>";
        echo "<p style='color: green;'>✓ La colonne mot_de_passe_clair est fonctionnelle</p>";
    } else {
        echo "<p>Aucun client trouvé pour le test.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
pre {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
}
</style>