<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemple d'intégration du tableau</title>
    
    <!-- IMPORTANT: Inclure les CSS nécessaires -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tableau.css">
    <link rel="stylesheet" href="css/panier.css">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .ma-page {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-titre {
            background: #2A256D;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .panier-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-panier {
            background: #F05124;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
        }
        
        .btn-panier:hover {
            background: #d43f1a;
        }
    </style>
</head>
<body>
    <div class="ma-page">
        
        <!-- En-tête de ma page -->
        <div class="section-titre">
            <h1>Ma Page avec Tableau Intégré</h1>
            <p>Exemple d'intégration du tableau des classeurs</p>
        </div>
        
        <!-- Header avec panier -->
        <div class="panier-header">
            <div>
                <h2>Nos Classeurs</h2>
                <p>Sélectionnez vos produits ci-dessous</p>
            </div>
            <button class="btn-panier" onclick="ouvrirPanier()">
                🛒 Panier <span class="compteur-panier">0</span>
            </button>
        </div>
        
        <!-- MÉTHODE 1: Inclusion directe PHP (côté serveur) -->
        <div class="tableau-container">
            <?php
            // IMPORTANT: Ajuster le chemin selon votre structure
            require_once __DIR__ . '/includes/tableau.php';
            
            // Afficher les classeurs directement
            afficherTableauProduits('RELI');
            ?>
        </div>
        
        <!-- 
        MÉTHODE 2: Si vous voulez changer dynamiquement, ajoutez des boutons :
        <div style="margin: 20px 0; text-align: center;">
            <button class="btn-famille" data-famille="CLAS">Classeurs</button>
            <button class="btn-famille" data-famille="RELI">Reliure</button>
            <button class="btn-famille" data-famille="CART">Cartonnage</button>
        </div>
        
        <div id="tableau-dynamique">
            <!-- Le contenu sera chargé ici via AJAX -->
        </div>
        -->
        
    </div>

    <!-- IMPORTANT: Inclure le JavaScript du panier -->
    <script src="js/panier.js"></script>
    
    <!-- Script pour méthode dynamique (optionnel) -->
    <script>
        // Si vous utilisez la méthode 2 (boutons dynamiques), décommentez :
        /*
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-famille').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const famille = this.dataset.famille;
                    
                    // Mise à jour visuelle
                    document.querySelectorAll('.btn-famille').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Chargement AJAX
                    try {
                        const response = await fetch(`includes/tableau.php?famille=${famille}`);
                        const html = await response.text();
                        document.getElementById('tableau-dynamique').innerHTML = html;
                    } catch (error) {
                        console.error('Erreur chargement:', error);
                    }
                });
            });
            
            // Charger les classeurs par défaut
            const btnClas = document.querySelector('.btn-famille[data-famille="CLAS"]');
            if (btnClas) btnClas.click();
        });
        */
    </script>
</body>
</html>