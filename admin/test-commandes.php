<?php
echo "<h1>Test client-commandes.php</h1>";
echo "<p>Client ID: " . ($_GET['id'] ?? 'non défini') . "</p>";
echo "<p>Le fichier fonctionne !</p>";

// Test de la connexion à la base de données
try {
    require_once '../includes/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p>✅ Connexion à la base de données OK</p>";
    
    $client_id = $_GET['id'] ?? 0;
    if ($client_id) {
        $stmt = $db->prepare("SELECT prenom, nom FROM clients WHERE id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($client) {
            echo "<p>✅ Client trouvé: " . htmlspecialchars($client['prenom'] . ' ' . $client['nom']) . "</p>";
        } else {
            echo "<p>❌ Client non trouvé avec ID: $client_id</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Erreur base de données: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Afficher le contenu complet de $_GET
echo "<h2>Paramètres reçus:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";
?>