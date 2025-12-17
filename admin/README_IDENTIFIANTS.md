# Gestion des Identifiants Administrateurs

## Vue d'ensemble

Ce système permet de gérer les identifiants de connexion à l'interface d'administration de manière sécurisée et flexible.

## Fonctionnalités

### 1. Stockage sécurisé
- Les identifiants sont stockés dans le fichier `.admin_credentials.json`
- Le fichier est protégé par `.htaccess` contre l'accès web direct
- Les mots de passe sont hachés avec `password_hash()`

### 2. Gestion complète
- **Ajouter** de nouveaux identifiants administrateurs
- **Modifier** les mots de passe existants
- **Activer/Désactiver** des comptes
- **Supprimer** des identifiants (sauf le dernier)
- **Visualiser** les mots de passe (avec bouton œil)

### 3. Suivi des connexions
- Date de création de chaque compte
- Date de dernière connexion
- Statut actif/inactif

## Utilisation

### Accès à la gestion
1. Connectez-vous à l'administration avec vos identifiants
2. Cliquez sur "Identifiants admin" dans le menu de navigation
3. Vous accédez à l'interface de gestion complète

### Identifiants par défaut
```
Utilisateur : admin
Mot de passe : admin123
```

> **Important :** Changez ces identifiants par défaut dès la première connexion !

### Ajouter un nouvel identifiant
1. Dans la section "Ajouter un nouvel identifiant"
2. Saisissez le nom d'utilisateur et le mot de passe
3. Cliquez sur "Ajouter"

### Modifier un identifiant
1. Cliquez sur l'icône de modification (crayon) dans le tableau
2. Modifiez le mot de passe ou le statut
3. Laissez le mot de passe vide pour ne pas le changer
4. Cliquez sur "Modifier"

### Supprimer un identifiant
1. Cliquez sur l'icône de suppression (poubelle)
2. Confirmez la suppression
3. **Note :** Il doit toujours rester au moins un identifiant actif

## Sécurité

### Bonnes pratiques
- Utilisez des mots de passe forts (lettres, chiffres, caractères spéciaux)
- Désactivez les comptes non utilisés au lieu de les supprimer
- Vérifiez régulièrement les dates de dernière connexion
- Gardez un nombre minimal d'identifiants administrateurs

### Protection des fichiers
- `.admin_credentials.json` : Fichier protégé par `.htaccess`
- `.htaccess` : Protection contre l'accès web aux fichiers cachés
- Les mots de passe sont hachés avec `PASSWORD_DEFAULT`

## Structure du fichier JSON

```json
{
  "admin": {
    "username": "admin",
    "password": "admin123",
    "password_hash": "$2y$10$...",
    "created_at": "2024-01-01 12:00:00",
    "last_login": "2024-01-02 14:30:00",
    "active": true
  }
}
```

## Migration depuis l'ancien système

Le système est compatible avec l'ancien système d'authentification :
- Si le fichier `.admin_credentials.json` n'existe pas, l'identifiant par défaut est créé automatiquement
- L'authentification fonctionne avec les anciens mots de passe en clair et les nouveaux hachés

## Dépannage

### Fichier JSON corrompu
Si le fichier `.admin_credentials.json` est corrompu :
1. Supprimez le fichier
2. Rechargez la page de connexion
3. Le système recréera l'identifiant par défaut

### Tous les identifiants perdus
1. Supprimez le fichier `.admin_credentials.json`
2. Utilisez l'identifiant par défaut : `admin` / `admin123`
3. Recréez vos identifiants personnalisés

### Problème d'accès au fichier
Vérifiez les permissions :
```bash
chmod 644 .admin_credentials.json
chmod 644 .htaccess
```

## Fichiers modifiés

- `admin/gestion-identifiants.php` : Interface de gestion (nouveau)
- `admin/login.php` : Système d'authentification (modifié)
- `admin/functions.php` : Fonction checkAuth() (modifiée)
- `admin/header.php` : Ajout du lien menu (modifié)
- `admin/.htaccess` : Protection fichiers (nouveau)
- `admin/.admin_credentials.json` : Stockage identifiants (généré automatiquement)