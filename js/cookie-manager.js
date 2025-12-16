// Gestion des cookies et consentement RGPD
class CookieManager {
    constructor() {
        this.consentGiven = false;
        this.preferences = {
            essential: true, // Toujours true
            analytics: false,
            marketing: false
        };
        
        this.init();
    }
    
    init() {
        // Vérifier si un consentement existe déjà
        const savedConsent = localStorage.getItem('cookie-consent');
        if (savedConsent) {
            this.preferences = JSON.parse(savedConsent);
            this.consentGiven = true;
            this.loadCookies();
        } else {
            // Afficher le bandeau de consentement
            this.showConsentBanner();
        }
    }
    
    showConsentBanner() {
        // Créer le bandeau de consentement
        const banner = document.createElement('div');
        banner.id = 'cookie-banner';
        banner.innerHTML = `
            <div class="cookie-banner">
                <div class="cookie-content">
                    <div class="cookie-text">
                        <h4><i class="fas fa-cookie-bite"></i> Gestion des cookies</h4>
                        <p>Nous utilisons des cookies pour améliorer votre expérience sur notre site. 
                        Vous pouvez accepter tous les cookies ou personnaliser vos préférences.</p>
                    </div>
                    <div class="cookie-actions">
                        <button id="accept-all" class="btn-accept">Tout accepter</button>
                        <button id="customize" class="btn-customize">Personnaliser</button>
                        <button id="refuse-all" class="btn-refuse">Refuser</button>
                    </div>
                </div>
            </div>
            
            <div id="cookie-preferences" class="cookie-preferences" style="display: none;">
                <div class="preferences-content">
                    <h4>Préférences des cookies</h4>
                    <div class="cookie-category">
                        <div class="category-header">
                            <label>
                                <input type="checkbox" id="essential" checked disabled>
                                <span>Cookies essentiels</span>
                            </label>
                            <i class="fas fa-info-circle" title="Ces cookies sont nécessaires au fonctionnement du site"></i>
                        </div>
                        <p class="category-description">Nécessaires pour le fonctionnement du site (connexion, panier, sécurité)</p>
                    </div>
                    
                    <div class="cookie-category">
                        <div class="category-header">
                            <label>
                                <input type="checkbox" id="analytics">
                                <span>Cookies d'analyse</span>
                            </label>
                            <i class="fas fa-info-circle" title="Google Analytics pour améliorer le site"></i>
                        </div>
                        <p class="category-description">Google Analytics pour comprendre l'utilisation du site et l'améliorer</p>
                    </div>
                    
                    <div class="cookie-category">
                        <div class="category-header">
                            <label>
                                <input type="checkbox" id="marketing">
                                <span>Cookies marketing</span>
                            </label>
                            <i class="fas fa-info-circle" title="Publicités personnalisées"></i>
                        </div>
                        <p class="category-description">Personnalisation des publicités et mesure de performance</p>
                    </div>
                    
                    <div class="preferences-actions">
                        <button id="save-preferences" class="btn-save">Enregistrer mes préférences</button>
                        <button id="back-banner" class="btn-back">Retour</button>
                    </div>
                    
                    <p class="preferences-note">
                        <a href="politique.php" target="_blank">Consulter notre politique de confidentialité</a>
                    </p>
                </div>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Attacher les événements
        this.attachBannerEvents();
    }
    
    attachBannerEvents() {
        document.getElementById('accept-all').addEventListener('click', () => {
            this.preferences = { essential: true, analytics: true, marketing: true };
            this.saveConsent();
        });
        
        document.getElementById('refuse-all').addEventListener('click', () => {
            this.preferences = { essential: true, analytics: false, marketing: false };
            this.saveConsent();
        });
        
        document.getElementById('customize').addEventListener('click', () => {
            document.querySelector('.cookie-banner').style.display = 'none';
            document.getElementById('cookie-preferences').style.display = 'block';
        });
        
        document.getElementById('back-banner').addEventListener('click', () => {
            document.querySelector('.cookie-banner').style.display = 'block';
            document.getElementById('cookie-preferences').style.display = 'none';
        });
        
        document.getElementById('save-preferences').addEventListener('click', () => {
            this.preferences = {
                essential: true,
                analytics: document.getElementById('analytics').checked,
                marketing: document.getElementById('marketing').checked
            };
            this.saveConsent();
        });
    }
    
    saveConsent() {
        localStorage.setItem('cookie-consent', JSON.stringify(this.preferences));
        localStorage.setItem('cookie-consent-date', new Date().toISOString());
        this.consentGiven = true;
        
        // Supprimer le bandeau
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.remove();
        }
        
        // Charger les cookies selon les préférences
        this.loadCookies();
    }
    
    loadCookies() {
        // Cookies essentiels (toujours chargés)
        this.loadEssentialCookies();
        
        // Cookies d'analyse
        if (this.preferences.analytics) {
            this.loadAnalyticsCookies();
        }
        
        // Cookies marketing
        if (this.preferences.marketing) {
            this.loadMarketingCookies();
        }
    }
    
    loadEssentialCookies() {
        // Ces cookies sont déjà chargés par PHP (session, CSRF, etc.)
        console.log('Cookies essentiels chargés');
    }
    
    loadAnalyticsCookies() {
        // Google Analytics
        if (typeof gtag === 'undefined') {
            // [À COMPLÉTER : Remplacer GA_MEASUREMENT_ID par votre ID Google Analytics]
            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID';
            document.head.appendChild(script);
            
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'GA_MEASUREMENT_ID', {
                anonymize_ip: true,
                cookie_flags: 'SameSite=None;Secure'
            });
        }
        console.log('Cookies d\'analyse chargés');
    }
    
    loadMarketingCookies() {
        // [À COMPLÉTER : Ajouter vos cookies marketing si nécessaire]
        console.log('Cookies marketing chargés');
    }
    
    // Méthode pour réouvrir les préférences
    showPreferences() {
        this.showConsentBanner();
        document.querySelector('.cookie-banner').style.display = 'none';
        document.getElementById('cookie-preferences').style.display = 'block';
        
        // Pré-remplir avec les préférences actuelles
        document.getElementById('analytics').checked = this.preferences.analytics;
        document.getElementById('marketing').checked = this.preferences.marketing;
    }
    
    // Méthode pour vérifier si un type de cookie est autorisé
    isAllowed(type) {
        return this.preferences[type] || false;
    }
}

// CSS pour le bandeau de cookies
const cookieCSS = `
    #cookie-banner {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        border-top: 3px solid #2c5aa0;
    }

