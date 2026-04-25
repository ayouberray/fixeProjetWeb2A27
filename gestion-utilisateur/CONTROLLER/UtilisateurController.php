<?php
// Fichier: CONTROLLER/UtilisateurController.php
// Contrôleur de gestion des utilisateurs (Back Office)

session_start();
require_once __DIR__ . '/../MODEL/Utilisateur.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../VIEW/frontoffice/login.php');
    exit();
}

$utilisateur = new Utilisateur();

// ==================== AJOUTER UN UTILISATEUR ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'],
        'cin' => $_POST['cin'],
        'telephone' => $_POST['telephone'],
        'password' => $_POST['password'],
        'role' => $_POST['role'],
        'ville' => $_POST['ville'] ?? 'Tunis'
    ];
    
    if ($utilisateur->create($data)) {
        $_SESSION['success'] = "Utilisateur ajouté avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout";
    }
    
    header('Location: ../VIEW/backoffice/liste_utilisateurs.php');
    exit();
}

// ==================== MODIFIER UN UTILISATEUR ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'],
        'cin' => $_POST['cin'],
        'telephone' => $_POST['telephone'],
        'role' => $_POST['role']
    ];
    
    if ($utilisateur->update($id, $data)) {
        $_SESSION['success'] = "Utilisateur modifié avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la modification";
    }
    
    header('Location: ../VIEW/backoffice/liste_utilisateurs.php');
    exit();
}

// ==================== SUPPRIMER UN UTILISATEUR ====================
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ne pas supprimer soi-même
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
    } else {
        if ($utilisateur->delete($id)) {
            $_SESSION['success'] = "Utilisateur supprimé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
    }
    
    header('Location: ../VIEW/backoffice/liste_utilisateurs.php');
    exit();
}

// ==================== RÉCUPÉRER LES STATISTIQUES POUR L'AFFICHAGE ====================
$users = $utilisateur->getAll();
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');
?>