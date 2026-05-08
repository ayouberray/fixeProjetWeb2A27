# Système d'Authentification et Redirection d'InnoGov

## Vue d'ensemble

Le système d'authentification fonctionne selon le **rôle** de l'utilisateur:

### Rôles et Accès

| Rôle | Type de Compte | Accès À |
|------|---|---|
| `admin` | N/A | **Backoffice** (gestion complète) |
| `agent` | `agent_public` | **Backoffice** (gestion limitée) |
| `user` | `citoyen` ou `professionnel` | **Frontoffice** (services citoyens) |

## Flux de Connexion

```
1. Utilisateur remplit le formulaire de login
2. Formulaire envoie vers CONTROLLER/AuthController.php (action=login)
3. AuthController vérifie les identifiants
4. Si succès:
   - Variables de session définies
   - Redirection selon le rôle:
     - admin/agent → backoffice
     - user → frontoffice
5. Si erreur: retour à login avec message
```

## Flux d'Inscription

```
1. Utilisateur s'inscrit avec le formulaire register.php
2. Formulaire envoie vers CONTROLLER/AuthController.php (action=register)
3. Validations effectuées
4. Rôle assigné automatiquement selon type_compte:
   - 'agent_public' → role = 'agent'
   - 'citoyen' / 'professionnel' → role = 'user'
5. Utilisateur enregistré dans la BDD
6. Redirection vers login.php avec message de succès
```

## Utilisation du fichier auth_check.php

### Protection des pages Backend (Backoffice)

Dans les fichiers du backoffice, ajoutez en début:

```php
<?php
session_start();
require_once(__DIR__ . '/../../CONFIG/auth_check.php');
checkBackofficeAccess(); // Vérifie que l'utilisateur est admin ou agent
?>
```

### Protection des pages Frontend (Frontoffice)

Dans les fichiers du frontoffice client, ajoutez en début:

```php
<?php
session_start();
require_once(__DIR__ . '/../../CONFIG/auth_check.php');
checkFrontofficeAccess(); // Vérifie que l'utilisateur est un client
?>
```

### Protection Admin Uniquement

Pour les pages accessibles uniquement aux administrateurs:

```php
<?php
session_start();
require_once(__DIR__ . '/../../CONFIG/auth_check.php');
checkAdminAccess(); // Vérifie que l'utilisateur est admin
?>
```

## Fonctions Disponibles dans auth_check.php

### Vérifications
- `checkAuth()` - Vérifie que l'utilisateur est connecté
- `checkBackofficeAccess()` - Accès backoffice (admin + agent)
- `checkFrontofficeAccess()` - Accès frontoffice (clients)
- `checkAdminAccess()` - Accès admin uniquement

### Informations
- `getCurrentUser()` - Retourne les données de l'utilisateur connecté
- `isAdmin()` - Retourne true si admin
- `isAgent()` - Retourne true si agent
- `isClient()` - Retourne true si client
- `isAuthenticated()` - Retourne true si connecté

## Exemple d'Usage

```php
<?php
session_start();
require_once(__DIR__ . '/../../CONFIG/auth_check.php');
checkBackofficeAccess();

$user = getCurrentUser();
echo "Bienvenue " . $user['nom'];

if (isAdmin()) {
    echo "Vous êtes administrateur";
}
?>
```

## Variables de Session

Après la connexion, les variables suivantes sont disponibles:

```php
$_SESSION['user_id']    // ID de l'utilisateur
$_SESSION['user_nom']   // Nom complet
$_SESSION['user_email'] // Email
$_SESSION['user_role']  // Rôle (admin, agent, user)
```

## Initialisation des Rôles

### Créer un Admin (Directement dans la BDD)

```sql
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte, 
    cin, telephone, ville, sexe, date_naissance, statut, date_creation
) VALUES (
    'Admin', 'InnoGov', 'admin@innogov.tn', 
    PASSWORD_HASH('password123', PASSWORD_DEFAULT), 'admin', 'N/A',
    '00000000', '29999999', 'Tunis', 'Homme', '1990-01-01', 'actif', NOW()
);
```

### Créer un Agent (Via formulaire ou BDD)

**Méthode 1: Via formulaire d'inscription**
- Type de Compte: `agent_public`
- Sera automatiquement défini avec rôle = `agent`

**Méthode 2: Directement dans la BDD**
```sql
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, nom_organisation, statut, date_creation
) VALUES (
    'Jean', 'Agent', 'agent@mail.tn',
    PASSWORD_HASH('password123', PASSWORD_DEFAULT), 'agent', 'agent_public',
    '12345678', '22222222', 'Tunis', 'Homme', '1985-05-10', 'Ministère de...', 'actif', NOW()
);
```

### Créer un Client (Via formulaire ou BDD)

**Méthode 1: Via formulaire d'inscription**
- Type de Compte: `citoyen` ou `professionnel`
- Sera automatiquement défini avec rôle = `user`

**Méthode 2: Directement dans la BDD**
```sql
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, statut, date_creation
) VALUES (
    'Ahmed', 'Citoyen', 'ahmed@mail.tn',
    PASSWORD_HASH('password123', PASSWORD_DEFAULT), 'user', 'citoyen',
    '87654321', '99999999', 'Sfax', 'Homme', '1995-03-15', 'actif', NOW()
);
```

## Redirection Automatique

- **Admin/Agent** qui essaient d'accéder à la frontoffice → Redirigés au backoffice
- **Clients** qui essaient d'accéder au backoffice → Redirigés à la frontoffice
- **Non connectés** qui accèdent à des pages protégées → Redirigés à login.php

## Notes Importantes

1. **Variables de session**: Vérifiez toujours `$_SESSION['user_role']` pour contrôler les accès
2. **Mot de passe**: Toujours hasher avec `PASSWORD_DEFAULT` (PASSWORD_BCRYPT par défaut)
3. **Sécurité**: N'exposez jamais le mot de passe, utilisez seulement le rôle
4. **Affichage conditionnellemen**: Utilisez les fonctions `isAdmin()`, `isAgent()`, etc. pour l'affichage UI

