<?php
require_once 'functions.php';

// Vérifier l'authentification
checkAuth();

// Traitement du formulaire
if ($_POST) {
    // Préparer les données selon la vraie structure de la table
    $estCompose = isset($_POST['est_compose']) && $_POST['est_compose'] == '1';
    $compositionAuto = isset($_POST['composition_auto']) && $_POST['composition_auto'] == '1';
        $personnalisation = isset($_POST['personnalisation']) && $_POST['personnalisation'] == '1';
    
    $donnees = [
        'famille' => $_POST['famille'] ?? '',
        'nomDeLaFamille' => $_POST['nomDeLaFamille'] ?? '',
        'reference' => $_POST['reference'] ?? '',
        'designation' => $_POST['designation'] ?? '',
        'format' => $_POST['format'] ?? '',
        'ordre' => isset($_POST['ordre']) ? intval($_POST['ordre']) : 0,
        'est_compose' => $estCompose ? 1 : 0,
        'composition_auto' => $compositionAuto ? 1 : 0,
        'personnalisation' => $personnalisation ? 1 : 0,
        'prixAchat' => floatval($_POST['prixAchat'] ?? 0),
        'prixVente' => floatval($_POST['prixVente'] ?? 0),
        'conditionnement' => $_POST['conditionnement'] ?? '',
        'matiere' => $_POST['matiere'] ?? '',
        'couleur_interieur' => $_POST['couleur_interieur'] ?? ''
    ];
    
    // Traiter les 13 couleurs extérieures selon la vraie structure
    for ($i = 1; $i <= 13; $i++) {
        $donnees["couleur_ext$i"] = $_POST["couleur$i"] ?? '';
        $donnees["imageCoul$i"] = $_POST["imageCoul$i"] ?? ''; // Chemin de l'image
    }
    
    // Créer le produit
    $produitId = creerProduit($donnees);
    
    if ($produitId) {
        // Si c'est un article composé, traiter les composants
        if ($estCompose) {
            $composantsData = $_POST['composants_data'] ?? '';
            if (!empty($composantsData)) {
                $composants = json_decode($composantsData, true);
                if ($composants && is_array($composants)) {
                    ajouterComposants($produitId, $composants);
                }
            }
        }
        
        $_SESSION['message'] = 'Produit créé avec succès';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['message'] = 'Erreur lors de la création du produit';
        $_SESSION['message_type'] = 'error';
    }
}

// Récupérer les familles existantes pour l'aide à la saisie
$familles = getFamilles();

