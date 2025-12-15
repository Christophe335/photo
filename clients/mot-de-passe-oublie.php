<?php 
session_start();
include '../includes/header.php'; 
?>

<!-- Styles spécifiques pour la page mot de passe oublié -->
<style>
.forgot-password-container {
    max-width: 600px;
    margin: 80px auto;
    padding: 40px 20px;
}

.forgot-password-title {
    text-align: center;
    font-size: 2.2rem;
    color: #333;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.forgot-password-form {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.instruction-text {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
    font-size: 1.1rem;
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
    border-color: #007bff;
}

.btn-container {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 30px;
}

.btn-primary {
    background: #007bff;
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
    background: #0056b3;
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

.back-link {
    text-align: center;
    margin-top: 20px;
}

.back-link a {
    color: #007bff;
    text-decoration: none;
}

.back-link a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .forgot-password-title {
        font-size: 1.8rem;
    }
    
    .forgot-password-form {
        padding: 30px 20px;
    }
    
    .btn-container {
        flex-direction: column;
    }
}
</style>

<main>
    <div class="forgot-password-container">
        <h1 class="forgot-password-title">Mot de passe oublié</h1>
        
        <?php if (isset($_SESSION['forgot_errors'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['forgot_errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['forgot_errors']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <form class="forgot-password-form" action="process-forgot-password.php" method="POST" id="forgotPasswordForm">
            <p class="instruction-text">
                Saisissez votre adresse e-mail ci-dessous. Nous vous enverrons un lien 
                pour réinitialiser votre mot de passe dans les minutes qui suivent.
            </p>
            
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com" 
                       value="<?php echo isset($_SESSION['forgot_email']) ? htmlspecialchars($_SESSION['forgot_email']) : ''; ?>">
                <?php unset($_SESSION['forgot_email']); ?>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn-primary">Envoyer le lien</button>
                <a href="connexion.php" class="btn-cancel">Retour</a>
            </div>
            
            <div class="back-link">
                <p><a href="connexion.php">← Retour à la connexion</a></p>
            </div>
        </form>
    </div>
</main>

<script>
// Validation côté client
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    
    if (!email) {
        e.preventDefault();
        alert('Veuillez entrer votre adresse e-mail.');
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