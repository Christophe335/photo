/**
 * Gestion du panier d'achat
 */
class PanierManager {
  constructor() {
    this.panier = this.chargerPanier();
    this.initialiserEventListeners();
  }

  /**
   * Charge le panier depuis le localStorage
   */
  chargerPanier() {
    const panier = localStorage.getItem("panier");
    return panier ? JSON.parse(panier) : [];
  }

  /**
   * Sauvegarde le panier dans le localStorage
   */
  sauvegarderPanier() {
    localStorage.setItem("panier", JSON.stringify(this.panier));
    this.mettreAJourCompteurPanier();
  }

  /**
   * Ajoute un produit au panier
   */
  ajouterProduit(produitId, quantite, prix, details) {
    // Vérifier si le produit existe déjà
    const index = this.panier.findIndex((item) => item.id === produitId);

    if (index > -1) {
      // Produit existant : augmenter la quantité
      this.panier[index].quantite += quantite;
    } else {
      // Nouveau produit
      this.panier.push({
        id: produitId,
        quantite: quantite,
        prix: prix,
        details: details,
        dateAjout: new Date().toISOString(),
      });
    }

    this.sauvegarderPanier();
    this.afficherNotification("Produit ajouté au panier");
  }

  /**
   * Met à jour le compteur du panier
   */
  mettreAJourCompteurPanier() {
    const compteur = document.querySelector(".compteur-panier");
    if (compteur) {
      const totalItems = this.panier.reduce(
        (total, item) => total + item.quantite,
        0
      );
      compteur.textContent = totalItems;
      compteur.style.display = totalItems > 0 ? "inline" : "none";
    }
  }

