// Gestion des devis
let clients = [];
let produits = [];
let nextArticleId = 1;

document.addEventListener("DOMContentLoaded", function () {
  // Initialisation
  chargerClients();
  chargerProduits();

  // Event listeners avec vérification d'existence
  const typeClient = document.getElementById("type_client");
  if (typeClient) typeClient.addEventListener("change", toggleClientForm);

  const clientExistant = document.getElementById("client_existant_id");
  if (clientExistant)
    clientExistant.addEventListener("change", chargerClientExistant);

  const btnAjouterProduit = document.getElementById("btn-ajouter-produit");
  if (btnAjouterProduit)
    btnAjouterProduit.addEventListener("click", ouvrirModalProduits);

  const btnAjouterLibre = document.getElementById("btn-ajouter-libre");
  if (btnAjouterLibre)
    btnAjouterLibre.addEventListener("click", ajouterArticleLibre);

  // Les frais de port sont maintenant calculés automatiquement

  // Gestion modal
  const closeBtn = document.querySelector(".close");
  if (closeBtn) closeBtn.addEventListener("click", fermerModal);

  const rechercheInput = document.getElementById("recherche-produit");
  if (rechercheInput)
    rechercheInput.addEventListener("input", rechercherProduits);

  // Boutons formulaire
  const btnEnregistrer = document.getElementById("btn-enregistrer");
  if (btnEnregistrer)
    btnEnregistrer.addEventListener("click", enregistrerDevis);

  const btnImprimer = document.getElementById("btn-imprimer");
  if (btnImprimer) btnImprimer.addEventListener("click", imprimerDevis);

  calculerTotaux();

  console.log("Devis.js initialisé - Boutons trouvés:", {
    btnAjouterProduit: !!btnAjouterProduit,
    btnAjouterLibre: !!btnAjouterLibre,
    btnEnregistrer: !!btnEnregistrer,
  });

  // Attacher les listeners aux lignes d'articles existantes (si présente sur la page)
  const existingArticles = document.querySelectorAll(".article-item");
  if (existingArticles.length > 0) {
    existingArticles.forEach((articleDiv) => {
      // inputs à surveiller
      const inputs = articleDiv.querySelectorAll("input, select");
      inputs.forEach((inp) => {
        inp.addEventListener("input", () => calculerLigneTotal(articleDiv));
        inp.addEventListener("change", () => calculerLigneTotal(articleDiv));
      });
      // recalcul initial de la ligne pour normaliser l'affichage
      calculerLigneTotal(articleDiv);
    });
  }
});

// Chargement des clients
async function chargerClients() {
  console.log("chargerClients() appelée");
  try {
    const candidatePaths = [
      "../ajax/get-clients.php",
      "ajax/get-clients.php",
      "/admin/ajax/get-clients.php",
      "/ajax/get-clients.php",
    ];

    let response = null;
    let usedPath = null;

    for (const p of candidatePaths) {
      try {
        console.log("Trying to fetch clients from:", p);
        const r = await fetch(p, { cache: "no-store" });
        if (r.ok) {
          response = r;
          usedPath = p;
          break;
        } else {
          console.warn("Path returned non-ok status:", p, r.status);
        }
      } catch (err) {
        console.warn("Fetch failed for path:", p, err && err.message);
      }
    }

    if (!response) {
      throw new Error(
        "Impossible de charger la liste des clients (404/erreur réseau)"
      );
    }

    console.log(
      "Using clients endpoint:",
      usedPath,
      "status:",
      response.status
    );
    const responseText = await response.text();
    console.log("Raw response:", responseText);

    clients = JSON.parse(responseText);
    console.log("Clients parsed:", clients);

    const select = document.getElementById("client_existant_id");
    if (!select) {
      console.error("Element client_existant_id non trouvé !");
      return;
    }

    console.log("Remplissage du select...");
    select.innerHTML = '<option value="">Choisir un client...</option>';

    if (!Array.isArray(clients)) {
      console.error("clients n'est pas un tableau:", clients);
      return;
    }

    clients.forEach((client, index) => {
      console.log(`Ajout client ${index}:`, client);
      const option = document.createElement("option");
      option.value = client.id;
      const societeText = client.societe
        ? ` - ${client.societe}`
        : " - Particulier";
      option.textContent = `${client.nom} ${client.prenom}${societeText}`;
      select.appendChild(option);
    });

    console.log(`${clients.length} clients ajoutés au select`);
  } catch (error) {
    console.error("Erreur chargement clients:", error);
    alert(
      "Erreur lors du chargement des clients: " +
        error.message +
        "\nVérifie que le fichier ajax/get-clients.php existe sur le serveur."
    );
  }
}

