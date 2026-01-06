// Gestion des cookies et consentement RGPD
class CookieManager {
  constructor() {
    this.consentGiven = false;
    this.preferences = {
      essential: true, // Toujours true
      analytics: false,
      marketing: false,
    };

    this.init();
  }

  init() {
    // Vérifier si un consentement existe déjà
    const savedConsent = localStorage.getItem("cookie-consent");
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
    const banner = document.createElement("div");
    banner.id = "cookie-banner";
    banner.innerHTML = `
        <div class="cookie-banner cookie-popup" role="dialog" aria-label="Consentement cookies">
          <div class="cookie-content">
                <div class="cookie-header">
                  <div class="cookie-title">
                    <h4>Gestion des cookies</h4>
                  </div>
                  <div class="cookie-logos">
                    <img class="cookie-img" src="/images/logo-icon/cookie.webp" alt="Cookies">
                    <div class="company-logo-placeholder"></div>
                  </div>
                </div>

                <div class="cookie-desc">
                  <p>Nous utilisons des cookies pour améliorer votre expérience sur notre site. Vous pouvez accepter tous les cookies ou personnaliser vos préférences.</p>
                </div>

                <div class="cookie-actions">
                  <button id="accept-all" class="btn-accept">Tout accepter</button>
                  <button id="customize" class="btn-customize">Personnaliser</button>
                  <button id="refuse-all" class="btn-refuse">Refuser</button>
                </div>
            </div>
          </div>
        </div>
        `;

    document.body.appendChild(banner);

    // Créer et ajouter l'overlay et le panneau de préférences en dehors du banner (pour éviter qu'ils soient contraints)
    const overlay = document.createElement("div");
    overlay.id = "cookie-modal-overlay";
    overlay.className = "cookie-modal-overlay";
    overlay.style.display = "none";
    document.body.appendChild(overlay);

    const prefs = document.createElement("div");
    prefs.id = "cookie-preferences";
    prefs.className = "cookie-preferences";
    prefs.style.display = "none";
    prefs.innerHTML = `
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
                <a href="../politique.php" target="_blank">Consulter notre politique de confidentialité</a>
            </p>
        </div>
    `;
    document.body.appendChild(prefs);

    // Si le logo de la page existe (render PHP dans header.php avec class "logo-img"), le cloner dans la popup
    try {
      const serverLogo = document.querySelector(".logo-img");
      const companyPlaceholder = banner.querySelector(
        ".company-logo-placeholder"
      );
      if (companyPlaceholder) {
        if (serverLogo) {
          companyPlaceholder.appendChild(serverLogo.cloneNode(true));
        } else {
          const defaultLogo = document.createElement("img");
          defaultLogo.src = "/images/logo.png";
          defaultLogo.alt = "Logo";
          defaultLogo.className = "company-logo";
          companyPlaceholder.appendChild(defaultLogo);
        }
      }
    } catch (e) {
      // noop
    }

    // Attacher les événements
    this.attachBannerEvents();
  }

  attachBannerEvents() {
    document.getElementById("accept-all").addEventListener("click", () => {
      this.preferences = { essential: true, analytics: true, marketing: true };
      this.saveConsent();
    });

    document.getElementById("refuse-all").addEventListener("click", () => {
      this.preferences = {
        essential: true,
        analytics: false,
        marketing: false,
      };
      this.saveConsent();
    });

    document.getElementById("customize").addEventListener("click", () => {
      document.querySelector(".cookie-banner").style.display = "none";
      const overlay = document.getElementById("cookie-modal-overlay");
      if (overlay) overlay.style.display = "block";
      const prefs = document.getElementById("cookie-preferences");
      if (prefs) {
        prefs.style.display = "block";
      }
    });

    document.getElementById("back-banner").addEventListener("click", () => {
      document.querySelector(".cookie-banner").style.display = "block";
      const overlay = document.getElementById("cookie-modal-overlay");
      if (overlay) overlay.style.display = "none";
      document.getElementById("cookie-preferences").style.display = "none";
    });

    document
      .getElementById("save-preferences")
      .addEventListener("click", () => {
        this.preferences = {
          essential: true,
          analytics: document.getElementById("analytics").checked,
          marketing: document.getElementById("marketing").checked,
        };
        this.saveConsent();
      });
  }

  saveConsent() {
    localStorage.setItem("cookie-consent", JSON.stringify(this.preferences));
    localStorage.setItem("cookie-consent-date", new Date().toISOString());
    this.consentGiven = true;

    // Supprimer le bandeau et l'overlay modal
    const banner = document.getElementById("cookie-banner");
    if (banner) {
      banner.remove();
    }
    const overlay = document.getElementById("cookie-modal-overlay");
    if (overlay) overlay.remove();
    const prefs = document.getElementById("cookie-preferences");
    if (prefs) prefs.remove();

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
    console.log("Cookies essentiels chargés");
  }

