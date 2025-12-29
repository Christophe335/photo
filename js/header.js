// Gestion des menus déroulants du header
document.addEventListener("DOMContentLoaded", function () {
  const navItems = document.querySelectorAll(".nav-item");
  const navButtons = document.querySelectorAll(".nav-btn");

  // Gestion des menus déroulants sur mobile
  if (window.innerWidth <= 768) {
    navButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        const navItem = this.parentElement;
        const isActive = navItem.classList.contains("active");

        // Fermer tous les autres menus
        navItems.forEach((item) => {
          item.classList.remove("active");
        });

        // Ouvrir/fermer le menu cliqué
        if (!isActive) {
          navItem.classList.add("active");
        }
      });
    });

    // Fermer le menu si on clique ailleurs
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".nav-item")) {
        navItems.forEach((item) => {
          item.classList.remove("active");
        });
      }
    });
  }

  // Gestion du compteur de panier
  function updateCartCount(count) {
    const cartCounts = document.querySelectorAll(".cart-count");
    cartCounts.forEach(function (cartCount) {
      cartCount.textContent = count;
      // Toujours afficher le compteur dans le bouton compact
      if (cartCount.closest(".btn-cart-compact")) {
        cartCount.style.display = "flex";
      } else {
        if (count > 0) {
          cartCount.style.display = "flex";
        } else {
          cartCount.style.display = "none";
        }
      }
    });
  }

  // Initialiser le compteur à 0
  updateCartCount(0);

  // Gestion de la recherche
  const searchInput = document.querySelector(".search-input");
  const searchBtn = document.querySelector(".search-btn");

  if (searchBtn) {
    searchBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const searchTerm = searchInput.value.trim();
      if (searchTerm) {
        // Ici vous pouvez ajouter la logique de recherche
        console.log("Recherche:", searchTerm);
        // Exemple: window.location.href = 'search.php?q=' + encodeURIComponent(searchTerm);
      }
    });
  }

  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        searchBtn.click();
      }
    });
  }

  // Gestion responsive - recalculer lors du redimensionnement
  window.addEventListener("resize", function () {
    // Réinitialiser les menus actifs lors du changement de taille d'écran
    navItems.forEach((item) => {
      item.classList.remove("active");
    });
  });

  // Animation d'apparition des menus au survol (desktop uniquement)
  if (window.innerWidth > 768) {
    navItems.forEach((item) => {
      const menu = item.querySelector(".dropdown-menu");
      let timeoutId;

      item.addEventListener("mouseenter", function () {
        clearTimeout(timeoutId);
        menu.style.opacity = "1";
        menu.style.visibility = "visible";
      });

      item.addEventListener("mouseleave", function () {
        timeoutId = setTimeout(() => {
          menu.style.opacity = "0";
          menu.style.visibility = "hidden";
        }, 150);
      });
    });
  }

  // Gestion du scroll - fixer le navigation-banner en haut
  let lastScrollTop = 0;
  const header = document.querySelector(".header");
  const topBanner = document.querySelector(".top-banner");
  const navigationBanner = document.querySelector(".navigation-banner");
  const body = document.body;

  // Calculer la hauteur du top-banner
  const topBannerHeight = topBanner.offsetHeight;

  window.addEventListener("scroll", function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const navigationBanner = document.querySelector(".navigation-banner");

    if (scrollTop > lastScrollTop && scrollTop > topBannerHeight) {
      // Scroll vers le bas - fixer le navigation-banner en haut
      header.classList.add("scrolled-down");
      body.classList.add("nav-fixed");
      if (navigationBanner) navigationBanner.classList.add("nav-compact");
    } else if (scrollTop < topBannerHeight / 2) {
      // Scroll vers le haut - remettre le header normal
      header.classList.remove("scrolled-down");
      body.classList.remove("nav-fixed");
      if (navigationBanner) navigationBanner.classList.remove("nav-compact");
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
});

// Fonction utilitaire pour ajouter des articles au panier
function addToCart(productId, quantity = 1) {
  // Logique d'ajout au panier
  console.log("Ajout au panier:", productId, quantity);

  // Mettre à jour le compteur (exemple)
  const currentCount =
    parseInt(document.querySelector(".cart-count").textContent) || 0;
  updateCartCount(currentCount + quantity);
}