    .cookie-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
        gap: 30px;
    }

    .cookie-text {
        flex: 1;
        color: white;
    }

    .cookie-text h4 {
        margin: 0 0 10px 0;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cookie-text p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.4;
        opacity: 0.9;
    }

    .cookie-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cookie-actions button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-accept {
        background: #28a745;
        color: white;
    }

    .btn-accept:hover {
        background: #218838;
    }

    .btn-customize {
        background: #2c5aa0;
        color: white;
    }

    .btn-customize:hover {
        background: #1e3d6f;
    }

    .btn-refuse {
        background: #6c757d;
        color: white;
    }

    .btn-refuse:hover {
        background: #545b62;
    }

    .cookie-preferences {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10001;
        background: white;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.3);
        border-top: 3px solid #2c5aa0;
        max-height: 80vh;
        overflow-y: auto;
    }

    .preferences-content {
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }

    .preferences-content h4 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .cookie-category {
        background: #f8f9fa;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        border-left: 4px solid #2c5aa0;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .category-header label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
    }

    .category-header input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #2c5aa0;
    }

    .category-header i {
        color: #6c757d;
        cursor: help;
    }

    .category-description {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
        line-height: 1.4;
    }

    .preferences-actions {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .btn-save {
        background: #28a745;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        margin-right: 10px;
    }

    .btn-save:hover {
        background: #218838;
    }

    .btn-back {
        background: #6c757d;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-back:hover {
        background: #545b62;
    }

    .preferences-note {
        text-align: center;
        margin-top: 15px;
        font-size: 0.85rem;
    }

    .preferences-note a {
        color: #2c5aa0;
        text-decoration: none;
    }

    .preferences-note a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .cookie-banner {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }

        .cookie-actions {
            justify-content: center;
        }

        .preferences-content {
            padding: 20px;
        }
    }
`;

// Ajouter le CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = cookieCSS;
document.head.appendChild(styleSheet);

// Initialiser le gestionnaire de cookies
let cookieManager;
document.addEventListener('DOMContentLoaded', function() {
    cookieManager = new CookieManager();
});

// Fonction globale pour rouvrir les préférences (utilisée dans le footer)
function openCookiePreferences() {
    if (cookieManager) {
        cookieManager.showPreferences();
    }
}