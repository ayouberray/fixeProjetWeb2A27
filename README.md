# InnoGov — Portail de gestion des services gouvernementaux

![PHP](https://img.shields.io/badge/PHP-8.1+-blue) ![MySQL](https://img.shields.io/badge/MySQL-8.0-orange) ![MVC](https://img.shields.io/badge/Architecture-MVC-green) ![PDO](https://img.shields.io/badge/DB-PDO-purple)

## Description

InnoGov est une application web de gestion des utilisateurs pour les institutions gouvernementales tunisiennes. Elle propose une interface **FrontOffice** pour les citoyens et un **BackOffice** complet pour les administrateurs.

---

## Architecture MVC

```
innogov/
├── config/
│   └── database.php          # Constantes de configuration
├── core/
│   ├── Database.php           # Connexion PDO (Singleton)
│   ├── Model.php              # Classe abstraite Model
│   ├── Controller.php         # Classe abstraite Controller
│   └── Validator.php          # Moteur de validation PHP
├── models/
│   └── UserModel.php          # CRUD + requêtes Users
├── controllers/
│   ├── AuthController.php     # Login / Register / Logout
│   ├── UserController.php     # BackOffice Admin (CRUD)
│   └── CitizenController.php  # FrontOffice Citoyen
├── views/
│   ├── frontoffice/
│   │   ├── auth/              # login.php, register.php
│   │   └── citizen/           # dashboard.php, profile.php
│   └── backoffice/
│       ├── _sidebar.php       # Layout partagé
│       ├── dashboard/         # index.php
│       └── users/             # index.php, create.php, edit.php, show.php
├── database/
│   └── innogov.sql            # Schéma + données initiales
├── .htaccess
└── index.php                  # Front Controller (routeur)
```

---

## Fonctionnalités

### FrontOffice (Citoyen)
- Inscription avec validation complète (CIN, email unique, mot de passe fort)
- Connexion sécurisée avec vérification du statut du compte
- Tableau de bord personnel
- Modification du profil + changement de mot de passe

### BackOffice (Admin)
- Tableau de bord avec statistiques (rôles, statuts, derniers inscrits)
- **CRUD complet** sur les utilisateurs :
  - **L**iste avec recherche (nom, email, CIN) et filtres (rôle, statut)
  - **C**réation avec tous les champs + validation
  - **R**ead : fiche détaillée de l'utilisateur
  - **U**pdate : modification avec conservation du mot de passe si vide
  - **D**elete : suppression avec protection contre l'auto-suppression

---

## Principes techniques respectés

| Critère | Implémentation |
|---------|---------------|
| Architecture MVC | Séparation stricte Model / View / Controller |
| POO | Classes abstraites, héritage, encapsulation, Singleton |
| PDO | Requêtes préparées, zéro concaténation SQL |
| Validation PHP | Classe `Validator` — aucune dépendance sur HTML5 |
| Sécurité | `password_hash()`, `htmlspecialchars()`, `session_regenerate_id()` |
| Rôles | `admin` → BackOffice, `client` → FrontOffice |

---

## Installation

### Prérequis
- PHP >= 8.1
- MySQL >= 8.0
- Apache avec `mod_rewrite`

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-compte/innogov.git
   cd innogov
   ```

2. **Importer la base de données**
   ```bash
   mysql -u root -p < database/innogov.sql
   ```

3. **Configurer la connexion**
   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'innogov');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('BASE_URL', 'http://localhost/innogov/');
   ```

4. **Démarrer**
   - Placez le dossier dans `htdocs/` (XAMPP) ou `www/` (WAMP)
   - Accédez à `http://localhost/innogov/`

---

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | `admin@innogov.tn` | `password` |
| Citoyen | `mehdi@email.tn` | `password` |
| Citoyen | `sarra@email.tn` | `password` |

> ⚠️ Changez les mots de passe en production !

---

## Règles de validation PHP (Classe Validator)

- `required` — champ obligatoire
- `minLength / maxLength` — longueur min/max
- `email` — format d'email valide
- `password` — min 8 caractères, 1 majuscule, 1 chiffre, 1 symbole
- `matches` — confirmation de mot de passe
- `phone` — format téléphone (+216…)
- `cin` — exactement 8 chiffres
- `date` — format Y-m-d valide
- `inList` — valeur dans une liste autorisée

---

## Sécurité

- Mots de passe hashés avec `PASSWORD_BCRYPT` (coût 12)
- Requêtes SQL préparées (protection injection SQL)
- Échappement HTML systématique (`htmlspecialchars`)
- Régénération de l'ID de session à la connexion
- Protection `.htaccess` contre l'accès direct aux fichiers PHP
- Vérification du rôle à chaque action admin

---

## Auteur

Projet InnoGov — Module Authentification & Gestion Utilisateurs
