<?php
/**
 * Fichier de déconnexion sécurisé
 * Nettoie toutes les données de session et les cookies
 */

session_start();

// Effacer toutes les variables de session
$_SESSION = [];

// Supprimer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Redirection vers login.php
header('Location: login.php');
exit();
?>