<?php
require_once 'functions.php';

// Fonction pour charger les identifiants admin
function loadAdminCredentials() {
    $admin_credentials_file = __DIR__ . '/.admin_credentials.json';
    if (file_exists($admin_credentials_file)) {
        $content = file_get_contents($admin_credentials_file);
        return json_decode($content, true) ?: [];
    }
    // Retourner l'identifiant par défaut s'il n'existe pas
    return [
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'last_login' => null,
            'active' => true
        ]
    ];
}

// Fonction pour sauvegarder les identifiants
function saveAdminCredentials($credentials) {
    $admin_credentials_file = __DIR__ . '/.admin_credentials.json';
    return file_put_contents($admin_credentials_file, json_encode($credentials, JSON_PRETTY_PRINT));
}

// Traitement de la connexion
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Charger les identifiants depuis le fichier JSON
    $credentials = loadAdminCredentials();
    
    // Vérifier les identifiants
    if (isset($credentials[$username]) && $credentials[$username]['active']) {
        $user_data = $credentials[$username];
        
        // Vérifier le mot de passe (compatible avec l'ancien système)
        if (password_verify($password, $user_data['password_hash']) || $password === $user_data['password']) {
            // Mettre à jour la date de dernière connexion
            $credentials[$username]['last_login'] = date('Y-m-d H:i:s');
            saveAdminCredentials($credentials);
            
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $username;
            $_SESSION['message'] = 'Connexion réussie';
            $_SESSION['message_type'] = 'success';
            
            header('Location: index.php');
            exit;
        }
    }
    
    $error = 'Nom d\'utilisateur ou mot de passe incorrect';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Connexion</title>
    
    <!-- Polices et styles -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/connexion.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-lock"></i> Administration</h1>
            <p>Gestion des produits de reliure</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Informations de connexion pour le développement -->
        <div class="credentials-info">
            <strong>Identifiants par défaut :</strong><br>
            Utilisateur : <code>admin</code><br>
            Mot de passe : <code>admin123</code><br>
            <small><em>Vous pouvez modifier ces identifiants dans la gestion des identifiants après connexion.</em></small>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Nom d'utilisateur
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Entrez votre nom d'utilisateur">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key"></i> Mot de passe
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Entrez votre mot de passe">
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>

        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au site
        </a>
    </div>

    <script>
        // Focus automatique sur le premier champ
        document.getElementById('username').focus();
        
        // Gestion du formulaire avec Enter
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const activeElement = document.activeElement;
                if (activeElement.id === 'username') {
                    document.getElementById('password').focus();
                } else if (activeElement.id === 'password') {
                    document.querySelector('form').submit();
                }
            }
        });
    </script>
</body>
</html>