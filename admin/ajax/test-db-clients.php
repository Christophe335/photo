<?php
// Test simple pour vérifier les clients
require_once '../../includes/database.php';

echo "<h3>Test de la base de données clients</h3>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Compter les clients
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $result = $stmt->fetch();
    echo "<p>Nombre total de clients dans la base : " . $result['total'] . "</p>";
    
    if ($result['total'] == 0) {
        echo "<p style='color: orange;'>Aucun client trouvé ! Création d'un client de test...</p>";
        
        // Créer un client de test
        $stmt = $db->prepare("
            INSERT INTO clients (nom, prenom, email, telephone, entreprise, adresse, ville, code_postal) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Dupont',
            'Jean',
            'jean.dupont@email.com',
            '0123456789',
            'Entreprise Test',
            '123 rue de la Paix',
            'Paris',
            '75001'
        ]);
        
        echo "<p style='color: green;'>Client de test créé avec succès !</p>";
    }
    
    // Lister les 5 premiers clients
    $stmt = $db->query("SELECT id, nom, prenom, email, entreprise FROM clients ORDER BY id LIMIT 5");
    $clients = $stmt->fetchAll();
    
    echo "<h4>Premiers clients :</h4>";
    foreach ($clients as $client) {
        echo "<p>ID: {$client['id']} - {$client['nom']} {$client['prenom']} ({$client['email']}) - {$client['entreprise']}</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>