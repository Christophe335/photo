<?php
// Inclusion de la configuration de base de données
require_once __DIR__ . '/database.php';

/**
 * Affichage du tableau des produits par famille (choix entre panier et personnalisation)
 * @param string $famille
 * @param bool $afficherCouleur
 */
if (!function_exists('afficherTableauProduitsChoix')) {
    function afficherTableauProduitsChoix($famille, $afficherCouleur = true) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM produits WHERE famille = ? ORDER BY (CASE WHEN ordre IS NULL OR ordre = 0 THEN 1 ELSE 0 END) ASC, (CASE WHEN ordre IS NULL OR ordre = 0 THEN NULL ELSE ordre END) ASC, reference ASC");
        $stmt->execute([$famille]);
        $produits = $stmt->fetchAll();

        if (empty($produits)) {
            echo '<p>Aucun produit trouvé pour cette famille.</p>';
            return;
        }

        $nomFamille = $produits[0]['nomDeLaFamille'];
        ?>
        <div class="tableau-produits<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
            <h2 class="titre-famille"><?= htmlspecialchars($nomFamille) ?></h2>
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

            <?php foreach ($produits as $produit): ?>
            <div class="ligne-produit<?= !$afficherCouleur ? ' sans-couleur' : '' ?>" data-id="<?= $produit['id'] ?>" data-prix="<?= $produit['prixVente'] ?>">
                <div class="col-code<?= !$afficherCouleur ? ' sans-couleur' : '' ?>"><?= htmlspecialchars($produit['reference']) ?></div>
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

                <?php if ($afficherCouleur): ?>
                <div class="col-couleur">
                    <div class="couleurs-container">
                        <?php for ($i = 1; $i <= 13; $i++): ?>
                            <?php if (!empty($produit["couleur_ext$i"])): ?>
                                <div class="couleur-item">
                                    <span class="couleur-nom"><?= htmlspecialchars($produit["couleur_ext$i"]) ?></span>
                                    <?php if (!empty($produit["imageCoul$i"])): ?>
                                        <img src="../images/couleurs/<?= htmlspecialchars($produit["imageCoul$i"]) ?>" alt="<?= htmlspecialchars($produit["couleur_ext$i"]) ?>" class="couleur-image">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-nb<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if (!empty($produit['conditionnement'])): ?>
                        <?= htmlspecialchars($produit['conditionnement']) ?>
                    <?php endif; ?>
                </div>

                <div class="col-quantite<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <div class="quantite-control">
                        <button type="button" class="btn-moins" onclick="modifierQuantite(this, -1)">−</button>
                        <input type="number" class="input-quantite" value="1" min="1" readonly>
                        <button type="button" class="btn-plus" onclick="modifierQuantite(this, 1)">+</button>
                    </div>
                </div>

                <div class="col-prix<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?= number_format($produit['prixVente'], 2, ',', ' ') ?> € HT
                </div>

                <div class="col-action<?= !$afficherCouleur ? ' sans-couleur' : '' ?>">
                    <?php if ($produit['prixVente'] > 0): ?>
                        <div style="display:flex;gap:8px;align-items:center;">
                        <button type="button" class="btn-ajouter-panier" onclick="addDirectToCart(this)">
                            Panier
                        </button>
                        <?php if (!empty($produit['personnalisation'])): ?>
                            <button type="button" 
                                    class="personalize-item" 
                                    onclick="uploaderImagesAvecQuantite(this, <?= $produit['id'] ?>, '<?= htmlspecialchars($produit['reference'], ENT_QUOTES) ?>', '<?= htmlspecialchars($produit['designation'], ENT_QUOTES) ?>', '<?= htmlspecialchars($produit['format'] ?? '', ENT_QUOTES) ?>', <?= $produit['prixVente'] ?>, '<?= htmlspecialchars($produit['conditionnement'] ?? '', ENT_QUOTES) ?>')">
                                Personnaliser
                            </button>
                        <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <button type="button" class="btn-nous-consulter" style="background-color: orange; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: default;" disabled>
                            NOUS CONSULTER
                        </button>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
        <script>
        function addDirectToCart(button) {
            const ligne = button.closest('.ligne-produit');
            const produitId = parseInt(ligne.dataset.id);
            const prix = parseFloat(ligne.dataset.prix) || 0;
            const quantite = parseInt(ligne.querySelector('.input-quantite').value) || 1;
            const reference = ligne.querySelector('.col-code')?.textContent.trim() || '';
            const designation = ligne.querySelector('.designation')?.textContent.trim() || '';
            const format = ligne.querySelector('.format')?.textContent.trim() || '';
            const conditionnement = ligne.querySelector('.col-nb')?.textContent.trim() || '';

            const produit = {
                id: produitId,
                quantite: quantite,
                prix: prix,
                reference: reference,
                designation: designation,
                format: format,
                conditionnement: conditionnement
            };

            if (typeof window.ajouterAuPanierProduit === 'function') {
                window.ajouterAuPanierProduit(produit);
            } else if (typeof ajouterAuPanier === 'function') {
                // fallback: call existing ajouterAuPanier behaviour
                ajouterAuPanier(button);
            } else {
                console.error("Fonction d'ajout au panier introuvable.");
            }
        }
        </script>
        <script>
        (function(){
            function exists(filename){
                return !!document.querySelector('script[src$="'+filename+'"]');
            }
            function add(src){
                if(document.querySelector('script[src="'+src+'"]')) return;
                var s=document.createElement('script'); s.src=src; s.defer=true; document.head.appendChild(s);
            }

            if (!exists('upload-perso.js')){
                var paths = ['/js/upload-perso.js','js/upload-perso.js','../js/upload-perso.js'];
                for(var i=0;i<paths.length;i++){ try{ add(paths[i]); break; }catch(e){} }
            }

            if (!exists('panier.js')){
                var paths2 = ['/js/panier.js','js/panier.js','../js/panier.js'];
                for(var j=0;j<paths2.length;j++){ try{ add(paths2[j]); break; }catch(e){} }
            }
        })();
        </script>
        <?php

    } catch (Exception $e) {
        echo '<p class="erreur">Erreur lors du chargement des produits : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
    }
}

// Si le fichier est appelé directement avec un paramètre famille
if (isset($_GET['famille'])) {
    afficherTableauProduitsChoix($_GET['famille']);
}

// Compatibilité : créer un alias `afficherTableauProduits` si nécessaire
if (!function_exists('afficherTableauProduits') && function_exists('afficherTableauProduitsChoix')) {
    function afficherTableauProduits($famille, $afficherCouleur = true) {
        return afficherTableauProduitsChoix($famille, $afficherCouleur);
    }
}
?>
