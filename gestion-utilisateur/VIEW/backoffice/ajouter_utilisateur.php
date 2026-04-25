<?php
session_start();
require_once '../../MODEL/Utilisateur.php';

$utilisateur = new Utilisateur();

if (!$utilisateur->isAdmin()) {
    header('Location: ../frontoffice/login.php');
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
    $password = $_POST['password'];
    
    // Vérifications
    if ($utilisateur->cinExists($cin)) {
        $error = "Ce CIN est déjà utilisé";
    } elseif ($utilisateur->emailExists($email)) {
        $error = "Cet email est déjà utilisé";
    } else {
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'cin' => $cin,
            'email' => $email,
            'telephone' => $telephone,
            'password' => $password,
            'role' => $role
        ];
        
        if ($utilisateur->create($data)) {
            $message = "✅ Utilisateur ajouté avec succès !";
        } else {
            $error = "❌ Erreur lors de l'ajout";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
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
        h2 { color: #2e7d32; margin-bottom: 1.5rem; text-align: center; }
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
            background: #2e7d32;
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
        <h2>➕ Ajouter un utilisateur</h2>
        <?php if($message): ?><div class="success"><?= $message ?></div><?php endif; ?>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Nom</label><input type="text" name="nom" required></div>
            <div class="form-group"><label>Prénom</label><input type="text" name="prenom" required></div>
            <div class="form-group"><label>CIN (8 chiffres)</label><input type="text" name="cin" maxlength="8" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Téléphone</label><input type="text" name="telephone"></div>
            <div class="form-group"><label>Mot de passe</label><input type="password" name="password" required></div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="citoyen">Citoyen</option>
                    <option value="agent">Agent</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" class="btn">Ajouter</button>
            <a href="liste_utilisateurs.php" class="btn btn-back">Retour à la liste</a>
        </form>
    </div>
</body>
</html>