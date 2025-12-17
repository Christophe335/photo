// Gestion des devis
let clients = [];
let produits = [];
let nextArticleId = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    chargerClients();
    chargerProduits();
    
    // Event listeners avec vérification d'existence
    const typeClient = document.getElementById('type_client');
    if (typeClient) typeClient.addEventListener('change', toggleClientForm);
    
    const clientExistant = document.getElementById('client_existant_id');
    if (clientExistant) clientExistant.addEventListener('change', chargerClientExistant);
    
    const btnAjouterProduit = document.getElementById('btn-ajouter-produit');
    if (btnAjouterProduit) btnAjouterProduit.addEventListener('click', ouvrirModalProduits);
    
    const btnAjouterLibre = document.getElementById('btn-ajouter-libre');
    if (btnAjouterLibre) btnAjouterLibre.addEventListener('click', ajouterArticleLibre);
    
    // Les frais de port sont maintenant calculés automatiquement
    
    // Gestion modal
    const closeBtn = document.querySelector('.close');
    if (closeBtn) closeBtn.addEventListener('click', fermerModal);
    
    const rechercheInput = document.getElementById('recherche-produit');
    if (rechercheInput) rechercheInput.addEventListener('input', rechercherProduits);
    
    // Boutons formulaire
    const btnEnregistrer = document.getElementById('btn-enregistrer');
    if (btnEnregistrer) btnEnregistrer.addEventListener('click', enregistrerDevis);
    
    const btnImprimer = document.getElementById('btn-imprimer');
    if (btnImprimer) btnImprimer.addEventListener('click', imprimerDevis);
    
    calculerTotaux();
    
    console.log('Devis.js initialisé - Boutons trouvés:', {
        btnAjouterProduit: !!btnAjouterProduit,
        btnAjouterLibre: !!btnAjouterLibre,
        btnEnregistrer: !!btnEnregistrer
    });
});

// Chargement des clients
async function chargerClients() {
    console.log('chargerClients() appelée');
    try {
        console.log('Fetching clients from: ajax/get-clients.php');
        const response = await fetch('ajax/get-clients.php');
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        clients = JSON.parse(responseText);
        console.log('Clients parsed:', clients);
        
        const select = document.getElementById('client_existant_id');
        if (!select) {
            console.error('Element client_existant_id non trouvé !');
            return;
        }
        
        console.log('Remplissage du select...');
        select.innerHTML = '<option value="">Choisir un client...</option>';
        
        if (!Array.isArray(clients)) {
            console.error('clients n\'est pas un tableau:', clients);
            return;
        }
        
        clients.forEach((client, index) => {
            console.log(`Ajout client ${index}:`, client);
            const option = document.createElement('option');
            option.value = client.id;
            const societeText = client.societe ? ` - ${client.societe}` : ' - Particulier';
            option.textContent = `${client.nom} ${client.prenom}${societeText}`;
            select.appendChild(option);
        });
        
        console.log(`${clients.length} clients ajoutés au select`);
        
    } catch (error) {
        console.error('Erreur chargement clients:', error);
        alert('Erreur lors du chargement des clients: ' + error.message);
    }
}

// Chargement des produits
async function chargerProduits() {
    try {
        console.log('Chargement des produits...');
        const response = await fetch('ajax/get-produits.php');
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        const data = await response.json();
        if (data.error) {
            throw new Error(data.error);
        }
        produits = data;
        console.log('Produits chargés:', produits.length);
        return produits;
    } catch (error) {
        console.error('Erreur chargement produits:', error);
        produits = [];
        return [];
    }
}

// Toggle entre client existant et nouveau
function toggleClientForm() {
    const typeClient = document.getElementById('type_client').value;
    const existantDiv = document.getElementById('client-existant');
    const nouveauDiv = document.getElementById('client-nouveau');
    
    if (typeClient === 'existant') {
        existantDiv.style.display = 'block';
        nouveauDiv.style.display = 'block'; // Garder les champs visibles
        // Désactiver l'édition des champs car ils seront remplis automatiquement
        setClientFieldsReadonly(true);
    } else {
        existantDiv.style.display = 'none';
        nouveauDiv.style.display = 'block';
        // Réactiver l'édition des champs
        setClientFieldsReadonly(false);
        // Vider les champs et la sélection du client existant
        clearClientFields();
        document.getElementById('client_existant_id').value = '';
    }
}

