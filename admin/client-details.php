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
    
    // Récupérer les infos du client
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        header('Location: gestion-clients.php');
        exit;
    }
    
    // Récupérer les commandes du client
    $stmt = $db->prepare("
        SELECT c.*, 
               COUNT(ci.id) as nb_articles,
               SUM(ci.quantite * ci.prix_unitaire) as total_ht,
               GROUP_CONCAT(CONCAT(ci.quantite, 'x ', ci.designation) SEPARATOR '<br>') as produits
        FROM commandes c
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        WHERE c.client_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$client_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques du client
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as nb_commandes,
            COALESCE(SUM(total), 0) as total_depense,
            AVG(total) as panier_moyen,
            COUNT(CASE WHEN statut IN ('confirmee', 'en_preparation', 'en_cours', 'expediee') THEN 1 END) as commandes_en_cours
        FROM commandes 
        WHERE client_id = ?
    ");
    $stmt->execute([$client_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur client details: " . $e->getMessage());
    header('Location: gestion-clients.php');
    exit;
}

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<div class="toolbar">
    <a href="gestion-clients.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
    <h2><i class="fas fa-user"></i> <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h2>
    <div style="flex: 1;"></div>
    <a href="client-edit.php?id=<?php echo $client['id']; ?>" class="btn btn-warning">
        <i class="fas fa-edit"></i> Modifier
    </a>
</div>

<div class="client-layout">
    <!-- Informations client -->
    <div class="client-info-card">
        <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Nom complet</label>
                <span><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></span>
            </div>
            
            <div class="info-item">
                <label>Email</label>
                <span><a href="mailto:<?php echo htmlspecialchars($client['email']); ?>"><?php echo htmlspecialchars($client['email']); ?></a></span>
            </div>
            
            <div class="info-item">
                <label>Téléphone</label>
                <span>
                    <?php if ($client['telephone']): ?>
                        <a href="tel:<?php echo htmlspecialchars($client['telephone']); ?>"><?php echo htmlspecialchars($client['telephone']); ?></a>
                    <?php else: ?>
                        <span class="text-muted">Non renseigné</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>Statut</label>
                <span>
                    <span class="badge <?php echo $client['actif'] ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $client['actif'] ? 'Actif' : 'Inactif'; ?>
                    </span>
                </span>
            </div>
            
            <div class="info-item">
                <label>Inscription</label>
                <span><?php echo date('d/m/Y à H:i', strtotime($client['date_creation'])); ?></span>
            </div>
            
            <div class="info-item">
                <label>Dernière connexion</label>
                <span>
                    <?php if ($client['derniere_connexion']): ?>
                        <?php echo date('d/m/Y à H:i', strtotime($client['derniere_connexion'])); ?>
                    <?php else: ?>
                        <span class="text-muted">Jamais connecté</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="info-item">
                <label>Mot de passe</label>
                <span>
                    <div class="password-section">
                        <input type="<?php echo $client['mot_de_passe_clair'] ? 'text' : 'password'; ?>" id="client-password" value="<?php echo $client['mot_de_passe_clair'] ? htmlspecialchars($client['mot_de_passe_clair']) : 'Aucun mot de passe défini'; ?>" readonly class="password-display">
                        <?php if ($client['mot_de_passe_clair']): ?>
                            <button type="button" id="toggle-password" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye-slash" id="eye-icon"></i>
                            </button>
                        <?php else: ?>
                            <span class="text-muted">Pas de mot de passe défini</span>
                        <?php endif; ?>
                        <button type="button" id="change-password" class="btn btn-sm btn-warning">
                            <i class="fas fa-key"></i> Modifier
                        </button>
                        <button type="button" id="generate-password" class="btn btn-sm btn-info">
                            <i class="fas fa-random"></i> Générer
                        </button>
                        <button type="button" id="copy-password" class="btn btn-sm btn-success">
                            <i class="fas fa-copy"></i> Copier
                        </button>
                    </div>
                </span>
            </div>
        </div>
        
        <div class="address-container">
            <?php if ($client['adresse']): ?>
            <div class="address-section">
                <h4><i class="fas fa-map-marker-alt"></i> Adresse de facturation</h4>
                <div class="address-info">
                    <?php echo nl2br(htmlspecialchars($client['adresse'])); ?><br>
                    <?php echo htmlspecialchars($client['code_postal'] . ' ' . $client['ville']); ?><br>
                    <?php echo htmlspecialchars($client['pays'] ?: 'France'); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($client['adresse_livraison_differente'] && $client['adresse_livraison']): ?>
            <div class="address-section">
                <h4><i class="fas fa-shipping-fast"></i> Adresse de livraison</h4>
                <div class="address-info">
                    <?php echo nl2br(htmlspecialchars($client['adresse_livraison'])); ?><br>
                    <?php echo htmlspecialchars($client['code_postal_livraison'] . ' ' . $client['ville_livraison']); ?><br>
                    <?php echo htmlspecialchars($client['pays_livraison'] ?: 'France'); ?>
                </div>
            </div>
            <?php elseif ($client['adresse']): ?>
            <div class="address-section">
                <h4><i class="fas fa-shipping-fast"></i> Adresse de livraison</h4>
                <div class="address-info same-as-billing">
                    <span class="text-muted"><i class="fas fa-link"></i> Identique à l'adresse de facturation</span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-card">
        <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['nb_commandes']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo number_format($stats['total_depense'] ?? 0, 0, ',', ' '); ?> €</div>
                <div class="stat-label">Total dépensé</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo number_format($stats['panier_moyen'] ?? 0, 0, ',', ' '); ?> €</div>
                <div class="stat-label">Panier moyen</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['commandes_en_cours']; ?></div>
                <div class="stat-label">En cours</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal changement mot de passe -->
<div id="password-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4><i class="fas fa-key"></i> Changer le mot de passe de <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h4>
            <span class="close" id="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="password-form">
                <div class="form-group">
                    <label for="new-password">Nouveau mot de passe :</label>
                    <div class="input-group">
                        <input type="text" id="new-password" name="new_password" class="form-control" required minlength="6">
                        <button type="button" id="generate-random" class="btn btn-outline-info">Générer aléatoire</button>
                    </div>
                    <small class="text-muted">Minimum 6 caractères - Ce mot de passe sera visible pour transmission au client</small>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmer le mot de passe :</label>
                    <input type="text" id="confirm-password" name="confirm_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Le mot de passe sera hashé en base mais restera visible dans cette interface pour transmission au client.
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancel-password">Annuler</button>
            <button type="button" class="btn btn-primary" id="save-password">Changer le mot de passe</button>
        </div>
    </div>
</div>

<!-- Commandes du client -->
<div class="commandes-card">
    <h3><i class="fas fa-shopping-cart"></i> Commandes (<?php echo count($commandes); ?>)</h3>
    
    <?php if (empty($commandes)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart fa-2x"></i>
            <p>Aucune commande pour ce client</p>
        </div>
    <?php else: ?>
        <div class="commandes-list">
            <?php foreach ($commandes as $commande): ?>
                <div class="commande-item">
                    <div class="commande-header">
                        <div class="commande-info">
                            <strong>Commande #<?php echo htmlspecialchars($commande['numero_commande']); ?></strong>
                            <span class="commande-date"><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></span>
                        </div>
                        
                        <div class="commande-status">
                            <?php
                            $statuts = [
                                'en_attente' => ['En attente', 'badge-warning'],
                                'confirmee' => ['Confirmée', 'badge-info'],
                                'en_preparation' => ['En préparation', 'badge-primary'],
                                'en_cours' => ['En cours', 'badge-primary'],
                                'expediee' => ['Expédiée', 'badge-secondary'],
                                'livree' => ['Livrée', 'badge-success'],
                                'annulee' => ['Annulée', 'badge-danger']
                            ];
                            $statut_info = $statuts[$commande['statut']] ?? [$commande['statut'], 'badge-light'];
                            ?>
                            <span class="badge <?php echo $statut_info[1]; ?>"><?php echo $statut_info[0]; ?></span>
                        </div>
                        
                        <div class="commande-total">
                            <?php
                            // Calcul des frais de port selon la logique du panier
                            $totalHT = $commande['total_ht'] ? $commande['total_ht'] : 0;
                            $fraisPort = ($totalHT > 200) ? 0 : 13.95;
                            $fraisAffiches = $commande['frais_livraison'] !== null ? $commande['frais_livraison'] : $fraisPort;
                            ?>
                            <div class="total-detail">
                                <div style="font-size: 0.85em; color: #666;">
                                    HT: <?php echo number_format($totalHT, 2, ',', ' '); ?> € + Port: 
                                    <?php echo $fraisAffiches > 0 ? number_format($fraisAffiches, 2, ',', ' ') . ' €' : 'Gratuit'; ?>
                                </div>
                                <strong><?php echo number_format($commande['total'], 2, ',', ' '); ?> € TTC</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="commande-details">
                        <div class="commande-produits">
                            <strong><?php echo $commande['nb_articles']; ?> article<?php echo $commande['nb_articles'] > 1 ? 's' : ''; ?></strong>
                            <div class="produits-list"><?php echo $commande['produits']; ?></div>
                        </div>
                        
                        <div class="commande-actions">
                            <!-- Changement de statut -->
                            <select class="statut-select" data-commande-id="<?php echo $commande['id']; ?>">
                                <?php foreach ($statuts as $statut_key => $statut_data): ?>
                                    <option value="<?php echo $statut_key; ?>" 
                                            <?php echo $commande['statut'] === $statut_key ? 'selected' : ''; ?>>
                                        <?php echo $statut_data[0]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <a href="detail-commande.php?numero=<?php echo urlencode($commande['numero_commande']); ?>" 
                               class="btn btn-sm btn-info" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($commande['statut'] === 'livree'): ?>
                                <a href="generer-facture.php?commande=<?php echo $commande['id']; ?>" 
                                   class="btn btn-sm btn-success" title="Générer facture">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-sm btn-danger" 
                                    onclick="confirmerSuppressionCommande(<?php echo $commande['id']; ?>, '<?php echo htmlspecialchars($commande['numero_commande']); ?>')"
                                    title="Supprimer commande">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>



<script>
// Gestion du changement de statut
document.querySelectorAll('.statut-select').forEach(select => {
    select.addEventListener('change', function() {
        const commandeId = this.dataset.commandeId;
        const nouveauStatut = this.value;
        
        if (confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
            // Envoyer la requête AJAX
            fetch('update-statut-commande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    commande_id: commandeId,
                    statut: nouveauStatut
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour: ' + data.message);
                    // Remettre l'ancienne valeur
                    this.value = this.dataset.oldValue;
                }
            })
            .catch(error => {
                alert('Erreur lors de la mise à jour');
                this.value = this.dataset.oldValue;
            });
        } else {
            // Annuler le changement
            this.value = this.dataset.oldValue;
        }
    });
    
    // Stocker la valeur initiale
    select.dataset.oldValue = select.value;
});

// Gestion du mot de passe
const toggleButton = document.getElementById('toggle-password');
if (toggleButton) {
    toggleButton.addEventListener('click', function() {
        const passwordField = document.getElementById('client-password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.className = 'fas fa-eye-slash';
        } else {
            passwordField.type = 'password';
            eyeIcon.className = 'fas fa-eye';
        }
    });
}

document.getElementById('change-password').addEventListener('click', function() {
    document.getElementById('password-modal').style.display = 'block';
    document.getElementById('new-password').focus();
});

document.getElementById('close-modal').addEventListener('click', function() {
    document.getElementById('password-modal').style.display = 'none';
    document.getElementById('password-form').reset();
});

document.getElementById('cancel-password').addEventListener('click', function() {
    document.getElementById('password-modal').style.display = 'none';
    document.getElementById('password-form').reset();
});

// Génération de mot de passe aléatoire
function genererMotDePasseAleatoire() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    let result = '';
    for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

document.getElementById('generate-random').addEventListener('click', function() {
    const password = genererMotDePasseAleatoire();
    document.getElementById('new-password').value = password;
    document.getElementById('confirm-password').value = password;
});

document.getElementById('generate-password').addEventListener('click', function() {
    const password = genererMotDePasseAleatoire();
    if (confirm('Générer et appliquer le mot de passe: ' + password + ' ?')) {
        changerMotDePasse(<?php echo $client['id']; ?>, password);
    }
});

document.getElementById('copy-password').addEventListener('click', function() {
    const passwordField = document.getElementById('client-password');
    const password = passwordField.value;
    
    if (password && password !== '*****') {
        navigator.clipboard.writeText(password).then(function() {
            // Feedback visuel
            const btn = document.getElementById('copy-password');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copié !';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
            }, 2000);
        }).catch(function() {
            alert('Impossible de copier dans le presse-papier. Mot de passe: ' + password);
        });
    } else {
        alert('Aucun mot de passe à copier.');
    }
});

