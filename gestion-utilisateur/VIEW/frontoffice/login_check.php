<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'innogov_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur de connexion BDD";
    header('Location: login.php');
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Veuillez remplir tous les champs";
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM utilisateurs WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    
    // Redirection selon le rôle
    if ($user['role'] === 'admin' || $user['role'] === 'agent') {
        // Admin et agents accèdent au backoffice
        header('Location: ../backoffice/backoffice.php');
    } else {
        // Clients/citoyens/professionnels accèdent à la frontoffice
        header('Location: index.php');
    }
    exit();
} else {
    $_SESSION['error'] = "Email ou mot de passe incorrect";
    header('Location: login.php');
    exit();
}
?>