// Activer/désactiver l'édition des champs client
function setClientFieldsReadonly(readonly) {
    const fields = ['nouveau_societe', 'nouveau_nom', 'nouveau_prenom', 'nouveau_email', 'nouveau_telephone', 
                    'nouveau_adresse', 'nouveau_code_postal', 'nouveau_ville'];
    
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.readOnly = readonly;
            if (readonly) {
                element.style.backgroundColor = '#f8f9fa';
                element.style.color = '#6c757d';
            } else {
                element.style.backgroundColor = 'white';
                element.style.color = '#333';
            }
        }
    });
}

// Vider les champs du client
function clearClientFields() {
    const fields = ['nouveau_societe', 'nouveau_nom', 'nouveau_prenom', 'nouveau_email', 'nouveau_telephone', 
                    'nouveau_adresse', 'nouveau_code_postal', 'nouveau_ville'];
    
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.value = '';
        }
    });
}

// Charger les données du client sélectionné
async function chargerClientExistant() {
    const clientId = document.getElementById('client_existant_id').value;
    console.log('chargerClientExistant appelée avec ID:', clientId);
    
    if (!clientId) {
        clearClientFields();
        return;
    }
    
    try {
        console.log('Fetching client data from:', `ajax/get-client-details.php?id=${clientId}`);
        const response = await fetch(`ajax/get-client-details.php?id=${clientId}`);
        console.log('Response status:', response.status);
        
        const client = await response.json();
        console.log('Client data received:', client);
        
        if (client && !client.error) {
            // Remplir les champs avec les données du client
            fillClientFields(client);
        } else {
            console.error('Erreur client:', client.error || 'Données client invalides');
            alert('Erreur lors du chargement des données client: ' + (client.error || 'Données invalides'));
        }
    } catch (error) {
        console.error('Erreur lors du chargement des détails client:', error);
        alert('Erreur de communication avec le serveur');
    }
}

