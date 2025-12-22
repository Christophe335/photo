/**
 * Gestion du panier d'achat
 */
class PanierManager {
  constructor() {
    this.panier = this.chargerPanier();
    this.mettreAJourCompteurPanier();
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
    // On distingue chaque couleur comme un article unique
    let couleur = details.couleur || "";
    let idUnique = produitId + "_" + couleur;
    const index = this.panier.findIndex((item) => item.id === idUnique);

    if (index > -1) {
      // Variante déjà présente : augmenter la quantité
      this.panier[index].quantite += quantite;
    } else {
      // Nouvelle variante (référence + couleur)
      this.panier.push({
        id: idUnique,
        quantite: quantite,
        prix: prix,
        details: details,
        dateAjout: new Date().toISOString(),
      });
    }

    this.sauvegarderPanier();
  }

  /**
   * Met à jour le compteur du panier
   */
  mettreAJourCompteurPanier() {
    // Mettre à jour tous les compteurs du panier
    const totalItems = this.panier.reduce(
      (total, item) => total + item.quantite,
      0
    );
    document
      .querySelectorAll(".cart-count, .compteur-panier")
      .forEach((compteur) => {
        compteur.textContent = totalItems;
        compteur.style.display = totalItems > 0 ? "inline" : "none";
      });
  }

