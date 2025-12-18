<?php
session_start();
// Initialisation du panier si besoin
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Gestion de l'ajout de produit avec photos via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_avec_photos') {
    header('Content-Type: application/json');
    
    $produit = json_decode($_POST['produit'], true);
    
    if ($produit && isset($produit['id'])) {
        // Convertir le format du produit pour le panier existant
        $itemPanier = [
            'id' => $produit['id'],
            'quantite' => $produit['quantite'] ?? 1,
            'prix' => $produit['prix'],
            'details' => [
                'code' => $produit['reference'],
                'designation' => $produit['designation'],
                'format' => $produit['format'] ?? '',
                'conditionnement' => $produit['conditionnement'] ?? '',
                'couleur' => ''
            ],
            'photos' => $produit['photos'] ?? [],
            'nombrePhotos' => $produit['nombrePhotos'] ?? 0
        ];
        
        // Vérifier si le produit existe déjà
        $found = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['id'] == $produit['id']) {
                $item = $itemPanier; // Remplacer
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['panier'][] = $itemPanier;
        }
        
        echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="/css/tableau.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1 style="text-align:center;">Votre panier</h1>
    <div id="panier-content">
        <?php
        $panier = $_SESSION['panier'];
        if (empty($panier)) {
            echo '<p style="text-align:center;">Votre panier est vide.</p>';
            echo '<img src="/images/logo-icon/caddie-vide.webp" alt="Panier vide" style="display:block; margin:20px auto; width:375px;">';
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
                        echo ' <img src="' . $imageCouleur . '" alt="' . $couleur . '" style="width:22px;height:22px;border-radius:50%;margin-left:6px;vertical-align:middle;">';
                    }
                    echo '</span>';
                }
                
                // Affichage des photos uploadées
                if (isset($item['photos']) && !empty($item['photos'])) {
                    $nombrePhotos = count($item['photos']);
                    $conditionInt = intval($conditionnement);
                    $quantiteCalculee = $quantite;
                    $totalPhotosPayees = $conditionInt > 0 ? ($quantiteCalculee * $conditionInt) : $nombrePhotos;
                    
                    echo '<br><div style="background:#f8f9fa; padding:8px; border-radius:4px; margin-top:5px; border-left:3px solid #28a745;">';
                    echo '<strong style="color:#28a745;">📸 ' . $nombrePhotos . ' photo' . ($nombrePhotos > 1 ? 's' : '') . ' uploadée' . ($nombrePhotos > 1 ? 's' : '') . '</strong>';
                    
                    // Affichage des informations de conditionnement
                    if ($conditionInt > 0 && $totalPhotosPayees > $nombrePhotos) {
                        echo '<br><small style="color:#ffc107; font-weight:500;">⚠️ Vous payez pour ' . $totalPhotosPayees . ' photos (conditionnement par ' . $conditionInt . ')</small>';
                    }
                    
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
                echo '<td><button class="btn-supprimer-panier" onclick="supprimerDuPanierPage(\'' . $id . '\')">🗑️</button></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
            // Calculs TVA, TTC, frais de port
            $tva = $totalHT * 0.20;
            $ttc = $totalHT + $tva;
            $fraisPort = ($totalHT > 200) ? 0 : 13.95;
            echo '<div class="recap-panier">';
            echo '<p style="display:flex; justify-content:space-between;">Total HT : <strong>' . number_format($totalHT, 2, ',', ' ') . ' €</strong></p>';
            echo '<p style="display:flex; justify-content:space-between;">TVA (20%) : <strong>' . number_format($tva, 2, ',', ' ') . ' €</strong></p>';
            echo '<hr>';
            echo '<p style="display:flex; justify-content:space-between;">Total TTC : <strong>' . number_format($ttc, 2, ',', ' ') . ' €</strong></p>';
            if ($fraisPort == 0) {
                echo '<p style="display:flex; justify-content:space-between;">Frais de port : <strong style="color:green">Gratuit</strong></p>';
            } else {
                echo '<p style="display:flex; justify-content:space-between;">Frais de port : <strong>' . number_format($fraisPort, 2, ',', ' ') . ' €</strong></p>';
            }
            echo '<hr>';
            echo '<p style="display:flex; justify-content:space-between;"><strong>Total à payer :</strong><strong>' . number_format($ttc + $fraisPort, 2, ',', ' ') . ' €</strong>
      </p>';
            echo '</div>';
            
            // Section de commande
            echo '<div class="panier-actions">';
            if (isset($_SESSION['client_id'])) {
                // Client connecté - afficher le bouton de commande
                echo '<form action="../clients/process-commande.php" method="POST" class="commande-form">';
                echo '<div class="commande-options">';
                echo '<h3>Finaliser votre commande</h3>';
                
                echo '<div class="option-group">';
                echo '<label for="mode_paiement">Mode de paiement :</label>';
                echo '<select name="mode_paiement" id="mode_paiement" required>';
                echo '<option value="carte_bancaire">Carte bancaire</option>';
                echo '<option value="paypal">PayPal</option>';
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
                
                echo '<button type="submit" class="btn-commander">Valider ma commande</button>';
                echo '</form>';
            } else {
                // Client non connecté - invitation à se connecter
                echo '<div class="connexion-required">';
                echo '<h3>Finaliser votre commande</h3>';
                echo '<p>Pour passer votre commande, vous devez être connecté.</p>';
                echo '<div class="connexion-actions">';
                echo '<a href="../clients/connexion.php" class="btn-connexion">Se connecter</a>';
                echo '<a href="../clients/creer-compte.php" class="btn-creer-compte">Créer un compte</a>';
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
            console.log('[DEBUG] Suppression de l\'article:', id);
            let panier = JSON.parse(localStorage.getItem('panier')) || [];
            console.log('[DEBUG] Panier avant suppression:', panier);
            panier = panier.filter(item => item.id !== id);
            console.log('[DEBUG] Panier après suppression:', panier);
            
            // Toujours sauvegarder même si le panier devient vide
            if (panier.length === 0) {
                localStorage.setItem('panier', '[]');
                console.log('[DEBUG] Panier vide, sauvegardé comme tableau vide');
            } else {
                localStorage.setItem('panier', JSON.stringify(panier));
            }
            
            localStorage.removeItem('panier_synced');
            
            // Synchronisation forcée puis rechargement
            fetch('/pages/sync_panier.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(panier)
            }).then(function(response) {
                console.log('[DEBUG] Synchronisation terminée, rechargement...');
                window.location.reload();
            }).catch(function(error) {
                console.log('[DEBUG] Erreur synchronisation, rechargement quand même...', error);
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
    } catch (e) {
        console.error('[Panier] Erreur', e);
    }
    </script>
</body>
</html>