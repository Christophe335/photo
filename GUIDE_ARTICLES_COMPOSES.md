# Guide d'utilisation - Système d'Articles Composés

## 📋 Vue d'ensemble

Le système d'articles composés vous permet de créer des produits constitués de plusieurs autres articles. Par exemple, vous pouvez créer une "Boite complète" qui contient "Boite partie haute", "Boite partie basse" et "Instructions d'assemblage".

## 🚀 Installation

1. **Exécutez le script SQL** pour créer les nouvelles tables :
   ```sql
   -- Exécutez le contenu du fichier : sql/create_articles_composes.sql
   ```

2. **Les fichiers modifiés automatiquement :**
   - `admin/ajouter.php` - Formulaire de création
   - `admin/modifier.php` - Formulaire de modification
   - `admin/functions.php` - Nouvelles fonctions
   - `admin/footer.php` - Affichage dans la liste
   - `admin/recherche_articles.php` - Recherche AJAX
   - `admin/voir_composition.php` - Visualisation des compositions

## 💡 Comment utiliser

### Créer un article composé

1. **Allez dans "Ajouter un produit"**
2. **Cochez la case "Cet article est un article composé"**
3. **Choisissez le mode :**
   - ✅ **Calcul automatique** (recommandé) : La désignation et le prix sont calculés automatiquement
   - ❌ **Manuel** : Vous saisissez vous-même la désignation et le prix

4. **Ajoutez des composants :**
   - Tapez dans le champ de recherche (référence ou désignation)
   - Sélectionnez l'article dans la liste déroulante
   - Spécifiez la quantité
   - Cliquez sur "Ajouter"

5. **Sauvegardez** : L'article composé est créé avec tous ses composants

### Modifier un article composé

1. **Cliquez sur "Modifier"** dans la liste des produits
2. **La section "Article composé" s'affiche** avec les composants existants
3. **Vous pouvez :**
   - Ajouter de nouveaux composants
   - Modifier les quantités
   - Supprimer des composants
   - Changer le mode de calcul

### Visualiser une composition

1. **Dans la liste des produits**, les articles composés sont marqués avec l'icône 🔗
2. **Cliquez sur l'icône "œil" 👁️** pour voir la composition détaillée
3. **La page affiche :**
   - Les informations générales de l'article
   - La liste complète des composants
   - Les prix unitaires et totaux
   - Les éventuelles différences de prix

## ✨ Fonctionnalités

### Mode automatique
- **Désignation automatique** : "Article 1 + Article 2 + Article 3"
- **Prix automatique** : Somme des prix des composants × leurs quantités
- **Prix d'achat estimé** : 70% du prix de vente total

### Affichage dans la liste
- **Icône distinctive** : 🔗 pour identifier les articles composés
- **Label "(Composé)"** dans la désignation
- **Bouton "Voir composition"** pour les détails

### Recherche et gestion
- **Recherche en temps réel** des articles à ajouter
- **Prévention des doublons** dans la composition
- **Gestion des quantités** pour chaque composant
- **Calcul automatique des totaux**

## 🎯 Cas d'usage

### Exemple 1 : Boite personnalisée
```
Article composé : "Boite Custom Deluxe"
├── Boite partie haute (×1) - 15,00 €
├── Boite partie basse (×1) - 12,00 €
├── Séparateurs (×3) - 3,00 € chacun
└── Instructions (×1) - 2,00 €
Total : 38,00 €
```

### Exemple 2 : Kit photo mariage
```
Article composé : "Kit Photo Mariage"
├── Album 30x30 cm (×1) - 45,00 €
├── Livre d'or (×1) - 25,00 €
├── Boite de rangement (×1) - 18,00 €
└── Marque-pages (×5) - 2,50 € chacun
Total : 100,50 €
```

## 🔧 Fonctions techniques

### Nouvelles tables
- `produits.est_compose` : Indique si l'article est composé
- `produits.composition_auto` : Mode de calcul automatique
- `produit_compositions` : Liaison entre articles parents et enfants

### Nouvelles fonctions
- `ajouterComposants($parentId, $composants)`
- `getComposantsProduit($produitId)`
- `recalculerArticleCompose($produitId)`
- `supprimerProduitAvecCompositions($id)`

## 📈 Avantages pour votre business

1. **Simplification de la gestion** : Un seul article = plusieurs produits
2. **Calcul automatique des prix** : Pas d'erreur de calcul
3. **Traçabilité complète** : Vous savez exactement ce qui compose chaque commande
4. **Gestion des stocks facilitée** : Chaque composant est tracé individuellement
5. **Flexibilité** : Possibilité de créer des articles composés dans des articles composés

## 🎉 Prêt à utiliser !

Votre système d'articles composés est maintenant opérationnel. Vous pouvez commencer à créer vos premiers articles composés dès maintenant !