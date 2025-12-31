<?php
// Détection automatique du niveau de profondeur
$currentPath = $_SERVER['PHP_SELF'];
$pathParts = explode('/', $currentPath);
$isInSubfolder = (count($pathParts) > 2 && !in_array('index.php', $pathParts));

// Définir les chemins selon l'emplacement
$basePath = $isInSubfolder ? '../' : '';
$cssPath = $basePath . 'css/';
$jsPath = $basePath . 'js/';
$imagesPath = $basePath . 'images/';
$indexPath = $isInSubfolder ? '../index.php' : 'index.php';
$pagesPath = $isInSubfolder ? '../pages/' : 'pages/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo - Impression et Personnalisation</title>
    <link rel="stylesheet" href="<?php echo $cssPath; ?>style.css">
    <link rel="stylesheet" href="<?php echo $cssPath; ?>responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Gestion des cookies RGPD -->
    <script src="<?php echo $jsPath; ?>cookie-manager.js" defer></script>
    <script src="<?php echo $jsPath; ?>site-search.js" defer></script>
</head>
<body>
    <header class="header">
        <!-- Bandeau principal -->
        <div class="top-banner">
            <div class="container">
                <div class="top-banner-content">
                    <div class="top-row">
                        <!-- Logo -->
                        <a class="logo" href="<?php echo $indexPath; ?>">
                            <img src="<?php echo $imagesPath; ?>logo-icon/logo3.svg" alt="Logo" class="logo-img">
                        </a>

                        <!-- Coordonnées (icone + numéro seulement) -->
                        <div class="tagcontact">
                            <i class="fas fa-phone-alt" aria-hidden="true"></i>
                            <a class="lienTel" href="tel:0384783839">03 84 78 38 39</a>
                        </div>

                        <!-- Bouton burger (déplacé ici, à droite) -->
                        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Ouvrir le menu">
                            <i class="fas fa-bars" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="middle-row">
                        <!-- Phrase centrale (pleine largeur) -->
                        <div class="tagline">
                            <h1>Nous imprimons pour vous</h1>
                        </div>
                    </div>

                    <div class="bottom-row">
                        <!-- Zone actions (recherche, compte, panier) -->
                        <div class="header-actions">
                            <!-- Barre de recherche -->
                            <div class="search-bar">
                                <input type="text" id="site-search-input" placeholder="Recherche par thème..." class="search-input">
                                <button class="search-btn" id="site-search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            
                            <!-- Bouton Compte -->
                            <div class="account-btn">
                                <a href="<?php echo $basePath; ?>compte.php" class="btn-account">
                                    <i class="fas fa-user"></i>
                                    <span>Compte</span>
                                </a>
                            </div>
                            <!-- Bouton Contact -->
                            <div class="account-btn">
                                <a href="<?php echo $basePath; ?>formulaires/contact.php" class="btn-account">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>Contact</span>
                                </a>
                            </div>
                            <!-- Bouton Devis -->
                            <div class="account-btn">
                                <a href="<?php echo $basePath; ?>formulaires/devis.php" class="btn-account">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>Devis</span>
                                </a>
                            </div>
                            <!-- Panier -->
                            <div class="cart">
                                <a href="/pages/panier.php" class="btn-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-count">0</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bandeau de navigation -->
        <div class="navigation-banner">
            <nav class="main-nav">
                    <!-- Indicateur de page actuelle sous forme de pseudo-bouton -->
                    <?php
                    // Récupération du nom de la page actuelle
                    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
                    $currentPath = $_SERVER['PHP_SELF'];
                    
                    // Tableau des titres des pages
                    $pageTitles = [
                        'index' => 'Accueil',
                        'catalogue' => 'Catalogue',
                        'compte' => 'Mon Compte',
                        'panier' => 'Mon Panier',
                        'qui-sommes-nous' => 'Qui sommes-nous ?',
                        'mentions' => 'Mentions légales',
                        'politique' => 'Politique de confidentialité',
                        // Pages de tirage
                        'tirage-petit' => 'Tirages petit format',
                        'tirage-grand' => 'Tirages grand format',
                        'tirage-xxl' => 'Tirages XXL',
                        // Pages couvertures
                        'luxe' => 'Finition Luxe',
                        'couverture-souple' => 'Couverture Souple',
                        'couverture-rigide' => 'Couverture Rigide',
                        // Pages panneaux/boîtes
                        'panneau-bambou' => 'Panneaux Bambou',
                        'panneau-acrylique' => 'Panneaux Acrylique',
                        'panneau-photo' => 'Panneaux Photo',
                        'boite-a5' => 'Boîte A5',
                        'boite-a4' => 'Boîte A4',
                        'metal' => 'Alu-Print',
                        'magnet' => 'Magnets',
                        // Pages cadeaux
                        'album' => 'Albums Photos',
                        'calendrier' => 'Calendriers',
                        'calendrier-glissant' => 'Calendrier Glissant',
                        'infinity' => 'Infinity',
                        'toile' => 'Toiles',
                        'pochette' => 'Pochettes',
                        // Pages personnalisation
                        'couverture-rigide-perso' => 'Couverture Rigide Personnalisé',
                        'couverture-panorama-perso' => 'Panorama Personnalisé',
                        'couverture-souple-perso' => 'Couverture Souple Personnalisé',
                        'album-perso' => 'Album Photos Personnalisé',
                        'pochette-perso' => 'Pochettes Personnalisé',
                        'boite-a5-perso' => 'Boîte A5 Personnalisé',
                        'boite-a4-perso' => 'Boîte A4 Personnalisé',
                        'infinity-perso' => 'Infinity Personnalisé',
                        'toile-perso' => 'Toile Personnalisé',
                        // Pages formulaires
                        'photo' => 'Upload photos',
                        'perso' => 'Upload personnalisation',
                        'contact' => 'Contact',
                        'devis' => 'Demande de devis',
                        // Pages clients
                        'connexion' => 'Connexion',
                        'creer-compte' => 'Créer un compte',
                        'mon-compte' => 'Mon compte',
                        'mot-de-passe-oublie' => 'Mot de passe oublié',
                        'reset-password' => 'Réinitialiser le mot de passe'
                    ];
                    
                    // Récupération du titre de la page
                    $pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : ucfirst(str_replace('-', ' ', $currentPage));
                    
                    // Affichage du fil d'Ariane sous forme de pseudo-bouton (affiché aussi sur la page d'accueil)
                    echo '<div class="nav-item breadcrumb-item">'
                        . '<div class="breadcrumb-btn">Vous êtes ici : <strong>' . htmlspecialchars($pageTitle) . '</strong></div>'
                        . '</div>';
                    ?>
                    
                    <!-- Bouton Tirage Photos -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="photo">Tirage Photos</button>
                        <div class="dropdown-menu tirage-menu" id="photo-menu">
                            <!-- Groupe Petit Format -->
                            <div class="format-group">
                                <div class="group-items">
                                    <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="dropdown-item">10 x 15 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="dropdown-item">12 x 12 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="dropdown-item">13 x 18 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="dropdown-item">15 x 15 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="dropdown-item">15 x 20 cm</a>
                                </div>
                                <div class="group-label">Petit Format</div>
                            </div>
                            
                            <!-- Groupe Grand Format -->
                            <div class="format-group">
                                <div class="group-items">
                                    <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="dropdown-item">20 x 20 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="dropdown-item">20 x 25 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="dropdown-item">20 x 30 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="dropdown-item">21 x 21 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="dropdown-item">25 x 25 cm</a>
                                </div>
                                <div class="group-label">Grand Format</div>
                            </div>
                            
                            <!-- Groupe A4/A3 -->
                            <div class="format-group">
                                <div class="group-items">
                                    <a href="<?php echo $pagesPath; ?>tirage-A4A3.php" class="dropdown-item">A4 - 21 x 29.7 cm</a>
                                    <a href="<?php echo $pagesPath; ?>tirage-A4A3.php" class="dropdown-item">A3 - 29.7 x 42 cm</a>
                                </div>
                                <div class="group-label">Format A4/A3</div>
                            </div>
                            
                            <!-- Séparateur -->
                            <div class="menu-separator"></div>
                            
                            <!-- Personnalisé -->
                            <a href="<?php echo $pagesPath; ?>tirage-Perso.php" class="dropdown-item personalize-item">Personnalisé</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Livres / Albums -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="photo">Livres / Albums</button>
                        <div class="dropdown-menu" id="photo-menu">
                            <a href="<?php echo $pagesPath; ?>livre-photo.php" class="dropdown-item">Livre Photos</a>
                            <a href="<?php echo $pagesPath; ?>album.php" class="dropdown-item">Album Photos</a>
                            <a href="<?php echo $pagesPath; ?>infinity.php" class="dropdown-item">Photo Book Infinity</a>
                            <a href="<?php echo $pagesPath; ?>toile.php" class="dropdown-item">Impression sur Toile</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Calendriers -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="calendrier">Calendriers</button>
                        <div class="dropdown-menu" id="calendrier-menu">
                            <a href="<?php echo $pagesPath; ?>calendrier-bureau.php" class="dropdown-item">Calendrier de bureau</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-mural.php" class="dropdown-item">Calendrier Mural</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-glissant.php" class="dropdown-item">Calendrier Glissant</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Couvertures -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="personnalisation">Couvertures</button>
                        <div class="dropdown-menu" id="personnalisation-menu"> 
                            <a href="<?php echo $pagesPath; ?>couverture-souple.php" class="dropdown-item">Couverture Souple</a> 
                            <a href="<?php echo $pagesPath; ?>couverture-rigide.php" class="dropdown-item">Couverture Rigide</a>
                            <a href="<?php echo $pagesPath; ?>classeur.php" class="dropdown-item">Classeur</a>
                            <a href="<?php echo $pagesPath; ?>luxe.php" class="dropdown-item">Finition Luxe</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Panneaux / Boîtes -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="cadeaux">Panneaux / Coffrets</button>
                        <div class="dropdown-menu" id="cadeaux-menu">
                            <a href="<?php echo $pagesPath; ?>boite-a4.php" class="dropdown-item">Prestige Box A4</a>
                            <a href="<?php echo $pagesPath; ?>boite-a5.php" class="dropdown-item">Prestige Box A5</a>
                            <a href="<?php echo $pagesPath; ?>portfolio.php" class="dropdown-item">Portfolio Prestige</a>
                            <a href="<?php echo $pagesPath; ?>panneau-photo.php" class="dropdown-item">Panneaux Photo</a>
                            <a href="<?php echo $pagesPath; ?>panneau-bambou.php" class="dropdown-item">Panneaux Bambou</a>
                            <a href="<?php echo $pagesPath; ?>panneau-acrylique.php" class="dropdown-item">Panneaux Acrylique</a>
                            <a href="<?php echo $pagesPath; ?>metal.php" class="dropdown-item">Alu-Print</a>
                            <a href="<?php echo $pagesPath; ?>magnet.php" class="dropdown-item">Magnets</a>
                        </div>
                    </div>
                    <!-- Bouton Personnalisation -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="photo">Personnalisation</button>
                        <div class="dropdown-menu" id="photo-menu">
                            <div class="format-group">
                                <div class="group-items">
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/album-perso.php" class="dropdown-item">Album Photos</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/couverture-souple-perso.php" class="dropdown-item">Couverture Souple</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/couverture-rigide-perso.php" class="dropdown-item">Couverture Rigide</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/pochette-perso.php" class="dropdown-item">Chemise</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/boite-a4-perso.php" class="dropdown-item">Coffret Prestige A4</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/boite-a5-perso.php" class="dropdown-item">Coffret Prestige A5</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/classeur-perso.php" class="dropdown-item">Classeur</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/portfolio-perso.php" class="dropdown-item">PortFolio Prestige</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/infinity-perso.php" class="dropdown-item">Photo Book Prestige</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/luxe-perso.php" class="dropdown-item">Finition Luxe</a>
                                    <a href="<?php echo $pagesPath; ?>../pages-perso/toile-perso.php" class="dropdown-item">Toile</a>
                                </div>
                                <div class="group-label">Personnalisation</div>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- Menu mobile (contenu affiché par le burger) -->
                <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Tirage Photos <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="mobile-menu-link">10 x 15 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="mobile-menu-link">12 x 12 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="mobile-menu-link">13 x 18 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="mobile-menu-link">15 x 15 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-petit.php" class="mobile-menu-link">15 x 20 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="mobile-menu-link">20 x 20 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-grand.php" class="mobile-menu-link">20 x 25 cm</a>
                            <a href="<?php echo $pagesPath; ?>tirage-A4A3.php" class="mobile-menu-link">A4 / A3</a>
                            <a href="<?php echo $pagesPath; ?>tirage-Perso.php" class="mobile-menu-link">Personnalisé</a>
                        </div>
                    </div>

                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Livres / Albums <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>livre-photo.php" class="mobile-menu-link">Livre Photo</a>
                            <a href="<?php echo $pagesPath; ?>album.php" class="mobile-menu-link">Album Photos</a>
                            <a href="<?php echo $pagesPath; ?>infinity.php" class="mobile-menu-link">Dépliant Accordéon</a>
                            <a href="<?php echo $pagesPath; ?>toile.php" class="mobile-menu-link">Impression sur Toile</a>
                        </div>
                    </div>

                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Calendriers <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>calendrier-bureau.php" class="mobile-menu-link">Calendrier de bureau</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-mural.php" class="mobile-menu-link">Calendrier Mural</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-glissant.php" class="mobile-menu-link">Calendrier Glissant</a>
                        </div>
                    </div>

                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Couvertures <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>couverture-souple.php" class="mobile-menu-link">Couverture Souple</a>
                            <a href="<?php echo $pagesPath; ?>couverture-rigide.php" class="mobile-menu-link">Couverture Rigide</a>
                            <a href="<?php echo $pagesPath; ?>couverture-panorama.php" class="mobile-menu-link">Couverture Panorama</a>
                            <a href="<?php echo $pagesPath; ?>luxe.php" class="mobile-menu-link">Finition Luxe</a>
                        </div>
                    </div>

                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Panneaux / Boîtes <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>panneau-bambou.php" class="mobile-menu-link">Panneaux Bambou</a>
                            <a href="<?php echo $pagesPath; ?>panneau-acrylique.php" class="mobile-menu-link">Panneaux Acrylique</a>
                            <a href="<?php echo $pagesPath; ?>panneau-photo.php" class="mobile-menu-link">Panneaux Photo</a>
                            <a href="<?php echo $pagesPath; ?>boite-a5.php" class="mobile-menu-link">Boîte A5</a>
                            <a href="<?php echo $pagesPath; ?>boite-a4.php" class="mobile-menu-link">Boîte A4</a>
                        </div>
                    </div>

                    <div class="mobile-group">
                        <button class="mobile-group-title" aria-expanded="false">Personnalisation <span class="arrow">▾</span></button>
                        <div class="mobile-group-items">
                            <a href="<?php echo $pagesPath; ?>../pages-perso/album-perso.php" class="mobile-menu-link">Album Personnalisé</a>
                            <a href="<?php echo $pagesPath; ?>../pages-perso/couverture-rigide-perso.php" class="mobile-menu-link">Couv. Rigide Personnalisé</a>
                            <a href="<?php echo $pagesPath; ?>../pages-perso/infinity-perso.php" class="mobile-menu-link">Infinity Personnalisé</a>
                        </div>
                    </div>

                    <div class="mobile-menu-separator" aria-hidden="true"></div>
                    <a href="<?php echo $basePath; ?>compte.php" class="mobile-menu-link">Compte</a>
                    <a href="<?php echo $basePath; ?>formulaires/contact.php" class="mobile-menu-link">Contact</a>
                    <a href="<?php echo $basePath; ?>formulaires/devis.php" class="mobile-menu-link">Devis</a>
                </div>

                <!-- Boutons actions header (mode compact) -->
                    <div class="nav-actions-compact">
                        <a href="<?php echo $basePath; ?>clients/connexion.php" class="btn-account-compact">
                            <i class="fas fa-user"></i>
                            <span>Compte</span>
                        </a>
                        <a href="<?php echo $basePath; ?>formulaires/contact.php" class="btn-account-compact">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                        <a href="<?php echo $basePath; ?>formulaires/devis.php" class="btn-account-compact">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Devis</span>
                        </a>
                        <a href="/pages/panier.php" class="btn-cart-compact">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count">0</span>
                        </a>
                    </div>
            </div>
        </div>
    </header>
    
    <script src="<?php echo $jsPath; ?>header.js"></script>
