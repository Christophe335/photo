<?php
echo "<h3>Diagnostic de la base de données clients</h3>";

try {
    echo "1. Test du chemin vers database.php<br>";
    $dbPath = '../../includes/database.php';
    echo "Chemin: $dbPath<br>";
    echo "Fichier existe: " . (file_exists($dbPath) ? 'OUI' : 'NON') . "<br><br>";
    
    if (file_exists($dbPath)) {
        echo "2. Inclusion du fichier<br>";
        require_once $dbPath;
        echo "Fichier inclus avec succès<br><br>";
        
        echo "3. Test de la classe Database<br>";
        if (class_exists('Database')) {
            echo "Classe Database trouvée<br>";
            
            echo "4. Test de connexion<br>";
            $db = Database::getInstance()->getConnection();
            echo "Connexion réussie<br><br>";
            
            echo "5. Test des tables existantes<br>";
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Tables trouvées: " . implode(', ', $tables) . "<br><br>";
            
            echo "6. Test de la table clients<br>";
            $stmt = $db->query("SHOW TABLES LIKE 'clients'");
            $clientsTableExists = $stmt->rowCount() > 0;
            echo "Table clients existe: " . ($clientsTableExists ? 'OUI' : 'NON') . "<br>";
            
            if ($clientsTableExists) {
                echo "<br>7. Structure de la table clients<br>";
                $stmt = $db->query("DESCRIBE clients");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<table border='1'>";
                echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                foreach ($columns as $col) {
                    echo "<tr>";
                    echo "<td>{$col['Field']}</td>";
                    echo "<td>{$col['Type']}</td>";
                    echo "<td>{$col['Null']}</td>";
                    echo "<td>{$col['Key']}</td>";
                    echo "<td>{$col['Default']}</td>";
                    echo "</tr>";
                }
                echo "</table><br>";
                
                echo "8. Contenu de la table clients<br>";
                $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
                $count = $stmt->fetch();
                echo "Nombre de clients: {$count['total']}<br>";
                
                if ($count['total'] > 0) {
                    echo "<br>9. Premiers clients:<br>";
                    $stmt = $db->query("SELECT * FROM clients ORDER BY id LIMIT 5");
                    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<table border='1'>";
                    if (!empty($clients)) {
                        echo "<tr>";
                        foreach (array_keys($clients[0]) as $key) {
                            echo "<th>$key</th>";
                        }
                        echo "</tr>";
                        
                        foreach ($clients as $client) {
                            echo "<tr>";
                            foreach ($client as $value) {
                                echo "<td>" . htmlspecialchars($value) . "</td>";
                            }
                            echo "</tr>";
                        }
                    }
                    echo "</table>";
                } else {
                    echo "<strong>La table clients est vide!</strong>";
                }
            } else {
                echo "<strong>La table clients n'existe pas!</strong>";
            }
            
        } else {
            echo "ERREUR: Classe Database non trouvée<br>";
        }
    } else {
        echo "ERREUR: Fichier database.php non trouvé<br>";
    }
    
} catch (Exception $e) {
    echo "<br><strong>ERREUR:</strong> " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
    echo "<br>Trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}
?>