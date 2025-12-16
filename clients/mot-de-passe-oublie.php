<?php 
session_start();
include '../includes/header.php'; 
?>

<head>
    <link rel="stylesheet" href="../css/client.css">
</head>
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