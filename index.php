<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $seo_title = 'Accueil - Bindy Studio | Impression photo et albums personnalisés';
    $seo_description = 'Bindy Studio transforme vos souvenirs en albums photo et produits personnalisés de haute qualité. Impression professionnelle, finitions soignées et livraison rapide.';
    $seo_image = '/images/bandeaux/index.webp';
    $canonical = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    include 'includes/seo.php';
    ?>
    <link rel="icon" type="image/x-icon" href="images/logo-icon/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
    /* Désactiver les transforms CSS natifs sur les vignettes/images
       pour que seul le script gère les rotations (effet 'regard'). */
    .vignette-index,
    .vignette-index:hover,
    .vignette-index:active {
        transform: none !important;
    }
    .vignette-index .image-index,
    .vignette-index .image-index:hover,
    .vignette-index .image-index:active {
        transition: transform 220ms cubic-bezier(.2,.8,.2,1);
        transform-style: preserve-3d;
        backface-visibility: hidden;
        will-change: transform;
    }
    </style>
    <script src="js/script.js" defer></script>
    <script src="js/site-search.js" defer></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="cadre">
        <div class="container">
            <h1 class="title-h1">Imprimez vos souvenirs sur tous nos supports, de l'album photo </br>aux couvertures personnalisés, toute une gamme de produits variés</h1>
        </div>
            <img class="centre-div" style="width: 100%;" src="../images/bandeaux/index.webp" alt="Un bandeau présentant divers produits d'impression et de personnalisation" loading="lazy">
            <p class="subtitle">Découvrez nos services d'impression et de personnalisation.</p>

        <div class="container">    
            <div class="ligne">
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>album.php">
                        <img class="image-index" src="../images/bandeaux/album.webp" width="375px" height="250px" alt="Sur une table sont présentés 3 albums photos personnalisés avec des photos imprimées en couverture." loading="lazy">
                        <span class="span-index">Album Photo</span>
                        <p class="text-index">Créer un livre photo personnalisé pour partager tous vos souvenir avec vos proches.</p>
                    </a>
                </div>
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>calendrier-mural.php">
                        <img class="image-index" src="../images/bandeaux/calendrier.webp" width="375px" height="250px" alt="Photo d'un calendrier de couleur noir et personnalisé avec une photo." loading="lazy">
                        <span class="span-index">Calendrier</span>
                        <p class="text-index">Créer un calendrier personnalisé pour partager vos moments importants tout au long de l'année.</p>
                    </a>
                </div>
            </div>
            <div class="ligne">
                <div class="vignette-index">
                    <img class="image-index" src="../images/bandeaux/photo.webp" width="375px" height="250px" alt="Sur une table sont présentés 3 albums photos personnalisés avec des photos imprimées en couverture." loading="lazy">
                    <span class="span-index">Tirages Photo</span>
                    <p class="text-index">Notre service d'impression de photo et là pour immortaliser vos moments précieux avec des tirages de haute qualité.</p>
                </div>
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>panneau-photo.php">
                        <img class="image-index" src="../images/bandeaux/panneau.webp" width="375px" height="250px" alt="Photo d'un calendrier de couleur noir et personnalisé avec une photo." loading="lazy">
                        <span class="span-index">Panneaux Photo</span>
                        <p class="text-index">Toute une gamme de panneaux photo personnalisés pour embellir votre intérieur ou votre espace professionnel.</p>
                    </a>
                </div>
            </div>
            
            
        </div>
    </main>
    <script>
    // Ouvrir le menu 'Tirage Photos' au clic sur la vignette de la page d'accueil
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const vignettes = document.querySelectorAll('.vignette-index');
            let cible = null;
            vignettes.forEach(v => {
                const label = v.querySelector('.span-index');
                if (label && label.textContent.trim().toLowerCase().includes('tirage')) {
                    cible = v;
                }
            });

            if (!cible) return;

            cible.style.cursor = 'pointer';
            cible.addEventListener('click', function (e) {
                e.preventDefault();
                // Trouver le bouton de navigation correspondant (texte contient 'Tirage')
                const navBtns = document.querySelectorAll('.nav-btn');
                let targetBtn = null;
                navBtns.forEach(btn => {
                    if (btn.textContent.trim().toLowerCase().includes('tirage')) {
                        targetBtn = btn;
                    }
                });

                if (!targetBtn) {
                    console.warn('Bouton Tirage non trouvé');
                    return;
                }

                const navItem = targetBtn.closest('.nav-item');
                const dropdown = navItem ? navItem.querySelector('.dropdown-menu') : null;

                // Sur mobile, simuler le click pour ouvrir le menu
                if (window.innerWidth <= 768) {
                    targetBtn.click();
                    return;
                }

                // Sur desktop, forcer l'affichage du menu
                if (dropdown) {
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                    // Fermer après 6 secondes si l'utilisateur ne fait rien
                    setTimeout(() => {
                        dropdown.style.opacity = '';
                        dropdown.style.visibility = '';
                    }, 6000);
                }
            });
        } catch (err) {
            console.error('Erreur ouverture menu Tirage:', err);
        }
    });

document.addEventListener('DOMContentLoaded', function() {
    const card = document.querySelectorAll('.vignette-index');

    card.forEach(el => {
    // Cibler de manière robuste l'élément image et l'overlay
    const media = el.querySelector('.image-index') || el.querySelector('img');
    const overlay = el.querySelector('.span-index') || el.querySelector('.overlay') || null;

    if (media) {
        // Protéger l'image contre les déformations visuelles
        media.style.display = media.style.display || 'block';
        media.style.width = media.style.width || '100%';
        media.style.height = media.style.height || 'auto';
        media.style.transition = media.style.transition || 'transform 200ms ease';
        media.style.transformStyle = 'preserve-3d';
        media.style.backfaceVisibility = 'hidden';
        media.style.willChange = 'transform';
    }

    el.addEventListener('mousemove', e => {
        if (!media) return;
        const elRect = el.getBoundingClientRect();
        const x = e.clientX - elRect.x;
        const y = e.clientY - elRect.y;

        const midCardWidth = elRect.width / 2;
        const midCardHeight = elRect.height / 2;

        // Douces rotations 3D (quelques degrés)
        const angleY = -(x - midCardWidth) / 10; // diviser par 10 pour adoucir
        const angleX = (y - midCardHeight) / 10;

        const glowX = x / elRect.width * 100;
        const glowY = y / elRect.height * 100;

        // Appliquer transformations sans scale pour éviter zoom
        media.style.transform = `rotateX(${angleX}deg) rotateY(${angleY}deg)`;

        // Ne pas modifier le bouton/label (.span-index) : le laisser à l'état d'origine
    });

    el.addEventListener('mouseleave', () => {
        if (media) media.style.transform = 'rotateX(0deg) rotateY(0deg)';
        if (overlay) overlay.style.transform = 'rotateX(0deg) rotateY(0deg)';
    });
    });
});

    </script>
    <?php include 'includes/footer.php'; ?>
