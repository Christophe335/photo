<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Produits</title>
    
    <!-- Polices et styles -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    
  
</head>
<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <div class="header-container">
            <h1><i class="fas fa-cogs"></i> Administration et Gestion</h1>
            <nav class="admin-nav">
                <a href="index.php"><i class="fas fa-boxes"></i> Produits</a>
                <a href="gestion-clients.php"><i class="fas fa-users"></i> Clients</a>
                <a href="gestion-mots-de-passe.php"><i class="fas fa-key"></i> Mots de passe clients</a>
                <a href="gestion-identifiants.php"><i class="fas fa-users-cog"></i> Identifiants admin</a>
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php 
            unset($_SESSION['message'], $_SESSION['message_type']); 
            endif; 
        ?>