document.getElementById('save-password').addEventListener('click', function() {
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (newPassword.length < 6) {
        alert('Le mot de passe doit contenir au moins 6 caractères.');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas.');
        return;
    }
    
    if (confirm('Êtes-vous sûr de vouloir changer le mot de passe de ce client ?')) {
        changerMotDePasse(<?php echo $client['id']; ?>, newPassword);
    }
});

function changerMotDePasse(clientId, nouveauMotDePasse) {
    fetch('changer-mot-de-passe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            client_id: clientId,
            nouveau_mot_de_passe: nouveauMotDePasse
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mot de passe modifié avec succès\\n\\nNouveau mot de passe: ' + data.nouveau_mot_de_passe_clair + '\\n\\nVous pouvez maintenant le communiquer au client.');
            document.getElementById('password-modal').style.display = 'none';
            document.getElementById('password-form').reset();
            // Mettre à jour l'affichage du mot de passe en clair
            const passwordField = document.getElementById('client-password');
            passwordField.value = data.nouveau_mot_de_passe_clair;
            passwordField.type = 'text';
            // Recharger la page pour mettre à jour l'interface complète
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Erreur lors de la modification: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du mot de passe');
    });
}

// Fonctions de suppression de commande
function confirmerSuppressionCommande(commandeId, numeroCommande) {
    if (confirm('Êtes-vous sûr de vouloir supprimer définitivement la commande #' + numeroCommande + ' ?\n\nCette action est irréversible et supprimera :\n- La commande et tous ses articles\n- L\'historique des modifications\n- Toutes les données associées')) {
        supprimerCommande(commandeId);
    }
}

function supprimerCommande(commandeId) {
    fetch('supprimer-commande.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            commande_id: commandeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Commande supprimée avec succès');
            location.reload();
        } else {
            alert('Erreur lors de la suppression: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression de la commande');
    });
}
</script>

<?php include 'footer_simple.php'; ?>