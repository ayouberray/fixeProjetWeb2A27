<?php
session_start();
require_once '../../MODEL/Utilisateur.php';

$utilisateur = new Utilisateur();

if (!$utilisateur->isAdmin()) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$id = $_GET['id'] ?? 0;

// Ne pas supprimer soi-même
if ($id == $_SESSION['user_id']) {
    header('Location: liste_utilisateurs.php?error=Vous ne pouvez pas vous supprimer vous-même');
    exit();
}

if ($utilisateur->delete($id)) {
    header('Location: liste_utilisateurs.php?message=Utilisateur supprimé avec succès');
} else {
    header('Location: liste_utilisateurs.php?error=Erreur lors de la suppression');
}
exit();
?>