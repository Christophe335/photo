<?php
// Inclusion de la configuration de base de données
require_once __DIR__ . '/database.php';

/**
 * Affichage du tableau des produits de personnalisation par famille
 * @param string $famille - Code de la famille de produits à afficher ('dorure' ou 'imprime')
 */
function afficherTablenalogPersonnalisation_deprecated($famille) {
    // backward compatibility placeholder
    afficherTableauPersonnalisation($famille, 1);
}

function afficherTableauPersonnalisation($famille, $quantiteParDefaut = 1) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupération des produits de la famille
        $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY reference ASC");
        $stmt->execute([$famille]);
        $produits = $stmt->fetchAll();
        
        if (empty($produits)) {
            echo '<p style="text-align: center; color: #666; padding: 20px;">Aucun produit de personnalisation trouvé pour cette catégorie. Les produits seront bientôt disponibles.</p>';
            return;
        }
        
        // Déterminer si c'est de la dorure (affiche couleurs) ou impression (pas de couleurs)
        $afficherCouleur = ($famille === 'dorure');
        $titreFamille = ($famille === 'dorure') ? 'Dorure' : 'Impression Couleur';
            // Colonnes de grille : spécifique pour dorure
            $gridColsDorure = '80px 1fr 200px 100px 90px 120px';
            $gridColsDefault = '140px 1fr 120px 120px 160px 100px';
            $gridCols = $afficherCouleur ? $gridColsDorure : $gridColsDefault;
        
        ?>
        <div style="display:flex; justify-content:center; margin: 20px 0;">
            <div class="tableau-produits<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" style="max-width: 980px; width:100%;">
            <!-- Titre d'attention -->
            <h4 style="color: #d63384; margin-bottom: 20px; text-align: center; font-weight: 700; background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #d63384;">
                ⚠️ ATTENTION : Choisir autant de quantité que vous avez de produits par conditionnement
            </h4>
            
            <!-- En-têtes du tableau -->
                <div class="tableau-header<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" style="display: grid; grid-template-columns: <?= $gridCols ?>; gap: 12px; align-items: center;">
                <div class="col-code<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Code</div>
                <div class="col-description<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Description</div>
                <?php if ($afficherCouleur): ?>
                <div class="col-couleur">Couleur</div>
                <?php endif; ?>
                <div class="col-nb<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Format</div>
                <div class="col-quantite<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Quantité</div>
                <div class="col-prix<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Prix unitaire</div>
                <div class="col-action<?= !$afficherCouleur ? ' sans-couleur' : '' ?>"></div>
            </div>
            
            <!-- Lignes de produits -->
              <?php foreach ($produits as $produit): ?>
                  <div class="ligne-produit<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" 
                      style="display: grid; grid-template-columns: <?= $gridCols ?>; gap: 12px; align-items: center; padding: 12px 18px; border-bottom: 1px solid #eee;"
                  data-id="<?= $produit['id'] ?>" 
                  data-prix="<?= $produit['prixVente'] ?>"
                  data-reference="<?= htmlspecialchars($produit['reference']) ?>"
                  data-designation="<?= htmlspecialchars($produit['designation']) ?>"
                  data-format="<?= htmlspecialchars($produit['format'] ?? '') ?>">
                
                <!-- Code/Référence -->
                <div class="col-code<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?= htmlspecialchars($produit['reference']) ?>
                </div>
                
                <!-- Description -->
                <div class="col-description<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <div class="designation"><?= htmlspecialchars($produit['designation']) ?></div>
                    <?php if (!empty($produit['matiere'])): ?>
                        <div class="matiere"><?= htmlspecialchars($produit['matiere']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Couleurs (seulement pour dorure) -->
                <?php if ($afficherCouleur): ?>
                <div class="col-couleur">
                    <div class="couleurs-container">
                        <?php 
                        $couleursDisponibles = [];
                        for ($i = 1; $i <= 13; $i++): 
                            if (!empty($produit["couleur_ext$i"])): 
                                $couleursDisponibles[] = [
                                    'nom' => $produit["couleur_ext$i"],
                                    'image' => $produit["imageCoul$i"] ?? ''
                                ];
                            endif; 
                        endfor; 
                        ?>
                        
                        <?php if (!empty($couleursDisponibles)): ?>
                            <select class="couleur-select" onchange="selectionnerCouleurPersonnalisation(this, <?= $produit['id'] ?>)">
                                <option value="">Choisir une couleur</option>
                                <?php foreach ($couleursDisponibles as $couleur): ?>
                                    <option value="<?= htmlspecialchars($couleur['nom']) ?>" 
                                            data-image="<?= htmlspecialchars($couleur['image']) ?>">
                                        <?= htmlspecialchars($couleur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="couleur-preview" id="couleur-preview-<?= $produit['id'] ?>" style="margin-top: 5px; display: none;">
                                <img class="couleur-image-preview" src="" alt="" style="width:30px;height:30px;border-radius:50%;border:1px solid #ddd;">
                                <span class="couleur-nom-preview"></span>
                            </div>
                        <?php else: ?>
                            <span class="couleur-unique">Standard</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Format -->
                <div class="col-nb<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if (!empty($produit['format'])): ?>
                        <?= htmlspecialchars($produit['format']) ?>
                    <?php else: ?>
                        Standard
                    <?php endif; ?>
                </div>
                
                <!-- Contrôle quantité -->
                    <div class="col-quantite<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                        <div class="quantite-control">
                            <button type="button" class="btn-moins" onclick="modifierQuantitePersonnalisation(this, -1)">−</button>
                            <input type="number" class="input-quantite" value="<?= intval($quantiteParDefaut) ?>" min="1" readonly>
                            <button type="button" class="btn-plus" onclick="modifierQuantitePersonnalisation(this, 1)">+</button>
                        </div>
                    </div>
                
                <!-- Prix -->
                <div class="col-prix<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?= number_format($produit['prixVente'], 2, ',', ' ') ?> € HT
                </div>
                
                <!-- Bouton ajouter au panier -->
                <div class="col-action<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if ($produit['prixVente'] > 0): ?>
                        <button type="button" class="btn-ajouter-panier" onclick="ajouterPersonnalisationAuPanierDepuisTableau(this)">
                            Ajouter au panier
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-nous-consulter" style="background-color: orange; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: default;" disabled>
                            NOUS CONSULTER
                        </button>
                    <?php endif; ?>
                </div>
                
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php
        
    } catch (Exception $e) {
        echo '<p class="erreur">Erreur lors du chargement des produits : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

// Si le fichier est appelé directement avec un paramètre famille
if (isset($_GET['famille'])) {
    afficherTableauPersonnalisation($_GET['famille']);
}
?>