// Chargement des produits
async function chargerProduits() {
  try {
    console.log("Chargement des produits...");
    const response = await fetch("../ajax/get-produits.php");
    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    const data = await response.json();
    if (data.error) {
      throw new Error(data.error);
    }
    produits = data;
    console.log("Produits chargés:", produits.length);
    return produits;
  } catch (error) {
    console.error("Erreur chargement produits:", error);
    produits = [];
    return [];
  }
}

// Toggle entre client existant et nouveau
function toggleClientForm() {
  const typeClient = document.getElementById("type_client").value;
  const existantDiv = document.getElementById("client-existant");
  const nouveauDiv = document.getElementById("client-nouveau");

  if (typeClient === "existant") {
    existantDiv.style.display = "block";
    nouveauDiv.style.display = "block"; // Garder les champs visibles
    // Désactiver l'édition des champs car ils seront remplis automatiquement
    setClientFieldsReadonly(true);
  } else {
    existantDiv.style.display = "none";
    nouveauDiv.style.display = "block";
    // Réactiver l'édition des champs
    setClientFieldsReadonly(false);
    // Vider les champs et la sélection du client existant
    clearClientFields();
    document.getElementById("client_existant_id").value = "";
  }
}

// Activer/désactiver l'édition des champs client
function setClientFieldsReadonly(readonly) {
  const fields = [
    "nouveau_societe",
    "nouveau_nom",
    "nouveau_prenom",
    "nouveau_email",
    "nouveau_telephone",
    "nouveau_adresse",
    "nouveau_code_postal",
    "nouveau_ville",
  ];

  fields.forEach((fieldId) => {
    const element = document.getElementById(fieldId);
    if (element) {
      element.readOnly = readonly;
      if (readonly) {
        element.style.backgroundColor = "#f8f9fa";
        element.style.color = "#6c757d";
      } else {
        element.style.backgroundColor = "white";
        element.style.color = "#333";
      }
    }
  });
}

// Vider les champs du client
function clearClientFields() {
  const fields = [
    "nouveau_societe",
    "nouveau_nom",
    "nouveau_prenom",
    "nouveau_email",
    "nouveau_telephone",
    "nouveau_adresse",
    "nouveau_code_postal",
    "nouveau_ville",
  ];

  fields.forEach((fieldId) => {
    const element = document.getElementById(fieldId);
    if (element) {
      element.value = "";
    }
  });
}

// Charger les données du client sélectionné
async function chargerClientExistant() {
  const clientId = document.getElementById("client_existant_id").value;
  console.log("chargerClientExistant appelée avec ID:", clientId);

  if (!clientId) {
    clearClientFields();
    return;
  }

  try {
    console.log(
      "Fetching client data from:",
      `ajax/get-client-details.php?id=${clientId}`
    );
    const response = await fetch(`ajax/get-client-details.php?id=${clientId}`);
    console.log("Response status:", response.status);

    const client = await response.json();
    console.log("Client data received:", client);

    if (client && !client.error) {
      // Remplir les champs avec les données du client
      fillClientFields(client);
    } else {
      console.error(
        "Erreur client:",
        client.error || "Données client invalides"
      );
      alert(
        "Erreur lors du chargement des données client: " +
          (client.error || "Données invalides")
      );
    }
  } catch (error) {
    console.error("Erreur lors du chargement des détails client:", error);
    alert("Erreur de communication avec le serveur");
  }
}

// Remplir les champs avec les données du client sélectionné
function fillClientFields(client) {
  console.log("fillClientFields appelée avec:", client);

  // Mapping des champs
  const fieldMapping = {
    nouveau_societe: client.societe || "",
    nouveau_nom: client.nom || "",
    nouveau_prenom: client.prenom || "",
    nouveau_email: client.email || "",
    nouveau_telephone: client.telephone || "",
    nouveau_adresse: client.adresse || "",
    nouveau_code_postal: client.code_postal || "",
    nouveau_ville: client.ville || "",
  };

  console.log("Field mapping:", fieldMapping);

  // Remplir chaque champ
  Object.keys(fieldMapping).forEach((fieldId) => {
    const element = document.getElementById(fieldId);
    console.log(
      `Champ ${fieldId}:`,
      element ? "trouvé" : "NON TROUVÉ",
      "valeur:",
      fieldMapping[fieldId]
    );
    if (element) {
      element.value = fieldMapping[fieldId];
      console.log(`${fieldId} rempli avec:`, element.value);
    }
  });

  // Ajouter une indication visuelle que les données proviennent d'un client existant
  const nouveauDiv = document.getElementById("client-nouveau");
  let infoDiv = document.getElementById("client-info-notice");

  if (!infoDiv) {
    infoDiv = document.createElement("div");
    infoDiv.id = "client-info-notice";
    infoDiv.className = "alert alert-info";
    nouveauDiv.insertBefore(infoDiv, nouveauDiv.firstChild);
  }

  infoDiv.innerHTML = `
        <i class="fas fa-info-circle"></i> 
        Les informations ci-dessous ont été automatiquement remplies à partir du client sélectionné.
        Elles sont en lecture seule tant qu'un client existant est sélectionné.
    `;
}

