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
            echo '<thead><tr><th>Référence</th><th>Désignation</th><th>Qté</th><th>PU HT</th><th>Total HT</th><th>Action</th></tr></thead><tbody>';
            $totalHT = 0;
            foreach ($panier as $index => $item) {
                $reference = htmlspecialchars($item['details']['code'] ?? '');
                $designation = htmlspecialchars($item['details']['designation'] ?? '');
                $format = htmlspecialchars($item['details']['format'] ?? '');
                $couleur = htmlspecialchars($item['details']['couleur'] ?? '');
                $quantite = (int)$item['quantite'];
                $prix = number_format($item['prix'], 2, ',', ' ');
                $total = $item['prix'] * $quantite;
                $totalHT += $total;
                $id = htmlspecialchars($item['id']);
                echo '<tr data-id="' . $id . '">';
                // Colonne Référence
                echo '<td>' . $reference . '</td>';
                // Colonne Désignation
                echo '<td>' . $designation;
                if ($format) echo '<br><span style="color:#666;font-size:13px">Format : ' . $format . '</span>';
                if ($couleur) echo '<br><span style="color:#666;font-size:13px">Couleur : ' . $couleur . '</span>';
                echo '</td>';
                // Colonne Quantité
                echo '<td><span class="quantite-panier">' . $quantite . '</span></td>';
                // Colonne PU HT
                echo '<td>' . $prix . ' €</td>';
                // Colonne Total HT
                echo '<td>' . number_format($total, 2, ',', ' ') . ' €</td>';
                // Colonne Action
                echo '<td><button class="btn-supprimer-panier" onclick="supprimerDuPanier(\'' . $id . '\')">🗑️</button></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div>';
            // Calculs TVA, TTC, frais de port
            $tva = $totalHT * 0.20;
            $ttc = $totalHT + $tva;
            $fraisPort = ($totalHT > 200) ? 0 : 13.95;
            echo '<div class="recap-panier">';
            echo '<p>Total HT : <strong>' . number_format($totalHT, 2, ',', ' ') . ' €</strong></p>';
            echo '<p>TVA (20%) : <strong>' . number_format($tva, 2, ',', ' ') . ' €</strong></p>';
            echo '<p>Total TTC : <strong>' . number_format($ttc, 2, ',', ' ') . ' €</strong></p>';
            if ($fraisPort == 0) {
                echo '<p>Frais de port : <strong style="color:green">Gratuit</strong></p>';
            } else {
                echo '<p>Frais de port : <strong>' . number_format($fraisPort, 2, ',', ' ') . ' €</strong></p>';
            }
            echo '<p><strong>Total à payer : ' . number_format($ttc + $fraisPort, 2, ',', ' ') . ' €</strong></p>';
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
                            // Recharger la page uniquement si le panier PHP était vide avant
                            var panierContent = document.querySelector('#panier-content p');
                            if (panierContent && panierContent.textContent.includes('vide')) {
                                window.location.reload();
                            }
                        }
                    });
                });
            }
        }
        window.addEventListener('DOMContentLoaded', function() {
            localStorage.removeItem('panier_synced'); // Toujours supprimer le flag au chargement
            var panierJS = localStorage.getItem('panier');
            if (panierJS && JSON.parse(panierJS).length > 0) {
                syncPanierToSession();
            }
        });
        // Fonctions JS pour modifier/supprimer dans le panier
        function modifierQuantitePanier(id, delta) {
            let panier = JSON.parse(localStorage.getItem('panier') || '[]');
            let item = panier.find(p => p.id == id);
            if (item) {
                item.quantite += delta;
                if (item.quantite < 1) item.quantite = 1;
                localStorage.setItem('panier', JSON.stringify(panier));
                localStorage.removeItem('panier_synced');
                syncPanierToSession();
            }
        }
        function supprimerDuPanier(id) {
            let panier = JSON.parse(localStorage.getItem('panier') || '[]');
            panier = panier.filter(p => p.id != id);
            localStorage.setItem('panier', JSON.stringify(panier));
            localStorage.removeItem('panier_synced');
            syncPanierToSession();
        }
    } catch(e) {
        console.error('Erreur JS panier :', e);
    }
    </script>
  
    </script>
</body>
</html>