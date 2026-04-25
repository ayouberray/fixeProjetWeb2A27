<?php
session_start();
require_once '../../../MODEL/Auth.php';

if (!Auth::isAdmin()) {
    header('Location: ../../frontoffice/login.php');
    exit();
}

$id = $_GET['id'] ?? 0;

$host = 'localhost';
$dbname = '2a27';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];
    
    try {
        $sql = "UPDATE users SET nom = :nom, prenom = :prenom, cin = :cin, email = :email, telephone = :telephone, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':cin' => $cin,
            ':email' => $email,
            ':telephone' => $telephone,
            ':role' => $role,
            ':id' => $id
        ]);
        $message = "✅ Utilisateur modifié avec succès !";
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
    } catch(PDOException $e) {
        $error = "❌ Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier utilisateur</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h2 { color: #2563eb; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #334155; }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn-back {
            background: #64748b;
            margin-top: 0.5rem;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        .success { background: #d1fae5; color: #059669; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem; }
        .error { background: #fee2e2; color: #dc2626; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="form-container">
        <h2>✏️ Modifier l'utilisateur</h2>
        <?php if($message): ?><div class="success"><?= $message ?></div><?php endif; ?>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></div>
            <div class="form-group"><label>Prénom</label><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required></div>
            <div class="form-group"><label>CIN</label><input type="text" name="cin" value="<?= $user['cin'] ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
            <div class="form-group"><label>Téléphone</label><input type="text" name="telephone" value="<?= $user['telephone'] ?>"></div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="citoyen" <?= $user['role'] === 'citoyen' ? 'selected' : '' ?>>Citoyen</option>
                    <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : '' ?>>Agent</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>
            <button type="submit" class="btn">Enregistrer</button>
            <a href="index.php" class="btn btn-back">Retour à la liste</a>
        </form>
    </div>
</body>
</html>