// Modal des produits
function ouvrirModalProduits() {
  console.log("Ouverture modal produits, nombre de produits:", produits.length);
  const modal = document.getElementById("modal-produits");
  if (modal) {
    modal.style.display = "block";
    if (produits.length > 0) {
      afficherProduits(produits);
    } else {
      console.log("Aucun produit chargé, rechargement...");
      chargerProduits().then(() => {
        if (produits.length > 0) {
          afficherProduits(produits);
        }
      });
    }
  } else {
    console.error("Modal produits non trouvée");
  }
}

function fermerModal() {
  document.getElementById("modal-produits").style.display = "none";
}

function rechercherProduits() {
  const recherche = document
    .getElementById("recherche-produit")
    .value.toLowerCase();
  const produitsFiltres = produits.filter(
    (p) =>
      (p.designation && p.designation.toLowerCase().includes(recherche)) ||
      (p.nom && p.nom.toLowerCase().includes(recherche)) ||
      p.reference.toLowerCase().includes(recherche) ||
      (p.famille && p.famille.toLowerCase().includes(recherche))
  );
  afficherProduits(produitsFiltres);
}

function afficherProduits(produitsListe) {
  const container = document.getElementById("produits-liste");
  container.innerHTML = "";

  produitsListe.forEach((produit) => {
    const div = document.createElement("div");
    div.className = "produit-item";
    div.innerHTML = `
            <div>
                <strong>${produit.designation || produit.nom}</strong><br>
                <small>Réf: ${produit.reference}</small>
            </div>
            <div>${produit.description || ""}</div>
            <div class="prix">${parseFloat(
              produit.prixVente || produit.prix
            ).toFixed(2)} €</div>
        `;
    div.addEventListener("click", () => ajouterProduit(produit));
    container.appendChild(div);
  });
}

// Ajouter un produit depuis la modal
function ajouterProduit(produit) {
  const articlesContainer = document.getElementById("articles-container");
  const articleDiv = document.createElement("div");
  articleDiv.className = "article-item";
  articleDiv.dataset.produitId = produit.id;

  const prix = produit.prixVente || produit.prix || 0;
  const nom = produit.designation || produit.nom || "";
  const formatVal = produit.format || produit.produit_format || "";
  const couleurVal =
    produit.couleur_interieur ||
    produit.couleur ||
    produit.produit_couleur ||
    "";
  const conditionnementVal = produit.conditionnement || "";

  const descriptionPrefill = `${produit.description || ""}${
    formatVal ? "\nFormat: " + formatVal : ""
  }${couleurVal ? "\nCouleur: " + couleurVal : ""}`;

  articleDiv.innerHTML = `
      <div class="ref-col">
        <input type="text" name="produit_reference[]" value="${
          produit.reference || ""
        }" readonly class="ref-input">
      </div>
      <div class="designation-col">
        <input type="text" name="designation[]" value="${nom}" required>
        <textarea name="description[]" placeholder="Description détaillée...">${descriptionPrefill}</textarea>
      </div>
      <div class="cdt-col">
        <input type="text" name="conditionnement[]" value="${conditionnementVal}" readonly class="cdt-input">
      </div>
      <div>
        <input type="number" name="quantite[]" value="1" min="1" step="1" class="quantite-input" required>
      </div>
      <div>
        <input type="number" name="prix_unitaire[]" value="${prix}" min="0" step="0.01" class="prix-input" required>
      </div>
      <div class="remise-group">
        <input type="number" name="remise_valeur[]" value="0" min="0" step="0.01" class="remise-input">
        <select name="remise_type[]" class="remise-type">
          <option value="percent">%</option>
          <option value="fixe">€</option>
        </select>
      </div>
      <div>
        <input type="number" name="total_ligne[]" class="total-ligne" value="${prix}" readonly>
      </div>
      <input type="hidden" name="produit_id[]" value="${produit.id}">
      <div>
        <button type="button" class="btn-remove">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    `;

  // Ajouter les event listeners pour les calculs
  const inputs = articleDiv.querySelectorAll(
    ".quantite-input, .prix-input, .remise-input, .remise-type"
  );
  inputs.forEach((input) => {
    input.addEventListener("input", () => calculerLigneTotal(articleDiv));
    input.addEventListener("change", () => calculerLigneTotal(articleDiv));
  });

  // Event listener pour supprimer
  articleDiv
    .querySelector(".btn-remove")
    .addEventListener("click", () => retirerArticle(articleDiv));

  articlesContainer.appendChild(articleDiv);
  fermerModal();

  // Si le produit ne contenait pas de conditionnement, tenter de récupérer les détails via AJAX
  (function ensureDetails() {
    const condInput = articleDiv.querySelector(
      'input[name="conditionnement[]"]'
    );
    const prixInput = articleDiv.querySelector('input[name="prix_unitaire[]"]');
    if (condInput && (!condInput.value || condInput.value === "")) {
      const ref = produit.reference || "";
      if (ref) {
        fetch("../ajax/get-produit-details.php?ref=" + encodeURIComponent(ref))
          .then((r) => r.json())
          .then((data) => {
            if (data && data.success) {
              if (condInput) condInput.value = data.conditionnement || "";
              if (prixInput && data.prixVente)
                prixInput.value = parseFloat(data.prixVente).toFixed(2);
              // Recalculate totals after update
              calculerLigneTotal(articleDiv);
              calculerTotaux();
            }
          })
          .catch(() => {});
      }
    }
  })();

  calculerTotaux();
}

