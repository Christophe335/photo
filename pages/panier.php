<?php
session_start();
// Initialisation du panier si besoin
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Handler pour vider le panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'vider_panier') {
    // Debug: écrire un fichier pour vérifier la requête reçue
    try {
        $storageDir = __DIR__ . '/../storage';
        if (!is_dir($storageDir)) @mkdir($storageDir, 0777, true);
        $dump = [
            'time' => time(),
            'post' => $_POST,
            'server_request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null
        ];
        @file_put_contents($storageDir . '/debug_vider_panier_' . time() . '.json', json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } catch (Exception $e) {
        error_log('Impossible d\'écrire debug vider_panier: ' . $e->getMessage());
    }

    unset($_SESSION['panier']);
    $_SESSION['success_message'] = 'Votre panier a été vidé.';
    // rediriger vers la même page (utiliser chemin relatif explicite)
    header('Location: /pages/panier.php');
    exit;
}

// (La validation de commande est gérée par clients/process-commande.php)

// Gestion de l'ajout de produit avec photos via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_avec_photos') {
    header('Content-Type: application/json');
    
    $produit = json_decode($_POST['produit'], true);
    
    if ($produit && isset($produit['id'])) {
        // Créer un ID unique basé sur le produit et un timestamp pour éviter les conflits
        $idUnique = $produit['id'] . '_' . time() . '_' . rand(1000, 9999);
        
        // Convertir le format du produit pour le panier existant
        $itemPanier = [
            'id' => $idUnique,
            'produit_id_origine' => $produit['id'], // Garder l'ID original pour référence
            'quantite' => $produit['quantite'] ?? 1,
            'prix' => $produit['prix'],
            'details' => [
                'code' => $produit['reference'],
                'designation' => $produit['designation'],
                'format' => $produit['format'] ?? '',
                'conditionnement' => $produit['conditionnement'] ?? '',
                'couleur' => $produit['couleur'] ?? '',
                'imageCouleur' => $produit['imageCouleur'] ?? ''
            ],
            'photos' => $produit['photos'] ?? [],
            'nombrePhotos' => $produit['nombrePhotos'] ?? 0,
            'source' => $produit['source'] ?? 'photo' // 'photo' ou 'perso'
        ];
        
        // Toujours ajouter comme nouvel item (permet d'avoir plusieurs variantes du même produit)
        $found = false;
        
        if (!$found) {
            $_SESSION['panier'][] = $itemPanier;
        }
        
        echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
    }
    exit;
}

// API pour synchroniser les paniers localStorage et session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sync_panier') {
    header('Content-Type: application/json');
    
    $panierClient = json_decode($_POST['panier_client'] ?? '[]', true);
    
    // Créer un panier unifié sans doublons
    $panierUnifie = $_SESSION['panier']; // Commencer par les articles session (photo/perso)
    
    // Ajouter les articles localStorage qui ne sont pas déjà présents
    if (!empty($panierClient)) {
        foreach ($panierClient as $itemClient) {
            // Éviter les doublons en vérifiant l'ID
            $dejaPresent = false;
            foreach ($panierUnifie as $itemExistant) {
                if ($itemExistant['id'] === $itemClient['id']) {
                    $dejaPresent = true;
                    break;
                }
            }
            
            if (!$dejaPresent) {
                $panierUnifie[] = [
                    'id' => $itemClient['id'],
                    'quantite' => $itemClient['quantite'],
                    'prix' => $itemClient['prix'],
                    'details' => $itemClient['details'],
                    'dateAjout' => $itemClient['dateAjout'] ?? date('c'),
                    'fromLocalStorage' => true
                ];
            }
        }
    }
    
    // Retourner le panier unifié (sans modifier la session pour l'instant)
    echo json_encode([
        'success' => true, 
        'panier_session' => $panierUnifie,
        'total_items' => count($panierUnifie)
    ]);
    exit;
}

