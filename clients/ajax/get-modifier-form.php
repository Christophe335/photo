<?php
session_start();
require_once '../../includes/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    http_response_code(401);
    echo '<div class="alert alert-error">Vous devez être connecté pour accéder à cette page.</div>';
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les informations du client
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        echo '<div class="alert alert-error">Erreur: client non trouvé (ID: ' . $_SESSION['client_id'] . ')</div>';
        exit;
    }
    
} catch (Exception $e) {
    error_log("Erreur get-modifier-form: " . $e->getMessage());
    echo '<div class="alert alert-error">Erreur lors du chargement du formulaire: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}
?>

<form id="modifier-form" class="modifier-form">
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
                   value="<?php echo htmlspecialchars($client['telephone'] ?: ''); ?>">
        </div>
    </div>
    
    <div class="form-section">
        <h3>Adresse de facturation</h3>
        <div class="form-group">
            <label for="adresse">Adresse *</label>
            <textarea id="adresse" name="adresse" required rows="2"><?php echo htmlspecialchars($client['adresse']); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="code_postal">Code postal *</label>
                <input type="text" id="code_postal" name="code_postal" required maxlength="10"
                       value="<?php echo htmlspecialchars($client['code_postal']); ?>">
            </div>
            <div class="form-group">
                <label for="ville">Ville *</label>
                <input type="text" id="ville" name="ville" required 
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
                <option value="Autre" <?php echo !in_array($client['pays'], ['France', 'Belgique', 'Suisse', 'Luxembourg']) ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>
    </div>
    
    <div class="form-section">
        <h3>Adresse de livraison</h3>
        <div class="checkbox-group">
            <input type="checkbox" id="adresse_livraison_differente" name="adresse_livraison_differente" 
                   <?php echo $client['adresse_livraison_differente'] ? 'checked' : ''; ?>>
            <label for="adresse_livraison_differente">Adresse de livraison différente de la facturation</label>
        </div>
        
        <div id="adresse_livraison_fields" class="livraison-fields" 
             style="display: <?php echo $client['adresse_livraison_differente'] ? 'block' : 'none'; ?>;">
            
            <div class="form-group">
                <label for="adresse_livraison">Adresse de livraison</label>
                <textarea id="adresse_livraison" name="adresse_livraison" rows="2"><?php echo htmlspecialchars($client['adresse_livraison'] ?: ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="code_postal_livraison">Code postal</label>
                    <input type="text" id="code_postal_livraison" name="code_postal_livraison" maxlength="10"
                           value="<?php echo htmlspecialchars($client['code_postal_livraison'] ?: ''); ?>">
                </div>
                <div class="form-group">
                    <label for="ville_livraison">Ville</label>
                    <input type="text" id="ville_livraison" name="ville_livraison" 
                           value="<?php echo htmlspecialchars($client['ville_livraison'] ?: ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="pays_livraison">Pays</label>
                <select id="pays_livraison" name="pays_livraison">
                    <option value="France" <?php echo $client['pays_livraison'] === 'France' ? 'selected' : ''; ?>>France</option>
                    <option value="Belgique" <?php echo $client['pays_livraison'] === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                    <option value="Suisse" <?php echo $client['pays_livraison'] === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                    <option value="Luxembourg" <?php echo $client['pays_livraison'] === 'Luxembourg' ? 'selected' : ''; ?>>Luxembourg</option>
                    <option value="Autre" <?php echo !in_array($client['pays_livraison'], ['France', 'Belgique', 'Suisse', 'Luxembourg']) ? 'selected' : ''; ?>>Autre</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <h3>Modifier le mot de passe</h3>
        <p style="color: #666; font-size: 0.9rem;">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>
        
        <div class="form-group">
            <label for="mot_de_passe_actuel">Mot de passe actuel</label>
            <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
                <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" minlength="6">
            </div>
            <div class="form-group">
                <label for="confirmer_mot_de_passe">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" minlength="6">
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <h3>Préférences</h3>
        <div class="checkbox-group">
            <input type="checkbox" id="newsletter" name="newsletter" 
                   <?php echo $client['newsletter'] ? 'checked' : ''; ?>>
            <label for="newsletter">Recevoir la newsletter et les offres promotionnelles</label>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary" id="btn-save">
            <span id="btn-text">Enregistrer les modifications</span>
            <span id="btn-loading" style="display: none;">Enregistrement...</span>
        </button>
        <button type="button" class="btn-cancel" onclick="resetForm()">Annuler</button>
    </div>
</form>

<div id="modification-result"></div>

<style>
.modifier-form {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 1.2rem;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    color: #555;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
}

.checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 20px;
}

.checkbox-group input[type="checkbox"] {
    margin-top: 3px;
    width: auto;
}

.checkbox-group label {
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.4;
}

.livraison-fields {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-primary,
.btn-cancel {
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #0056b3;
}

.btn-primary:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #545b62;
}

#modification-result {
    margin-top: 20px;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .modifier-form {
        padding: 20px;
    }
}
</style>