// Ajouter un article libre
function ajouterArticleLibre() {
  const articlesContainer = document.getElementById("articles-container");
  const articleDiv = document.createElement("div");
  articleDiv.className = "article-item";
  articleDiv.dataset.id = nextArticleId++;

  articleDiv.innerHTML = `
        <div class="designation-col">
            <input type="text" name="designation[]" placeholder="Désignation..." required>
            <textarea name="description[]" placeholder="Description détaillée..."></textarea>
        </div>
      <div class="ref-col"><input type="text" name="produit_reference[]" value="" readonly class="ref-input"></div>
      <div class="cdt-col"><input type="text" name="conditionnement[]" value="" readonly class="cdt-input"></div>
      <div><input type="number" name="quantite[]" value="1" min="1" step="1" class="quantite-input" required></div>
        <div><input type="number" name="prix_unitaire[]" value="0" min="0" step="0.01" class="prix-input" required></div>
        <div class="remise-group">
            <input type="number" name="remise_valeur[]" value="0" min="0" step="0.01" class="remise-input">
            <select name="remise_type[]" class="remise-type">
                <option value="percent">%</option>
                <option value="fixe">€</option>
            </select>
        </div>
        <input type="number" name="total_ligne[]" class="total-ligne" value="0" readonly>
        <input type="hidden" name="produit_id[]" value="">
        <button type="button" class="btn-remove">
            <i class="fas fa-trash"></i>
        </button>
    `;

  // Ajouter les event listeners pour les calculs
  const inputs = articleDiv.querySelectorAll(
    ".quantite-input, .prix-input, .remise-input, .remise-type"
  );
  inputs.forEach((input) => {
    input.addEventListener("input", () => calculerLigneTotal(articleDiv));
    input.addEventListener("change", () => calculerLigneTotal(articleDiv));
  });

  // Event listener pour supprimer
  articleDiv
    .querySelector(".btn-remove")
    .addEventListener("click", () => retirerArticle(articleDiv));

  articlesContainer.appendChild(articleDiv);
  calculerTotaux();
}

// Retirer un article
function retirerArticle(articleDiv) {
  if (confirm("Êtes-vous sûr de vouloir retirer cet article ?")) {
    articleDiv.remove();
    calculerTotaux();
  }
}

// Calculer le total d'une ligne
function calculerLigneTotal(articleDiv) {
  const quantiteEl = articleDiv.querySelector(
    'input[name="quantite[]"], input[name*="[quantite]"], .quantite-input, .input-quantite'
  );
  const prixEl = articleDiv.querySelector(
    'input[name="prix_unitaire[]"], input[name*="[prix_unitaire]"], .prix-input, .input-prix'
  );
  const remiseEl = articleDiv.querySelector(
    'input[name="remise_valeur[]"], input[name*="[remise_valeur]"], .remise-input, .input-remise'
  );
  const remiseTypeEl = articleDiv.querySelector(
    'select[name="remise_type[]"], select[name*="[remise_type]"], .remise-type, .select-remise-type'
  );

  const quantite = parseFloat(quantiteEl ? quantiteEl.value : 0) || 0;
  const prixUnitaire = parseFloat(prixEl ? prixEl.value : 0) || 0;
  const remiseValeur = parseFloat(remiseEl ? remiseEl.value : 0) || 0;
  const remiseType = remiseTypeEl ? remiseTypeEl.value : "percent";

  let sousTotal = quantite * prixUnitaire;
  let remiseMontant = 0;

  if (remiseValeur > 0) {
    if (remiseType === "percent") {
      remiseMontant = sousTotal * (remiseValeur / 100);
    } else {
      remiseMontant = remiseValeur * quantite;
    }
  }

  const totalLigne = Math.max(0, sousTotal - remiseMontant);
  const totalLigneEl = articleDiv.querySelector(".total-ligne");
  if (totalLigneEl) {
    if (totalLigneEl.tagName === "INPUT" || totalLigneEl.tagName === "input") {
      totalLigneEl.value = totalLigne.toFixed(2);
    } else {
      // afficher format français avec virgule
      totalLigneEl.textContent = totalLigne.toFixed(2).replace(".", ",") + " €";
    }
  }

  calculerTotaux();
}

