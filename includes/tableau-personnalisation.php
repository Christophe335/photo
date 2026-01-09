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

function afficherTableauPersonnalisation($famille, $quantiteParDefaut = 1, $produit_ref = null, $produit_format = null) {
    try {
        $db = Database::getInstance()->getConnection();

        // Récupération des produits de personnalisation selon les liaisons (si référence produit fournie)
        $produits = [];
        $use_liaison = false;
        if (!empty($produit_ref)) {
                try {
                // Récupérer la liaison pour ce produit (on peut avoir plusieurs lignes mais on s'intéresse aux champs ref_pre_encollage/ref_impression/ref_impression_2/ref_impression_3/enabled)
                $stmtL = $db->prepare("SELECT ref_pre_encollage, ref_impression, ref_impression_2, ref_impression_3, type, enabled FROM personnalisation_liaisons WHERE produit_ref = ?");
                $stmtL->execute([$produit_ref]);
                $liaisons = $stmtL->fetchAll();
            } catch (Exception $e) {
                $liaisons = [];
            }

            // Si on a des liaisons, déterminer l'affichage selon la famille demandée
            if (!empty($liaisons)) {
                $use_liaison = true;

                // Mode debug: afficher les liaisons récupérées si demandé
                if (isset($_GET['debug']) && $_GET['debug'] == '1') {
                    echo '<pre style="background:#eef;padding:10px;border:1px solid #99c;margin-bottom:10px;">DEBUG LIAISONS: ' . htmlspecialchars(print_r($liaisons, true)) . '</pre>';
                }

                // Si on demande la dorure et qu'une liaison dorure active existe -> afficher toute la famille 'dorure'
                if (stripos($famille, 'dorure') !== false) {
                    $foundDorure = false;
                    foreach ($liaisons as $l) {
                        // La présence de la case 'enabled' active l'accès à la dorure, indépendamment du champ type
                        if (isset($l['enabled']) && $l['enabled']) { $foundDorure = true; break; }
                    }
                    if ($foundDorure) {
                        $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY reference ASC");
                        $stmt->execute([$famille]);
                        $produits = $stmt->fetchAll();
                    } else {
                        echo '<div style="text-align:center;padding:20px;background:#fff3f3;border:1px solid #ffd6d6;border-radius:8px;color:#8a1f1f;">Aucune personnalisation pour ce produit. Contactez-nous</div>';
                        return;
                    }
                }
                // Si on demande les feuilles pré-encollées (ex: "Tirage Panoramique") : chercher la ref_pre_encollage et afficher uniquement cette référence (si fournie)
                elseif (stripos($famille, 'feuilles') !== false || stripos($famille, 'panoram') !== false || stripos($famille, 'pré-encoll') !== false) {
                    $refs = [];
                    foreach ($liaisons as $l) {
                        if (!empty($l['ref_pre_encollage'])) $refs[] = trim($l['ref_pre_encollage']);
                    }
                    $refs = array_values(array_unique(array_filter($refs)));
                    if (!empty($refs)) {
                        $placeholders = implode(',', array_fill(0, count($refs), '?'));
                        $stmt = $db->prepare("SELECT * FROM produits WHERE TRIM(reference) IN ($placeholders) ORDER BY reference ASC");
                        $stmt->execute($refs);
                        $produits = $stmt->fetchAll();
                    } else {
                        echo '<div style="text-align:center;padding:20px;background:#fff3f3;border:1px solid #ffd6d6;border-radius:8px;color:#8a1f1f;">Aucune personnalisation pour ce produit. Contactez-nous</div>';
                        return;
                    }
                }
                // Sinon (typiquement les familles de type "Tirage Photo ...") on considère qu'il s'agit d'impression couleur
                elseif (true) {
                    $refs = [];
                    foreach ($liaisons as $l) {
                        if (!empty($l['ref_impression'])) $refs[] = trim($l['ref_impression']);
                        if (!empty($l['ref_impression_2'])) $refs[] = trim($l['ref_impression_2']);
                        if (!empty($l['ref_impression_3'])) $refs[] = trim($l['ref_impression_3']);
                    }
                    $refs = array_values(array_unique(array_filter($refs)));
                    if (!empty($refs)) {
                        $placeholders = implode(',', array_fill(0, count($refs), '?'));
                        $stmt = $db->prepare("SELECT * FROM produits WHERE TRIM(reference) IN ($placeholders) ORDER BY reference ASC");
                        $stmt->execute($refs);
                        $produits = $stmt->fetchAll();
                    } else {
                        echo '<div style="text-align:center;padding:20px;background:#fff3f3;border:1px solid #ffd6d6;border-radius:8px;color:#8a1f1f;">Aucune personnalisation pour ce produit. Contactez-nous</div>';
                        return;
                    }
                }
            }
        }

        // Si on n'utilise pas la table de liaison ou qu'il n'y a pas de référence fournie, on affiche la famille complète
        if (!$use_liaison) {
            $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY reference ASC");
            $stmt->execute([$famille]);
            $produits = $stmt->fetchAll();
        }

        if (empty($produits)) {
            echo '<p style="text-align: center; color: #666; padding: 20px;">Aucun produit de personnalisation trouvé pour cette catégorie. Les produits seront bientôt disponibles.</p>';
            return;
        }
        
        // Déterminer si c'est de la dorure (affiche couleurs) ou impression (pas de couleurs)
        $afficherCouleur = (strtolower($famille) === 'dorure');
        $titreFamille = ($afficherCouleur) ? 'Dorure' : 'Impression Couleur';
            // Colonnes de grille : spécifique pour dorure
            $gridColsDorure = '65px 1fr 160px 112px 43px 112px 162px';
            $gridColsDefault = '78px 1fr 120px 91px 127px 170px';
            $gridCols = $afficherCouleur ? $gridColsDorure : $gridColsDefault;
        
        ?>
        <div style="display:flex; justify-content:center; margin: 20px 0;">
            <div class="tableau-produits<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" style="max-width: 1080px; width:100%;">
            <!-- Titre d'attention -->
            <h4 style="color: var(--noir1); margin-bottom: 20px; text-align: center; font-weight: 700; background: var(--or3); padding: 15px; border-radius: 8px; border-left: 4px solid var(--or2);">
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
                            Panier
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