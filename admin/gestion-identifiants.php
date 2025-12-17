<?php
require_once 'functions.php';
require_once '../includes/database.php';

// Vérifier l'authentification admin
checkAuth();

// Fichier de stockage des identifiants admin
$admin_credentials_file = __DIR__ . '/.admin_credentials.json';

// Fonction pour charger les identifiants
function loadAdminCredentials() {
    global $admin_credentials_file;
    if (file_exists($admin_credentials_file)) {
        $content = file_get_contents($admin_credentials_file);
        return json_decode($content, true) ?: [];
    }
    // Créer avec l'identifiant par défaut s'il n'existe pas
    return [
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'last_login' => null,
            'active' => true
        ]
    ];
}

// Fonction pour sauvegarder les identifiants
function saveAdminCredentials($credentials) {
    global $admin_credentials_file;
    return file_put_contents($admin_credentials_file, json_encode($credentials, JSON_PRETTY_PRINT));
}

// Traitement des actions
$message = '';
$message_type = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    $credentials = loadAdminCredentials();
    
    switch ($action) {
        case 'add':
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            
            if (empty($username) || empty($password)) {
                $message = 'Le nom d\'utilisateur et le mot de passe sont obligatoires.';
                $message_type = 'error';
            } elseif (isset($credentials[$username])) {
                $message = 'Ce nom d\'utilisateur existe déjà.';
                $message_type = 'error';
            } else {
                $credentials[$username] = [
                    'username' => $username,
                    'password' => $password,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'last_login' => null,
                    'active' => true
                ];
                
                if (saveAdminCredentials($credentials)) {
                    $message = 'Identifiant ajouté avec succès.';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la sauvegarde.';
                    $message_type = 'error';
                }
            }
            break;
            
        case 'edit':
            $username = $_POST['username'];
            $new_password = trim($_POST['new_password']);
            
            if (isset($credentials[$username])) {
                if (!empty($new_password)) {
                    $credentials[$username]['password'] = $new_password;
                    $credentials[$username]['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);
                }
                $credentials[$username]['active'] = isset($_POST['active']);
                
                if (saveAdminCredentials($credentials)) {
                    $message = 'Identifiant modifié avec succès.';
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la sauvegarde.';
                    $message_type = 'error';
                }
            }
            break;
            
        case 'delete':
            $username = $_POST['username'];
            if (isset($credentials[$username])) {
                if (count($credentials) <= 1) {
                    $message = 'Impossible de supprimer le dernier identifiant administrateur.';
                    $message_type = 'error';
                } else {
                    unset($credentials[$username]);
                    if (saveAdminCredentials($credentials)) {
                        $message = 'Identifiant supprimé avec succès.';
                        $message_type = 'success';
                    } else {
                        $message = 'Erreur lors de la sauvegarde.';
                        $message_type = 'error';
                    }
                }
            }
            break;
    }
}

$credentials = loadAdminCredentials();

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<div class="admin-content">
    <h1><i class="fas fa-users-cog"></i> Gestion des Identifiants Administrateurs</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire d'ajout -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus"></i> Ajouter un nouvel identifiant</h3>
        </div>
        <div class="card-body">
            <form method="POST" class="form-horizontal">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="text" id="password" name="password" required class="form-control">
                    <small class="form-text text-muted">Utilisez un mot de passe fort (lettres, chiffres, caractères spéciaux)</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </form>
        </div>
    </div>
    
    <!-- Liste des identifiants existants -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Identifiants existants</h3>
        </div>
        <div class="card-body">
            <?php if (empty($credentials)): ?>
                <p class="text-muted">Aucun identifiant configuré.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom d'utilisateur</th>
                                <th>Mot de passe</th>
                                <th>Créé le</th>
                                <th>Dernière connexion</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($credentials as $username => $data): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($username) ?></strong></td>
                                    <td>
                                        <span class="password-hidden" id="pwd-<?= htmlspecialchars($username) ?>">
                                            ••••••••
                                        </span>
                                        <span class="password-visible" id="pwd-show-<?= htmlspecialchars($username) ?>" style="display: none;">
                                            <?= htmlspecialchars($data['password']) ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ml-2" 
                                                onclick="togglePassword('<?= htmlspecialchars($username) ?>')">
                                            <i class="fas fa-eye" id="eye-<?= htmlspecialchars($username) ?>"></i>
                                        </button>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($data['created_at'])) ?></td>
                                    <td>
                                        <?= $data['last_login'] ? date('d/m/Y H:i', strtotime($data['last_login'])) : 'Jamais' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['active'] ? 'success' : 'secondary' ?>">
                                            <?= $data['active'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="editCredential('<?= htmlspecialchars($username) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteCredential('<?= htmlspecialchars($username) ?>')"
                                                <?= count($credentials) <= 1 ? 'disabled' : '' ?>>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Informations de sécurité -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-shield-alt"></i> Informations de sécurité</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Important :</h5>
                <ul>
                    <li>Les identifiants sont stockés dans le fichier <code>.admin_credentials.json</code></li>
                    <li>Ce fichier est protégé par un point au début du nom (fichier caché)</li>
                    <li>Assurez-vous que ce fichier n'est pas accessible via le web</li>
                    <li>Utilisez des mots de passe forts et uniques</li>
                    <li>Il doit toujours rester au moins un identifiant actif</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'édition -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Modifier l'identifiant</h3>
        <form id="editForm" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="username" id="edit_username">
            
            <div class="form-group">
                <label for="edit_new_password">Nouveau mot de passe (laisser vide pour ne pas modifier) :</label>
                <input type="text" id="edit_new_password" name="new_password" class="form-control">
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" id="edit_active" checked> Compte actif
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Modifier</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
        </form>
    </div>
</div>



<script>
function togglePassword(username) {
    const hiddenSpan = document.getElementById('pwd-' + username);
    const visibleSpan = document.getElementById('pwd-show-' + username);
    const eyeIcon = document.getElementById('eye-' + username);
    
    if (hiddenSpan.style.display === 'none') {
        hiddenSpan.style.display = 'inline';
        visibleSpan.style.display = 'none';
        eyeIcon.className = 'fas fa-eye';
    } else {
        hiddenSpan.style.display = 'none';
        visibleSpan.style.display = 'inline';
        eyeIcon.className = 'fas fa-eye-slash';
    }
}

function editCredential(username) {
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_new_password').value = '';
    document.getElementById('editModal').style.display = 'block';
}

function deleteCredential(username) {
    if (confirm('Êtes-vous sûr de vouloir supprimer l\'identifiant "' + username + '" ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="username" value="' + username + '">';
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php include 'footer_simple.php'; ?>