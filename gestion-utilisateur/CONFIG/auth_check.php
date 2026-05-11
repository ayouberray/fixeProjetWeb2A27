<?php
/**
 * Fichier de vérification d'authentification
 * À inclure dans chaque page qui nécessite une authentification
 * 
 * Usage: require_once(__DIR__ . '/../CONFIG/auth_check.php');
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 * Redirige vers la page de login si ce n'est pas le cas
 */
function checkAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
        header('Location: ../../VIEW/frontoffice/login.php');
        exit();
    }
}

/**
 * Vérifie si l'utilisateur est un admin ou un agent (accès backoffice)
 * Redirige vers la frontoffice si ce n'est pas le cas
 */
function checkBackofficeAccess() {
    checkAuth();
    
    if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent') {
        $_SESSION['error'] = "Vous n'avez pas les permissions pour accéder à cette zone";
        header('Location: ../../VIEW/frontoffice/index.php');
        exit();
    }
}

/**
 * Vérifie si l'utilisateur est un client (accès frontoffice)
 * Redirige vers le backoffice si c'est un admin/agent
 */
function checkFrontofficeAccess() {
    checkAuth();
    
    if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'agent') {
        $_SESSION['error'] = "Vous devez utiliser le backoffice pour accéder à cette zone";
        header('Location: ../../VIEW/backoffice/backoffice.php');
        exit();
    }
}

/**
 * Vérifie si l'utilisateur est admin uniquement
 */
function checkAdminAccess() {
    checkAuth();
    
    if ($_SESSION['user_role'] !== 'admin') {
        $_SESSION['error'] = "Vous devez être administrateur pour accéder à cette zone";
        header('Location: ../../VIEW/backoffice/backoffice.php');
        exit();
    }
}

/**
 * Retourne les informations de l'utilisateur connecté
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nom' => $_SESSION['user_nom'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? ''
    ];
}

/**
 * Vérifie si l'utilisateur connecté est admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Vérifie si l'utilisateur connecté est agent
 */
function isAgent() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'agent';
}

/**
 * Vérifie si l'utilisateur connecté est client
 */
function isClient() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
?>
