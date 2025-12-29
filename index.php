<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo | Accueil</title>
    <link rel="icon" type="image/x-icon" href="images/logo-icon/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/script.js" defer></script>
    <script src="js/site-search.js" defer></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="cadre">
        <div class="container">
            <h1 class="title-h1">Imprimez vos souvenirs sur tous nos supports, de l'album photo </br>aux couvertures personnalisés, toute une gamme de produits variés</h1>
        </div>
            <img class="centre-div" style="width: 100%;" src="../images/bandeaux/index.webp" alt="Un bandeau présentant divers produits d'impression et de personnalisation">
            <p class="subtitle">Découvrez nos services d'impression et de personnalisation.</p>

        <div class="container">    
            <div class="ligne">
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>album.php">
                        <img class="image-index" src="../images/bandeaux/album.webp" width="375px" height="250px" alt="Sur une table sont présentés 3 albums photos personnalisés avec des photos imprimées en couverture.">
                        <span class="span-index">Album Photo</span>
                        <p class="text-index">Créer un livre photo personnalisé pour partager tous vos souvenir avec vos proches.</p>
                    </a>
                </div>
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>calendrier-mural.php">
                        <img class="image-index" src="../images/bandeaux/calendrier.webp" width="375px" height="250px" alt="Photo d'un calendrier de couleur noir et personnalisé avec une photo.">
                        <span class="span-index">Calendrier</span>
                        <p class="text-index">Créer un calendrier personnalisé pour partager vos moments importants tout au long de l'année.</p>
                    </a>
                </div>
            </div>
            <div class="ligne">
                <div class="vignette-index">
                    <img class="image-index" src="../images/bandeaux/photo.webp" width="375px" height="250px" alt="Sur une table sont présentés 3 albums photos personnalisés avec des photos imprimées en couverture.">
                    <span class="span-index">Tirages Photo</span>
                    <p class="text-index">Notre service d'impression de photo et là pour immortaliser vos moments précieux avec des tirages de haute qualité.</p>
                </div>
                <div class="vignette-index">
                    <a style="text-decoration: none;" href="<?php echo $pagesPath; ?>panneau-photo.php">
                        <img class="image-index" src="../images/bandeaux/panneau.webp" width="375px" height="250px" alt="Photo d'un calendrier de couleur noir et personnalisé avec une photo.">
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
    </script>
    <?php include 'includes/footer.php'; ?>
