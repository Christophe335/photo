<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Produits - Reliure Personnalisée</title>
    
    <!-- Polices -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="css/tableau.css">
    <link rel="stylesheet" href="css/panier.css">
    
    <style>
        /* Styles généraux de la page */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: var(--primary-dark);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 500;
        }
        
        .navigation {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .navigation a {
            color: white;
            text-decoration: none;
            font-weight: 400;
            transition: color 0.2s ease;
        }
        
        .navigation a:hover {
            color: var(--primary-orange);
        }
        
        /* Container principal */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Sélecteur de famille */
        .famille-selector {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .famille-selector h2 {
            color: var(--primary-dark);
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .famille-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-famille {
            background: var(--background-light);
            border: 2px solid var(--border-color);
            color: var(--primary-dark);
            padding: 12px 24px;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-famille:hover,
        .btn-famille.active {
            background: var(--primary-dark);
            color: white;
            border-color: var(--primary-dark);
        }
        
        /* Zone de contenu */
        .contenu-catalogue {
            min-height: 400px;
        }
        
        /* Footer */
        .footer {
            background: var(--primary-dark);
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
        
        /* Loading spinner */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        
        .spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--primary-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .navigation {
                gap: 20px;
            }
            
            .famille-buttons {
                gap: 10px;
            }
            
            .btn-famille {
                padding: 10px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>Reliure Personnalisée</h1>
            </div>
            <nav class="navigation">
                <a href="#" onclick="ouvrirPanier()">
                    <span class="icone-panier">🛒 <span class="compteur-panier">0</span></span>
                    Panier
                </a>
                <a href="#">Contact</a>
                <a href="#">Mon Compte</a>
            </nav>
        </div>
    </header>

    <!-- Container principal -->
    <div class="container">
        
        <!-- Sélecteur de famille -->
        <div class="famille-selector">
            <h2>Choisissez une famille de produits</h2>
            <div class="famille-buttons">
                <button class="btn-famille" data-famille="CLAS">Classeurs</button>
                <button class="btn-famille" data-famille="RELI">Reliure</button>
                <button class="btn-famille" data-famille="CART">Cartonnage</button>
                <button class="btn-famille" data-famille="ACCE">Accessoires</button>
                <button class="btn-famille" data-famille="OUTI">Outils</button>
            </div>
        </div>
        
        <!-- Zone de contenu -->
        <div id="contenu-catalogue" class="contenu-catalogue">
            <div class="loading" style="display: none;">
                <div class="spinner"></div>
                <p>Chargement des produits...</p>
            </div>
            
            <div class="message-accueil" style="text-align: center; padding: 60px 20px; color: #666;">
                <h3>Bienvenue dans notre catalogue</h3>
                <p>Sélectionnez une famille de produits pour commencer votre commande.</p>
            </div>
        </div>
        
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Reliure Personnalisée - Tous droits réservés</p>
    </footer>

    <!-- Scripts -->
    <script src="js/panier.js"></script>
    
    <script>
        /**
         * Charge les produits d'une famille
         */
        async function chargerFamille(codeFamille, btnElement) {
            console.log('Chargement famille:', codeFamille);
            
            // Mise à jour de l'interface
            document.querySelectorAll('.btn-famille').forEach(btn => {
                btn.classList.remove('active');
            });
            if (btnElement) btnElement.classList.add('active');
            
            // Affichage du loading
            const contenu = document.getElementById('contenu-catalogue');
            const loading = document.querySelector('.loading');
            const messageAccueil = document.querySelector('.message-accueil');
            
            if (messageAccueil) messageAccueil.style.display = 'none';
            loading.style.display = 'block';
            
            try {
                // Appel AJAX pour charger le tableau
                const url = `includes/tableau.php?famille=${codeFamille}`;
                console.log('URL appelée:', url);
                
                const response = await fetch(url);
                console.log('Réponse reçue:', response.status, response.statusText);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const html = await response.text();
                console.log('HTML reçu (longueur):', html.length);
                console.log('Début du HTML:', html.substring(0, 200));
                
                // Masquer le loading
                loading.style.display = 'none';
                
                // Afficher le contenu
                contenu.innerHTML = html;
                
                // Animation d'apparition
                const tableau = contenu.querySelector('.tableau-produits');
                if (tableau) {
                    console.log('Tableau trouvé, animation en cours');
                    tableau.style.opacity = '0';
                    setTimeout(() => {
                        tableau.style.transition = 'opacity 0.3s ease';
                        tableau.style.opacity = '1';
                    }, 50);
                } else {
                    console.warn('Aucun tableau trouvé dans le HTML reçu');
                }
                
            } catch (error) {
                console.error('Erreur lors du chargement:', error);
                loading.style.display = 'none';
                contenu.innerHTML = `<div class="erreur">Erreur lors du chargement des produits: ${error.message}</div>`;
            }
        }
        
        /**
         * Initialisation de la page
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Ajout des event listeners sur les boutons de famille
            document.querySelectorAll('.btn-famille').forEach(btn => {
                btn.addEventListener('click', function() {
                    const famille = this.dataset.famille;
                    chargerFamille(famille, this);
                });
            });
            
            // Charger automatiquement les classeurs au démarrage
            setTimeout(() => {
                const btnClasseurs = document.querySelector('.btn-famille[data-famille="CLAS"]');
                if (btnClasseurs) {
                    btnClasseurs.click();
                }
            }, 500);
        });
    </script>
</body>
</html>