<?php 
session_start();
include '../includes/header.php'; 
?>

<head>
    <link rel="stylesheet" href="../css/client.css">
</head>

<main class="account-background">
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
            
            <h3 class="address-section-title">Adresse de Facturation</h3>
            
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
                <label for="pays">Zone Géographique</label>
                <select id="pays" name="pays">
                    <option value="France" selected>France</option>
                    <option value="Corse">Corse</option>
                    <option value="Dom">DOM / TOM</option>
                    <option value="Belgique">Belgique</option>
                    <option value="Suisse">Suisse</option>
                    <option value="Luxembourg">Luxembourg</option>
                    <option value="eu">Union Européenne</option>
                    <option value="heu">HORS Union Européenne</option>
                </select>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="adresse_livraison_differente" name="adresse_livraison_differente" value="1">
                <label for="adresse_livraison_differente">
                    Adresse de livraison (si différente)
                </label>
            </div>
            
            <div id="adresse_livraison_fields" class="address-section" style="display: none;">
                <h3 class="address-section-title">Adresse de Livraison</h3>
                
                <div class="form-group">
                    <label for="adresse_livraison">Adresse</label>
                    <input type="text" id="adresse_livraison" name="adresse_livraison">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal_livraison">Code postal</label>
                        <input type="text" id="code_postal_livraison" name="code_postal_livraison" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="ville_livraison">Ville</label>
                        <input type="text" id="ville_livraison" name="ville_livraison">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="pays">Zone Géographique</label>
                    <select id="pays" name="pays">
                        <option value="France" selected>France</option>
                        <option value="Corse">Corse</option>
                        <option value="Dom">DOM / TOM</option>
                        <option value="Belgique">Belgique</option>
                        <option value="Suisse">Suisse</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="eu">Union Européenne</option>
                        <option value="heu">HORS Union Européenne</option>
                    </select>
                </div>
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

// Gestion de l'affichage de l'adresse de livraison
document.getElementById('adresse_livraison_differente').addEventListener('change', function() {
    const adresseLivraisonFields = document.getElementById('adresse_livraison_fields');
    const inputs = adresseLivraisonFields.querySelectorAll('input, select');
    
    if (this.checked) {
        adresseLivraisonFields.style.display = 'block';
        // Rendre les champs optionnellement requis
        inputs.forEach(input => {
            if (['adresse_livraison', 'code_postal_livraison', 'ville_livraison'].includes(input.id)) {
                input.required = true;
            }
        });
    } else {
        adresseLivraisonFields.style.display = 'none';
        // Enlever la validation requise et vider les champs
        inputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>