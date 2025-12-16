<?php
session_start();

// Test basique pour voir si la session est bien configurée
echo "<h2>Test de debug pour le formulaire de modification</h2>";

echo "<p><strong>Session active:</strong> " . (session_id() ? 'Oui (' . session_id() . ')' : 'Non') . "</p>";
echo "<p><strong>Client ID:</strong> " . (isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 'Non défini') . "</p>";

if (isset($_SESSION['client_id'])) {
    echo "<p><strong>Informations session:</strong></p>";
    echo "<ul>";
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'client_') === 0) {
            echo "<li>$key: " . htmlspecialchars($value) . "</li>";
        }
    }
    echo "</ul>";
    
    // Test de la base de données
    try {
        require_once '../includes/database.php';
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT id, prenom, nom, email FROM clients WHERE id = ?");
        $stmt->execute([$_SESSION['client_id']]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($client) {
            echo "<p><strong>Données client trouvées:</strong></p>";
            echo "<ul>";
            foreach ($client as $key => $value) {
                echo "<li>$key: " . htmlspecialchars($value) . "</li>";
            }
            echo "</ul>";
            
            echo '<p><a href="ajax/get-modifier-form.php" target="_blank">Tester le formulaire AJAX directement</a></p>';
        } else {
            echo "<p style='color: red;'>❌ Aucun client trouvé avec l'ID " . $_SESSION['client_id'] . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur base de données: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Aucune session client active</p>";
    echo '<p><a href="connexion.php">Se connecter</a></p>';
}

echo '<hr>';
echo '<p><a href="mon-compte.php">Retour au compte</a></p>';
?>