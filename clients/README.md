# Système de Gestion des Comptes Clients

## Vue d'ensemble

Ce système permet aux visiteurs de votre site de créer un compte client, se connecter, et gérer leurs informations personnelles.

## Structure des fichiers

### Pages principales
- `connexion.php` - Page de connexion avec formulaire d'authentification
- `creer-compte.php` - Page d'inscription pour nouveaux clients
- `mot-de-passe-oublie.php` - Page de réinitialisation du mot de passe
- `mon-compte.php` - Tableau de bord du client connecté
- `logout.php` - Script de déconnexion

### Scripts de traitement
- `process-login.php` - Traitement de la connexion
- `process-register.php` - Traitement de l'inscription
- `process-forgot-password.php` - Traitement de la demande de réinitialisation

### Base de données
- `../sql/create_table_clients.sql` - Script de création des tables clients

## Fonctionnalités implémentées

### ✅ Connexion
- Formulaire de connexion avec email/mot de passe
- Validation côté client et serveur
- Gestion des erreurs avec messages explicites
- Mémorisation de l'email en cas d'erreur
- Redirection après connexion réussie

### ✅ Inscription
- Formulaire complet d'inscription
- Validation robuste des données
- Vérification de l'unicité de l'email
- Hachage sécurisé des mots de passe
- Pré-remplissage des champs en cas d'erreur
- Activation automatique du compte (modifiable)

### ✅ Mot de passe oublié
- Formulaire de demande de réinitialisation
- Génération sécurisée de tokens
- Messages de confirmation standardisés
- Prêt pour l'intégration d'envoi d'emails

### ✅ Mon compte
- Tableau de bord personnalisé
- Affichage des informations personnelles
- Navigation latérale pour futures fonctionnalités
- Statistiques du compte
- Déconnexion sécurisée

## Intégration dans le header

Les boutons "Compte" du header (version normale et compacte) redirigent maintenant vers `clients/connexion.php`.

## Base de données

### Table `clients`
- Stockage sécurisé des informations clients
- Champs pour activation et réinitialisation
- Index pour optimiser les performances
- Support multilingue (pays)

### Sécurité
- Mots de passe hachés avec `password_hash()`
- Protection contre les injections SQL avec requêtes préparées
- Validation stricte des données
- Tokens sécurisés pour réinitialisation
- Sessions PHP sécurisées

## Installation

1. Exécutez le script SQL :
```sql
mysql -u username -p database_name < sql/create_table_clients.sql
```

2. Vérifiez que votre configuration de base de données dans `includes/database.php` est correcte.

3. Testez le système en visitant `/clients/connexion.php`

## Fonctionnalités à venir

### 🔄 En développement futur
- [ ] Envoi d'emails d'activation
- [ ] Envoi d'emails de réinitialisation
- [ ] Gestion des commandes
- [ ] Historique des achats
- [ ] Gestion des adresses multiples
- [ ] Liste de favoris
- [ ] Notifications par email
- [ ] Interface d'administration des clients

### 🎯 Améliorations possibles
- [ ] Authentification à deux facteurs
- [ ] Connexion via réseaux sociaux
- [ ] API REST pour mobile
- [ ] Système de parrainage
- [ ] Points de fidélité

## Configuration

### Variables de session utilisées
- `client_id` - ID du client connecté
- `client_nom` - Nom du client
- `client_prenom` - Prénom du client
- `client_email` - Email du client
- `redirect_after_login` - Page de redirection après connexion

### Messages temporaires
- `success_message` - Messages de succès
- `login_errors` - Erreurs de connexion
- `register_errors` - Erreurs d'inscription
- `forgot_errors` - Erreurs mot de passe oublié

## Support et maintenance

Ce système est prêt pour la production et inclut :
- Validation robuste des données
- Gestion d'erreurs complète
- Interface responsive
- Code documenté et maintenable
- Sécurité renforcée