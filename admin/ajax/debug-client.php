<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Test de connexion base de données<br>";

try {
    echo "1. Tentative de chargement de database.php...<br>";
    require_once '../../includes/database.php';
    echo "2. Fichier chargé avec succès<br>";
    
    echo "3. Tentative de connexion à la base...<br>";
    $db = Database::getInstance()->getConnection();
    echo "4. Connexion établie avec succès<br>";
    
    echo "5. Test de requête simple...<br>";
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $result = $stmt->fetch();
    echo "6. Nombre de clients: " . $result['total'] . "<br>";
    
    if (isset($_GET['id'])) {
        $client_id = $_GET['id'];
        echo "7. Recherche du client ID: $client_id<br>";
        
        $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($client) {
            echo "8. Client trouvé:<br>";
            echo "<pre>" . print_r($client, true) . "</pre>";
        } else {
            echo "8. Aucun client trouvé avec cet ID<br>";
        }
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
}
?>