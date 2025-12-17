<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

$error = '';
$success = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $devis_id = $_GET['id'];
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Vérifier que le devis existe et qu'il est en brouillon
        $stmt = $db->prepare("SELECT statut FROM devis WHERE id = ?");
        $stmt->execute([$devis_id]);
        $devis = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$devis) {
            $error = "Devis non trouvé.";
        } elseif ($devis['statut'] !== 'brouillon') {
            $error = "Seuls les devis en brouillon peuvent être supprimés.";
        } else {
            // Commencer une transaction
            $db->beginTransaction();
            
            try {
                // Supprimer les articles du devis
                $stmt = $db->prepare("DELETE FROM devis_items WHERE devis_id = ?");
                $stmt->execute([$devis_id]);
                
                // Supprimer le devis
                $stmt = $db->prepare("DELETE FROM devis WHERE id = ?");
                $stmt->execute([$devis_id]);
                
                // Valider la transaction
                $db->commit();
                
                $success = "Devis supprimé avec succès.";
                
                // Redirection après 2 secondes
                header("refresh:2;url=gestion-devis.php");
                
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $db->rollback();
                throw $e;
            }
        }
    } catch (Exception $e) {
        error_log("Erreur suppression devis: " . $e->getMessage());
        $error = "Erreur lors de la suppression du devis.";
    }
} else {
    $error = "ID de devis invalide.";
}

include 'header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-trash"></i> Suppression de Devis</h2>
    <div>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<div class="content-card">
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        
        <div class="text-center">
            <a href="gestion-devis.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour à la liste des devis
            </a>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success); ?>
            <br><small>Redirection automatique dans 2 secondes...</small>
        </div>
        
        <div class="text-center">
            <a href="gestion-devis.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour immédiat à la liste
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.text-center {
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    opacity: 0.8;
}
</style>

<?php include 'footer_simple.php'; ?>