<?php
session_start();
require_once '../includes/database.php';

// Simuler une session client pour le test
if (!isset($_SESSION['client_id'])) {
    // Récupérer le premier client pour le test
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id FROM clients LIMIT 1");
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        $_SESSION['client_id'] = $client['id'];
        echo "Session client simulée avec ID: " . $client['id'] . "<br>";
    } else {
        die("Aucun client trouvé pour le test");
    }
}

echo "<h2>Test de mise à jour du profil client</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les informations actuelles du client
    $stmt = $db->prepare("SELECT prenom, nom, email FROM clients WHERE id = ?");
    $stmt->execute([$_SESSION['client_id']]);
    $client_actuel = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Informations actuelles :</h3>";
    echo "Prénom : " . $client_actuel['prenom'] . "<br>";
    echo "Nom : " . $client_actuel['nom'] . "<br>";
    echo "Email : " . $client_actuel['email'] . "<br><br>";
    
    // Test de mise à jour du prénom
    $nouveau_prenom = "TestPrenom" . time();
    echo "<h3>Test de mise à jour du prénom vers : $nouveau_prenom</h3>";
    
    // Simuler la mise à jour comme dans update-profile.php
    $_POST = [
        'prenom' => $nouveau_prenom,
        'nom' => $client_actuel['nom'],
        'email' => $client_actuel['email'],
        'telephone' => '0123456789',
        'adresse' => '123 rue Test',
        'code_postal' => '75001',
        'ville' => 'Paris',
        'pays' => 'France',
        'newsletter' => '1'
    ];
    
    // Récupération et validation des données (comme dans le script original)
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    
    // Adresse de facturation
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $pays = $_POST['pays'] ?? 'France';
    
    // Adresse de livraison
    $adresse_livraison_differente = isset($_POST['adresse_livraison_differente']) ? 1 : 0;
    $adresse_livraison = $adresse_livraison_differente ? trim($_POST['adresse_livraison'] ?? '') : null;
    $code_postal_livraison = $adresse_livraison_differente ? trim($_POST['code_postal_livraison'] ?? '') : null;
    $ville_livraison = $adresse_livraison_differente ? trim($_POST['ville_livraison'] ?? '') : null;
    $pays_livraison = $adresse_livraison_differente ? ($_POST['pays_livraison'] ?? 'France') : null;
    
    // Préférences
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    echo "Données à mettre à jour :<br>";
    echo "- Prénom: '$prenom'<br>";
    echo "- Nom: '$nom'<br>";
    echo "- Email: '$email'<br><br>";
    
    // Validation
    $errors = [];
    if (empty($prenom)) $errors[] = "Le prénom est requis.";
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($email)) $errors[] = "L'adresse e-mail est requise.";
    
    if (!empty($errors)) {
        echo "<div style='color: red;'>Erreurs de validation :<br>" . implode('<br>', $errors) . "</div>";
        exit;
    }
    
    // Commencer une transaction
    $db->beginTransaction();
    
    // Préparer la requête de mise à jour (comme dans le script original)
    $sql_parts = [];
    $params = [];
    
    $sql_parts[] = "prenom = ?";
    $params[] = $prenom;
    
    $sql_parts[] = "nom = ?";
    $params[] = $nom;
    
    $sql_parts[] = "email = ?";
    $params[] = $email;
    
    $sql_parts[] = "telephone = ?";
    $params[] = $telephone;
    
    $sql_parts[] = "adresse = ?";
    $params[] = $adresse;
    
    $sql_parts[] = "code_postal = ?";
    $params[] = $code_postal;
    
    $sql_parts[] = "ville = ?";
    $params[] = $ville;
    
    $sql_parts[] = "pays = ?";
    $params[] = $pays;
    
    $sql_parts[] = "adresse_livraison_differente = ?";
    $params[] = $adresse_livraison_differente;
    
    $sql_parts[] = "adresse_livraison = ?";
    $params[] = $adresse_livraison;
    
    $sql_parts[] = "code_postal_livraison = ?";
    $params[] = $code_postal_livraison;
    
    $sql_parts[] = "ville_livraison = ?";
    $params[] = $ville_livraison;
    
    $sql_parts[] = "pays_livraison = ?";
    $params[] = $pays_livraison;
    
    $sql_parts[] = "newsletter = ?";
    $params[] = $newsletter;
    
    $params[] = $_SESSION['client_id'];
    
    $sql = "UPDATE clients SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    
    echo "<h3>Requête SQL générée :</h3>";
    echo "<code>$sql</code><br><br>";
    
    echo "<h3>Paramètres :</h3>";
    echo "<pre>" . print_r($params, true) . "</pre>";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "<div style='color: green;'>✓ Mise à jour réussie</div><br>";
        
        // Valider la transaction
        $db->commit();
        
        // Vérifier le résultat
        $stmt = $db->prepare("SELECT prenom, nom, email FROM clients WHERE id = ?");
        $stmt->execute([$_SESSION['client_id']]);
        $client_updated = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Informations après mise à jour :</h3>";
        echo "Prénom : " . $client_updated['prenom'] . "<br>";
        echo "Nom : " . $client_updated['nom'] . "<br>";
        echo "Email : " . $client_updated['email'] . "<br>";
        
        if ($client_updated['prenom'] === $nouveau_prenom) {
            echo "<div style='color: green;'>✓ Le prénom a été correctement mis à jour !</div>";
        } else {
            echo "<div style='color: red;'>✗ Le prénom n'a pas été mis à jour correctement</div>";
        }
        
    } else {
        echo "<div style='color: red;'>✗ Erreur lors de la mise à jour</div>";
        $db->rollBack();
        
        // Afficher l'erreur SQL
        $errorInfo = $stmt->errorInfo();
        echo "<pre>Erreur SQL: " . print_r($errorInfo, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Exception : " . $e->getMessage() . "</div>";
    if (isset($db)) {
        $db->rollBack();
    }
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
code {
    background: #f8f9fa;
    padding: 2px 5px;
    border-radius: 3px;
}
pre {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
}
</style>