<?php
session_start();
require_once '../../includes/database.php';

// Script de débogage pour comprendre le problème avec get-modifier-form.php

echo "<h1>Debug - Formulaire Modification</h1>";

// Vérifier la session
echo "<h2>1. État de la session:</h2>";
echo "<ul>";
echo "<li>Session ID: " . session_id() . "</li>";
echo "<li>Client ID: " . ($_SESSION['client_id'] ?? 'NON DÉFINI') . "</li>";
echo "<li>Session complète: " . print_r($_SESSION, true) . "</li>";
echo "</ul>";

if (!isset($_SESSION['client_id'])) {
    echo "<p style='color: red;'><strong>PROBLÈME:</strong> Aucun client_id dans la session !</p>";
    echo "<p>Vous devez être connecté pour accéder à ce formulaire.</p>";
    exit;
}

// Vérifier la base de données
echo "<h2>2. Vérification base de données:</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Connexion à la base de données OK</p>";
    
    // Récupérer le client
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        echo "<p style='color: green;'>✓ Client trouvé: " . htmlspecialchars($client['prenom']) . " " . htmlspecialchars($client['nom']) . "</p>";
        echo "<details>";
        echo "<summary>Voir toutes les données du client</summary>";
        echo "<pre>" . print_r($client, true) . "</pre>";
        echo "</details>";
    } else {
        echo "<p style='color: red;'>✗ Aucun client trouvé avec l'ID: " . $_SESSION['client_id'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur base de données: " . $e->getMessage() . "</p>";
}

// Tester get-modifier-form.php directement
echo "<h2>3. Test de get-modifier-form.php:</h2>";
echo "<button onclick='loadForm()'>Charger le formulaire</button>";
echo "<div id='form-result' style='border: 1px solid #ccc; margin-top: 10px; padding: 10px;'></div>";

// Tester update-profile.php avec des données simples
echo "<h2>4. Test direct update-profile.php:</h2>";
echo "<form method='POST' action='update-profile.php' style='border: 1px solid #ccc; padding: 15px; margin-top: 10px;'>";
if (isset($client)) {
    echo "<input type='text' name='prenom' value='" . htmlspecialchars($client['prenom']) . "_TEST' placeholder='Prénom'><br><br>";
    echo "<input type='text' name='nom' value='" . htmlspecialchars($client['nom']) . "' placeholder='Nom'><br><br>";
    echo "<input type='email' name='email' value='" . htmlspecialchars($client['email']) . "' placeholder='Email'><br><br>";
    echo "<input type='text' name='adresse' value='" . htmlspecialchars($client['adresse']) . "' placeholder='Adresse'><br><br>";
    echo "<input type='text' name='code_postal' value='" . htmlspecialchars($client['code_postal']) . "' placeholder='Code postal'><br><br>";
    echo "<input type='text' name='ville' value='" . htmlspecialchars($client['ville']) . "' placeholder='Ville'><br><br>";
    echo "<input type='hidden' name='pays' value='France'>";
    echo "<input type='hidden' name='newsletter' value='0'>";
}
echo "<button type='submit'>Tester la mise à jour (POST direct)</button>";
echo "</form>";

?>

<script>
function loadForm() {
    const resultDiv = document.getElementById('form-result');
    resultDiv.innerHTML = 'Chargement...';
    
    fetch('get-modifier-form.php')
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(html => {
            console.log('HTML reçu:', html.length, 'caractères');
            resultDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur:', error);
            resultDiv.innerHTML = '<p style="color: red;">Erreur: ' + error + '</p>';
        });
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #333; }
h2 { color: #666; border-bottom: 1px solid #ccc; }
ul { background: #f5f5f5; padding: 10px; }
pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
details { margin: 10px 0; }
form input { padding: 8px; margin: 5px; width: 300px; }
button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
</style>