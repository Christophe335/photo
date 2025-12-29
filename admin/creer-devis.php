<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

// Les clients et produits seront chargés dynamiquement via AJAX

include 'header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Créer un nouveau devis</h2>
    <div>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<form id="form-devis" method="POST" action="process-devis.php" class="devis-form">
    <input type="hidden" name="action" value="create">
    
    <!-- Informations client -->
    <div class="form-section">
        <h3><i class="fas fa-user"></i> Informations client</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="type_client">Type de client</label>
                <select id="type_client" name="type_client">
                    <option value="nouveau">Nouveau client</option>
                    <option value="existant">Client existant</option>
                </select>
            </div>
        </div>
        
        <!-- Sélection client existant -->
        <div id="client-existant" style="display: none;">
            <div class="form-group">
                <label for="client_id">Sélectionner un client</label>
                <select id="client_existant_id" name="client_id">
                    <option value="">-- Chargement des clients... --</option>
                </select>
            </div>
        </div>
        
        <!-- Nouveau client -->
        <div id="client-nouveau">
            <div class="form-group">
                <label for="nouveau_societe">Société</label>
                <input type="text" id="nouveau_societe" name="nouveau_societe">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_prenom">Prénom *</label>
                    <input type="text" id="nouveau_prenom" name="nouveau_prenom" required>
                </div>
                <div class="form-group">
                    <label for="client_nom">Nom *</label>
                    <input type="text" id="nouveau_nom" name="nouveau_nom" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_email">Email *</label>
                    <input type="email" id="nouveau_email" name="nouveau_email" required>
                </div>
                <div class="form-group">
                    <label for="client_telephone">Téléphone</label>
                    <input type="tel" id="nouveau_telephone" name="nouveau_telephone">
                </div>
            </div>
            
            <h4>Adresse de facturation</h4>
            <div class="form-group">
                <label for="client_adresse">Adresse *</label>
                <textarea id="nouveau_adresse" name="nouveau_adresse" rows="3" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_code_postal">Code postal *</label>
                    <input type="text" id="nouveau_code_postal" name="nouveau_code_postal" required>
                </div>
                <div class="form-group">
                    <label for="client_ville">Ville *</label>
                    <input type="text" id="nouveau_ville" name="nouveau_ville" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="client_pays">Pays</label>
                <select id="client_pays" name="nouveau_pays">
                    <option value="France">France</option>
                    <option value="Belgique">Belgique</option>
                    <option value="Suisse">Suisse</option>
                    <option value="Luxembourg">Luxembourg</option>
                </select>
            </div>
            
            <!-- Adresse de livraison -->
            <div class="form-group">
                <label>
                    <input type="checkbox" id="adresse_livraison_differente" name="adresse_livraison_differente" 
                           onchange="toggleAdresseLivraison()">
                    Adresse de livraison différente
                </label>
            </div>
            
            <div id="adresse-livraison" style="display: none;">
                <h4>Adresse de livraison</h4>
                <div class="form-group">
                    <label for="adresse_livraison">Adresse</label>
                    <textarea id="adresse_livraison" name="adresse_livraison" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal_livraison">Code postal</label>
                        <input type="text" id="code_postal_livraison" name="code_postal_livraison">
                    </div>
                    <div class="form-group">
                        <label for="ville_livraison">Ville</label>
                        <input type="text" id="ville_livraison" name="ville_livraison">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="pays_livraison">Pays</label>
                    <select id="pays_livraison" name="pays_livraison">
                        <option value="France">France</option>
                        <option value="Belgique">Belgique</option>
                        <option value="Suisse">Suisse</option>
                        <option value="Luxembourg">Luxembourg</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Articles du devis -->
    <div class="form-section">
        <h3><i class="fas fa-list"></i> Articles du devis</h3>
        
        <div class="articles-toolbar">
            <button type="button" id="btn-ajouter-produit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un produit
            </button>
            <button type="button" id="btn-ajouter-libre" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Ligne libre
            </button>
        </div>
        
        <div class="articles-container">
            <div class="articles-header">
                <div>Ref</div>
                <div>Désignation</div>
                <div>Cdt</div>
                <div>Quantité</div>
                <div>Prix unitaire</div>
                <div>Remise</div>
                <div>Total ligne</div>
                <div></div>
            </div>
            <div id="articles-container">
                <!-- Les articles seront ajoutés dynamiquement ici -->
            </div>
            
            <div class="totaux">
                <div class="totaux-row">
                    <span>Total HT :</span>
                    <span id="total-ht">0,00 €</span>
                </div>
                <div class="totaux-row">
                    <span>Frais de port :</span>
                    <span id="frais-port-display">13,95 €</span>
                    <input type="hidden" id="frais_port" name="frais_port" value="13.95">
                </div>
                <div class="totaux-row">
                    <span>Sous-total :</span>
                    <span id="sous-total">0,00 €</span>
                </div>
                <div class="totaux-row">
                    <span>TVA (20%) :</span>
                    <span id="tva-montant">0,00 €</span>
                </div>
                <div class="totaux-row total-final">
                    <span>Total TTC :</span>
                    <span id="total-ttc">0,00 €</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informations complémentaires -->
    <div class="form-section">
        <h3><i class="fas fa-info-circle"></i> Informations complémentaires</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="date_expiration">Date d'expiration</label>
                <input type="date" id="date_expiration" name="date_expiration" 
                       value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes internes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Notes visibles uniquement dans l'administration"></textarea>
        </div>
        
        <div class="form-group">
            <label for="conditions_particulieres">Conditions particulières</label>
            <textarea id="conditions_particulieres" name="conditions_particulieres" rows="4" 
                      placeholder="Conditions spécifiques qui apparaîtront sur le devis imprimé"></textarea>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="form-actions">
        <button type="button" id="btn-enregistrer" class="btn btn-success">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <button type="button" id="btn-imprimer" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="gestion-devis.php" class="btn btn-secondary">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>

<!-- Modal pour sélectionner un produit -->
<div id="modal-produits" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sélectionner un produit</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <input type="text" id="recherche-produit" placeholder="Rechercher un produit...">
            <div id="produits-liste">
                <!-- Les produits seront chargés dynamiquement via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script src="../js/devis.js"></script>

<?php include 'footer_simple.php'; ?>