// Remplir les champs avec les données du client sélectionné
function fillClientFields(client) {
    console.log('fillClientFields appelée avec:', client);
    
    // Mapping des champs
    const fieldMapping = {
        'nouveau_societe': client.societe || '',
        'nouveau_nom': client.nom || '',
        'nouveau_prenom': client.prenom || '',
        'nouveau_email': client.email || '',
        'nouveau_telephone': client.telephone || '',
        'nouveau_adresse': client.adresse || '',
        'nouveau_code_postal': client.code_postal || '',
        'nouveau_ville': client.ville || ''
    };
    
    console.log('Field mapping:', fieldMapping);
    
    // Remplir chaque champ
    Object.keys(fieldMapping).forEach(fieldId => {
        const element = document.getElementById(fieldId);
        console.log(`Champ ${fieldId}:`, element ? 'trouvé' : 'NON TROUVÉ', 'valeur:', fieldMapping[fieldId]);
        if (element) {
            element.value = fieldMapping[fieldId];
            console.log(`${fieldId} rempli avec:`, element.value);
        }
    });
    
    // Ajouter une indication visuelle que les données proviennent d'un client existant
    const nouveauDiv = document.getElementById('client-nouveau');
    let infoDiv = document.getElementById('client-info-notice');
    
    if (!infoDiv) {
        infoDiv = document.createElement('div');
        infoDiv.id = 'client-info-notice';
        infoDiv.className = 'alert alert-info';
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
    console.log('Ouverture modal produits, nombre de produits:', produits.length);
    const modal = document.getElementById('modal-produits');
    if (modal) {
        modal.style.display = 'block';
        if (produits.length > 0) {
            afficherProduits(produits);
        } else {
            console.log('Aucun produit chargé, rechargement...');
            chargerProduits().then(() => {
                if (produits.length > 0) {
                    afficherProduits(produits);
                }
            });
        }
    } else {
        console.error('Modal produits non trouvée');
    }
}

function fermerModal() {
    document.getElementById('modal-produits').style.display = 'none';
}

function rechercherProduits() {
    const recherche = document.getElementById('recherche-produit').value.toLowerCase();
    const produitsFiltres = produits.filter(p => 
        (p.designation && p.designation.toLowerCase().includes(recherche)) ||
        (p.nom && p.nom.toLowerCase().includes(recherche)) ||
        p.reference.toLowerCase().includes(recherche) ||
        (p.famille && p.famille.toLowerCase().includes(recherche))
    );
    afficherProduits(produitsFiltres);
}

function afficherProduits(produitsListe) {
    const container = document.getElementById('produits-liste');
    container.innerHTML = '';
    
    produitsListe.forEach(produit => {
        const div = document.createElement('div');
        div.className = 'produit-item';
        div.innerHTML = `
            <div>
                <strong>${produit.designation || produit.nom}</strong><br>
                <small>Réf: ${produit.reference}</small>
            </div>
            <div>${produit.description || ''}</div>
            <div class="prix">${parseFloat(produit.prixVente || produit.prix).toFixed(2)} €</div>
        `;
        div.addEventListener('click', () => ajouterProduit(produit));
        container.appendChild(div);
    });
}

// Ajouter un produit depuis la modal
function ajouterProduit(produit) {
    const articlesContainer = document.getElementById('articles-container');
    const articleDiv = document.createElement('div');
    articleDiv.className = 'article-item';
    articleDiv.dataset.id = nextArticleId++;
    articleDiv.dataset.produitId = produit.id;
    
    const prix = produit.prixVente || produit.prix || 0;
    const nom = produit.designation || produit.nom || '';
    
    articleDiv.innerHTML = `
        <div class="designation-col">
            <input type="text" name="designation[]" value="${nom}" required>
            <textarea name="description[]" placeholder="Description détaillée...">${produit.description || ''}</textarea>
        </div>
        <input type="number" name="quantite[]" value="1" min="1" step="1" class="quantite-input" required>
        <input type="number" name="prix_unitaire[]" value="${prix}" min="0" step="0.01" class="prix-input" required>
        <div class="remise-group">
            <input type="number" name="remise_valeur[]" value="0" min="0" step="0.01" class="remise-input">
            <select name="remise_type[]" class="remise-type">
                <option value="percent">%</option>
                <option value="fixe">€</option>
            </select>
        </div>
        <input type="number" name="total_ligne[]" class="total-ligne" value="${prix}" readonly>
        <input type="hidden" name="produit_id[]" value="${produit.id}">
        <button type="button" class="btn-remove">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    // Ajouter les event listeners pour les calculs
    const inputs = articleDiv.querySelectorAll('.quantite-input, .prix-input, .remise-input, .remise-type');
    inputs.forEach(input => {
        input.addEventListener('input', () => calculerLigneTotal(articleDiv));
        input.addEventListener('change', () => calculerLigneTotal(articleDiv));
    });
    
    // Event listener pour supprimer
    articleDiv.querySelector('.btn-remove').addEventListener('click', () => retirerArticle(articleDiv));
    
    articlesContainer.appendChild(articleDiv);
    fermerModal();
    calculerTotaux();
}

// Ajouter un article libre
function ajouterArticleLibre() {
    const articlesContainer = document.getElementById('articles-container');
    const articleDiv = document.createElement('div');
    articleDiv.className = 'article-item';
    articleDiv.dataset.id = nextArticleId++;
    
    articleDiv.innerHTML = `
        <div class="designation-col">
            <input type="text" name="designation[]" placeholder="Désignation..." required>
            <textarea name="description[]" placeholder="Description détaillée..."></textarea>
        </div>
        <input type="number" name="quantite[]" value="1" min="1" step="1" class="quantite-input" required>
        <input type="number" name="prix_unitaire[]" value="0" min="0" step="0.01" class="prix-input" required>
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
    const inputs = articleDiv.querySelectorAll('.quantite-input, .prix-input, .remise-input, .remise-type');
    inputs.forEach(input => {
        input.addEventListener('input', () => calculerLigneTotal(articleDiv));
        input.addEventListener('change', () => calculerLigneTotal(articleDiv));
    });
    
    // Event listener pour supprimer
    articleDiv.querySelector('.btn-remove').addEventListener('click', () => retirerArticle(articleDiv));
    
    articlesContainer.appendChild(articleDiv);
    calculerTotaux();
}

// Retirer un article
function retirerArticle(articleDiv) {
    if (confirm('Êtes-vous sûr de vouloir retirer cet article ?')) {
        articleDiv.remove();
        calculerTotaux();
    }
}

// Calculer le total d'une ligne
function calculerLigneTotal(articleDiv) {
    const quantite = parseFloat(articleDiv.querySelector('input[name="quantite[]"]').value) || 0;
    const prixUnitaire = parseFloat(articleDiv.querySelector('input[name="prix_unitaire[]"]').value) || 0;
    const remiseValeur = parseFloat(articleDiv.querySelector('input[name="remise_valeur[]"]').value) || 0;
    const remiseType = articleDiv.querySelector('select[name="remise_type[]"]').value;
    
    let sousTotal = quantite * prixUnitaire;
    let remiseMontant = 0;
    
    if (remiseValeur > 0) {
        if (remiseType === 'percent') {
            remiseMontant = sousTotal * (remiseValeur / 100);
        } else {
            remiseMontant = remiseValeur * quantite;
        }
    }
    
    const totalLigne = Math.max(0, sousTotal - remiseMontant);
    articleDiv.querySelector('.total-ligne').value = totalLigne.toFixed(2);
    
    calculerTotaux();
}

// Calculer les totaux généraux
function calculerTotaux() {
    const lignes = document.querySelectorAll('.total-ligne');
    let totalHT = 0;
    
    lignes.forEach(ligne => {
        totalHT += parseFloat(ligne.value) || 0;
    });
    
    // Logique frais de port : 13.95€ ou gratuit si totalHT >= 200€
    let fraisPort = 0;
    let fraisPortDisplay = '';
    
    if (totalHT >= 200) {
        fraisPort = 0;
        fraisPortDisplay = 'Frais de port Offerts';
    } else {
        fraisPort = 13.95;
        fraisPortDisplay = '13,95 €';
    }
    
    // Mise à jour des valeurs
    document.getElementById('frais_port').value = fraisPort;
    document.getElementById('frais-port-display').textContent = fraisPortDisplay;
    
    const sousTotal = totalHT + fraisPort;
    const tva = sousTotal * 0.20; // 20% de TVA
    const totalTTC = sousTotal + tva;
    
    // Affichage
    document.getElementById('total-ht').textContent = totalHT.toFixed(2) + ' €';
    document.getElementById('sous-total').textContent = sousTotal.toFixed(2) + ' €';
    document.getElementById('tva-montant').textContent = tva.toFixed(2) + ' €';
    document.getElementById('total-ttc').textContent = totalTTC.toFixed(2) + ' €';
}

// Enregistrer le devis
async function enregistrerDevis() {
    const form = document.getElementById('form-devis');
    const formData = new FormData(form);
    
    // Validation basique
    if (!validerFormulaire()) return;
    
    try {
        document.getElementById('btn-enregistrer').disabled = true;
        document.getElementById('btn-enregistrer').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        
        const response = await fetch('process-devis.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Devis enregistré avec succès !');
            window.location.href = 'gestion-devis.php';
        } else {
            alert('Erreur : ' + result.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'enregistrement');
    } finally {
        document.getElementById('btn-enregistrer').disabled = false;
        document.getElementById('btn-enregistrer').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
    }
}

// Validation du formulaire
function validerFormulaire() {
    // Vérifier qu'il y a au moins un article
    const articles = document.querySelectorAll('.article-item');
    if (articles.length === 0) {
        alert('Veuillez ajouter au moins un article au devis');
        return false;
    }
    
    // Vérifier les champs client
    const typeClient = document.getElementById('type_client').value;
    if (typeClient === 'existant') {
        const clientId = document.getElementById('client_existant_id').value;
        if (!clientId) {
            alert('Veuillez sélectionner un client');
            return false;
        }
        // Vérifier que les champs sont remplis (ils devraient l'être automatiquement)
        const nom = document.getElementById('nouveau_nom').value.trim();
        const prenom = document.getElementById('nouveau_prenom').value.trim();
        const email = document.getElementById('nouveau_email').value.trim();
        
        if (!nom || !prenom || !email) {
            alert('Erreur: Les données du client n\'ont pas été chargées correctement');
            return false;
        }
    } else {
        const nom = document.getElementById('nouveau_nom').value.trim();
        const prenom = document.getElementById('nouveau_prenom').value.trim();
        const email = document.getElementById('nouveau_email').value.trim();
        
        if (!nom || !prenom || !email) {
            alert('Veuillez remplir les champs obligatoires du nouveau client');
            return false;
        }
    }
    
    return true;
}

// Imprimer le devis
function imprimerDevis() {
    // Vérifier s'il y a des articles
    const articles = document.querySelectorAll('.article-item');
    if (articles.length === 0) {
        alert('Veuillez ajouter au moins un article avant d\'imprimer le devis.');
        return;
    }
    
    // Créer une nouvelle fenêtre pour l'impression
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
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
    const typeClient = document.getElementById('type_client').value;
    let clientInfo = '';
    
    if (typeClient === 'existant') {
        const clientSelect = document.getElementById('client_existant_id');
        const selectedOption = clientSelect.options[clientSelect.selectedIndex];
        clientInfo = selectedOption ? selectedOption.textContent : 'Client non sélectionné';
    } else {
        const nom = document.getElementById('nouveau_nom').value || '';
        const prenom = document.getElementById('nouveau_prenom').value || '';
        const entreprise = document.getElementById('nouveau_entreprise').value || '';
        const email = document.getElementById('nouveau_email').value || '';
        const adresse = document.getElementById('nouveau_adresse').value || '';
        const codePostal = document.getElementById('nouveau_code_postal').value || '';
        const ville = document.getElementById('nouveau_ville').value || '';
        
        clientInfo = `
            <strong>${prenom} ${nom}</strong><br>
            ${entreprise ? entreprise + '<br>' : ''}
            ${email}<br>
            ${adresse}<br>
            ${codePostal} ${ville}
        `;
    }
    
    // Générer le tableau des articles
    const articles = document.querySelectorAll('.article-item');
    let articlesHTML = '';
    
    articles.forEach(article => {
        const designation = article.querySelector('input[name="designation[]"]').value;
        const quantite = article.querySelector('input[name="quantite[]"]').value;
        const prixUnitaire = parseFloat(article.querySelector('input[name="prix_unitaire[]"]').value).toFixed(2);
        const totalLigne = parseFloat(article.querySelector('.total-ligne').value).toFixed(2);
        
        articlesHTML += `
            <tr>
                <td>${designation}</td>
                <td style="text-align: center;">${quantite}</td>
                <td style="text-align: right;">${prixUnitaire} €</td>
                <td style="text-align: right;">${totalLigne} €</td>
            </tr>
        `;
    });
    
    // Récupérer les totaux
    const totalHT = document.getElementById('total-ht').textContent;
    const fraisPort = document.getElementById('frais-port-display').textContent;
    const sousTotal = document.getElementById('sous-total').textContent;
    const tva = document.getElementById('tva-montant').textContent;
    const totalTTC = document.getElementById('total-ttc').textContent;
    
    const dateActuelle = new Date().toLocaleDateString('fr-FR');
    
    return `
        <div class="header">
            <h1>DEVIS</h1>
            <p>Date : ${dateActuelle}</p>
        </div>
        
        <div class="client-info">
            <h3>Client :</h3>
            ${clientInfo}
        </div>
        
        <table class="articles-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total ligne</th>
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
    `;
}

// Gestion des clics en dehors de la modal
window.onclick = function(event) {
    const modal = document.getElementById('modal-produits');
    if (event.target === modal) {
        fermerModal();
    }
}