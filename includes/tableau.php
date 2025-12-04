<?php
// Inclusion de la configuration de base de données
require_once __DIR__ . '/database.php';

/**
 * Affichage du tableau des produits par famille
 * @param string $famille - Code de la famille de produits à afficher
 */
function afficherTableauProduits($famille) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupération des produits de la famille
        $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY reference");
        $stmt->execute([$famille]);
        $produits = $stmt->fetchAll();
        
        if (empty($produits)) {
            echo '<p>Aucun produit trouvé pour cette famille.</p>';
            return;
        }
        
        // Récupération du nom de la famille (premier produit)
        $nomFamille = $produits[0]['nomDeLaFamille'];
        
        ?>
        <div class="tableau-produits">
            <!-- Titre de la famille -->
            <h2 class="titre-famille"><?= htmlspecialchars($nomFamille) ?></h2>
            
            <!-- En-têtes du tableau -->
            <div class="tableau-header">
                <div class="col-code">Code</div>
                <div class="col-description">Description</div>
                <div class="col-couleur">Couleur</div>
                <div class="col-nb">Nb</div>
                <div class="col-quantite">Quantité</div>
                <div class="col-prix">Prix</div>
                <div class="col-action"></div>
            </div>
            
            <!-- Lignes de produits -->
            <?php foreach ($produits as $produit): ?>
            <div class="ligne-produit" data-id="<?= $produit['id'] ?>" data-prix="<?= $produit['prixVente'] ?>">
                
                <!-- Code/Référence -->
                <div class="col-code">
                    <?= htmlspecialchars($produit['reference']) ?>
                </div>
                
                <!-- Description -->
                <div class="col-description">
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
                
                <!-- Conditionnement -->
                <div class="col-nb2">
                    <?php if (!empty($produit['conditionnement'])): ?>
                        <?= htmlspecialchars($produit['conditionnement']) ?>
                    <?php endif; ?>
                </div>
                
                <!-- Contrôle quantité -->
                <div class="col-quantite">
                    <div class="quantite-control">
                        <button type="button" class="btn-moins" onclick="modifierQuantite(this, -1)">−</button>
                        <input type="number" class="input-quantite" value="1" min="1" readonly>
                        <button type="button" class="btn-plus" onclick="modifierQuantite(this, 1)">+</button>
                    </div>
                </div>
                
                <!-- Prix -->
                <div class="col-prix">
                    <?= number_format($produit['prixVente'], 2, ',', ' ') ?> € HT
                </div>
                
                <!-- Bouton ajouter au panier -->
                <div class="col-action">
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
