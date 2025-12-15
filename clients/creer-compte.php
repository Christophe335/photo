<?php 
session_start();
include '../includes/header.php'; 
?>

<!-- Styles spécifiques pour la création de compte -->
<style>
.create-account-container {
    max-width: 800px;
    margin: 80px auto;
    padding: 40px 20px;
}

.create-account-title {
    text-align: center;
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 40px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.create-account-form {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
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

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
}

.form-group.required label::after {
    content: " *";
    color: #e74c3c;
}

.checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 25px;
}

.checkbox-group input[type="checkbox"] {
    margin-top: 3px;
    width: auto;
}

.checkbox-group label {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.btn-container {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 30px;
}

.btn-primary {
    background: #28a745;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background: #1e7e34;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn-cancel:hover {
    background: #545b62;
}

.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    color: #007bff;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .create-account-title {
        font-size: 2rem;
    }
    
    .create-account-form {
        padding: 30px 20px;
    }
    
    .btn-container {
        flex-direction: column;
    }
}
</style>

<main>
    <div class="create-account-container">
        <h1 class="create-account-title">Créer mon compte</h1>
        
        <?php if (isset($_SESSION['register_errors'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['register_errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['register_errors']); ?>
        <?php endif; ?>
        
        <form class="create-account-form" action="process-register.php" method="POST" id="registerForm">
            <div class="form-row">
                <div class="form-group required">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required 
                           value="<?php echo isset($_SESSION['register_data']['prenom']) ? htmlspecialchars($_SESSION['register_data']['prenom']) : ''; ?>">
                </div>
                <div class="form-group required">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required 
                           value="<?php echo isset($_SESSION['register_data']['nom']) ? htmlspecialchars($_SESSION['register_data']['nom']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group required">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com" 
                       value="<?php echo isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group required">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group required">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                </div>
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="0123456789" 
                       value="<?php echo isset($_SESSION['register_data']['telephone']) ? htmlspecialchars($_SESSION['register_data']['telephone']) : ''; ?>">
            </div>
            
            <div class="form-group required">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" required>
            </div>
            
            <div class="form-row">
                <div class="form-group required">
                    <label for="code_postal">Code postal</label>
                    <input type="text" id="code_postal" name="code_postal" required maxlength="5">
                </div>
                <div class="form-group required">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="pays">Pays</label>
                <select id="pays" name="pays">
                    <option value="France" selected>France</option>
                    <option value="Belgique">Belgique</option>
                    <option value="Suisse">Suisse</option>
                    <option value="Luxembourg">Luxembourg</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="newsletter" name="newsletter" value="1">
                <label for="newsletter">
                    Je souhaite recevoir la newsletter et les offres promotionnelles par e-mail
                </label>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="cgv" name="cgv" required>
                <label for="cgv">
                    J'ai lu et j'accepte les <a href="../mentions.php" target="_blank">conditions générales de vente</a> 
                    et la <a href="../politique.php" target="_blank">politique de confidentialité</a> *
                </label>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn-primary">Créer mon compte</button>
                <a href="connexion.php" class="btn-cancel">Annuler</a>
            </div>
            
            <div class="login-link">
                <p>Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous ici</a></p>
            </div>
        </form>
        
        <?php unset($_SESSION['register_data']); ?>
    </div>
</main>

<script>
// Validation côté client
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const email = document.getElementById('email').value;
    const cgv = document.getElementById('cgv').checked;
    
    if (!cgv) {
        e.preventDefault();
        alert('Vous devez accepter les conditions générales de vente.');
        return false;
    }
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 6 caractères.');
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

// Validation en temps réel des mots de passe
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const passwordConfirm = this.value;
    
    if (passwordConfirm && password !== passwordConfirm) {
        this.style.borderColor = '#e74c3c';
    } else {
        this.style.borderColor = '#ddd';
    }
});
</script>

<?php include '../includes/footer.php'; ?>