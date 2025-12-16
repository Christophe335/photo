<?php 
session_start();
include '../includes/header.php'; 
?>

<!-- Styles spécifiques pour la page de connexion -->
<style>
.connexion-container {
    max-width: 1200px;
    margin: 1px auto;
    padding: 10px 20px;
}

.connexion-title {
    text-align: center;
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 50px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.connexion-content {
    display: grid;
    grid-template-columns: 260px 375px 375px 260px;
    gap: 10px;
    max-width: 1300px;
    margin: 0 auto;
}

.connexion-section {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 600;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #24256d;
}

.btn-primary {
    background: #24256d;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background: #1a1b4d;
}

.btn-secondary {
    background: #28a745;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-secondary:hover {
    background: #1e7e34;
}

.forgot-password {
    text-align: center;
    margin-top: 15px;
    margin-bottom: 25px;
}

.forgot-password a {
    color: #007bff;
    text-decoration: none;
    font-size: 0.9rem;
}

.forgot-password a:hover {
    text-decoration: underline;
}

.create-account-section {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.create-account-text {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .connexion-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .connexion-title {
        font-size: 2rem;
    }
    
    .connexion-section {
        padding: 30px 20px;
    }
}
</style>

<main>
    <div class="connexion-container">
        <h1 class="connexion-title">Connexion au site</h1>
        
        <?php if (isset($_SESSION['login_errors'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['login_errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['logout_message'])): ?>
            <div style="background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_SESSION['logout_message']); ?>
            </div>
            <?php unset($_SESSION['logout_message']); ?>
        <?php endif; ?>
        
        <div class="connexion-content">
            <!-- image gauche -->
            <div class="connexion-image">
                <img src="../images/logo-icon/se-connecter-01.svg" alt="Image de connexion" style="width:261px; height:500px;">
            </div>
            <!-- Section Connexion -->
            <div class="connexion-section">
                <h2 class="section-title">Mon compte</h2>
                
                <form action="process-login.php" method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" required placeholder="votre@email.com" 
                               value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>">
                        <?php unset($_SESSION['login_email']); ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
                    </div>
                    
                    <div class="forgot-password">
                        <a href="mot-de-passe-oublie.php">Mot de passe oublié ?</a>
                    </div>
                    
                    <button type="submit" class="btn-primary">Connexion</button>
                </form>
            </div>
            
            <!-- Section Création de compte -->
            <div class="connexion-section create-account-section">
                <h2 class="section-title">Nouveau client</h2>
                
                <p class="create-account-text">
                    Vous n'avez pas encore de compte ?<br>
                    Créez votre compte gratuitement pour accéder à vos commandes, 
                    suivre vos livraisons et bénéficier d'offres exclusives.
                </p>
                
                <a href="creer-compte.php" class="btn-secondary">Je crée mon compte</a>
            </div>
            <!-- image droite -->
            <div class="connexion-image">
                <img src="../images/logo-icon/se-connecter-02.svg" alt="Image de connexion" style="width:261px; height:500px;">
            </div>
        </div>
    </div>
</main>

<script>
// Validation côté client
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs requis.');
        return false;
    }
    
    if (!isValidEmail(email)) {
        e.preventDefault();
        alert('Veuillez entrer une adresse e-mail valide.');
        return false;
    }
});

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
</script>

<?php include '../includes/footer.php'; ?>