  /**
   * Affiche une notification
   */
  afficherNotification(message, type = "success", produit = null) {
    // Si la page a demandé de désactiver complètement les popups panier, ne rien afficher
    if (typeof window !== 'undefined' && window.disablePanierPopup === true) {
      return;
    }
    // Supprimer les notifications existantes
    const existingNotif = document.querySelector(".notification-popup");
    if (existingNotif) {
      existingNotif.remove();
    }

    // Détail du produit
    let detailsHtml = "";
    if (produit) {
      detailsHtml = `<div style='text-align:left;margin-bottom:8px;'>
        <strong>Référence :</strong> ${produit.code}<br>
        <strong>Désignation :</strong> ${produit.designation}<br>
        ${
          produit.format
            ? `<strong>Format :</strong> ${produit.format}<br>`
            : ""
        }
        ${
          produit.couleur
            ? `<strong>Couleur :</strong> ${produit.couleur}
              ${
                produit.imageCouleur
                  ? `<img src='${
                      produit.imageCouleur.startsWith("/")
                        ? produit.imageCouleur
                        : "/" + produit.imageCouleur.replace("../", "")
                    }' alt='${
                      produit.couleur
                    }' style='width:22px;height:22px;border-radius:50%;margin-left:6px;vertical-align:middle;' onerror='this.style.display="none"'>`
                  : ""
              }<br>`
            : ""
        }
        <strong>Quantité :</strong> ${produit.quantite}
      </div>`;
    }

    // Créer la popup
    const popup = document.createElement("div");
    popup.className = `notification-popup notification-${type}`;
    popup.innerHTML = `
      <div class="popup-content">
        <p>${message}</p>
        ${detailsHtml}
        <div class="popup-actions">
          <button class="btn-panier">Voir le panier</button>
          <button class="btn-continuer">Continuer mes achats</button>
        </div>
      </div>
    `;

    document.body.appendChild(popup);
    setTimeout(() => popup.classList.add("show"), 100);

    // Bouton voir le panier
    // Bouton voir le panier
    const btnPanier = popup.querySelector(".btn-panier");
    if (btnPanier) btnPanier.onclick = function () { window.location.href = "/pages/panier.php"; };
    // Bouton continuer
    popup.querySelector(".btn-continuer").onclick = function () {
      popup.classList.remove("show");
      setTimeout(() => popup.remove(), 300);
    };

    // Suppression automatique désactivée : la popup ne disparaît que sur action utilisateur
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
let panierManager;

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
  // Récupérer le conditionnement (essayer col-nb puis col-nb2 si nécessaire)
  let conditionnement =
    ligneProduit.querySelector(".col-nb")?.textContent.trim() || "";
  if (!conditionnement) {
    conditionnement =
      ligneProduit.querySelector(".col-nb2")?.textContent.trim() || "";
  }

  // Récupère la couleur sélectionnée (active), sinon la première
  let couleur = "";
  let imageCouleur = "";
  const couleurItemActive = ligneProduit.querySelector(".couleur-item.active");
  if (couleurItemActive) {
    const couleurActive = couleurItemActive.querySelector(".couleur-nom");
    if (couleurActive) couleur = couleurActive.textContent.trim();
  } else {
    const couleurDefault = ligneProduit.querySelector(".couleur-nom");
    if (couleurDefault) couleur = couleurDefault.textContent.trim();
  }
  // Construction du chemin d'image big
  if (couleur) {
    // Au lieu de formater le nom, utiliser le chemin de l'image mini
    const couleurItem =
      couleurItemActive || ligneProduit.querySelector(".couleur-item");
    const imgMini = couleurItem ? couleurItem.querySelector("img") : null;

    if (imgMini && imgMini.src) {
      // Extraire le nom du fichier depuis l'image mini
      const cheminMini = imgMini.src;
      const nomFichier = cheminMini.split("/").pop().replace(".webp", "");
      imageCouleur = `../images/couleurs/big/${nomFichier}-B.webp`;
    } else {
      // Fallback: utiliser l'ancienne méthode si pas d'image mini trouvée
      let nomFichier = couleur
        .toLowerCase()
        .replace(/ /g, "-")
        .replace(/[éèêë]/g, "e")
        .replace(/[àâä]/g, "a")
        .replace(/[îï]/g, "i")
        .replace(/[ôö]/g, "o")
        .replace(/[ûü]/g, "u")
        .replace(/[^a-z0-9\-]/g, "");
      imageCouleur = `../images/couleurs/big/${nomFichier}-B.webp`;
    }
  }
  const details = {
    code: code,
    designation: designation,
    conditionnement: conditionnement,
    format: format,
    matiere: matiere,
    couleur: couleur,
    imageCouleur: imageCouleur,
  };

  // Animation du bouton
  bouton.classList.add("adding");
  setTimeout(() => bouton.classList.remove("adding"), 1000);

  // Ajout au panier
  panierManager.ajouterProduit(produitId, quantite, prix, details);

  // Afficher la popup avec les détails
  panierManager.afficherNotification(
    "Votre article a bien été ajouté au panier !",
    "success",
    {
      code: code,
      designation: designation,
      format: format,
      couleur: couleur,
      imageCouleur: imageCouleur,
      quantite: quantite,
    }
  );

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
          <div class="item-designation">${item.details.designation}</div>
          ${
            item.details.conditionnement
              ? `<div class="item-conditionnement" style="color:#666;font-size:13px">Conditionnement : ${item.details.conditionnement}</div>`
              : ""
          }
          ${
            item.details.couleur
              ? `<div class="item-couleur" style="color:#666;font-size:13px">Couleur : ${
                  item.details.couleur
                }${
                  item.details.imageCouleur
                    ? ` <img src='${item.details.imageCouleur}' alt='${item.details.couleur}' style='width:22px;height:22px;border-radius:50%;margin-left:6px;vertical-align:middle;'>`
                    : ""
                }</div>`
              : ""
          }
          ${
            item.details.format
              ? `<div class="item-format">${item.details.format}</div>`
              : ""
          }
        </div>
        <div class="item-quantite">
          <button type="button" class="btn-quantite-minus">−</button>
          <span class="item-quantite-valeur">${item.quantite}</span>
          <button type="button" class="btn-quantite-plus">+</button>
        </div>
        <div class="item-prix">${(item.prix * item.quantite)
          .toFixed(2)
          .replace(".", ",")} €</div>
        <button type="button" class="btn-supprimer">🗑️</button>
      </div>
    `;
  });
  html += "</div>";

  contenu.innerHTML = html;
  // Attacher des écouteurs d'événements de manière sûre (évite les problèmes avec les quotes dans les id)
  contenu.querySelectorAll(".item-panier").forEach(function (elem) {
    const produitId = elem.dataset.id;
    // Boutons quantité
    const btnMinus = elem.querySelector(".btn-quantite-minus");
    const btnPlus = elem.querySelector(".btn-quantite-plus");
    const quantiteSpan = elem.querySelector(".item-quantite-valeur");
    if (btnMinus)
      btnMinus.addEventListener("click", function () {
        modifierQuantitePanier(produitId, -1);
      });
    if (btnPlus)
      btnPlus.addEventListener("click", function () {
        modifierQuantitePanier(produitId, 1);
      });
    // Bouton supprimer
    const btnSup = elem.querySelector(".btn-supprimer");
    if (btnSup)
      btnSup.addEventListener("click", function () {
        supprimerDuPanier(produitId);
      });
  });
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

    // Actualiser l'affichage systématiquement
    const modal = document.getElementById("modal-panier");
    if (modal) {
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
    if (modal) {
      actualiserContenuPanier(modal);
    }
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

// Gestionnaire pour les popups de notification
document.addEventListener("DOMContentLoaded", function () {
  // Bouton "Voir le panier" dans les popups
  document.body.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-panier")) {
      const modal = document.getElementById("modal-panier");
      if (modal) {
        actualiserContenuPanier(modal);
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
      }
    }
  });
});

// Gestion de la sélection de couleur dans les tableaux (délégation d'événements)
document.addEventListener("click", function (e) {
  if (e.target.closest(".couleur-item")) {
    const item = e.target.closest(".couleur-item");
    const ligne = item.closest(".ligne-produit");

    // Retire la classe active des autres couleurs de la même ligne
    ligne.querySelectorAll(".couleur-item").forEach(function (c) {
      c.classList.remove("active");
    });
    // Ajoute la classe active à la couleur cliquée
    item.classList.add("active");

    const couleurActive = item.querySelector(".couleur-nom");
    let couleur = couleurActive ? couleurActive.textContent.trim() : "";
    if (couleur) {
      // Au lieu de transformer le nom, récupérer le chemin de l'image mini existante
      const imgMini = item.querySelector("img");
      if (imgMini && imgMini.src) {
        // Extraire le nom du fichier depuis l'image mini
        const cheminMini = imgMini.src;
        const nomFichier = cheminMini.split("/").pop().replace(".webp", "");

        // Construire le chemin vers l'image big
        let cheminBig = `../images/couleurs/big/${nomFichier}-B.webp`;

        let imgBig = ligne.querySelector(".image-couleur-big");
        if (!imgBig) {
          imgBig = document.createElement("img");
          imgBig.className = "image-couleur-big";
          imgBig.style.width = "80px";
          imgBig.style.height = "80px";
          imgBig.style.borderRadius = "12px";
          imgBig.style.margin = "10px 0";
          imgBig.style.boxShadow = "0 2px 8px rgba(0,0,0,0.12)";

          // Ajoute l'image juste après le tableau des couleurs
          const couleursContainer = ligne.querySelector(".couleurs-container");
          if (couleursContainer) {
            couleursContainer.parentNode.insertBefore(
              imgBig,
              couleursContainer.nextSibling
            );
          } else {
            ligne.appendChild(imgBig);
          }
        }
        imgBig.src = cheminBig;
        imgBig.alt = couleur;
      }
    }
  }
});

// Initialisation
document.addEventListener("DOMContentLoaded", function () {
  panierManager = new PanierManager();
  console.log("PanierManager initialisé");
});

// Debug & fallback listener pour les boutons supprimer (page + modal)
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".btn-supprimer, .btn-supprimer-panier");
  if (!btn) return;

  // Trouver l'ID de l'élément (supporte <tr data-id> ou .item-panier[data-id])
  const row =
    btn.closest("tr[data-id]") || btn.closest(".item-panier[data-id]");
  const id = row ? row.dataset.id : null;
  console.log("[DEBUG] Clic suppression détecté", {
    btnClass: btn.className,
    id: id,
    btn: btn,
  });

  // Si c'est le bouton modal (.btn-supprimer) appeler supprimerDuPanier
  if (btn.classList.contains("btn-supprimer") && id) {
    try {
      supprimerDuPanier(id);
      console.log("[DEBUG] supprimerDuPanier appelé pour", id);
    } catch (err) {
      console.error("[DEBUG] Erreur appel supprimerDuPanier", err);
    }
    e.preventDefault();
    return;
  }

  // Si c'est le bouton page (.btn-supprimer-panier) appeler supprimerDuPanierPage si définie
  if (btn.classList.contains("btn-supprimer-panier") && id) {
    if (typeof window.supprimerDuPanierPage === "function") {
      try {
        window.supprimerDuPanierPage(id);
        console.log("[DEBUG] supprimerDuPanierPage appelé pour", id);
      } catch (err) {
        console.error("[DEBUG] Erreur appel supprimerDuPanierPage", err);
      }
    } else {
      // Fallback local: retirer de localStorage et synchroniser
      try {
        let panier = JSON.parse(localStorage.getItem("panier")) || [];
        panier = panier.filter((item) => item.id !== id);
        localStorage.setItem("panier", JSON.stringify(panier));
        localStorage.removeItem("panier_synced");
        fetch("/pages/sync_panier.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(panier),
        })
          .then(function () {
            window.location.reload();
          })
          .catch(function () {
            window.location.reload();
          });
        console.log("[DEBUG] Fallback suppression exécutée pour", id);
      } catch (err) {
        console.error("[DEBUG] Fallback suppression erreur", err);
      }
    }
    e.preventDefault();
    return;
  }
});

/**
 * Fonction globale pour ajouter un produit au panier (utilisée par les autres scripts)
 */
window.ajouterAuPanierProduit = function (produit) {
  if (typeof panierManager !== "undefined") {
    panierManager.ajouterProduit(
      produit.id,
      produit.quantite || 1,
      produit.prix,
      {
        code: produit.reference,
        designation: produit.designation,
        format: produit.format || "",
        conditionnement: produit.conditionnement || "",
        couleur: produit.couleur || "",
        imageCouleur: produit.imageCouleur || "",
      }
    );
    // Afficher la popup pour cette fonction globale aussi
    panierManager.afficherNotification(
      "Votre article a bien été ajouté au panier !",
      "success",
      {
        code: produit.reference,
        designation: produit.designation,
        format: produit.format || "",
        couleur: produit.couleur || "",
        imageCouleur: produit.imageCouleur || "",
        quantite: produit.quantite || 1,
      }
    );
  } else {
    console.error("PanierManager non disponible");
  }
};
