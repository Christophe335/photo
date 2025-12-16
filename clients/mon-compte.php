<?php 
session_start();
require_once '../includes/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    $_SESSION['redirect_after_login'] = 'mon-compte.php';
    header('Location: connexion.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les informations complètes du client
    $stmt = $db->prepare("
        SELECT *, DATE_FORMAT(date_creation, '%M %Y') as membre_depuis
        FROM clients 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        session_destroy();
        header('Location: connexion.php');
        exit;
    }
    
    // Statistiques des commandes
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as nb_commandes,
            COALESCE(SUM(total), 0) as total_depense,
            COUNT(CASE WHEN statut = 'en_cours' OR statut = 'en_preparation' OR statut = 'expediee' THEN 1 END) as commandes_en_cours
        FROM commandes 
        WHERE client_id = ?
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Dernières commandes
    $stmt = $db->prepare("
        SELECT numero_commande, date_commande, statut, total
        FROM commandes 
        WHERE client_id = ? 
        ORDER BY date_commande DESC 
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $dernieres_commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erreur mon-compte: " . $e->getMessage());
    $client = [];
    $stats = ['nb_commandes' => 0, 'total_depense' => 0, 'commandes_en_cours' => 0];
    $dernieres_commandes = [];
}

include '../includes/header.php'; 
?>
<head>
    <link rel="stylesheet" href="../css/client.css">
    <style>
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        .menu-link {
            cursor: pointer;
        }
        
        .recent-orders {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .order-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-info strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        .order-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-status {
            text-align: right;
        }
        
        .order-status span {
            display: block;
            margin-bottom: 5px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-en_attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmee {
            background: #d4edda;
            color: #155724;
        }
        
        .status-en_preparation,
        .status-en_cours {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-expediee {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .status-livree {
            background: #d4edda;
            color: #155724;
        }
        
        /* Nouvelles sections informations personnelles */
        .personal-info-section {
            space-y: 25px;
        }
        
        .basic-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .addresses-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .address-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #007bff;
        }
        
        .address-card h4 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .address-content {
            line-height: 1.5;
            color: #555;
        }
        
        .same-as-billing {
            text-align: center;
            font-style: italic;
            padding: 10px 0;
        }
        
        .account-status {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            font-size: 0.9rem;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .status-item strong {
            color: #333;
        }
        
        .account-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            font-size: 1rem;
            color: #666;
            border-top: 1px solid #eee;
            margin-top: 25px;
            padding-top: 20px;
        }
        
        .stats-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stats-item strong {
            color: #333;
        }
        
        .stats-value {
            color: #007bff;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        @media (max-width: 768px) {
            .addresses-grid {
                grid-template-columns: 1fr;
            }
            
            .basic-info {
                grid-template-columns: 1fr;
            }
            
            .account-status {
                flex-direction: column;
                gap: 10px;
            }
            
            .account-stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<main class="account-background">
    <div class="account-container">
        <h1 class="account-title">Mon compte</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="welcome-message">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="account-content">
            <!-- Sidebar -->
            <div class="account-sidebar">
                <h3 class="sidebar-title">Navigation</h3>
                <ul class="sidebar-menu">
                    <li><a href="#informations" class="menu-link active" data-section="informations">Informations personnelles</a></li>
                    <li><a href="#commandes" class="menu-link" data-section="commandes">Suivi de vos commandes</a></li>
                    <li><a href="#historique" class="menu-link" data-section="historique">Historique des achats</a></li>
                    <li><a href="#modifier" class="menu-link" data-section="modifier">Modifier mes informations</a></li>
                    <li><a href="logout.php" class="btn-logout">Déconnexion</a></li>
                </ul>
            </div>
            
            <!-- Contenu principal -->
            <div class="account-main">
                <!-- Section Informations personnelles -->
                <div id="section-informations" class="content-section active">
                    <h2 class="section-title">Vos informations personnelles</h2>
                    
                    <div class="personal-info-section">
                        <!-- Informations de base -->
                        <div class="basic-info">
                            <div class="info-group">
                                <div class="info-label">Nom complet</div>
                                <div class="info-value"><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Adresse e-mail</div>
                                <div class="info-value"><?php echo htmlspecialchars($client['email']); ?></div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Téléphone</div>
                                <div class="info-value"><?php echo htmlspecialchars($client['telephone'] ?: 'Non renseigné'); ?></div>
                            </div>
                        </div>
                        
                        <!-- Adresses côte à côte -->
                        <div class="addresses-grid">
                            <div class="address-card">
                                <h4><i class="fas fa-map-marker-alt"></i> Adresse de facturation</h4>
                                <?php if ($client['adresse']): ?>
                                    <div class="address-content">
                                        <?php echo nl2br(htmlspecialchars($client['adresse'])); ?><br>
                                        <?php echo htmlspecialchars($client['code_postal'] . ' ' . $client['ville']); ?><br>
                                        <?php echo htmlspecialchars($client['pays'] ?: 'France'); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="address-content text-muted">Non renseignée</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="address-card">
                                <h4><i class="fas fa-shipping-fast"></i> Adresse de livraison</h4>
                                <?php if ($client['adresse_livraison_differente'] && $client['adresse_livraison']): ?>
                                    <div class="address-content">
                                        <?php echo nl2br(htmlspecialchars($client['adresse_livraison'])); ?><br>
                                        <?php echo htmlspecialchars($client['code_postal_livraison'] . ' ' . $client['ville_livraison']); ?><br>
                                        <?php echo htmlspecialchars($client['pays_livraison'] ?: 'France'); ?>
                                    </div>
                                <?php elseif ($client['adresse']): ?>
                                    <div class="address-content same-as-billing">
                                        <span class="text-muted"><i class="fas fa-link"></i> Identique à l'adresse de facturation</span>
                                    </div>
                                <?php else: ?>
                                    <div class="address-content text-muted">Non renseignée</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Statuts sur une ligne -->
                        <div class="account-status">
                            <span class="status-item">
                                <strong>Statut du compte :</strong> 
                                <span style="color: #28a745;">✓ Compte actif</span>
                            </span>
                            <span class="status-item">
                                <strong>Membre depuis :</strong> 
                                <?php echo $client['membre_depuis'] ?: 'Récemment'; ?>
                            </span>
                            <span class="status-item">
                                <strong>Dernière connexion :</strong>
                                <?php 
                                if ($client['derniere_connexion']) {
                                    echo date('d/m/Y H:i', strtotime($client['derniere_connexion']));
                                } else {
                                    echo 'Première connexion';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Statistiques du compte -->
                    <div class="account-stats">
                        <span class="stats-item">
                            <strong>Commandes :</strong> 
                            <span class="stats-value"><?php echo $stats['nb_commandes']; ?></span>
                        </span>
                        <span class="stats-item">
                            <strong>En cours :</strong> 
                            <span class="stats-value"><?php echo $stats['commandes_en_cours']; ?></span>
                        </span>
                        <span class="stats-item">
                            <strong>Total dépensé :</strong> 
                            <span class="stats-value"><?php echo number_format($stats['total_depense'], 0, ',', ' '); ?>€</span>
                        </span>
                    </div>
                </div>
                
                <!-- Section Suivi des commandes -->
                <div id="section-commandes" class="content-section">
                    <h2 class="section-title">Suivi de vos commandes en temps réel</h2>
                    <div id="commandes-content">
                        <!-- Contenu chargé via JavaScript -->
                        <p>Chargement...</p>
                    </div>
                </div>
                
                <!-- Section Historique -->
                <div id="section-historique" class="content-section">
                    <h2 class="section-title">Historique complet de vos achats</h2>
                    <div id="historique-content">
                        <!-- Contenu chargé via JavaScript -->
                        <p>Chargement...</p>
                    </div>
                </div>
                
                <!-- Section Modification -->
                <div id="section-modifier" class="content-section">
                    <h2 class="section-title">Modification des informations client</h2>
                    <div id="modifier-content">
                        <!-- Contenu chargé via JavaScript -->
                        <p>Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Gestion des onglets
document.addEventListener('DOMContentLoaded', function() {
    const menuLinks = document.querySelectorAll('.menu-link');
    const contentSections = document.querySelectorAll('.content-section');
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            console.log('Clic sur section:', section);
            
            // Supprimer les classes active
            menuLinks.forEach(l => l.classList.remove('active'));
            contentSections.forEach(s => {
                s.classList.remove('active');
                s.style.display = 'none';
                s.style.visibility = 'hidden';
            });
            
            // Ajouter la classe active
            this.classList.add('active');
            const sectionElement = document.getElementById('section-' + section);
            if (sectionElement) {
                sectionElement.classList.add('active');
                sectionElement.style.display = 'block';
                sectionElement.style.visibility = 'visible';
                sectionElement.style.opacity = '1';
                console.log('Section affichée:', section);
            } else {
                console.error('Section non trouvée:', 'section-' + section);
            }
            
            // Charger le contenu si nécessaire
            if (section === 'commandes') {
                loadCommandes();
            } else if (section === 'historique') {
                loadHistorique();
            } else if (section === 'modifier') {
                loadModifier();
            }
        });
    });
});

function loadCommandes() {
    const content = document.getElementById('commandes-content');
    content.innerHTML = '<p>Chargement des commandes...</p>';
    
    fetch('ajax/get-commandes.php')
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            content.style.display = 'block';
            content.style.visibility = 'visible';
        })
        .catch(error => {
            content.innerHTML = '<p class="alert alert-error">Erreur lors du chargement des commandes.</p>';
        });
}

