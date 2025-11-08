<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo - Impression et Personnalisation</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <!-- Bandeau principal -->
        <div class="top-banner">
            <div class="container">
                <div class="top-banner-content">
                    <!-- Logo -->
                    <div class="logo">
                        <img src="../images/logo.svg" alt="Logo" class="logo-img">
                    </div>
                    
                    <!-- Phrase centrale -->
                    <div class="tagline">
                        <h1>Nous imprimons pour vous</h1>
                    </div>
                    
                    <!-- Zone actions (recherche, compte, panier) -->
                    <div class="header-actions">
                        <!-- Barre de recherche -->
                        <div class="search-bar">
                            <input type="text" placeholder="Rechercher..." class="search-input">
                            <button class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Bouton Compte -->
                        <div class="account-btn">
                            <button class="btn-account">
                                <i class="fas fa-user"></i>
                                <span>Compte</span>
                            </button>
                        </div>
                        
                        <!-- Panier -->
                        <div class="cart">
                            <button class="btn-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count">0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bandeau de navigation -->
        <div class="navigation-banner">
            <div class="container">
                <nav class="main-nav">
                    <!-- Bouton Photo -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="photo">Photo</button>
                        <div class="dropdown-menu" id="photo-menu">
                            <a href="livre-photo.php" class="dropdown-item">Livre Photo</a>
                            <a href="livre-dela.php" class="dropdown-item">Livre Photo Delà</a>
                            <a href="album.php" class="dropdown-item">Album Photos</a>
                            <a href="#" class="dropdown-item">Dépliant Accordéon</a>
                            <a href="toile.php" class="dropdown-item">Impression sur Toile</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Calendrier -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="calendrier">Calendrier</button>
                        <div class="dropdown-menu" id="calendrier-menu">
                            <a href="calendrier-bureau.php" class="dropdown-item">Calendrier de bureau</a>
                            <a href="calendrier-mural.php" class="dropdown-item">Calendrier Mural</a>
                            <a href="calendrier-glissant.php" class="dropdown-item">Calendrier Glissant</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Personnalisation -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="personnalisation">Personnalisation</button>
                        <div class="dropdown-menu" id="personnalisation-menu">
                            <a href="luxe.php" class="dropdown-item">Finition Luxe</a>
                            <a href="couverture-souple.php" class="dropdown-item">Couverture Souple</a>
                            <a href="couverture-rigide.php" class="dropdown-item">Couverture Rigide</a>
                            <a href="couverture-panorama.php" class="dropdown-item">Couverture Panorama</a>
                            <a href="pochette.php" class="dropdown-item">Pochette de prospection</a>
                            <a href="metal.php" class="dropdown-item">Impression sur métal</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Cadeaux -->
                    <div class="nav-item">
                        <button class="nav-btn" data-menu="cadeaux">Cadeaux</button>
                        <div class="dropdown-menu" id="cadeaux-menu">
                            <a href="magnet.php" class="dropdown-item">Magnets</a>
                            <a href="panneau-bambou.php" class="dropdown-item">Panneaux Bambou</a>
                            <a href="panneau-acrylique.php" class="dropdown-item">Panneaux Acrylique</a>
                            <a href="panneau-photo.php" class="dropdown-item">Panneaux Photo</a>
                            <a href="boite-a5.php" class="dropdown-item">Boîte personnalisé A5</a>
                            <a href="boite-a4.php" class="dropdown-item">Boîte personnalisé A4</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <script src="../js/header.js"></script>