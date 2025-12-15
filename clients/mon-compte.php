<?php 
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    $_SESSION['redirect_after_login'] = 'mon-compte.php';
    header('Location: connexion.php');
    exit;
}

include '../includes/header.php'; 
?>

<!-- Styles spécifiques pour la page mon compte -->
<style>
.account-container {
    max-width: 1200px;
    margin: 80px auto;
    padding: 40px 20px;
}

.account-title {
    text-align: center;
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 40px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.welcome-message {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 30px;
    text-align: center;
}

.account-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 40px;
}

.account-sidebar {
    background: #f8f9fa;
    padding: 30px 20px;
    border-radius: 10px;
    height: fit-content;
}

.sidebar-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin-bottom: 10px;
}

.sidebar-menu a {
    display: block;
    padding: 12px 15px;
    color: #555;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #007bff;
    color: white;
}

.account-main {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.info-group {
    margin-bottom: 20px;
}

.info-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
}

.info-value {
    color: #333;
    font-size: 1.1rem;
}

.btn-edit {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-edit:hover {
    background: #0056b3;
}

.btn-logout {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-logout:hover {
    background: #c82333;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Responsive */
@media (max-width: 768px) {
    .account-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .account-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main>
    <div class="account-container">
        <h1 class="account-title">Mon compte</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="welcome-message">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="account-content">
            <!-- Sidebar -->
            <div class="account-sidebar">
                <h3 class="sidebar-title">Navigation</h3>
                <ul class="sidebar-menu">
                    <li><a href="#" class="active">Informations personnelles</a></li>
                    <li><a href="#">Mes commandes</a></li>
                    <li><a href="#">Mes adresses</a></li>
                    <li><a href="#">Mes favoris</a></li>
                    <li><a href="#">Paramètres</a></li>
                    <li><a href="logout.php" class="btn-logout">Déconnexion</a></li>
                </ul>
            </div>
            
            <!-- Contenu principal -->
            <div class="account-main">
                <h2 class="section-title">Bonjour <?php echo htmlspecialchars($_SESSION['client_prenom'] . ' ' . $_SESSION['client_nom']); ?> !</h2>
                
                <div class="info-grid">
                    <div>
                        <div class="info-group">
                            <div class="info-label">Nom complet</div>
                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['client_prenom'] . ' ' . $_SESSION['client_nom']); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Adresse e-mail</div>
                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['client_email']); ?></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="info-group">
                            <div class="info-label">Statut du compte</div>
                            <div class="info-value" style="color: #28a745;">✓ Compte actif</div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Membre depuis</div>
                            <div class="info-value"><?php echo date('F Y'); ?></div>
                        </div>
                    </div>
                </div>
                
                <a href="#" class="btn-edit">Modifier mes informations</a>
                
                <!-- Statistiques du compte -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">0</div>
                        <div class="stat-label">Commandes</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">0</div>
                        <div class="stat-label">Favoris</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">0€</div>
                        <div class="stat-label">Total dépensé</div>
                    </div>
                </div>
                
                <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <h3>Prochaines fonctionnalités</h3>
                    <ul>
                        <li>Suivi de vos commandes en temps réel</li>
                        <li>Historique complet de vos achats</li>
                        <li>Gestion de vos adresses de livraison</li>
                        <li>Liste de favoris personnalisée</li>
                        <li>Offres exclusives membres</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>