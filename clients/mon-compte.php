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
                    
                    <div class="info-grid">
                        <div>
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
                        
                        <div>
                            <div class="info-group">
                                <div class="info-label">Statut du compte</div>
                                <div class="info-value" style="color: #28a745;">✓ Compte actif</div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Membre depuis</div>
                                <div class="info-value"><?php echo $client['membre_depuis'] ?: 'Récemment'; ?></div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Dernière connexion</div>
                                <div class="info-value">
                                    <?php 
                                    if ($client['derniere_connexion']) {
                                        echo date('d/m/Y H:i', strtotime($client['derniere_connexion']));
                                    } else {
                                        echo 'Première connexion';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques du compte -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['nb_commandes']; ?></div>
                            <div class="stat-label">Commandes</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $stats['commandes_en_cours']; ?></div>
                            <div class="stat-label">En cours</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-number"><?php echo number_format($stats['total_depense'], 0, ',', ' '); ?>€</div>
                            <div class="stat-label">Total dépensé</div>
                        </div>
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
    
    fetch('ajax/get-modifier-form.php')
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
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement:', error);
            modifierContent.innerHTML = '<p style="color: red;">Erreur: ' + error.message + '</p>';
        });
}
</script>

<?php include '../includes/footer.php'; ?>