  /**
   * Affiche une notification
   */
  afficherNotification(message, type = "success") {
    // Supprimer les notifications existantes
    const existingNotif = document.querySelector(".notification");
    if (existingNotif) {
      existingNotif.remove();
    }

    // Créer la nouvelle notification
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    // Ajouter au DOM
    document.body.appendChild(notification);

    // Animation d'apparition
    setTimeout(() => notification.classList.add("show"), 100);

    // Suppression automatique
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  /**
   * Initialise les event listeners
   */
  initialiserEventListeners() {
    // Mise à jour du compteur au chargement
    document.addEventListener("DOMContentLoaded", () => {
      this.mettreAJourCompteurPanier();
    });
  }

  /**
   * Obtient le total du panier
   */
  getTotal() {
    return this.panier.reduce(
      (total, item) => total + item.prix * item.quantite,
      0
    );
  }

  /**
   * Vide le panier
   */
  viderPanier() {
    this.panier = [];
    this.sauvegarderPanier();
  }
}

// Instance globale du gestionnaire de panier
const panierManager = new PanierManager();

/**
 * Modifie la quantité d'un produit
 */
function modifierQuantite(bouton, increment) {
  const quantiteInput = bouton.parentNode.querySelector(".input-quantite");
  let quantite = parseInt(quantiteInput.value) || 1;

  quantite += increment;
  if (quantite < 1) quantite = 1;

  quantiteInput.value = quantite;

  // Animation du bouton
  bouton.style.transform = "scale(0.9)";
  setTimeout(() => (bouton.style.transform = "scale(1)"), 150);
}

/**
 * Ajoute un produit au panier
 */
function ajouterAuPanier(bouton) {
  const ligneProduit = bouton.closest(".ligne-produit");
  const produitId = ligneProduit.dataset.id;
  const prix = parseFloat(ligneProduit.dataset.prix);
  const quantite = parseInt(
    ligneProduit.querySelector(".input-quantite").value
  );

  // Récupération des détails du produit
  const code = ligneProduit.querySelector(".col-code").textContent.trim();
  const designation = ligneProduit
    .querySelector(".designation")
    .textContent.trim();
  const format =
    ligneProduit.querySelector(".format")?.textContent.trim() || "";
  const matiere =
    ligneProduit.querySelector(".matiere")?.textContent.trim() || "";

  const details = {
    code: code,
    designation: designation,
    format: format,
    matiere: matiere,
  };

  // Animation du bouton
  bouton.classList.add("adding");
  setTimeout(() => bouton.classList.remove("adding"), 1000);

  // Ajout au panier
  panierManager.ajouterProduit(produitId, quantite, prix, details);

  // Remettre la quantité à 1
  ligneProduit.querySelector(".input-quantite").value = 1;
}

/**
 * Ouvre le modal du panier
 */
function ouvrirPanier() {
  // Créer le modal du panier si il n'existe pas
  let modal = document.getElementById("modal-panier");
  if (!modal) {
    modal = creerModalPanier();
    document.body.appendChild(modal);
  }

  // Actualiser le contenu
  actualiserContenuPanier(modal);

  // Afficher le modal
  modal.style.display = "flex";
  document.body.style.overflow = "hidden";
}

/**
 * Ferme le modal du panier
 */
function fermerPanier() {
  const modal = document.getElementById("modal-panier");
  if (modal) {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }
}

/**
 * Crée le modal du panier
 */
function creerModalPanier() {
  const modal = document.createElement("div");
  modal.id = "modal-panier";
  modal.className = "modal-panier";
  modal.innerHTML = `
        <div class="modal-contenu">
            <div class="modal-header">
                <h2>Mon Panier</h2>
                <button type="button" class="btn-fermer" onclick="fermerPanier()">×</button>
            </div>
            <div class="modal-body">
                <div id="contenu-panier"></div>
                <div class="panier-total">
                    <strong>Total: <span id="total-panier">0,00 €</span> HT</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-vider" onclick="viderPanier()">Vider le panier</button>
                <button type="button" class="btn-commander">Commander</button>
            </div>
        </div>
    `;
  return modal;
}

/**
 * Actualise le contenu du panier
 */
function actualiserContenuPanier(modal) {
  const contenu = modal.querySelector("#contenu-panier");
  const totalElement = modal.querySelector("#total-panier");

  if (panierManager.panier.length === 0) {
    contenu.innerHTML = '<p class="panier-vide">Votre panier est vide</p>';
    totalElement.textContent = "0,00 €";
    return;
  }

  let html = '<div class="items-panier">';
  panierManager.panier.forEach((item) => {
    html += `
            <div class="item-panier" data-id="${item.id}">
                <div class="item-details">
                    <div class="item-code">${item.details.code}</div>
                    <div class="item-designation">${
                      item.details.designation
                    }</div>
                    ${
                      item.details.format
                        ? `<div class="item-format">${item.details.format}</div>`
                        : ""
                    }
                </div>
                <div class="item-quantite">
                    <button type="button" onclick="modifierQuantitePanier('${
                      item.id
                    }', -1)">−</button>
                    <span>${item.quantite}</span>
                    <button type="button" onclick="modifierQuantitePanier('${
                      item.id
                    }', 1)">+</button>
                </div>
                <div class="item-prix">${(item.prix * item.quantite)
                  .toFixed(2)
                  .replace(".", ",")} €</div>
                <button type="button" class="btn-supprimer" onclick="supprimerDuPanier('${
                  item.id
                }')">🗑️</button>
            </div>
        `;
  });
  html += "</div>";

  contenu.innerHTML = html;
  totalElement.textContent =
    panierManager.getTotal().toFixed(2).replace(".", ",") + " €";
}

/**
 * Modifie la quantité d'un item dans le panier
 */
function modifierQuantitePanier(produitId, increment) {
  const index = panierManager.panier.findIndex((item) => item.id === produitId);
  if (index > -1) {
    panierManager.panier[index].quantite += increment;
    if (panierManager.panier[index].quantite <= 0) {
      panierManager.panier.splice(index, 1);
    }
    panierManager.sauvegarderPanier();

    // Actualiser l'affichage
    const modal = document.getElementById("modal-panier");
    if (modal && modal.style.display !== "none") {
      actualiserContenuPanier(modal);
    }
  }
}

/**
 * Supprime un item du panier
 */
function supprimerDuPanier(produitId) {
  const index = panierManager.panier.findIndex((item) => item.id === produitId);
  if (index > -1) {
    panierManager.panier.splice(index, 1);
    panierManager.sauvegarderPanier();

    // Actualiser l'affichage
    const modal = document.getElementById("modal-panier");
    if (modal && modal.style.display !== "none") {
      actualiserContenuPanier(modal);
    }

    panierManager.afficherNotification("Produit supprimé du panier");
  }
}

/**
 * Vide complètement le panier
 */
function viderPanier() {
  if (confirm("Êtes-vous sûr de vouloir vider votre panier ?")) {
    panierManager.viderPanier();

    // Actualiser l'affichage
    const modal = document.getElementById("modal-panier");
    if (modal && modal.style.display !== "none") {
      actualiserContenuPanier(modal);
    }

    panierManager.afficherNotification("Panier vidé");
  }
}

// Fermeture du modal en cliquant à l'extérieur
document.addEventListener("click", function (e) {
  const modal = document.getElementById("modal-panier");
  if (modal && e.target === modal) {
    fermerPanier();
  }
});

// Fermeture du modal avec la touche Escape
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    fermerPanier();
  }
});
