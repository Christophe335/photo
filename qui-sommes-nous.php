<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $seo_title = 'Qui sommes-nous - Bindy Studio';
    $seo_description = 'Découvrez Bindy Studio : notre histoire, nos valeurs et notre savoir-faire pour sublimer vos photos en albums et produits personnalisés.';
    $seo_image = '/images/bandeaux/photo-album.webp';
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
    
    <main class="cadre about-content">
        <div class="container">
            <h1 class="title-h1">Qui sommes-nous ?</h1>
            
            <!-- Section Hero -->
            <div class="about-hero">
                <div class="hero-content">
                    <h2>Notre passion : donner vie à vos souvenirs</h2>
                    <p class="hero-text">Nous vous accompagnons dans la création de vos albums photo, de vos plus beaux voyages aux moments les plus précieux de votre vie. Chaque page est imprimée avec le plus grand soin afin de préserver vos souvenirs dans les moindres détails et de les faire vivre encore longtemps, pour vous accompagner et inspirer les histoires de demain.</p>
                </div>
                <div class="hero-image">
                    
                        <img src="../images/bandeaux/photo-album.webp" width="400px" height="auto" style="border-radius: 8px;" alt="Photo noir et blanc d'un album photo ouvert"  loading="lazy">
                        
                    
                </div>
            </div>

            <!-- Notre Histoire -->
            <div class="about-section">
                <h2><i class="fas fa-history"></i> Notre Histoire</h2>
                <div class="about-content-box">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h3>Notre histoire</h3>
                                <p>Bindy Studio est né d’une passion pour le livre, l’image et le savoir-faire artisanal. Spécialistes de la reliure, nous avons choisi de mettre notre expertise au service de la création d’albums photo, afin d’offrir aux particuliers et aux professionnels des ouvrages uniques, conçus pour traverser le temps. Chaque album est pensé comme un objet précieux, où la qualité des matériaux et le souci du détail transforment vos souvenirs en véritables livres d’histoires à conserver et à transmettre.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h3>Nos promesses</h3>
                                <p>Notre ambition est simple : sublimer vos souvenirs et leur offrir une place durable.</p>
                                <ul>
                                    <li>Un savoir-faire expert – La reliure est au cœur de notre métier. Chaque album est confectionné avec précision, dans le respect des techniques traditionnelles et des exigences contemporaines.</li>
                                    <li>Une qualité durable – Nous sélectionnons soigneusement nos papiers, couvertures et finitions pour garantir des albums solides, élégants et faits pour durer.</li>
                                    <li>Une expérience de confiance – Nous vous accompagnons à chaque étape de votre projet, avec exigence et attention, pour que le résultat final soit à la hauteur de vos attentes.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h3>Nos produits</h3>
                                <p>Bindy Studio conçoit et fabrique des albums photo reliés, pensés pour mettre en valeur vos moments les plus précieux. Albums de voyage, albums de famille, souvenirs de naissance ou projets professionnels : chaque création est réalisée sur mesure, avec une attention particulière portée à la reliure et aux finitions. Nos albums photo sont bien plus que de simples supports d’images : ce sont des objets durables, conçus pour raconter vos histoires, les préserver et les faire vivre au fil des années.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nos Valeurs -->
            <div class="about-section">
                <h2><i class="fas fa-heart"></i> Nos Valeurs</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>Qualité</h3>
                        <p>La qualité est au cœur de chacune de nos créations. De la sélection des matériaux à la précision de la reliure, chaque album est confectionné avec le plus grand soin afin de garantir un rendu élégant, durable et fidèle à vos souvenirs.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Service Client</h3>
                        <p>Nous plaçons l’écoute et l’accompagnement au centre de notre démarche. Notre équipe est à vos côtés à chaque étape, pour vous conseiller, vous guider et assurer une expérience fluide, humaine et personnalisée.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3>Responsabilité</h3>
                        <p>Conscients de notre impact, nous privilégions des pratiques responsables et des matériaux sélectionnés avec attention. Nous œuvrons chaque jour pour une production respectueuse, pensée pour durer et limiter le superflu.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3>Innovation</h3>
                        <p>Nous faisons évoluer notre savoir-faire en intégrant des solutions modernes et des techniques innovantes. Cette alliance entre tradition et innovation nous permet de créer des albums photo à la fois intemporels et adaptés aux attentes d’aujourd’hui.</p>
                    </div>
                </div>
            </div>

            <!-- Notre Équipe -->
            <!-- <div class="about-section">
                <h2><i class="fas fa-users-cog"></i> Notre Équipe</h2>
                <div class="about-content-box">
                    <p>[À COMPLÉTER : Présentation de votre équipe]</p>
                    
                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-photo">
                                
                                <div class="placeholder-photo">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <h3>[À COMPLÉTER : Nom]</h3>
                            <p class="member-role">[À COMPLÉTER : Fonction]</p>
                            <p class="member-description">[À COMPLÉTER : Courte présentation]</p>
                        </div>
                        
                        
                        <div class="team-member">
                            <div class="member-photo">
                                <div class="placeholder-photo">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <h3>[À COMPLÉTER : Nom]</h3>
                            <p class="member-role">[À COMPLÉTER : Fonction]</p>
                            <p class="member-description">[À COMPLÉTER : Courte présentation]</p>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Nos Services -->
            <div class="about-section">
                <h2><i class="fas fa-cogs"></i> Nos Services</h2>
                <div class="services-grid">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>Albums Photo</h3>
                        <p>Créez des livres photo personnalisés pour immortaliser vos plus beaux moments en famille, en voyage ou lors d'événements spéciaux.</p>
                    </div>
                    
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Calendriers</h3>
                        <p>Des calendriers muraux ou de bureau personnalisés avec vos photos favorites pour accompagner toute votre année.</p>
                    </div>
                    
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Tirages Photo</h3>
                        <p>Impression haute qualité de vos photos numériques sur différents formats et papiers professionnels.</p>
                    </div>
                    
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-rectangle-ad"></i>
                        </div>
                        <h3>Panneaux Décoratifs</h3>
                        <p>Transformez vos photos en œuvres d'art sur toile, acrylique, métal ou bois pour sublimer votre décoration.</p>
                    </div>
                </div>
            </div>

            <!-- Nos Engagements -->
            <div class="about-section">
                <h2><i class="fas fa-shield-alt"></i> Nos Engagements</h2>
                <div class="about-content-box">
                    <div class="commitments-grid">
                        <div class="commitment-item">
                            <i class="fas fa-lock"></i>
                            <h3>Confidentialité</h3>
                            <p>Vos photos et données personnelles sont protégées selon les standards les plus stricts du RGPD.</p>
                        </div>
                        
                        <div class="commitment-item">
                            <i class="fas fa-truck-fast"></i>
                            <h3>Livraison Rapide</h3>
                            <p>Livraison sous 5 à 7 jours ouvrés sur toute la France métropolitaine.</p>
                        </div>
                        
                        <div class="commitment-item">
                            <i class="fas fa-undo"></i>
                            <h3>Satisfaction Garantie</h3>
                            <p>Nous assurons la satisfaction de nos clients avec une politique de réimpression gratuite en cas d'insatisfaction.</p>
                        </div>
                        
                        <div class="commitment-item">
                            <i class="fas fa-headset"></i>
                            <h3>Support Dédié</h3>
                            <p>Support client disponible du lundi au vendredi de 9h à 18h</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Nos Coordonnées -->
            <div class="about-section">
                <h2><i class="fas fa-map-marker-alt"></i> Nous Contacter</h2>
                <div class="contact-grid">
                    <div class="contact-info">
                        <h3>Informations de contact</h3>
                        <div class="contact-item">
                            <i class="fas fa-building"></i>
                            <div>
                                <strong>General Cover Office Products</strong><br>
                                9 rue de la gare<br>
                                70000 Vallerois-le-bois<br>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Téléphone :</strong><br>
                                03 84 78 38 39
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email :</strong><br>
                                contact@general-cover.com
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Horaires :</strong><br>
                                Du lundi au vendredi de 9h à 18h
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-map">
                        <img src="../images/bandeaux/france.webp" width="200px" height="auto" alt="carte de la france en forme de drapeau" loading="lazy">
                        <h3>General Cover Office Products</br>depuis 1999</h3>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .about-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 40px 0;
        }

        /* Hero Section */
        .about-hero {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .hero-content h2 {
            color: #2c5aa0;
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .hero-text {
            font-size: 1.2rem;
            line-height: 1.6;
            color: #666;
        }

        .placeholder-image, .map-placeholder {
            background: #e9ecef;
            height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 2px dashed #adb5bd;
            color: #6c757d;
            text-align: center;
        }

        /* Sections */
        .about-section {
            margin-bottom: 50px;
        }

        .about-section h2 {
            color: #2c5aa0;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.8rem;
        }

        .about-content-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #2c5aa0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2c5aa0;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(44, 90, 160, 0.3);
        }

        .timeline-year {
            font-weight: bold;
            color: #2c5aa0;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .timeline-content h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .timeline-content p {
            text-align: left;
        }

        .timeline-content ul li {
            text-align: left;
            margin-left: 50px;
        }

        /* Grilles */
        .values-grid, .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .value-card, .service-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .value-card:hover, .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .value-icon, .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2c5aa0, #3d6bb3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }

        .value-card h3, .service-item h3 {
            color: #2c5aa0;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        /* Équipe */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .team-member {
            text-align: center;
        }

        .member-photo {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
        }

        .placeholder-photo {
            width: 100%;
            height: 100%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #6c757d;
        }

        .member-role {
            color: #2c5aa0;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .member-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Engagements */
        .commitments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .commitment-item {
            text-align: center;
            padding: 20px;
        }

        .commitment-item i {
            font-size: 2.5rem;
            color: #2c5aa0;
            margin-bottom: 15px;
        }

        .commitment-item h3 {
            color: #333;
            margin-bottom: 10px;
        }

        /* Contact */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .contact-item i {
            color: #2c5aa0;
            font-size: 1.2rem;
            margin-top: 3px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .about-content {
                padding: 20px 0;
            }

            .about-hero {
                grid-template-columns: 1fr;
                padding: 30px 20px;
            }

            .hero-content h2 {
                font-size: 1.8rem;
            }

            .about-content-box {
                padding: 25px 20px;
                margin: 0 10px;
            }

            .values-grid, .services-grid {
                grid-template-columns: 1fr;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 20px;
            }
        }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>