// Calculer les totaux généraux
function calculerTotaux() {
  const lignes = document.querySelectorAll(".total-ligne");
  let totalHT = 0;

  lignes.forEach((ligne) => {
    let valeur = 0;
    // Si c'est un input, prendre la valeur
    if (ligne.tagName === "INPUT" || ligne.tagName === "input") {
      valeur = parseFloat(ligne.value) || 0;
    } else {
      // Sinon tenter de parser le texte (ex: "98,00 €" ou "98.00 €")
      const txt = (ligne.textContent || ligne.innerText || "").trim();
      // Remplacer espace insécable et espace, convertir virgule en point, supprimer caractères non numériques
      const cleaned = txt
        .replace(/\s/g, "")
        .replace(/,/g, ".")
        .replace(/[^0-9.\-]/g, "");
      valeur = parseFloat(cleaned) || 0;
    }
    totalHT += valeur;
  });

  // Logique frais de port : 13.95€ ou gratuit si totalHT >= 200€
  let fraisPort = 0;
  let fraisPortDisplay = "";

  if (totalHT >= 200) {
    fraisPort = 0;
    fraisPortDisplay = "Frais de port Offerts";
  } else {
    fraisPort = 13.95;
    fraisPortDisplay = "13,95 €";
  }

  // Mise à jour des valeurs
  document.getElementById("frais_port").value = fraisPort;
  document.getElementById("frais-port-display").textContent = fraisPortDisplay;

  const sousTotal = totalHT + fraisPort;
  const tva = sousTotal * 0.2; // 20% de TVA
  const totalTTC = sousTotal + tva;

  // Affichage
  document.getElementById("total-ht").textContent = totalHT.toFixed(2) + " €";
  document.getElementById("sous-total").textContent =
    sousTotal.toFixed(2) + " €";
  document.getElementById("tva-montant").textContent = tva.toFixed(2) + " €";
  document.getElementById("total-ttc").textContent = totalTTC.toFixed(2) + " €";
}

// Enregistrer le devis
async function enregistrerDevis() {
  const form = document.getElementById("form-devis");
  const formData = new FormData(form);

  // Validation basique
  if (!validerFormulaire()) return;

  try {
    document.getElementById("btn-enregistrer").disabled = true;
    document.getElementById("btn-enregistrer").innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

    const response = await fetch("process-devis.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      alert("Devis enregistré avec succès !");
      window.location.href = "gestion-devis.php";
    } else {
      alert("Erreur : " + result.message);
    }
  } catch (error) {
    console.error("Erreur:", error);
    alert("Erreur lors de l'enregistrement");
  } finally {
    document.getElementById("btn-enregistrer").disabled = false;
    document.getElementById("btn-enregistrer").innerHTML =
      '<i class="fas fa-save"></i> Enregistrer';
  }
}

// Validation du formulaire
function validerFormulaire() {
  // Vérifier qu'il y a au moins un article
  const articles = document.querySelectorAll(".article-item");
  if (articles.length === 0) {
    alert("Veuillez ajouter au moins un article au devis");
    return false;
  }

  // Vérifier les champs client
  const typeClient = document.getElementById("type_client").value;
  if (typeClient === "existant") {
    const clientId = document.getElementById("client_existant_id").value;
    if (!clientId) {
      alert("Veuillez sélectionner un client");
      return false;
    }
    // Vérifier que les champs sont remplis (ils devraient l'être automatiquement)
    const nom = document.getElementById("nouveau_nom").value.trim();
    const prenom = document.getElementById("nouveau_prenom").value.trim();
    const email = document.getElementById("nouveau_email").value.trim();

    if (!nom || !prenom || !email) {
      alert(
        "Erreur: Les données du client n'ont pas été chargées correctement"
      );
      return false;
    }
  } else {
    const nom = document.getElementById("nouveau_nom").value.trim();
    const prenom = document.getElementById("nouveau_prenom").value.trim();
    const email = document.getElementById("nouveau_email").value.trim();

    if (!nom || !prenom || !email) {
      alert("Veuillez remplir les champs obligatoires du nouveau client");
      return false;
    }
  }

  return true;
}

