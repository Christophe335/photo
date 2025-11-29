<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

// Traitement de la connexion
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Authentification simple (à améliorer avec une base de données)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['message'] = 'Connexion réussie';
        $_SESSION['message_type'] = 'success';
        
        header('Location: index.php');
        exit;
    } else {
        $error = 'Nom d\'utilisateur ou mot de passe incorrect';
    }
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
    
    <style>
        :root {
            --primary-dark: #2A256D;
            --primary-orange: #F05124;
            --background-light: #f8f9fa;
            --border-color: #dee2e6;
            --text-dark: #333;
            --text-muted: #6c757d;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1a1654 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary-dark);
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--text-muted);
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px rgba(42, 37, 109, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px 20px;
            background: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 20px;
        }
        
        .btn-login:hover {
            background: #1f1b5a;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .back-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        
        .back-link:hover {
            color: var(--primary-orange);
        }
        
        .credentials-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 12px;
            text-align: left;
        }
        
        .credentials-info strong {
            color: var(--primary-dark);
        }
    </style>
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
            <strong>Identifiants de test :</strong><br>
            Utilisateur : <code>admin</code><br>
            Mot de passe : <code>admin123</code>
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