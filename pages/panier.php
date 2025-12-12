<?php
session_start();
// Initialisation du panier si besoin
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
// ...affichage du panier à compléter plus tard...
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
    <h1>Votre panier</h1>
    <div id="panier-content">
        <?php
        $panier = $_SESSION['panier'];
        if (empty($panier)) {
            echo '<p>Votre panier est vide.</p>';
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