// Imprimer le devis
function imprimerDevis() {
  // Vérifier s'il y a des articles
  const articles = document.querySelectorAll(".article-item");
  if (articles.length === 0) {
    alert("Veuillez ajouter au moins un article avant d'imprimer le devis.");
    return;
  }

  // Créer une nouvelle fenêtre pour l'impression
  const printWindow = window.open("", "_blank", "width=800,height=600");

  // Générer le HTML pour l'impression
  const printContent = genererContenuImpression();

  printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Devis</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .client-info { margin-bottom: 20px; }
                .articles-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .articles-table th, .articles-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .articles-table th { background-color: #f5f5f5; }
                .totaux { margin-top: 20px; text-align: right; }
                .totaux div { margin: 5px 0; }
                .total-final { font-weight: bold; font-size: 1.2em; border-top: 2px solid #333; padding-top: 10px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            ${printContent}
            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                };
            </script>
        </body>
        </html>
    `);

  printWindow.document.close();
}

// Générer le contenu HTML pour l'impression
function genererContenuImpression() {
  const typeClient = document.getElementById("type_client")
    ? document.getElementById("type_client").value
    : "nouveau";
  // Préparer trois colonnes: col1 (nom+email), billing, shipping
  let col1_html = "";
  let billing_html = "";
  let shipping_html = "";

  if (typeClient === "existant") {
    const clientSelect = document.getElementById("client_existant_id");
    if (clientSelect && clientSelect.selectedIndex >= 0) {
      const opt = clientSelect.options[clientSelect.selectedIndex];
      col1_html = opt ? opt.textContent : "Client non sélectionné";
    } else {
      col1_html = "Client non sélectionné";
    }
    // pour un client existant, essayer de lire les champs remplis automatiquement dans le formulaire
    const societe = document.getElementById("nouveau_societe")
      ? document.getElementById("nouveau_societe").value
      : "";
    const nom = document.getElementById("nouveau_nom")
      ? document.getElementById("nouveau_nom").value
      : "";
    const prenom = document.getElementById("nouveau_prenom")
      ? document.getElementById("nouveau_prenom").value
      : "";
    const email = document.getElementById("nouveau_email")
      ? document.getElementById("nouveau_email").value
      : "";
    const adresse = document.getElementById("nouveau_adresse")
      ? document.getElementById("nouveau_adresse").value
      : "";
    const codePostal = document.getElementById("nouveau_code_postal")
      ? document.getElementById("nouveau_code_postal").value
      : "";
    const ville = document.getElementById("nouveau_ville")
      ? document.getElementById("nouveau_ville").value
      : "";
    const telephone = document.getElementById("nouveau_telephone")
      ? document.getElementById("nouveau_telephone").value
      : "";

    const billing_parts = [];
    if (societe) billing_parts.push(societe);
    if (prenom || nom) billing_parts.push((prenom + " " + nom).trim());
    if (adresse) billing_parts.push(adresse);
    if (codePostal || ville)
      billing_parts.push((codePostal + " " + ville).trim());
    if (telephone) billing_parts.push("Tél: " + telephone);
    billing_html = billing_parts.length
      ? billing_parts.join("<br>")
      : "<em>Voir fiche client</em>";

    // Adresse de livraison si renseignée (champs spécifiques), sinon même que facturation
    const adresseLiv = document.getElementById("adresse_livraison")
      ? document.getElementById("adresse_livraison").value
      : "";
    const cpLiv = document.getElementById("code_postal_livraison")
      ? document.getElementById("code_postal_livraison").value
      : "";
    const villeLiv = document.getElementById("ville_livraison")
      ? document.getElementById("ville_livraison").value
      : "";
    if (adresseLiv || cpLiv || villeLiv) {
      const shipping_parts = [];
      if (adresseLiv) shipping_parts.push(adresseLiv);
      if (cpLiv || villeLiv)
        shipping_parts.push((cpLiv + " " + villeLiv).trim());
      shipping_html = shipping_parts.join("<br>");
    } else {
      shipping_html = "<em>Même que facturation</em>";
    }
  } else {
    const nom = document.getElementById("nouveau_nom").value || "";
    const prenom = document.getElementById("nouveau_prenom").value || "";
    const societeEl = document.getElementById("nouveau_societe");
    const societe = societeEl ? societeEl.value || "" : "";
    const email = document.getElementById("nouveau_email").value || "";
    const adresse = document.getElementById("nouveau_adresse").value || "";
    const codePostal =
      document.getElementById("nouveau_code_postal").value || "";
    const ville = document.getElementById("nouveau_ville").value || "";

    col1_html = `<strong>${prenom} ${nom}</strong><br>${email}`;

    const billing_parts = [];
    if (societe) billing_parts.push(societe);
    if (nom || prenom) billing_parts.push(`${prenom} ${nom}`.trim());
    if (adresse) billing_parts.push(adresse);
    if (codePostal || ville)
      billing_parts.push(`${codePostal} ${ville}`.trim());
    billing_html = billing_parts.join("<br>") || "<em>—</em>";

    // Adresse de livraison optionnelle
    const adresseLiv = document.getElementById("adresse_livraison")
      ? document.getElementById("adresse_livraison").value
      : "";
    const cpLiv = document.getElementById("code_postal_livraison")
      ? document.getElementById("code_postal_livraison").value
      : "";
    const villeLiv = document.getElementById("ville_livraison")
      ? document.getElementById("ville_livraison").value
      : "";
    if (adresseLiv || cpLiv || villeLiv) {
      const shipping_parts = [];
      if (adresseLiv) shipping_parts.push(adresseLiv);
      if (cpLiv || villeLiv) shipping_parts.push(`${cpLiv} ${villeLiv}`.trim());
      shipping_html = shipping_parts.join("<br>");
    } else {
      shipping_html = "<em>Même que facturation</em>";
    }
  }

  // Construire le HTML 3 colonnes
  const clientColumnsHTML = `
    <div style="display:flex; gap:20px; margin-bottom:10px;">
      <div style="flex:1; background:#f8f9fa; padding:10px; border-radius:4px;">
        ${col1_html}
      </div>
      <div style="flex:1; background:#f8f9fa; padding:10px; border-radius:4px;">
        <strong>Adresse de facturation</strong><br>${billing_html}
      </div>
      <div style="flex:1; background:#f8f9fa; padding:10px; border-radius:4px;">
        <strong>Adresse de livraison</strong><br>${shipping_html}
      </div>
    </div>
  `;

  // Générer le tableau des articles (avec Ref, Description (format & couleur), Cdt, TVA)
  const articles = document.querySelectorAll(".article-item");
  let articlesHTML = "";

  function formatMoney(num) {
    const n = Number(num) || 0;
    return n.toFixed(2).replace(".", ",") + " €";
  }

  articles.forEach((article) => {
    // produit id / référence
    const produitId =
      article.dataset.produitId ||
      (article.querySelector('input[name="produit_id[]"]')
        ? article.querySelector('input[name="produit_id[]"]').value
        : null);
    let produit = null;
    if (produitId && Array.isArray(produits)) {
      produit = produits.find((p) => String(p.id) === String(produitId));
    }

    const ref =
      (article.querySelector('input[name="produit_reference[]"]') &&
        article.querySelector('input[name="produit_reference[]"]').value) ||
      (produit && (produit.reference || produit.produit_reference)) ||
      "";

    const designationEl =
      article.querySelector('input[name="designation[]"]') ||
      article.querySelector('textarea[name="designation[]"]') ||
      article.querySelector(".designation-col input") ||
      article.querySelector(".designation-col");
    const designation = designationEl
      ? (designationEl.value || designationEl.textContent || "").trim()
      : "";

    const format =
      (produit && (produit.format || produit.produit_format)) ||
      (article.querySelector('input[name="format[]"]')
        ? article.querySelector('input[name="format[]"]').value
        : "") ||
      "";
    const couleur =
      (produit &&
        (produit.couleur_interieur ||
          produit.couleur ||
          produit.produit_couleur)) ||
      (article.querySelector('input[name*="couleur"]')
        ? article.querySelector('input[name*="couleur"]').value
        : "") ||
      "";
    const conditionnement =
      (produit && produit.conditionnement) ||
      (article.querySelector('input[name="conditionnement[]"]')
        ? article.querySelector('input[name="conditionnement[]"]').value
        : "") ||
      "";

    const quantiteEl =
      article.querySelector('input[name="quantite[]"]') ||
      article.querySelector(".quantite-input") ||
      article.querySelector('input[name*="[quantite]"]');
    const quantite = quantiteEl
      ? quantiteEl.value || quantiteEl.textContent || "0"
      : "0";

    const prixEl =
      article.querySelector('input[name="prix_unitaire[]"]') ||
      article.querySelector(".prix-input") ||
      article.querySelector('input[name*="[prix_unitaire]"]');
    const prixUnitaire = prixEl
      ? parseFloat(prixEl.value || prixEl.textContent || 0)
      : 0;

    const tauxEl =
      article.querySelector('input[name="taux_tva[]"]') ||
      article.querySelector('input[name*="taux_tva"]');
    const tauxTva = tauxEl ? parseFloat(tauxEl.value) || 0 : 20;

    const totalLigneEl = article.querySelector(".total-ligne");
    const totalLigneRaw = totalLigneEl
      ? totalLigneEl.value || totalLigneEl.textContent || "0"
      : "0";
    const totalLigne =
      parseFloat(
        String(totalLigneRaw)
          .replace(",", ".")
          .replace(/[^0-9.\-]/g, "")
      ) || 0;

    const descParts = [];
    if (designation) descParts.push(designation);
    if (format) descParts.push(format);
    if (couleur) descParts.push(couleur);
    const description_display = descParts.join(" — ");

    articlesHTML += `
            <tr>
                <td>${ref}</td>
                <td>${description_display}</td>
                <td style="text-align:center">${conditionnement}</td>
                <td style="text-align:center">${quantite}</td>
                <td style="text-align:right">${formatMoney(prixUnitaire)}</td>
                <td style="text-align:right">${tauxTva}%</td>
                <td style="text-align:right">${formatMoney(totalLigne)}</td>
            </tr>
        `;
  });

  // Récupérer les totaux
  const totalHT = document.getElementById("total-ht")
    ? document.getElementById("total-ht").textContent
    : formatMoney(0);
  const fraisPort = document.getElementById("frais-port-display")
    ? document.getElementById("frais-port-display").textContent
    : formatMoney(0);
  const sousTotal = document.getElementById("sous-total")
    ? document.getElementById("sous-total").textContent
    : formatMoney(0);
  const tva = document.getElementById("tva-montant")
    ? document.getElementById("tva-montant").textContent
    : formatMoney(0);
  const totalTTC = document.getElementById("total-ttc")
    ? document.getElementById("total-ttc").textContent
    : formatMoney(0);

  const dateActuelle = new Date().toLocaleDateString("fr-FR");

  // Calculer durée de validité en jours (si date_expiration présent)
  let dureeValidite = 30;
  const dateExpEl = document.getElementById("date_expiration");
  if (dateExpEl && dateExpEl.value) {
    try {
      const exp = new Date(dateExpEl.value);
      const today = new Date();
      const diff = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
      if (diff > 0) dureeValidite = diff;
    } catch (e) {
      dureeValidite = 30;
    }
  }

  const conditionsPaiement =
    (document.getElementById("conditions_particulieres")
      ? document.getElementById("conditions_particulieres").value
      : "") || "À réception de facture";

  return `
        <div class="header" style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div style="flex:1;">
            <img src="../images/logo-icon/logo.svg" alt="Bindy Studio" style="max-height:70px;">
            <div style="font-weight:bold; color:#007bff;">Bindy Studio</div>
            <div style="color:#666;">by General Cover<br>9 rue de la gare<br>70000 Vallerois-le-Boiss<br>Tel: 03 84 78 38 39<br>contact@bindy-studio.fr</div>
          </div>
          <div style="text-align:right; flex:1;">
            <div style="font-size:24px; font-weight:bold; color:#007bff;">DEVIS</div>
            <div>Date : ${dateActuelle}</div>
          </div>
        </div>
        
        <div>
          <h3>Client :</h3>
          ${clientColumnsHTML}
        </div>
        
        <table class="articles-table">
            <thead>
              <tr>
                <th>Ref</th>
                <th>Description</th>
                <th>Cdt</th>
                <th>Qte</th>
                <th>Prix unitaire</th>
                <th>TVA</th>
                <th>Total HT</th>
              </tr>
            </thead>
            <tbody>
                ${articlesHTML}
            </tbody>
        </table>
        
        <div class="totaux">
          <div>Total HT : ${totalHT}</div>
          <div>Frais de port : ${fraisPort}</div>
          <div>Sous-total : ${sousTotal}</div>
          <div>TVA (20%) : ${tva}</div>
          <div class="total-final">Total TTC : ${totalTTC}</div>
        </div>

        <div class="footer" style="margin-top:30px; font-size:12px; color:#666; text-align:center;">
          <p>Ce devis est valable ${dureeValidite} jours à partir de la date d'émission.</p>
          <p>Conditions de paiement : ${conditionsPaiement}</p>
          <p>SIRET: 423 249 879 00010 - TVA: FR55423249879000010</p>
          <p>Téléphone: 03 84 78 38 39 - Email: contact@bindy-studio.fr</p>
        </div>
    `;
}

// Gestion des clics en dehors de la modal
window.onclick = function (event) {
  const modal = document.getElementById("modal-produits");
  if (event.target === modal) {
    fermerModal();
  }
};
