<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

$client_id = $_GET['id'] ?? 0;

if (!$client_id) {
    header('Location: gestion-clients.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $code_postal = trim($_POST['code_postal'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $pays = trim($_POST['pays'] ?? '');
        $adresse_livraison_differente = isset($_POST['adresse_livraison_differente']) ? 1 : 0;
        $adresse_livraison = trim($_POST['adresse_livraison'] ?? '');
        $code_postal_livraison = trim($_POST['code_postal_livraison'] ?? '');
        $ville_livraison = trim($_POST['ville_livraison'] ?? '');
        $pays_livraison = trim($_POST['pays_livraison'] ?? '');
        $actif = isset($_POST['actif']) ? 1 : 0;
        
        // Validation
        if (empty($prenom) || empty($nom) || empty($email)) {
            $error = "Les champs prénom, nom et email sont obligatoires.";
        } else {
            // Vérifier si l'email existe déjà pour un autre client
            $stmt = $db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
            $stmt->execute([$email, $client_id]);
            if ($stmt->fetch()) {
                $error = "Cette adresse email est déjà utilisée par un autre client.";
            } else {
                // Mettre à jour le client
                $stmt = $db->prepare("
                    UPDATE clients SET 
                        prenom = ?, nom = ?, email = ?, telephone = ?, 
                        adresse = ?, code_postal = ?, ville = ?, pays = ?, 
                        adresse_livraison_differente = ?, adresse_livraison = ?, 
                        code_postal_livraison = ?, ville_livraison = ?, pays_livraison = ?, actif = ?
                    WHERE id = ?
                ");
                
                if ($stmt->execute([$prenom, $nom, $email, $telephone, $adresse, $code_postal, $ville, $pays, 
                                  $adresse_livraison_differente, $adresse_livraison, $code_postal_livraison, 
                                  $ville_livraison, $pays_livraison, $actif, $client_id])) {
                    $_SESSION['message'] = "Client modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    header('Location: client-details.php?id=' . $client_id);
                    exit;
                } else {
                    $error = "Erreur lors de la modification du client.";
                }
            }
        }
    }
    
    // Récupérer les infos du client
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Location: gestion-clients.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Erreur client edit: " . $e->getMessage());
    $error = "Erreur lors du chargement des données.";
}

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/client.css">
</head>
<div class="toolbar">
    <a href="client-details.php?id=<?php echo $client['id']; ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour aux détails
    </a>
    <h2><i class="fas fa-user-edit"></i> Modifier le client</h2>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="content-card">
    <form method="POST" class="client-form">
        <div class="form-section">
            <h3>Informations personnelles</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required 
                           value="<?php echo htmlspecialchars($client['prenom']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required 
                           value="<?php echo htmlspecialchars($client['nom']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Adresse e-mail *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($client['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" 
                       value="<?php echo htmlspecialchars($client['telephone']); ?>">
            </div>
        </div>
        
        <div class="form-section">
            <h3>Adresse</h3>
            
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($client['adresse']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="code_postal">Code postal</label>
                    <input type="text" id="code_postal" name="code_postal" 
                           value="<?php echo htmlspecialchars($client['code_postal']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" 
                           value="<?php echo htmlspecialchars($client['ville']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="pays">Pays</label>
                <select id="pays" name="pays">
                    <option value="France" <?php echo $client['pays'] === 'France' ? 'selected' : ''; ?>>France</option>
                    <option value="Belgique" <?php echo $client['pays'] === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                    <option value="Suisse" <?php echo $client['pays'] === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                    <option value="Luxembourg" <?php echo $client['pays'] === 'Luxembourg' ? 'selected' : ''; ?>>Luxembourg</option>
                </select>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Adresse de livraison</h3>
            
            <div class="checkbox-group">
                <input type="checkbox" id="adresse_livraison_differente" name="adresse_livraison_differente" 
                       <?php echo $client['adresse_livraison_differente'] ? 'checked' : ''; ?>
                       onchange="toggleAdresseLivraison()">
                <label for="adresse_livraison_differente">Adresse de livraison différente de l'adresse principale</label>
            </div>
            
            <div id="adresse-livraison-fields" class="livraison-fields" style="<?php echo $client['adresse_livraison_differente'] ? '' : 'display: none;'; ?>">
                <div class="form-group">
                    <label for="adresse_livraison">Adresse de livraison</label>
                    <textarea id="adresse_livraison" name="adresse_livraison" rows="3"><?php echo htmlspecialchars($client['adresse_livraison'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal_livraison">Code postal</label>
                        <input type="text" id="code_postal_livraison" name="code_postal_livraison" 
                               value="<?php echo htmlspecialchars($client['code_postal_livraison'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="ville_livraison">Ville</label>
                        <input type="text" id="ville_livraison" name="ville_livraison" 
                               value="<?php echo htmlspecialchars($client['ville_livraison'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="pays_livraison">Pays</label>
                    <select id="pays_livraison" name="pays_livraison">
                        <option value="France" <?php echo ($client['pays_livraison'] ?? 'France') === 'France' ? 'selected' : ''; ?>>France</option>
                        <option value="Belgique" <?php echo ($client['pays_livraison'] ?? '') === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                        <option value="Suisse" <?php echo ($client['pays_livraison'] ?? '') === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                        <option value="Luxembourg" <?php echo ($client['pays_livraison'] ?? '') === 'Luxembourg' ? 'selected' : ''; ?>>Luxembourg</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Statut du compte</h3>
            
            <div class="checkbox-group">
                <input type="checkbox" id="actif" name="actif" <?php echo $client['actif'] ? 'checked' : ''; ?>>
                <label for="actif">Compte actif</label>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="client-details.php?id=<?php echo $client['id']; ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>

<script>
function toggleAdresseLivraison() {
    const checkbox = document.getElementById('adresse_livraison_differente');
    const fields = document.getElementById('adresse-livraison-fields');
    
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        // Vider les champs si on décoche
        document.getElementById('adresse_livraison').value = '';
        document.getElementById('code_postal_livraison').value = '';
        document.getElementById('ville_livraison').value = '';
        document.getElementById('pays_livraison').value = 'France';
    }
}
</script>

<style>
.livraison-fields {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.checkbox-group input[type="checkbox"] {
    margin: 0;
}
</style>

<?php //include 'footer.php'; ?>