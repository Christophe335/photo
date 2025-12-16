<?php
session_start();
require_once '../includes/database.php';

// Vérifier si un token a été fourni
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error_message'] = "Lien de réinitialisation invalide.";
    header('Location: connexion.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Vérifier la validité du token
    $stmt = $db->prepare("
        SELECT id, prenom, nom, email 
        FROM clients 
        WHERE token_reset = ? 
        AND token_reset_expiration > NOW() 
        AND actif = 1
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['error_message'] = "Lien de réinitialisation expiré ou invalide.";
        header('Location: connexion.php');
        exit;
    }
    
    // Traitement du formulaire de réinitialisation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'] ?? '';
        $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
        
        $errors = [];
        
        if (empty($nouveau_mot_de_passe)) {
            $errors[] = "Le nouveau mot de passe est requis.";
        } elseif (strlen($nouveau_mot_de_passe) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
        
        if ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        
        if (empty($errors)) {
            // Mettre à jour le mot de passe
            $password_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("
                UPDATE clients 
                SET mot_de_passe = ?, 
                    mot_de_passe_clair = ?,
                    token_reset = NULL, 
                    token_reset_expiration = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$password_hash, $nouveau_mot_de_passe, $user['id']]);
            
            $_SESSION['success_message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            header('Location: connexion.php');
            exit;
        } else {
            $_SESSION['reset_errors'] = $errors;
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur réinitialisation: " . $e->getMessage());
    $_SESSION['error_message'] = "Une erreur technique s'est produite.";
    header('Location: connexion.php');
    exit;
}

include '../includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h2><i class="fas fa-key"></i> Nouveau mot de passe</h2>
            <p>Bonjour <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>, veuillez choisir votre nouveau mot de passe.</p>
            
            <?php if (!empty($_SESSION['reset_errors'])): ?>
                <div class="alert alert-danger">
                    <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['reset_errors']); ?>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe :</label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" 
                           class="form-control" required minlength="6">
                    <small class="form-text">Minimum 6 caractères</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmer_mot_de_passe">Confirmer le mot de passe :</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" 
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="show-passwords"> Afficher les mots de passe
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Réinitialiser le mot de passe
                </button>
            </form>
            
            <div class="auth-links">
                <a href="connexion.php">Retour à la connexion</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('show-passwords').addEventListener('change', function() {
    const nouveauField = document.getElementById('nouveau_mot_de_passe');
    const confirmerField = document.getElementById('confirmer_mot_de_passe');
    
    if (this.checked) {
        nouveauField.type = 'text';
        confirmerField.type = 'text';
    } else {
        nouveauField.type = 'password';
        confirmerField.type = 'password';
    }
});
</script>

<?php include '../includes/footer.php'; ?>