  loadAnalyticsCookies() {
    // Google Analytics
    if (typeof gtag === "undefined") {
      // [À COMPLÉTER : Remplacer GA_MEASUREMENT_ID par votre ID Google Analytics]
      const script = document.createElement("script");
      script.async = true;
      script.src =
        "https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID";
      document.head.appendChild(script);

      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());
      gtag("config", "GA_MEASUREMENT_ID", {
        anonymize_ip: true,
        cookie_flags: "SameSite=None;Secure",
      });
    }
    console.log("Cookies d'analyse chargés");
  }

  loadMarketingCookies() {
    // [À COMPLÉTER : Ajouter vos cookies marketing si nécessaire]
    console.log("Cookies marketing chargés");
  }

  // Méthode pour réouvrir les préférences
  showPreferences() {
    this.showConsentBanner();
    document.querySelector(".cookie-banner").style.display = "none";
    const overlay = document.getElementById("cookie-modal-overlay");
    if (overlay) overlay.style.display = "block";
    document.getElementById("cookie-preferences").style.display = "block";

    // Pré-remplir avec les préférences actuelles
    document.getElementById("analytics").checked = this.preferences.analytics;
    document.getElementById("marketing").checked = this.preferences.marketing;
  }

  // Méthode pour vérifier si un type de cookie est autorisé
  isAllowed(type) {
    return this.preferences[type] || false;
  }
}

// CSS pour le bandeau de cookies
const cookieCSS = `
  /* Popup cookie 375x250px centré */
  #cookie-banner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10000;
    width: 22%;
    height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--fond-principal);
    box-shadow: 0 12px 40px rgba(0,0,0,0.35);
    border-radius: 12px;
    overflow: hidden;
    color: white;
  }

  .cookie-popup .cookie-content {
    width: 100%;
    padding: 18px 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
  }

  .cookie-header {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
  }

  .cookie-title h4 {
    margin: 0;
    color: white;
    font-size: 1.05rem;
  }

  .cookie-logos {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: flex-end;
  }

  .cookie-logos img,
  .cookie-logos .company-logo-placeholder img,
  .cookie-logos .company-logo-placeholder .logo-img {
    display: block;
    max-width: 46px;
    max-height: 46px;
    object-fit: contain;
  }

  .cookie-desc {
    width: 88%;
    margin: 8px auto 0 auto;
    text-align: justify;
    color: white;
    font-size: 0.9rem;
    line-height: 1.3;
  }

  .cookie-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
    width: 100%;
    padding: 0 18px;
    box-sizing: border-box;
  }

  .cookie-text {
    flex: 1;
    color: white;
  }

  .cookie-text h4 {
    margin: 0 0 6px 0;
    color: white;
    font-size: 1.05rem;
  }

  .cookie-text p {
    margin: 0;
    font-size: 0.86rem;
    line-height: 1.3;
    color: white;
    opacity: 0.95;
  }

  

  .cookie-actions button {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    font-size: 0.9rem;
  }

  .btn-accept {
    background: var(--or2);
    color: white;
  }

  .btn-accept:hover {
    background: var(--or1);
  }

  .btn-customize {
    background: var(--or2);
    color: white;
  }

  .btn-customize:hover {
    background: var(--or1);
  }

  .btn-refuse {
    background: #6c757d;
    color: white;
  }

  .btn-refuse:hover {
    background: #545b62;
  }

  .cookie-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    display: none;
  }

  .cookie-preferences {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10001;
    background: white;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    border-radius: 10px;
    width: 45%;
    height: 58vh;
    max-width: 1100px;
    min-width: 320px;
    overflow: auto;
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
    border-left: 4px solid var(--or2);
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
    accent-color: var(--or2);
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
    background: var(--or2);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    margin-right: 10px;
  }

  .btn-save:hover {
    background: var(--or1);
  }

  .btn-back {
    background: var(--noir2);
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
    #cookie-banner {
      width: calc(100% - 40px);
      height: auto;
      top: auto;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 10px;
    }

    .cookie-popup .cookie-content {
      padding: 14px;
    }

    .cookie-actions {
      justify-content: center;
      flex-wrap: wrap;
    }
    .cookie-preferences {
      width: calc(100% - 40px);
      height: auto;
      max-height: 80vh;
      top: auto;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 10px;
    }
  }

  /* Small phones (e.g. 375px wide) */
  @media (max-width: 420px) {
    .cookie-preferences {
      width: min(375px, calc(100% - 20px));
      height: 250px;
      max-width: 100%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  }
`;

// Ajouter le CSS
const styleSheet = document.createElement("style");
styleSheet.textContent = cookieCSS;
document.head.appendChild(styleSheet);

// Initialiser le gestionnaire de cookies
let cookieManager;
document.addEventListener("DOMContentLoaded", function () {
  cookieManager = new CookieManager();
});

// Fonction globale pour rouvrir les préférences (utilisée dans le footer)
function openCookiePreferences() {
  if (cookieManager) {
    cookieManager.showPreferences();
  }
}
