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
                    <!-- Logo -->
                    <a class="logo" href="<?php echo $indexPath; ?>">
                        <img src="<?php echo $imagesPath; ?>logo-icon/logo3.svg" alt="Logo" class="logo-img">
                    </a>
                    <!-- Coordonnées -->
                    <div class="tagcontact">
                        <i class="fas fa-phone-alt"></i>
                        <span>Appelez-nous : <a class="lienTel" href="tel:0384783839">03 84 78 38 39</a></span>
                    </div>
                    <!-- Phrase centrale -->
                    <div class="tagline">
                        <h1>Nous imprimons pour vous</h1>
                    </div>
                    
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
        
        <!-- Bandeau de navigation -->
        <div class="navigation-banner">
            <nav class="main-nav">
                    <!-- Bouton Photo -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="photo">Photo</button>
                        <div class="dropdown-menu" id="photo-menu">
                            <a href="<?php echo $pagesPath; ?>livre-photo.php" class="dropdown-item">Livre Photo</a>
                            <a href="<?php echo $pagesPath; ?>album.php" class="dropdown-item">Album Photos</a>
                            <a href="<?php echo $pagesPath; ?>infinity.php" class="dropdown-item">Dépliant Accordéon</a>
                            <a href="<?php echo $pagesPath; ?>toile.php" class="dropdown-item">Impression sur Toile</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Calendrier -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="calendrier">Calendrier</button>
                        <div class="dropdown-menu" id="calendrier-menu">
                            <a href="<?php echo $pagesPath; ?>calendrier-bureau.php" class="dropdown-item">Calendrier de bureau</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-mural.php" class="dropdown-item">Calendrier Mural</a>
                            <a href="<?php echo $pagesPath; ?>calendrier-glissant.php" class="dropdown-item">Calendrier Glissant</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Personnalisation -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="personnalisation">Personnalisation</button>
                        <div class="dropdown-menu" id="personnalisation-menu">
                            <a href="<?php echo $pagesPath; ?>luxe.php" class="dropdown-item">Finition Luxe</a>
                            <a href="<?php echo $pagesPath; ?>couverture-souple.php" class="dropdown-item">Couverture Souple</a>
                            <a href="<?php echo $pagesPath; ?>couverture-rigide.php" class="dropdown-item">Couverture Rigide</a>
                            <a href="<?php echo $pagesPath; ?>couverture-panorama.php" class="dropdown-item">Couverture Panorama</a>
                            <a href="<?php echo $pagesPath; ?>pochette.php" class="dropdown-item">Pochette de prospection</a>
                            <a href="<?php echo $pagesPath; ?>metal.php" class="dropdown-item">Impression sur métal</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Cadeaux -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="cadeaux">Cadeaux</button>
                        <div class="dropdown-menu" id="cadeaux-menu">
                            <a href="<?php echo $pagesPath; ?>magnet.php" class="dropdown-item">Magnets</a>
                            <a href="<?php echo $pagesPath; ?>panneau-bambou.php" class="dropdown-item">Panneaux Bambou</a>
                            <a href="<?php echo $pagesPath; ?>panneau-acrylique.php" class="dropdown-item">Panneaux Acrylique</a>
                            <a href="<?php echo $pagesPath; ?>panneau-photo.php" class="dropdown-item">Panneaux Photo</a>
                            <a href="<?php echo $pagesPath; ?>boite-a5.php" class="dropdown-item">Boîte personnalisé A5</a>
                            <a href="<?php echo $pagesPath; ?>boite-a4.php" class="dropdown-item">Boîte personnalisé A4</a>
                        </div>
                    </div>
                </nav>
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
                                    <a href="/pages/panier.php" class="btn-cart-compact">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="cart-count">0</span>
                                    </a>
                                </div>
            </div>
        </div>
    </header>
    
    <script src="<?php echo $jsPath; ?>header.js"></script>
