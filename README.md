# Projet Web PHP

Ce dépôt contient une application Web PHP développée pour un projet de gestion d'offres, shifts, candidatures et utilisateurs.

## Structure principale

- `index.php` : page d'accueil principale.
- `config.php` : configuration globale de l'application.
- `CONTROLLER/` : contrôleurs principaux métier.
- `MODEL/` : modèles de données.
- `VIEW/` : vues frontoffice et backoffice.
- `projet/` : sous-projet avec API, contrôleurs et modèles.
- `gestion-utilisateur/` : gestion des utilisateurs et authentification.
- `vendor/` : dépendances gérées par Composer.
- `ASSETS/` : ressources statiques (CSS, JS, images, vidéo).

## Installation

1. Installez XAMPP ou un serveur LAMP/WAMP compatible.
2. Placez le dossier dans le répertoire de votre serveur web (`htdocs` pour XAMPP).
3. Assurez-vous que `php` et `mysql` sont activés.
4. Exécutez `composer install` si nécessaire pour réinstaller les dépendances.

## Utilisation

1. Démarrez Apache et MySQL.
2. Importez les fichiers SQL depuis `database/` ou `gestion-utilisateur/CONFIG/` selon les besoins.
3. Ouvrez le navigateur à l'adresse correspondante, par exemple :
   - `http://localhost/try1/`
   - `http://localhost/try1/gestion-utilisateur/`

## Remarques

- Ce projet utilise des routes et des vues séparées selon les modules.
- Vérifiez les permissions d'écriture dans les dossiers de `uploads/` si nécessaire.
- Adaptez la configuration de la base de données dans `config.php` ou les fichiers de configuration spécifiques.

## Auteur

Projet géré localement dans `c:\xampp\htdocs\try1`.
