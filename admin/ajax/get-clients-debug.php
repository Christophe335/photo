<?php
// Version debug de get-clients.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ne pas envoyer le header JSON pour voir les erreurs
// header('Content-Type: application/json');

echo "Debug get-clients<br>";

try {
    echo "1. Test inclusion database.php<br>";
    require_once '../../includes/database.php';
    echo "2. OK - Database inclus<br>";
    
    echo "3. Test getInstance<br>";
    $db = Database::getInstance()->getConnection();
    echo "4. OK - Connexion obtenue<br>";
    
    echo "5. Test requête simple<br>";
    $stmt = $db->query("SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = 'photo'");
    $result = $stmt->fetch();
    echo "6. OK - Nombre de tables dans la base 'photo': " . $result['total'] . "<br>";
    
    echo "7. Test existence table clients<br>";
    $stmt = $db->query("SHOW TABLES LIKE 'clients'");
    $exists = $stmt->rowCount() > 0;
    echo "8. Table clients " . ($exists ? 'existe' : 'n\'existe pas') . "<br>";
    
    if (!$exists) {
        echo "9. Création de la table clients<br>";
        $db->exec("
            CREATE TABLE clients (
                id int(11) NOT NULL AUTO_INCREMENT,
                nom varchar(100) NOT NULL,
                prenom varchar(100) NOT NULL,
                email varchar(150) NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        echo "10. Table créée<br>";
        
        $stmt = $db->prepare("INSERT INTO clients (nom, prenom, email) VALUES (?, ?, ?)");
        $stmt->execute(['Test', 'Client', 'test@example.com']);
        echo "11. Client de test ajouté<br>";
    }
    
    echo "12. Requête clients<br>";
    $stmt = $db->query("SELECT id, nom, prenom, email FROM clients ORDER BY nom LIMIT 5");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "13. OK - " . count($clients) . " clients trouvés<br>";
    
    echo "14. JSON encode<br>";
    $json = json_encode($clients);
    echo "15. OK - JSON: " . $json . "<br>";
    
} catch (Exception $e) {
    echo "<br><strong>ERREUR:</strong> " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
    echo "Trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
?>