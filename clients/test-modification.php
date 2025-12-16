<?php
session_start();

// Test de la modification d'informations
if (!isset($_SESSION['client_id'])) {
    echo "Vous devez être connecté pour tester cette fonctionnalité.";
    exit;
}

echo "<h1>Test de modification des informations</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Données POST reçues :</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test d'appel à update-profile.php
    echo "<p>Test d'appel à update-profile.php...</p>";
} else {
?>

<form method="POST">
    <h2>Formulaire de test</h2>
    
    <div>
        <label>Prénom :</label>
        <input type="text" name="prenom" value="TestPrenom" required>
    </div>
    
    <div>
        <label>Nom :</label>
        <input type="text" name="nom" value="TestNom" required>
    </div>
    
    <div>
        <label>Email :</label>
        <input type="email" name="email" value="test@example.com" required>
    </div>
    
    <div>
        <label>Téléphone :</label>
        <input type="tel" name="telephone" value="0123456789">
    </div>
    
    <button type="submit">Tester la modification</button>
</form>

<hr>

<h2>Test AJAX</h2>
<button onclick="testAjax()">Tester l'appel AJAX</button>
<div id="result"></div>

<script>
function testAjax() {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = 'Test en cours...';
    
    const formData = new FormData();
    formData.append('prenom', 'TestPrenomAjax');
    formData.append('nom', 'TestNomAjax');
    formData.append('email', 'testajax@example.com');
    formData.append('telephone', '0987654321');
    
    fetch('ajax/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.text();
    })
    .then(data => {
        console.log('Réponse:', data);
        resultDiv.innerHTML = '<h3>Réponse:</h3><pre>' + data + '</pre>';
    })
    .catch(error => {
        console.error('Erreur:', error);
        resultDiv.innerHTML = '<h3>Erreur:</h3>' + error.message;
    });
}
</script>

<?php } ?>