<?php
// Fichier: CONTROLLER/AuthController.php
// Contrôleur d'authentification - Gère l'inscription et la connexion

session_start();
require_once '../MODEL/Utilisateur.php';

$utilisateur = new Utilisateur();

// ==================== TRAITEMENT DE L'INSCRIPTION ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    
    $errors = [];
    
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $sexe = $_POST['sexe'] ?? '';
    $dateNaissance = $_POST['date_naissance'] ?? '';
    $cin = trim($_POST['cin'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $typeCompte = $_POST['type_compte'] ?? '';
    $pays = $_POST['pays'] ?? 'Tunisie';
    $ville = $_POST['ville'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $nomOrganisation = trim($_POST['nom_organisation'] ?? '');
    $profession = trim($_POST['profession'] ?? '');
    
    // ===== VALIDATIONS =====
    
    // Validation nom
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    } elseif (strlen($nom) < 2) {
        $errors[] = "Le nom doit contenir au moins 2 caractères";
    }
    
    // Validation prénom
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis";
    } elseif (strlen($prenom) < 2) {
        $errors[] = "Le prénom doit contenir au moins 2 caractères";
    }
    
    // Validation sexe
    if (empty($sexe)) {
        $errors[] = "Le sexe est requis";
    }
    
    // Validation date de naissance
    if (empty($dateNaissance)) {
        $errors[] = "La date de naissance est requise";
    } else {
        $age = date('Y') - date('Y', strtotime($dateNaissance));
        if ($age < 18) {
            $errors[] = "Vous devez avoir au moins 18 ans";
        }
    }
    
    // Validation CIN
    if (empty($cin)) {
        $errors[] = "Le numéro CIN est requis";
    } elseif (!preg_match('/^[0-9]{8}$/', $cin)) {
        $errors[] = "Le CIN doit contenir exactement 8 chiffres";
    } elseif ($utilisateur->cinExists($cin)) {
        $errors[] = "Ce numéro CIN est déjà utilisé";
    }
    
    // Validation téléphone
    if (empty($telephone)) {
        $errors[] = "Le numéro de téléphone est requis";
    } elseif (!preg_match('/^[0-9]{8}$/', $telephone)) {
        $errors[] = "Le téléphone doit contenir 8 chiffres";
    }
    
    // Validation type de compte
    if (empty($typeCompte)) {
        $errors[] = "Veuillez sélectionner un type de compte";
    } elseif (!in_array($typeCompte, ['citoyen', 'professionnel', 'agent_public'])) {
        $errors[] = "Type de compte invalide";
    }
    
    // Validation ville
    if (empty($ville)) {
        $errors[] = "Veuillez sélectionner votre ville";
    }
    
    // Validation email
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer un email valide";
    } elseif ($utilisateur->emailExists($email)) {
        $errors[] = "Cet email est déjà utilisé";
    }
    
    // Validation mot de passe
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Validation champs conditionnels selon type de compte
    if ($typeCompte === 'professionnel') {
        if (empty($nomOrganisation)) {
            $errors[] = "Le nom de l'entreprise est requis";
        }
        if (empty($profession)) {
            $errors[] = "La profession est requise";
        }
    } elseif ($typeCompte === 'agent_public') {
        if (empty($nomOrganisation)) {
            $errors[] = "Le nom de l'institution est requis";
        }
    }
    
    // S'il n'y a pas d'erreurs, on enregistre
    if (empty($errors)) {
        $utilisateur->setNom($nom)
                    ->setPrenom($prenom)
                    ->setSexe($sexe)
                    ->setDateNaissance($dateNaissance)
                    ->setCin($cin)
                    ->setTelephone($telephone)
                    ->setTypeCompte($typeCompte)
                    ->setPays($pays)
                    ->setVille($ville)
                    ->setEmail($email)
                    ->setPlainPassword($password)
                    ->setNomOrganisation($nomOrganisation)
                    ->setProfession($profession)
                    ->setStatut('actif');
        
        if ($utilisateur->save()) {
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header('Location: ../VIEW/frontoffice/login.php');
            exit();
        } else {
            $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
    
    // S'il y a des erreurs, on les stocke en session
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../VIEW/frontoffice/register.php');
        exit();
    }
}

// ==================== TRAITEMENT DE LA CONNEXION ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs";
        header('Location: ../VIEW/frontoffice/login.php');
        exit();
    }
    
    $user = $utilisateur->login($email, $password);
    
    if ($user) {
        // Redirection selon le type de compte
        if ($user['type_compte'] === 'agent_public') {
            header('Location: ../VIEW/backoffice/dashboard.php');
        } else {
            header('Location: ../VIEW/frontoffice/index.php');
        }
        exit();
    } else {
        $_SESSION['error'] = "Email ou mot de passe incorrect";
        header('Location: ../VIEW/frontoffice/login.php');
        exit();
    }
}

// ==================== TRAITEMENT DE LA DÉCONNEXION ====================
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $utilisateur->logout();
    header('Location: ../VIEW/frontoffice/login.php');
    exit();
}
?>