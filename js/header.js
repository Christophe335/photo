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

  // Gestion du scroll - masquer/afficher le bandeau principal
  let lastScrollTop = 0;
  const header = document.querySelector('.header');
  const topBanner = document.querySelector('.top-banner');
  const navigationBanner = document.querySelector('.navigation-banner');
  
  window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > lastScrollTop && scrollTop > 50) {
      // Scroll vers le bas - masquer le top banner
      header.classList.add('scrolled-down');
    } else {
      // Scroll vers le haut - afficher le top banner
      header.classList.remove('scrolled-down');
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
