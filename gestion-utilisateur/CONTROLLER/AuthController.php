<?php
// Fichier: CONTROLLER/AuthController.php
session_start();
require_once '../MODEL/Utilisateur.php';
require_once '../CONFIG/config.php'; // Votre MailConfig

class AuthController {
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../VIEW/frontoffice/register.php');
            exit();
        }
        
        $errors = [];
        
        // ========== RÉCUPÉRATION DES DONNÉES ==========
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $sexe = $_POST['sexe'] ?? 'Homme';
        $date_naissance = $_POST['date_naissance'] ?? '';
        $cin = trim($_POST['cin'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $pays = $_POST['pays'] ?? 'Tunisie';
        $ville = $_POST['ville'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $type_compte = $_POST['type_compte'] ?? 'citoyen';
        $nom_organisation = trim($_POST['nom_organisation'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        
        // ========== CONVERSION DE LA DATE ==========
        if (!empty($date_naissance)) {
            $dateParts = explode('/', $date_naissance);
            if (count($dateParts) === 3) {
                $date_naissance = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            }
        } else {
            $date_naissance = '2000-01-01';
        }
        
        // ========== VALIDATIONS ==========
        if (empty($nom)) $errors[] = "Le nom est requis.";
        if (empty($prenom)) $errors[] = "Le prénom est requis.";
        if (empty($cin)) $errors[] = "Le CIN est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        if (empty($password)) $errors[] = "Le mot de passe est requis.";
        if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        if ($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        
        // Vérifier si l'email est un vrai email (domaine qui existe)
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            $errors[] = "Le domaine email '$domain' n'existe pas ou n'est pas valide.";
        }
        
        // ========== VÉRIFICATIONS BASE DE DONNÉES ==========
        $utilisateurModel = new Utilisateur();
        
        if ($utilisateurModel->getUserByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        
        if ($utilisateurModel->cinExists($cin)) {
            $errors[] = "Ce CIN est déjà utilisé.";
        }
        
        // ========== AFFICHAGE DES ERREURS ==========
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../VIEW/frontoffice/register.php');
            exit();
        }
        
        // ========== CRÉATION DE L'UTILISATEUR ==========
        // statut = 'en_attente' jusqu'à confirmation email
        $userId = $utilisateurModel->create([
            'nom' => $nom,
            'prenom' => $prenom,
            'sexe' => $sexe,
            'date_naissance' => $date_naissance,
            'type_compte' => $type_compte,
            'role' => 'user',
            'pays' => $pays,
            'ville' => $ville,
            'email' => $email,
            'telephone' => $telephone,
            'password' => $password,
            'cin' => $cin,
            'nom_organisation' => $nom_organisation,
            'profession' => $profession,
            'statut' => 'en_attente'  // En attente de vérification email
        ]);
        
        if ($userId) {
            // ========== GÉNÉRATION DU TOKEN DE VÉRIFICATION ==========
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            if ($utilisateurModel->saveVerificationToken($email, $token, $expires)) {
                // ========== CRÉATION DU LIEN DE VÉRIFICATION ==========
                $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                $verifLink = $protocol . $host . '/try1/gestion-utilisateur/VIEW/frontoffice/verify_email.php?token=' . $token;
                
                // ========== ENVOI DE L'EMAIL DE CONFIRMATION ==========
                $mailer = new MailConfig();
                $fullName = $prenom . ' ' . $nom;
                
                if ($mailer->sendVerificationEmail($email, $fullName, $verifLink)) {
                    $_SESSION['success'] = "✅ Un email de confirmation a été envoyé à <strong>" . htmlspecialchars($email) . "</strong><br>
                                            📧 Veuillez vérifier votre boîte de réception (et vos spams) pour activer votre compte.";
                } else {
                    $_SESSION['error'] = "❌ Votre compte a été créé mais l'envoi de l'email de confirmation a échoué. Veuillez contacter l'administrateur.";
                }
            } else {
                $_SESSION['error'] = "❌ Erreur technique lors de la création du compte. Veuillez réessayer.";
            }
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la création du compte. Veuillez réessayer.";
        }
        
        header('Location: ../VIEW/frontoffice/register.php');
        exit();
    }
    
    // ========== MÉTHODE DE CONNEXION AVEC VÉRIFICATION EMAIL ==========
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../VIEW/frontoffice/login.php');
            exit();
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            
            $role = strtolower(trim($user['role'] ?? ''));
            $isBackofficeUser = ($role === 'admin' || $role === 'agent');
            
            if ($isBackofficeUser) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $role;
                $_SESSION['user_type'] = $user['type_compte'];
                
                header('Location: ../VIEW/backoffice/backoffice.php');
                exit();
            }
            
            // Vérifier si l'email est confirmé pour les comptes clients
            if ($user['email_verifie'] != 1) {
                $_SESSION['error'] = "❌ Veuillez confirmer votre email avant de vous connecter.<br>
                                      Vérifiez votre boîte de réception (et vos spams).<br>
                                      <a href='resend_verification.php?email=" . urlencode($email) . "' style='color:#006D5B;'>Renvoyer l'email de confirmation</a>";
                header('Location: ../VIEW/frontoffice/login.php');
                exit();
            }
            
            // Vérifier si le compte est actif
            if ($user['statut'] !== 'actif') {
                $_SESSION['error'] = "❌ Votre compte n'est pas actif. Veuillez contacter l'administrateur.";
                header('Location: ../VIEW/frontoffice/login.php');
                exit();
            }
            
            // Connexion réussie pour client
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_type'] = $user['type_compte'];
            
            header('Location: ../VIEW/frontoffice/index.php');
            exit();
            
        } else {
            $_SESSION['error'] = "❌ Email ou mot de passe incorrect.";
            header('Location: ../VIEW/frontoffice/login.php');
            exit();
        }
    }
}

// ========== ROUTAGE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $controller = new AuthController();
    
    if ($action === 'register') {
        $controller->register();
    } elseif ($action === 'login') {
        $controller->login();
    } else {
        header('Location: ../VIEW/frontoffice/index.php');
        exit();
    }
} else {
    header('Location: ../VIEW/frontoffice/index.php');
    exit();
}
?>