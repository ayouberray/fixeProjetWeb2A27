<?php
/**
 * DEPRECATED - Ce fichier ne doit plus être utilisé
 * Utilisez CONTROLLER/AuthController.php à la place
 * 
 * Ce fichier est conservé pour la compatibilité rétroactive
 * mais il redirige vers le bon contrôleur d'authentification
 */

session_start();

// Redirect vers login.php avec un message d'erreur si accès direct
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Si un POST arrive ici, rediriger vers le bon contrôleur
$_SESSION['error'] = 'Veuillez utiliser le formulaire de connexion correct';
header('Location: ../../CONTROLLER/AuthController.php');
exit();
?>