function loadHistorique() {
    const content = document.getElementById('historique-content');
    content.innerHTML = '<p>Chargement de l\'historique...</p>';
    
    fetch('ajax/get-historique.php')
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            content.style.display = 'block';
            content.style.visibility = 'visible';
        })
        .catch(error => {
            content.innerHTML = '<p class="alert alert-error">Erreur lors du chargement de l\'historique.</p>';
        });
}

function loadModifier() {
    console.log('Chargement du formulaire de modification...');
    const modifierSection = document.getElementById('section-modifier');
    const modifierContent = document.getElementById('modifier-content');
    
    // Vérifier que les éléments existent
    if (!modifierSection) {
        console.error('Section modifier introuvable!');
        return;
    }
    if (!modifierContent) {
        console.error('Contenu modifier introuvable!');
        return;
    }
    
    console.log('Section modifier visible:', modifierSection.classList.contains('active'));
    console.log('Style display section:', getComputedStyle(modifierSection).display);
    
    modifierContent.innerHTML = '<p>Chargement du formulaire...</p>';
    
    fetch('ajax/get-modifier-form-clean.php')
        .then(response => {
            console.log('Réponse reçue:', response.status);
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.text();
        })
        .then(html => {
            console.log('HTML reçu, taille:', html.length);
            if (html.trim() === '') {
                modifierContent.innerHTML = '<p style="color: red;">Le formulaire est vide. Vérifiez les logs du serveur.</p>';
            } else {
                modifierContent.innerHTML = html;
                console.log('Formulaire inséré dans le DOM');
                
                // Forcer l'affichage de la section
                modifierSection.style.display = 'block';
                modifierSection.classList.add('active');
                console.log('Section forcée visible');
                
                // Attacher les événements JavaScript au formulaire chargé
                initializeFormEvents();
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement:', error);
            modifierContent.innerHTML = '<p style="color: red;">Erreur: ' + error.message + '</p>';
        });
}

// Initialiser les événements du formulaire après son chargement
function initializeFormEvents() {
    console.log('Initialisation des événements du formulaire...');
    
    // Gestion de l'affichage de l'adresse de livraison
    const adresseLivraisonCheckbox = document.getElementById('adresse_livraison_differente');
    if (adresseLivraisonCheckbox) {
        adresseLivraisonCheckbox.addEventListener('change', function() {
            const fields = document.getElementById('adresse_livraison_fields');
            const inputs = fields ? fields.querySelectorAll('input, textarea, select') : [];
            
            if (this.checked) {
                if (fields) fields.style.display = 'block';
            } else {
                if (fields) fields.style.display = 'none';
                // Vider les champs si masqués
                inputs.forEach(input => {
                    input.value = '';
                });
            }
        });
        console.log('✓ Event listener adresse livraison attaché');
    }

    // Gestion du formulaire de modification
    const form = document.getElementById('modifier-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
        console.log('✓ Event listener formulaire attaché');
    } else {
        console.error('✗ Formulaire modifier-form introuvable !');
    }
    
    // Gestion du bouton Annuler
    const cancelBtn = document.getElementById('btn-cancel');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', resetForm);
        console.log('✓ Event listener bouton annuler attaché');
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    console.log('🚀 Formulaire soumis !');
    
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
    if (btnSave) btnSave.disabled = true;
    if (btnText) btnText.style.display = 'none';
    if (btnLoading) btnLoading.style.display = 'inline';
    
    // Envoyer les données
    const formData = new FormData(e.target);
    
    console.log('📤 Données du formulaire:', Object.fromEntries(formData));
    
    fetch('ajax/update-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📨 Réponse reçue:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📊 Données de réponse:', data);
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
        console.error('❌ Erreur:', error);
    })
    .finally(() => {
        // Réactiver le bouton
        if (btnSave) btnSave.disabled = false;
        if (btnText) btnText.style.display = 'inline';
        if (btnLoading) btnLoading.style.display = 'none';
    });
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir annuler vos modifications ?')) {
        const form = document.getElementById('modifier-form');
        if (form) {
            form.reset();
            const fields = document.getElementById('adresse_livraison_fields');
            const checkbox = document.getElementById('adresse_livraison_differente');
            if (fields && checkbox) {
                fields.style.display = checkbox.checked ? 'block' : 'none';
            }
            const resultDiv = document.getElementById('modification-result');
            if (resultDiv) resultDiv.innerHTML = '';
        }
    }
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const resultDiv = document.getElementById('modification-result');
    if (resultDiv) {
        resultDiv.innerHTML = '<div class="alert ' + alertClass + '">' + message + '</div>';
        
        // Faire défiler vers le message
        resultDiv.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>