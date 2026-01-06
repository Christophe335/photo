<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $seo_title = 'Mentions légales - Bindy Studio';
    $seo_description = 'Mentions légales et informations légales de Bindy Studio, société spécialisée dans l\'impression photo et produits personnalisés.';
    $seo_image = '/images/logo-icon/logo3.svg';
    $canonical = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    include 'includes/seo.php';
    ?>
    <link rel="icon" type="image/x-icon" href="images/logo-icon/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="cadre legal-content">
        <div class="container">
            <h1 class="title-h1">Mentions Légales</h1>
            
            <div class="legal-section">
                <h2><i class="fas fa-info-circle"></i> Informations générales</h2>
                <div class="legal-content-box">
                    <h3>Éditeur du site</h3>
                    <p><strong>BINDY STUDIO</strong><br>
                    <p><strong>General Cover Office Products</strong><br>
                    Forme juridique : société à responsabilité limitée (SAS)<br>
                    Capital social : 24 960 €<br>
                    Siège social : 9 rue de la Gare, 70000 Vallerois le Bois (France).<br>
                    SIRET : 423 249 879 00010<br>
                    TVA intracommunautaire : FR55423249879000010<br>
                    Téléphone : 03 84 78 38 39<br>
                    Email : contact@general-cover.com</p>
                    
                    <h3>Directeur de la publication</h3>
                    <p>M. RENAUD Patrick</p>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-server"></i> Hébergement</h2>
                <div class="legal-content-box">
                    <p><strong>OVH Groupe SA</strong><br>
                    Adresse : 2 rue Kellermann - 59100 Roubaix - France<br>
                    Forme juridique : SAS au capital de 50 000 000 €<br>
                    Site web : www.ovhcloud.com/fr/</p>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-balance-scale"></i> Conditions d'utilisation</h2>
                <div class="legal-content-box">
                    <h3>Acceptation des conditions</h3>
                    <p>L'utilisation de ce site implique l'acceptation pleine et entière des conditions générales d'utilisation décrites ci-après. Ces conditions d'utilisation sont susceptibles d'être modifiées ou complétées à tout moment.</p>

                    <h3>Description des services</h3>
                    <p>Notre site propose des services d'impression photo et de personnalisation :</p>
                    <ul>
                        <li>Impression de photos sur différents supports</li>
                        <li>Création d'albums photo personnalisés</li>
                        <li>Impression sur panneaux décoratifs</li>
                        <li>Calendriers personnalisés</li>
                        <li>Autres produits de personnalisation photo</li>
                    </ul>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-copyright"></i> Propriété intellectuelle</h2>
                <div class="legal-content-box">
                    <h3>Droits sur le site</h3>
                    <p>La structure générale, les textes, images, sons, savoir-faire ainsi que tout élément composant le site sont la propriété de General cover ou de ses partenaires.</p>

                    <h3>Droits sur les images clients</h3>
                    <p><strong>Important :</strong> En utilisant nos services d'impression, vous garantissez :</p>
                    <ul>
                        <li>Être propriétaire des droits sur toutes les images transmises</li>
                        <li>Avoir l'autorisation d'utiliser les images contenant des personnes identifiables</li>
                        <li>Respecter le droit à l'image des personnes photographiées</li>
                        <li>Ne pas transmettre d'images protégées par des droits d'auteur sans autorisation</li>
                    </ul>
                    
                    <h3>Responsabilité du client</h3>
                    <p>Le client s'engage à :</p>
                    <ul>
                        <li>Ne pas utiliser d'images contraires à la loi ou aux bonnes mœurs</li>
                        <li>Respecter les droits de propriété intellectuelle de tiers</li>
                        <li>Indemniser General Cover en cas de réclamation de tiers</li>
                    </ul>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-shield-alt"></i> Limitation de responsabilité</h2>
                <div class="legal-content-box">
                    <p>Les informations contenues sur ce site sont aussi précises que possible et le site remis à jour à différentes périodes de l'année, mais peut toutefois contenir des inexactitudes ou des omissions.</p>
                    <p>General Cover ne pourra en aucun cas être tenue responsable de dommages directs ou indirects résultant de l'utilisation du site ou de l'impossibilité d'y accéder.</p>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-gavel"></i> Droit applicable et juridiction</h2>
                <div class="legal-content-box">
                    <p>Tout litige en relation avec l'utilisation du site est soumis au droit français. Il est fait attribution exclusive de juridiction au Tribunal de Commerce de VESOUL sera seul compétent.</p>
                </div>
            </div>

            <div class="legal-section">
                <h2><i class="fas fa-envelope"></i> Contact</h2>
                <div class="legal-content-box">
                    <p>Pour toute question relative aux mentions légales, vous pouvez nous contacter :</p>
                    <ul>
                        <li>Par email : contact@general-cover.com</li>
                        <li>Par téléphone : 03 84 78 38 39</li>
                        <li>Par courrier : General Cover Office Products - 9 rue de la gare - 70000 Vallerois-le-bois</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <style>
        .legal-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 40px 0;
        }

        .legal-section {
            margin-bottom: 40px;
        }

        .legal-section h2 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legal-content-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #2c5aa0;
            text-align: left;
        }

        .legal-content-box h3 {
            color: #333;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .legal-content-box h3:first-child {
            margin-top: 0;
        }

        .legal-content-box p {
            line-height: 1.6;
            margin-bottom: 15px;
            color: #555;
        }

        .legal-content-box ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .legal-content-box li {
            line-height: 1.6;
            margin-bottom: 8px;
            color: #555;
        }

        .legal-content-box strong {
            color: #2c5aa0;
            font-weight: 600;
        }

        /* Mise en évidence des éléments à compléter */
        .legal-content-box p strong:contains('[À COMPLÉTER'),
        .legal-content-box strong[style*="color: red"] {
            background-color: #fff3cd;
            color: #856404 !important;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #ffeaa7;
        }

        @media (max-width: 768px) {
            .legal-content {
                padding: 20px 0;
            }
            
            .legal-content-box {
                padding: 20px;
                margin: 0 10px;
            }
        }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
