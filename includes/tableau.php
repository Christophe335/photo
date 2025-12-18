<?php
// Inclusion de la configuration de base de données
require_once __DIR__ . '/database.php';

/**
 * Affichage du tableau des produits par famille
 * @param string $famille - Code de la famille de produits à afficher
 * @param bool $afficherCouleur - Si true, affiche la colonne couleur (par défaut true)
 */
function afficherTableauProduits($famille, $afficherCouleur = true) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupération des produits de la famille
        // Tri par champ 'ordre' (valeurs numériques d'abord), puis par reference
        $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY (CASE WHEN ordre IS NULL OR ordre = 0 THEN 1 ELSE 0 END) ASC, (CASE WHEN ordre IS NULL OR ordre = 0 THEN NULL ELSE ordre END) ASC, reference ASC");
        $stmt->execute([$famille]);
        $produits = $stmt->fetchAll();
        
        if (empty($produits)) {
            echo '<p>Aucun produit trouvé pour cette famille.</p>';
            return;
        }
        
        // Récupération du nom de la famille (premier produit)
        $nomFamille = $produits[0]['nomDeLaFamille'];
        
        ?>
        <div class="tableau-produits<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
            <!-- Titre de la famille -->
            <h2 class="titre-famille"><?= htmlspecialchars($nomFamille) ?></h2>
            
            <!-- En-têtes du tableau -->
            <div class="tableau-header<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                <div class="col-code<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Code</div>
                <div class="col-description<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Description</div>
                <?php if ($afficherCouleur): ?>
                <div class="col-couleur">Couleur</div>
                <?php endif; ?>
                <div class="col-nb<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Nb</div>
                <div class="col-quantite<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Quantité</div>
                <div class="col-prix<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">Prix</div>
                <div class="col-action<?= !$afficherCouleur ? ' sans-couleur' : '' ?>"></div>
            </div>
            
            <!-- Lignes de produits -->
            <?php foreach ($produits as $produit): ?>
            <div class="ligne-produit<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" data-id="<?= $produit['id'] ?>" data-prix="<?= $produit['prixVente'] ?>">
                
                <!-- Code/Référence -->
                <div class="col-code<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?= htmlspecialchars($produit['reference']) ?>
                </div>
                
                <!-- Description -->
                <div class="col-description<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <div class="designation"><?= htmlspecialchars($produit['designation']) ?></div>
                    <?php if (!empty($produit['format'])): ?>
                        <div class="format"><?= htmlspecialchars($produit['format']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($produit['matiere'])): ?>
                        <div class="matiere"><?= htmlspecialchars($produit['matiere']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($produit['couleur_interieur'])): ?>
                        <div class="couleur-int">Intérieur: <?= htmlspecialchars($produit['couleur_interieur']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Couleurs -->
                <?php if ($afficherCouleur): ?>
                <div class="col-couleur">
                    <div class="couleurs-container">
                        <?php for ($i = 1; $i <= 13; $i++): ?>
                            <?php if (!empty($produit["couleur_ext$i"])): ?>
                                <div class="couleur-item">
                                    <span class="couleur-nom"><?= htmlspecialchars($produit["couleur_ext$i"]) ?></span>
                                    <?php if (!empty($produit["imageCoul$i"])): ?>
                                        <img src="../images/couleurs/<?= htmlspecialchars($produit["imageCoul$i"]) ?>" 
                                             alt="<?= htmlspecialchars($produit["couleur_ext$i"]) ?>" 
                                             class="couleur-image">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Conditionnement -->
                <div class="col-nb<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if (!empty($produit['conditionnement'])): ?>
                        <?= htmlspecialchars($produit['conditionnement']) ?>
                    <?php endif; ?>
                </div>
                
                <!-- Contrôle quantité -->
                <div class="col-quantite<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <div class="quantite-control">
                        <button type="button" class="btn-moins" onclick="modifierQuantite(this, -1)">−</button>
                        <input type="number" class="input-quantite" value="1" min="1" readonly>
                        <button type="button" class="btn-plus" onclick="modifierQuantite(this, 1)">+</button>
                    </div>
                </div>
                
                <!-- Prix -->
                <div class="col-prix<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?= number_format($produit['prixVente'], 2, ',', ' ') ?> € HT
                </div>
                
                <!-- Bouton ajouter au panier -->
                <div class="col-action<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if ($produit['prixVente'] > 0): ?>
                        <button type="button" class="btn-ajouter-panier" onclick="ajouterAuPanier(this)">
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
    afficherTableauProduits($_GET['famille']);
}
?>