<script>
// Gestion de l'affichage de l'adresse de livraison
document.getElementById('adresse_livraison_differente').addEventListener('change', function() {
    const fields = document.getElementById('adresse_livraison_fields');
    const inputs = fields.querySelectorAll('input, textarea, select');
    
    if (this.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        // Vider les champs si masqués
        inputs.forEach(input => {
            input.value = '';
        });
    }
});

// Validation du formulaire
document.getElementById('modifier-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnSave = document.getElementById('btn-save');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    
    // Validation des mots de passe
    const motDePasseActuel = document.getElementById('mot_de_passe_actuel').value;
    const nouveauMotDePasse = document.getElementById('nouveau_mot_de_passe').value;
    const confirmerMotDePasse = document.getElementById('confirmer_mot_de_passe').value;
    
    if (nouveauMotDePasse || confirmerMotDePasse) {
        if (!motDePasseActuel) {
            showAlert('Veuillez saisir votre mot de passe actuel.', 'error');
            return;
        }
        
        if (nouveauMotDePasse !== confirmerMotDePasse) {
            showAlert('Les nouveaux mots de passe ne correspondent pas.', 'error');
            return;
        }
        
        if (nouveauMotDePasse.length < 6) {
            showAlert('Le nouveau mot de passe doit contenir au moins 6 caractères.', 'error');
            return;
        }
    }
    
    // Validation de l'adresse de livraison si cochée
    const adresseLivraisonDifferente = document.getElementById('adresse_livraison_differente').checked;
    if (adresseLivraisonDifferente) {
        const adresseLivraison = document.getElementById('adresse_livraison').value.trim();
        const codePostalLivraison = document.getElementById('code_postal_livraison').value.trim();
        const villeLivraison = document.getElementById('ville_livraison').value.trim();
        
        if (!adresseLivraison || !codePostalLivraison || !villeLivraison) {
            showAlert('Veuillez remplir tous les champs obligatoires de l\'adresse de livraison.', 'error');
            return;
        }
    }
    
    // Désactiver le bouton et afficher le loading
    btnSave.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    // Envoyer les données
    const formData = new FormData(this);
    
    fetch('ajax/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            // Mettre à jour les informations de session si nécessaire
            if (data.update_session) {
                // Recharger la page pour mettre à jour les données affichées
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Erreur lors de la sauvegarde. Veuillez réessayer.', 'error');
        console.error('Erreur:', error);
    })
    .finally(() => {
        // Réactiver le bouton
        btnSave.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    });
});

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir annuler vos modifications ?')) {
        document.getElementById('modifier-form').reset();
        document.getElementById('adresse_livraison_fields').style.display = 
            document.getElementById('adresse_livraison_differente').checked ? 'block' : 'none';
        document.getElementById('modification-result').innerHTML = '';
    }
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    document.getElementById('modification-result').innerHTML = 
        '<div class="alert ' + alertClass + '">' + message + '</div>';
    
    // Faire défiler vers le message
    document.getElementById('modification-result').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}
</script>