include 'header.php';
?>
<head>
    <link rel="stylesheet" href="../css/admin.css">  
    <style>
        .color-swatch{width:32px;height:32px;border:1px solid #ccc;display:inline-block;vertical-align:middle;margin-left:8px;border-radius:4px;background-size:cover;background-position:center;background-repeat:no-repeat}
        .couleur-fields{display:flex;gap:12px;align-items:flex-start}
        .couleur-col{flex:1;min-width:0}
        .couleur-item h4{display:flex;align-items:center;gap:8px;margin:0 0 6px 0}
        .couleur-item .form-group{margin:0}
        .couleur-item input[type="text"]{width:100%;box-sizing:border-box}
        .couleur-item .form-help{display:block;margin-top:4px}
    </style>
</head>
    <div class="page-header">
        <h2><i class="fas fa-plus-circle"></i> Ajouter un nouveau produit</h2>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <form method="POST" class="product-form">
        <!-- Informations générales -->
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
            
            <div class="form-row admin-form-row">
                <div class="form-group admin-col-ordre">
                    <label for="ordre">Ordre d'affichage</label>
                    <input type="number" id="ordre" name="ordre" min="0" step="1"
                           value="<?= htmlspecialchars($_POST['ordre'] ?? '0') ?>"
                           placeholder="0" class="admin-input-full">
                </div>

                <div class="form-group admin-col-reference">
                    <label for="reference">Référence *</label>
                    <input type="text" id="reference" name="reference" required 
                           value="<?= htmlspecialchars($_POST['reference'] ?? '') ?>"
                           placeholder="Ex: REL-A4-001" class="admin-input-full">
                </div>

                <div class="form-group admin-col-designation">
                    <label for="designation">Désignation *</label>
                    <input type="text" id="designation" name="designation" required 
                           value="<?= htmlspecialchars($_POST['designation'] ?? '') ?>"
                           placeholder="Ex: Reliure spirale A4" class="admin-input-full">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="personnalisation" name="personnalisation" value="1" <?= isset($_POST['personnalisation']) ? 'checked' : '' ?>>
                        <span>Personnalisation</span>
                    </label>
                    <small class="form-help">Afficher le bouton "Personnalisation" pour ce produit permettant d'imprimer ou de faire de la dorure.</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="famille">Famille *</label>
                    <input type="text" id="famille" name="famille" required list="famillesList"
                           value="<?= htmlspecialchars($_POST['famille'] ?? '') ?>"
                           placeholder="Ex: Reliures, Couvertures...">
                    <datalist id="famillesList">
                        <?php foreach ($familles as $famille): ?>
                            <option value="<?= htmlspecialchars($famille) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                
                <div class="form-group">
                    <label for="nomDeLaFamille">Nom de la famille</label>
                    <input type="text" id="nomDeLaFamille" name="nomDeLaFamille" 
                           value="<?= htmlspecialchars($_POST['nomDeLaFamille'] ?? '') ?>"
                           placeholder="Ex: Reliures personnalisées">
                    <small class="form-help">Nom descriptif complet de la famille</small>
                </div>
            </div>
            
            
            <div class="form-row">
                <div class="form-group">
                    <label for="format">Format</label>
                    <input type="text" id="format" name="format" 
                           value="<?= htmlspecialchars($_POST['format'] ?? '') ?>"
                           placeholder="Ex: A4, A5, 21x29.7cm">
                </div>
            </div>
        </div>

        <!-- Caractéristiques techniques -->
        <div class="form-section">
            <h3><i class="fas fa-cog"></i> Caractéristiques techniques</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="conditionnement">Conditionnement</label>
                    <input type="text" id="conditionnement" name="conditionnement" 
                           value="<?= htmlspecialchars($_POST['conditionnement'] ?? '') ?>"
                           placeholder="Ex: Par unité, Par lot de 10...">
                </div>
                
                <div class="form-group">
                    <label for="matiere">Matière ou Dos</label>
                    <input type="text" id="matiere" name="matiere" 
                           value="<?= htmlspecialchars($_POST['matiere'] ?? '') ?>"
                           placeholder="Ex: Papier, Carton, Plastique">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="couleur_interieur">Couleur intérieure</label>
                    <input type="text" id="couleur_interieur" name="couleur_interieur" 
                           value="<?= htmlspecialchars($_POST['couleur_interieur'] ?? '') ?>"
                           placeholder="Ex: Blanc, Ivoire, Couleur">
                </div>
            </div>
            
        </div>

        <!-- Prix et marge - Section importante -->
        <div class="form-section highlight-section">
            <h3><i class="fas fa-euro-sign"></i> Prix et Marge (Important pour les étapes à venir)</h3>

            <div class="form-row admin-form-row center">
                <div class="form-group admin-col-25">
                    <label for="prixAchat">Prix d'achat * (€)</label>
                    <input type="number" id="prixAchat" name="prixAchat" required 
                           min="0" step="0.01" 
                           value="<?= htmlspecialchars($_POST['prixAchat'] ?? '') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Coût d'achat unitaire HT</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="prixVente">Prix de vente * (€)</label>
                    <input type="number" id="prixVente" name="prixVente" required 
                           min="0" step="0.01" 
                           value="<?= htmlspecialchars($_POST['prixVente'] ?? '') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Prix de vente unitaire HT</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="marge">Marge calculée (%)</label>
                    <input type="number" id="marge" name="marge" readonly
                           value="<?= htmlspecialchars($_POST['marge'] ?? '') ?>"
                           placeholder="0.00" class="admin-input-full">
                    <small class="form-help">Calculée automatiquement</small>
                </div>

                <div class="form-group admin-col-25">
                    <label for="prixUnitaire" class="admin-prixunitaire-label">Prix unitaire de vente (€)</label>
                    <input type="number" id="prixUnitaire" name="prixUnitaire" readonly
                           class="admin-prixunitaire-input"
                           placeholder="0.00">
                    <small class="form-help admin-form-help-green">Prix de vente ÷ conditionnement</small>
                </div>
            </div>
        </div>

        <!-- Article composé -->
        <div class="form-section">
            <h3><i class="fas fa-layer-group"></i> Article composé</h3>
            <p class="section-description">Créer un article constitué de plusieurs autres articles</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="est_compose" name="est_compose" value="1" 
                               <?= isset($_POST['est_compose']) ? 'checked' : '' ?>>
                        <span>Cet article est un article composé</span>
                    </label>
                    <small class="form-help">Cochez cette case pour créer un article composé de plusieurs autres articles</small>
                </div>
            </div>
            
            <!-- Section des composants (masquée par défaut) -->
            <div id="section-composants" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="composition_auto" name="composition_auto" value="1" checked>
                            Calcul automatique de la désignation et du prix
                        </label>
                        <small class="form-help">Si coché, la désignation et le prix seront automatiquement calculés à partir des articles composants</small>
                    </div>
                </div>
                
                <div class="composants-container">
                    <h4>Articles composants</h4>
                    <div class="search-article-container">
                        <div class="form-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="recherche-article">Rechercher un article</label>
                                <input type="text" id="recherche-article" placeholder="Tapez une référence ou désignation...">
                            </div>
                            <div class="form-group" style="flex: 0 0 100px;">
                                <label for="quantite-article">Quantité</label>
                                <input type="number" id="quantite-article" min="1" value="1" style="width: 100%;">
                            </div>
                            <div class="form-group" style="flex: 0 0 auto; align-self: end;">
                                <button type="button" id="btn-ajouter-composant" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        
                        <div id="resultats-recherche" class="resultats-recherche" style="display: none;"></div>
                    </div>
                    
                    <div id="liste-composants" class="liste-composants">
                        <div class="composants-header">
                            <div>Référence</div>
                            <div>Désignation</div>
                            <div>Prix unitaire</div>
                            <div>Quantité</div>
                            <div>Prix total</div>
                            <div>Actions</div>
                        </div>
                        <div id="composants-vides" class="composants-vide">
                            Aucun article ajouté
                        </div>
                    </div>
                    
                    <div class="composants-total">
                        <strong>Prix total calculé : <span id="prix-total-calcule">0.00</span> €</strong>
                    </div>
                </div>
                
                <!-- Champs cachés pour stocker les données des composants -->
                <input type="hidden" id="composants-data" name="composants_data" value="">
            </div>
        </div>

        <!-- Gestion des couleurs -->
        <div class="form-section">
            <h3><i class="fas fa-palette"></i> Couleurs disponibles</h3>
            <p class="section-description">Saisissez les couleurs disponibles pour ce produit et leurs images (jusqu'à 13 couleurs)</p>
            
            <div class="couleurs-container">
                <?php for ($i = 1; $i <= 13; $i++): ?>
                        <div class="couleur-item">
                            <h4>Couleur <?= $i ?> <span class="color-swatch" id="swatch<?= $i ?>"></span></h4>
                            <div class="couleur-fields">
                                <div class="couleur-col">
                                    <div class="form-group">
                                        <label for="couleur<?= $i ?>">Nom de la couleur</label>
                                        <input type="text" id="couleur<?= $i ?>" name="couleur<?= $i ?>"
                                               value="<?= htmlspecialchars($_POST["couleur$i"] ?? '') ?>"
                                               placeholder="Exemple : Red">
                                    </div>
                                </div>
                                <div class="couleur-col">
                                    <div class="form-group">
                                        <label for="imageCoul<?= $i ?>">Chemin de l'image</label>
                                        <input type="text" id="imageCoul<?= $i ?>" name="imageCoul<?= $i ?>"
                                               value="<?= htmlspecialchars($_POST["imageCoul$i"] ?? '') ?>"
                                               placeholder="Exemple : mini/red.webp">
                                        <small class="form-help">Chemin relatif vers le fichier image de la couleur : taille 16x16px</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endfor; ?>
            </div>
        </div>







        <!-- Boutons d'action -->
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Créer le produit
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>


<script>
    // Calcul automatique de la marge et du prix unitaire
    function calculerMarge() {
        const prixAchat = parseFloat(document.getElementById('prixAchat').value) || 0;
        const prixVente = parseFloat(document.getElementById('prixVente').value) || 0;
        const conditionnement = parseFloat(document.getElementById('conditionnement').value) || 1;
        
        // Calcul de la marge
        if (prixAchat > 0 && prixVente > prixAchat) {
            const marge = ((prixVente - prixAchat) / prixVente) * 100;
            document.getElementById('marge').value = marge.toFixed(2);
        } else {
            document.getElementById('marge').value = '';
        }
        
        // Calcul du prix unitaire (arrondi au centime supérieur)
        if (prixVente > 0 && conditionnement > 0) {
            const prixUnitaire = prixVente / conditionnement;
            const prixUnitaireArrondi = Math.ceil(prixUnitaire * 100) / 100; // centime supérieur
            document.getElementById('prixUnitaire').value = prixUnitaireArrondi.toFixed(2);
        } else {
            document.getElementById('prixUnitaire').value = '';
        }
    }

    // Attacher les événements
    document.getElementById('prixAchat').addEventListener('input', calculerMarge);
    document.getElementById('prixVente').addEventListener('input', calculerMarge);
    document.getElementById('conditionnement').addEventListener('input', calculerMarge);

    // Calcul initial si les champs sont déjà remplis
    calculerMarge();

    // Génération automatique de référence (optionnel)
    document.getElementById('famille').addEventListener('blur', function() {
        const famille = this.value.trim();
        const reference = document.getElementById('reference');
        
        if (famille && !reference.value) {
            // Suggérer une référence basée sur la famille
            const prefix = famille.substring(0, 3).toUpperCase();
            reference.placeholder = `Ex: ${prefix}-001, ${prefix}-A4-001`;
        }
    });

    // ========== GESTION DES ARTICLES COMPOSÉS ==========
    let composants = [];
    let rechercheTimeout = null;

    // Attendre que le DOM soit complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé, initialisation des articles composés...');
        
        // Toggle de la section composants
        const estComposeCheckbox = document.getElementById('est_compose');
        const sectionComposants = document.getElementById('section-composants');
        
        console.log('Checkbox est_compose:', estComposeCheckbox);
        console.log('Section composants:', sectionComposants);
        
        if (estComposeCheckbox) {
            estComposeCheckbox.addEventListener('change', function() {
                console.log('Checkbox changée, état:', this.checked);
                const sectionComposants = document.getElementById('section-composants');
                const sectionPrix = document.querySelector('.highlight-section');
                
                if (this.checked) {
                    console.log('Affichage de la section composants');
                    if (sectionComposants) {
                        sectionComposants.style.display = 'block';
                        console.log('Section composants affichée');
                    }
                    // Masquer la section prix si composition automatique activée
                    toggleSectionPrix();
                } else {
                    console.log('Masquage de la section composants');
                    if (sectionComposants) sectionComposants.style.display = 'none';
                    if (sectionPrix) sectionPrix.style.display = 'block';
                    // Vider la liste des composants
                    composants = [];
                    mettreAJourAffichageComposants();
                }
            });
        } else {
            console.error('Checkbox est_compose non trouvée !');
        }

        // Toggle de la section prix selon le mode de composition
        const compositionAutoCheckbox = document.getElementById('composition_auto');
        if (compositionAutoCheckbox) {
            compositionAutoCheckbox.addEventListener('change', toggleSectionPrix);
        }

    function toggleSectionPrix() {
        const sectionPrix = document.querySelector('.highlight-section');
        const compositionAuto = document.getElementById('composition_auto').checked;
        const estCompose = document.getElementById('est_compose').checked;
        
        if (estCompose && compositionAuto) {
            sectionPrix.style.display = 'none';
            // Vider les champs prix
            document.getElementById('prixAchat').value = '';
            document.getElementById('prixVente').value = '';
        } else {
            sectionPrix.style.display = 'block';
        }
    }

        // Recherche d'articles
        const rechercheArticleInput = document.getElementById('recherche-article');
        if (rechercheArticleInput) {
            rechercheArticleInput.addEventListener('input', function() {
                const terme = this.value.trim();
                
                clearTimeout(rechercheTimeout);
                
                if (terme.length < 2) {
                    masquerResultatsRecherche();
                    return;
                }
                
                rechercheTimeout = setTimeout(() => {
                    rechercherArticles(terme);
                }, 300);
            });
        }

        // Masquer les résultats si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-article-container')) {
                masquerResultatsRecherche();
            }
        });
    }); // Fermeture du DOMContentLoaded

    async function rechercherArticles(terme) {
        console.log('Recherche lancée pour:', terme);
        try {
            const response = await fetch('recherche_articles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `terme=${encodeURIComponent(terme)}`
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const articles = await response.json();
            console.log('Articles trouvés:', articles);
            afficherResultatsRecherche(articles);
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            masquerResultatsRecherche();
        }
    }

    function afficherResultatsRecherche(articles) {
        const conteneur = document.getElementById('resultats-recherche');
        
        if (articles.length === 0) {
            masquerResultatsRecherche();
            return;
        }
        
        const html = articles.map(article => {
            const designationEchappee = article.designation.replace(/'/g, "\\'");
            
            // Construire les informations supplémentaires
            let infosSupp = [];
            if (article.format) {
                infosSupp.push(`Format: ${article.format}`);
            }
            if (article.matiere) {
                infosSupp.push(`Matière: ${article.matiere}`);
            }
            
            let couleursHtml = '';
            if (article.couleurs && article.couleurs.length > 0) {
                couleursHtml = `<div style="font-size: 0.85em; color: #666; margin-top: 2px;">
                    ${article.couleurs.join(' • ')}
                </div>`;
            }
            
            let infosSupplementaires = '';
            if (infosSupp.length > 0) {
                infosSupplementaires = `<div style="font-size: 0.85em; color: #666; font-style: italic;">
                    ${infosSupp.join(' • ')}
                </div>`;
            }
            
            return `
                <div class="resultat-item" onclick="selectionnerArticle(${article.id}, '${article.reference}', '${designationEchappee}', ${article.prixVente})">
                    <div><strong>${article.reference}</strong></div>
                    <div>${article.designation}</div>
                    ${infosSupplementaires}
                    ${couleursHtml}
                    <div style="color: var(--success-color); font-weight: 500; margin-top: 4px;">${parseFloat(article.prixVente).toFixed(2)} €</div>
                </div>
            `;
        }).join('');
        
        conteneur.innerHTML = html;
        conteneur.style.display = 'block';
    }

    function masquerResultatsRecherche() {
        document.getElementById('resultats-recherche').style.display = 'none';
    }

    function selectionnerArticle(id, reference, designation, prix) {
        const quantite = parseInt(document.getElementById('quantite-article').value) || 1;
        
        // Vérifier si l'article n'est pas déjà présent
        if (composants.find(c => c.id === id)) {
            alert('Cet article est déjà dans la composition');
            return;
        }
        
        // Ajouter l'article à la liste
        composants.push({
            id: id,
            reference: reference,
            designation: designation,
            prix: parseFloat(prix),
            quantite: quantite
        });
        
        // Mettre à jour l'affichage
        mettreAJourAffichageComposants();
        
        // Vider les champs de recherche
        document.getElementById('recherche-article').value = '';
        document.getElementById('quantite-article').value = 1;
        masquerResultatsRecherche();
        
        // Mettre à jour la désignation et le prix si mode automatique
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function supprimerComposant(index) {
        composants.splice(index, 1);
        mettreAJourAffichageComposants();
        
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function modifierQuantiteComposant(index, nouvelleQuantite) {
        if (nouvelleQuantite < 1) return;
        
        composants[index].quantite = nouvelleQuantite;
        mettreAJourAffichageComposants();
        
        if (document.getElementById('composition_auto').checked) {
            mettreAJourCompositionAutomatique();
        }
    }

    function mettreAJourAffichageComposants() {
        const conteneur = document.getElementById('liste-composants');
        const vide = document.getElementById('composants-vides');
        
        if (composants.length === 0) {
            vide.style.display = 'block';
            document.getElementById('prix-total-calcule').textContent = '0.00';
        } else {
            vide.style.display = 'none';
            
            const html = composants.map((composant, index) => {
                const prixTotal = composant.prix * composant.quantite;
                return `
                    <div class="composant-item">
                        <div>${composant.reference}</div>
                        <div>${composant.designation}</div>
                        <div style="text-align: right;">${composant.prix.toFixed(2)} €</div>
                        <div style="text-align: center;">
                            <input type="number" min="1" value="${composant.quantite}" 
                                   onchange="modifierQuantiteComposant(${index}, parseInt(this.value))"
                                   style="width: 60px; text-align: center;">
                        </div>
                        <div style="text-align: right; font-weight: 500; color: var(--success-color);">
                            ${prixTotal.toFixed(2)} €
                        </div>
                        <div style="text-align: center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="supprimerComposant(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            
            // Insérer après l'en-tête
            const header = conteneur.querySelector('.composants-header');
            const items = conteneur.querySelectorAll('.composant-item');
            items.forEach(item => item.remove());
            
            header.insertAdjacentHTML('afterend', html);
            
            // Calculer le prix total
            const prixTotal = composants.reduce((total, composant) => {
                return total + (composant.prix * composant.quantite);
            }, 0);
            
            document.getElementById('prix-total-calcule').textContent = prixTotal.toFixed(2);
        }
        
        // Mettre à jour le champ caché
        document.getElementById('composants-data').value = JSON.stringify(composants);
    }

    function mettreAJourCompositionAutomatique() {
        if (!document.getElementById('composition_auto').checked) return;
        
        // Générer la désignation automatique
        const designations = composants.map(c => c.designation);
        const designationComplete = designations.join(' + ');
        document.getElementById('designation').value = designationComplete;
        
        // Calculer le prix total
        const prixTotal = composants.reduce((total, composant) => {
            return total + (composant.prix * composant.quantite);
        }, 0);
        
        document.getElementById('prixVente').value = prixTotal.toFixed(2);
        document.getElementById('prixAchat').value = (prixTotal * 0.7).toFixed(2); // Estimation 70% du prix de vente
    }


</script>

<script>
    (function(){
        function normalize(name){
            return name.toLowerCase().trim()
                .normalize ? name.toLowerCase().trim().normalize('NFD').replace(/\p{Diacritic}/gu,'') : name.toLowerCase().trim()
                .replace(/\s+/g,'-')
                .replace(/[^a-z0-9\-]/g,'');
        }

        function trySetImage(url, sw, onOk, onErr){
            var img = new Image();
            img.onload = function(){ sw.style.backgroundImage = 'url("' + url + '")'; sw.style.backgroundColor = ''; if(onOk) onOk(true); };
            img.onerror = function(){ if(onErr) onErr(false); };
            img.src = url;
        }

        function setColorBg(sw, color){
            sw.style.backgroundImage = '';
            sw.style.backgroundColor = color || 'transparent';
        }

        function setSwatch(i){
            var sw = document.getElementById('swatch'+i);
            if(!sw) return;
            var name = (document.getElementById('couleur'+i) || {}).value || '';
            var imgField = (document.getElementById('imageCoul'+i) || {}).value || '';

            // If user provided an explicit image path, try it first
            var tried = false;
            if(imgField.trim() !== ''){
                var candidates = [];
                var baseName = imgField.split('/').pop();
                var baseNoExt = baseName.replace(/\.[^.]+$/, '');

                candidates.push(imgField);
                candidates.push('../' + imgField);
                candidates.push('images/couleurs/mini/' + baseName);
                candidates.push('../images/couleurs/mini/' + baseName);
                // big variants
                if(/-B(\.|$)/i.test(baseNoExt)){
                    candidates.push('../images/couleurs/big/' + baseName);
                } else {
                    candidates.push('../images/couleurs/big/' + baseNoExt + '-B.webp');
                }
                candidates.push('/images/couleurs/mini/' + baseName);

                (function tryNext(idx){
                    if(idx >= candidates.length){
                        // fallback to try by normalized name then css color
                        attemptAutoByName();
                        return;
                    }
                    trySetImage(candidates[idx], sw, function(ok){ if(ok) return; }, function(){ tryNext(idx+1); });
                })(0);
                return;
            }

            function attemptAutoByName(){
                var baseMini = '../images/couleurs/mini/';
                var baseBig = '../images/couleurs/big/';
                var norm = normalize(name || '');
                if(!norm){ setColorBg(sw, 'transparent'); return; }
                var mini = baseMini + norm + '.webp';
                var big = baseBig + norm + '-B.webp';

                trySetImage(mini, sw, function(ok){ if(!ok) trySetImage(big, sw, function(ok2){ if(!ok2) setColorBg(sw, name); }, function(){ setColorBg(sw, name); }); }, function(){ trySetImage(big, sw, function(ok2){ if(!ok2) setColorBg(sw, name); }, function(){ setColorBg(sw, name); }); });
            }

            attemptAutoByName();
        }

        for (var i=1;i<=13;i++){
            (function(i){
                var inColor = document.getElementById('couleur'+i);
                var inImg = document.getElementById('imageCoul'+i);
                if(inColor) inColor.addEventListener('input', function(){ setSwatch(i); });
                if(inImg) inImg.addEventListener('input', function(){ setSwatch(i); });
                // initial
                setSwatch(i);
            })(i);
        }
    })();
</script>

<?php include 'footer_simple.php'; ?>