// API pour obtenir le panier session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_panier_session') {
    header('Content-Type: application/json');
    echo json_encode([
        'panier_session' => $_SESSION['panier'],
        'total_items' => count($_SESSION['panier'])
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="/css/tableau.css">
    <style>
    /* Animation pour le caddie : entrer depuis la droite hors-écran, traverser et sortir à gauche en rétrécissant */
    .animated-caddie {
        position: fixed;
        top: 40%;
        left: 0;
        width: 375px;
        max-width: 90vw;
        transform-origin: center;
        z-index: 9999;
        pointer-events: none;
        animation: caddieSlide 16s linear infinite;
    }

    @keyframes caddieSlide {
        0% {
            transform: translateX(110vw) translateY(0) scale(1);
            opacity: 1;
        }
       
        100% {
            transform: translateX(-110vw) translateY(-50vh) scale(0.35);
            opacity: 1;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .animated-caddie { top: 45%; width: 300px; }
    }
    @media (max-width: 420px) {
        .animated-caddie { top: 48%; width: 220px; }
    }
    /* Empêcher le débordement horizontal causé par l'animation */
    html, body { overflow-x: hidden; }

    /* Conteneur réservé pour le caddie afin que la hauteur de la page reste stable */
    .caddie-container {
        min-height: 491px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 768px) {
        .caddie-container { min-height: 260px; }
    }
    @media (max-width: 420px) {
        .caddie-container { min-height: 200px; }
    }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <!-- Messages de confirmation/erreur -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px auto; max-width: 800px; border-radius: 5px; text-align: center;">
            <h3>✅</h3>
            <p><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['commande_validee']) && $_GET['commande_validee'] == '1'): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px auto; max-width: 800px; border-radius: 5px; text-align: center;">
            <h3>✅ Commande validée avec succès !</h3>
            <p>Votre commande a été envoyée à notre équipe. Vous recevrez une confirmation par email dans quelques minutes.</p>
            <?php if (isset($_GET['email_client']) && $_GET['email_client'] == '1'): ?>
                <p><em>Un email de confirmation vous a également été envoyé.</em></p>
            <?php endif; ?>
        </div>
    <?php elseif (isset($erreur_commande)): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px auto; max-width: 800px; border-radius: 5px; text-align: center;">
            <h3>❌ Erreur lors de la validation</h3>
            <p><?php echo htmlspecialchars($erreur_commande); ?></p>
        </div>
    <?php endif; ?>
    
    <h1 style="text-align:center;">Votre panier</h1>
    <?php if (!empty($_SESSION['panier'])): ?>
        <div style="text-align:center;margin-bottom:12px;">
            <form method="POST" onsubmit="if(!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) return false; try{ localStorage.removeItem('panier'); localStorage.removeItem('panier_synced'); }catch(e){};" style="display:inline-block;">
                <input type="hidden" name="action" value="vider_panier">
                <button type="submit" style="background:#dc3545;color:#fff;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;">Vider le panier</button>
            </form>
        </div>
    <?php endif; ?>
    <div id="panier-content">
        <?php
        $panier = $_SESSION['panier'];
        if (empty($panier)) {
            echo '<p style="text-align:center;">Votre panier est vide.</p>';
            echo '<div class="caddie-container">';
            echo '<img src="/images/logo-icon/caddie.webp" alt="Panier vide" class="animated-caddie">';
            echo '</div>';
        } else {
            echo '<div class="table-responsive">';
            echo '<table class="table-panier">';
            echo '<thead><tr><th>Référence</th><th>Désignation</th><th>Cdt</th><th>Qté</th><th>PU HT</th><th>Total HT</th><th>Action</th></tr></thead><tbody>';
            $totalHT = 0;
            foreach ($panier as $index => $item) {
                $reference = htmlspecialchars($item['details']['code'] ?? '');
                $designation = htmlspecialchars($item['details']['designation'] ?? '');
                $conditionnement = htmlspecialchars($item['details']['conditionnement'] ?? '');
                $format = htmlspecialchars($item['details']['format'] ?? '');
                $couleur = htmlspecialchars($item['details']['couleur'] ?? '');
                $imageCouleur = htmlspecialchars($item['details']['imageCouleur'] ?? '');
                $quantite = (int)$item['quantite'];
                $prix = number_format($item['prix'], 2, ',', ' ');
                $total = $item['prix'] * $quantite;
                $totalHT += $total;
                $id = htmlspecialchars($item['id']);

                // Si l'id contient un séparateur, on l'utilise pour garantir l'unicité
                echo '<tr data-id="' . $id . '">';
                // Colonne Référence
                echo '<td>' . $reference . '</td>';
                // Colonne Désignation
                echo '<td>' . $designation;
                if ($format) echo '<br><span style="color:#666;font-size:13px">Format : ' . $format . '</span>';
                if ($couleur) {
                    echo '<br><span style="color:#666;font-size:13px">Couleur : ' . $couleur;
                    if ($imageCouleur) {
                        // S'assurer que le chemin de l'image est correct
                        $cheminImage = $imageCouleur;
                        if (strpos($cheminImage, '../') === 0) {
                            $cheminImage = substr($cheminImage, 3); // Enlever '../' au début
                        }
                        if (strpos($cheminImage, '/') !== 0) {
                            $cheminImage = '/' . $cheminImage; // Ajouter '/' au début si absent
                        }
                        echo ' <img src="' . $cheminImage . '" alt="' . $couleur . '" style="width:22px;height:22px;border-radius:50%;margin-left:6px;vertical-align:middle;" onerror="this.style.display=\'none\'" loading="lazy">';
                    }
                    echo '</span>';
                }
                
                // Affichage des photos/personnalisations uploadées
                if (isset($item['photos']) && !empty($item['photos'])) {
                    $nombrePhotos = count($item['photos']);
                    $conditionInt = intval($conditionnement);
                    $quantiteCalculee = $quantite;
                    $totalPhotosPayees = $conditionInt > 0 ? ($quantiteCalculee * $conditionInt) : $nombrePhotos;
                    
                    // Déterminer si c'est une personnalisation ou des photos
                    $isPersonnalisation = (isset($item['source']) && $item['source'] === 'perso') || 
                                         strpos(strtolower($designation), 'personnalis') !== false ||
                                         strpos(strtolower($designation), 'custom') !== false;
                    
                    $icon = $isPersonnalisation ? '🎨' : '📸';
                    $typeLabel = $isPersonnalisation ? 'personnalisation' : 'photo';
                    $typeLabelPlural = $isPersonnalisation ? 'personnalisations' : 'photos';
                    
                    echo '<br><div style="background:#f8f9fa; padding:8px; border-radius:4px; margin-top:5px; border-left:3px solid #28a745;">';
                    echo '<strong style="color:#28a745;">' . $icon . ' ' . $nombrePhotos . ' ' . ($nombrePhotos > 1 ? $typeLabelPlural : $typeLabel) . ' ajoutée' . ($nombrePhotos > 1 ? 's' : '') . '</strong>';
                    
                    echo '<div style="margin-top:5px; font-size:12px; color:#666;">';
                    
                    foreach ($item['photos'] as $index => $photo) {
                        $nomFichier = htmlspecialchars($photo['nom'] ?? 'image_' . ($index + 1) . '.jpg');
                        echo '<div style="display:flex; align-items:center; margin:2px 0;">';
                        echo '<span style="color:#28a745; margin-right:5px;">•</span>';
                        echo '<span title="' . $nomFichier . '">' . (strlen($nomFichier) > 25 ? substr($nomFichier, 0, 22) . '...' : $nomFichier) . '</span>';
                        echo '</div>';
                    }
                    echo '</div></div>';
                }
                
                echo '</td>';
                // Colonne Conditionnement
                echo '<td>' . ($conditionnement ? $conditionnement : '-') . '</td>';
                // Colonne Quantité
                echo '<td><span class="quantite-panier">' . $quantite . '</span></td>';
                // Colonne PU HT
                echo '<td>' . $prix . ' €</td>';
                // Colonne Total HT
                echo '<td>' . number_format($total, 2, ',', ' ') . ' €</td>';
                // Colonne Action
                echo '<td><button class="btn-supprimer-panier" onclick="supprimerDuPanierPage(\'' . $id . '\')" aria-label="Supprimer du panier">🗑️</button></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
            // Calculs frais de port, TVA et TTC (frais placés entre HT et TVA)
            $fraisPort = ($totalHT > 200) ? 0 : 13.95;
            $tva = ($totalHT + $fraisPort) * 0.20; // TVA appliquée sur HT + frais
            $ttc = $totalHT + $fraisPort + $tva;

            echo '<div class="recap-panier">';
            echo '<p style="display:flex; justify-content:space-between;">Total HT : <strong>' . number_format($totalHT, 2, ',', ' ') . ' €</strong></p>';
            if ($fraisPort == 0) {
                echo '<p style="display:flex; justify-content:space-between;">Frais de port : <strong style="color:green">Gratuit</strong></p>';
            } else {
                echo '<p style="display:flex; justify-content:space-between;">Frais de port : <strong>' . number_format($fraisPort, 2, ',', ' ') . ' €</strong></p>';
            }
            echo '<p style="display:flex; justify-content:space-between;">TVA (20%) : <strong>' . number_format($tva, 2, ',', ' ') . ' €</strong></p>';
            echo '<hr>';
            echo '<p style="display:flex; justify-content:space-between;"><strong>Total à payer :</strong><strong>' . number_format($ttc, 2, ',', ' ') . ' €</strong>
      </p>';
            echo '</div>';
            
            // Section de commande
            echo '<div class="panier-actions">';
            if (isset($_SESSION['client_id'])) {
                // Client connecté - afficher le bouton de commande
                    echo '<form id="commande-form" action="/clients/process-commande.php" method="POST" class="commande-form">
                <input type="hidden" name="action" value="valider_commande">
                <input type="hidden" name="panier_complet" id="panier_complet" value="">';
                echo '<div class="commande-options">';
                echo '<h3>Finaliser votre commande</h3>';
                
                echo '<div class="option-group">';
                echo '<label for="mode_paiement">Mode de paiement :</label>';
                echo '<select name="mode_paiement" id="mode_paiement" required>';
                echo '<option value="carte_bancaire">Carte bancaire</option>';
                echo '<option value="mandat_administratif">Mandat Administratif</option>';
                echo '<option value="virement">Virement bancaire</option>';
                echo '</select>';
                echo '</div>';
                
                echo '<div class="option-group">';
                echo '<label>';
                echo '<input type="checkbox" name="utiliser_adresse_facturation" checked>';
                echo 'Livrer à l\'adresse de facturation';
                echo '</label>';
                echo '</div>';
                
                echo '<div class="option-group">';
                echo '<label for="commentaire">Commentaire (optionnel) :</label>';
                echo '<textarea name="commentaire" id="commentaire" rows="3" placeholder="Instructions particulières pour votre commande..."></textarea>';
                echo '</div>';
                
                echo '<div class="commande-resume">';
                echo '<p><strong>Récapitulatif :</strong></p>';
                echo '<p>Articles : ' . count($panier) . '</p>';
                echo '<p>Total TTC : ' . number_format($ttc, 2, ',', ' ') . ' €</p>';
                echo '<p>Frais de port : ' . ($fraisPort == 0 ? 'Gratuit' : number_format($fraisPort, 2, ',', ' ') . ' €') . '</p>';
                echo '<p class="total-final"><strong>Total à payer : ' . number_format($ttc + $fraisPort, 2, ',', ' ') . ' €</strong></p>';
                echo '</div>';
                
                echo '<button type="submit" class="btn-commander" onclick="preparerValidationCommande()">Valider ma commande</button>';
                echo '</form>';
            } else {
                // Client non connecté - invitation à se connecter
                echo '<div class="connexion-required">';
                echo '<h3>Finaliser votre commande</h3>';
                echo '<p>Pour passer votre commande, vous devez être connecté.</p>';
                echo '<div class="connexion-actions">';
                echo '<div class="account-btn" style="width: 115px;">
                        <a href="../formulaires/voir-compte.php" class="btn-contact">
                            <i class="fas fa-user"></i>
                            <span>Compte</span>
                        </a>
                    </div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
    ?>
    <?php include '../includes/footer.php'; ?>
    <script src="/js/panier.js"></script>
    <script>
    try {
        // Synchronisation du panier JS vers PHP (une seule fois)
        function syncPanierToSession() {
            console.log('[Panier] syncPanierToSession appelée');
            if (localStorage.getItem('panier_synced') === '1') {
                console.log('[Panier] déjà synchronisé');
                return;
            }
            var panier = localStorage.getItem('panier');
            if (panier) {
                console.log('[Panier] fetch vers /pages/sync_panier.php', panier);
                fetch('/pages/sync_panier.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: panier
                }).then(function(resp) {
                    resp.json().then(function(data) {
                        console.log('[Panier] Réponse sync_panier.php', data);
                        if (resp.ok) {
                            localStorage.setItem('panier_synced', '1');
                            console.log('[Panier] Synchronisation OK, pas de rechargement automatique');
                        }
                    });
                });
            }
        }
        window.addEventListener('DOMContentLoaded', function() {
            console.log('[Panier] Page panier chargée, vérification synchronisation...');
            localStorage.removeItem('panier_synced'); // Toujours supprimer le flag au chargement
            
            var panierJS = localStorage.getItem('panier');
            var panierJSArray = panierJS ? JSON.parse(panierJS) : [];
            
            // Vérifier si le panier JS et PHP sont différents
            var panierPHPVide = document.querySelector('#panier-content p') && 
                               document.querySelector('#panier-content p').textContent.includes('vide');
            var panierJSNonVide = panierJSArray.length > 0;
            
            console.log('[Panier] PHP vide:', panierPHPVide, 'JS non vide:', panierJSNonVide);
            
            // Si JS a des articles mais PHP est vide, synchroniser et recharger
            if (panierJSNonVide && panierPHPVide) {
                console.log('[Panier] Désynchronisation détectée, synchronisation et rechargement...');
                fetch('/pages/sync_panier.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(panierJSArray)
                }).then(function(response) {
                    console.log('[Panier] Synchronisation lors du chargement terminée, rechargement...');
                    window.location.reload();
                }).catch(function(error) {
                    console.log('[Panier] Erreur synchronisation chargement:', error);
                });
            } else if (panierJSNonVide) {
                // Simple synchronisation sans rechargement si tous les deux ont du contenu
                syncPanierToSession();
            }
            // Ajout automatique de la synchronisation et du rechargement après ajout au panier
            if (window.ajoutAuPanierEtSync === undefined) {
                window.ajoutAuPanierEtSync = function() {
                    localStorage.removeItem('panier_synced');
                    if (typeof syncPanierToSession === 'function') {
                        syncPanierToSession();
                        console.log('[Panier] Synchronisation demandée, pas de rechargement automatique');
                    }
                }
            }
            // Ajout direct dans le JS d'ajout au panier
            if (typeof ajouterAuPanier === 'function') {
                const oldAjouterAuPanier = ajouterAuPanier;
                window.ajouterAuPanier = function() {
                    oldAjouterAuPanier.apply(this, arguments);
                    if (typeof ajoutAuPanierEtSync === 'function') ajoutAuPanierEtSync();
                }
            }
            window.addEventListener('storage', function(e) {
                if (e.key === 'panier') {
                    localStorage.removeItem('panier_synced');
                    syncPanierToSession();
                    setTimeout(function(){ window.location.reload(); }, 400);
                }
            });
        });
        // Fonctions JS pour modifier/supprimer dans le panier
        function modifierQuantitePanier(id, delta) {
            let panier = JSON.parse(localStorage.getItem('panier'));
            for (let i = 0; i < panier.length; i++) {
                if (panier[i].id === id) {
                    panier[i].quantite = Math.max(1, (panier[i].quantite || 0) + delta);
                    break;
                }
            }
            localStorage.setItem('panier', JSON.stringify(panier));
            window.location.reload();
        }
        function supprimerDuPanierPage(id) {
            console.log('[DEBUG] Suppression page article:', id);

            // Tenter de mettre à jour localStorage si présent
            try {
                let panierLS = JSON.parse(localStorage.getItem('panier')) || [];
                const avant = panierLS.map(p => p.id);
                panierLS = panierLS.filter(item => item.id !== id);
                localStorage.setItem('panier', JSON.stringify(panierLS));
                if (avant.length !== panierLS.length) console.log('[DEBUG] localStorage mis à jour, ids now', panierLS.map(p=>p.id));
            } catch (e) {
                console.warn('[DEBUG] localStorage absent ou invalide', e);
            }

            // Appeler l'endpoint serveur pour supprimer l'article côté session
            fetch('/pages/remove_panier_item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            }).then(function(response) {
                return response.json().then(function(data) {
                    console.log('[DEBUG] remove_panier_item response', data);
                    // Forcer la suppression du flag de synchro et recharger pour refléter la session
                    localStorage.removeItem('panier_synced');
                    window.location.reload();
                });
            }).catch(function(error) {
                console.error('[DEBUG] Erreur suppression serveur, rechargement', error);
                window.location.reload();
            });
        }
        // Ajout d'un hook JS pour forcer la synchronisation et le rechargement après ajout au panier
        window.ajoutAuPanierEtSync = function() {
            localStorage.removeItem('panier_synced');
            if (typeof syncPanierToSession === 'function') {
                syncPanierToSession();
                setTimeout(function(){ window.location.reload(); }, 400);
            }
        }
        
        // Fonction pour préparer la validation de commande
        window.preparerValidationCommande = function() {
            console.log('[Commande] Préparation validation commande');
            
            // Récupérer le panier localStorage
            const panierLS = localStorage.getItem('panier');
            let panierComplet = [];
            
            if (panierLS) {
                try {
                    panierComplet = JSON.parse(panierLS);
                } catch (e) {
                    console.error('[Commande] Erreur parsing localStorage:', e);
                }
            }
            
            // Ajouter le panier session (déjà dans la page PHP)
            // Le panier session sera automatiquement inclus côté serveur
            
            // Passer le panier complet au formulaire
            document.getElementById('panier_complet').value = JSON.stringify(panierComplet);
            
            console.log('[Commande] Panier complet préparé:', panierComplet.length, 'articles');
            
            return true; // Permettre la soumission du formulaire
        }
        
    } catch (e) {
        console.error('[Panier] Erreur', e);
    }
    </script>
</body>
</html>