// Fonction utilitaire pour mettre à jour le compteur de panier
function updateCartCount(count) {
  const cartCount = document.querySelector(".cart-count");
  if (cartCount) {
    cartCount.textContent = count;
    if (count > 0) {
      cartCount.style.display = "flex";
    } else {
      cartCount.style.display = "none";
    }
  }
}

// Gestion des boutons "Nous Consulter" - Redirection vers la page contact
document.addEventListener("DOMContentLoaded", function () {
  console.log("Script boutons 'Nous Consulter' chargé");
  
  // Activer les boutons "nous consulter" et ajouter l'écoute des clics
  const nousConsulterBtns = document.querySelectorAll(".btn-nous-consulter");
  console.log("Boutons 'nous consulter' trouvés:", nousConsulterBtns.length);
  
  nousConsulterBtns.forEach(function(btn) {
    // Retirer l'attribut disabled et changer le cursor
    btn.disabled = false;
    btn.style.cursor = 'pointer';
    
    // Ajouter l'écouteur de clic
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      
      console.log("Clic détecté sur bouton 'Nous Consulter'");
      
      // Détecter si nous sommes dans un sous-dossier ou à la racine
      const currentPath = window.location.pathname;
      let contactUrl;
      
      if (currentPath.includes('/pages/') || 
          currentPath.includes('/admin/') || 
          currentPath.includes('/clients/')) {
        // Nous sommes dans un sous-dossier, remonter d'un niveau
        contactUrl = '../formulaires/contact.php';
      } else {
        // Nous sommes à la racine
        contactUrl = 'formulaires/contact.php';
      }
      
      console.log("Redirection vers:", contactUrl);
      
      // Redirection vers la page contact
      window.location.href = contactUrl;
    });
  });
});

// Toggle du menu mobile (burger)
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('mobileMenuToggle');
  if (!toggle) return;

  toggle.addEventListener('click', function (e) {
    e.preventDefault();
    document.body.classList.toggle('mobile-menu-open');
  });

  // Fermer le menu mobile quand on clique sur un lien de navigation (inclut le menu mobile)
  const navLinks = document.querySelectorAll('.main-nav a, .main-nav .nav-btn, .mobile-menu a');
  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      document.body.classList.remove('mobile-menu-open');
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileMenu) mobileMenu.setAttribute('aria-hidden', 'true');
    });
  });

  // Fermer le menu si on clique en dehors du nav quand il est ouvert
  document.addEventListener('click', function (e) {
    if (!document.body.classList.contains('mobile-menu-open')) return;
    if (!e.target.closest('.main-nav') && !e.target.closest('#mobileMenuToggle') && !e.target.closest('.mobile-menu')) {
      document.body.classList.remove('mobile-menu-open');
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileMenu) mobileMenu.setAttribute('aria-hidden', 'true');
    }
  });
  // Mettre à jour aria-hidden quand on ouvre/ferme
  toggle.addEventListener('click', function () {
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) {
      const open = document.body.classList.contains('mobile-menu-open');
      mobileMenu.setAttribute('aria-hidden', (!open).toString());
    }
  });
  // Ne pas forcer l'ouverture de tous les groupes au clic du burger.
  // Laisser l'utilisateur ouvrir un groupe à la fois (accordion behavior).
});

// Gestion des groupes dans le menu mobile (ouvrir au toucher / clic)
document.addEventListener('DOMContentLoaded', function () {
  const groupTitles = document.querySelectorAll('.mobile-group-title');
  groupTitles.forEach(function (title) {
    title.addEventListener('click', function (e) {
      e.preventDefault();
      const group = this.closest('.mobile-group');
      if (!group) return;

      // Accordion: fermer tous les autres groupes
      const allGroups = document.querySelectorAll('.mobile-group');
      allGroups.forEach(function (g) {
        if (g !== group) {
          g.classList.remove('open');
          const t = g.querySelector('.mobile-group-title');
          if (t) t.setAttribute('aria-expanded', 'false');
          const its = g.querySelector('.mobile-group-items');
          if (its) its.style.maxHeight = null;
        }
      });

      // Basculer le groupe cliqué
      const isOpen = group.classList.toggle('open');
      this.setAttribute('aria-expanded', isOpen.toString());
      const items = group.querySelector('.mobile-group-items');
      if (items) {
        if (isOpen) {
          items.style.maxHeight = items.scrollHeight + 'px';
        } else {
          items.style.maxHeight = null;
        }
      }
    });
  });
});
