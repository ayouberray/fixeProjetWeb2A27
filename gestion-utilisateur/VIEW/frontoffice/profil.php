<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$user = $utilisateur->getById($_SESSION['user_id']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $cin = trim($_POST['cin'] ?? '');
    $ville = $_POST['ville'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!password_verify($current_password, $user['password'])) {
        $error = "Mot de passe actuel incorrect";
    } else {
        $updateData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'cin' => $cin,
            'ville' => $ville,
            'role' => $user['role']
        ];
        
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $error = "Nouveau mot de passe trop court (min 6 caractères)";
            } elseif ($new_password !== $confirm_password) {
                $error = "Les mots de passe ne correspondent pas";
            } else {
                $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }
        
        if (empty($error) && $utilisateur->update($user['id'], $updateData)) {
            $success = "Vos informations ont été mises à jour avec succès";
            $user = $utilisateur->getById($_SESSION['user_id']);
            $_SESSION['user_nom'] = $user['nom'] . ' ' . $user['prenom'];
        } elseif (empty($error)) {
            $error = "Une erreur est survenue lors de la mise à jour";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Mon Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-mid: #008C75;
            --primary-light: #E6F4F0;
            --primary-glow: rgba(0, 109, 91, 0.18);
            --accent: #00C896;
            --white: #FFFFFF;
            --glass: rgba(255,255,255,0.92);
            --glass-border: rgba(255,255,255,0.6);
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-500: #64748B;
            --gray-700: #334155;
            --gray-900: #0F172A;
            --danger: #EF4444;
            --danger-light: #FEF2F2;
            --success: #10B981;
            --success-light: #ECFDF5;
            --shadow-card: 0 32px 80px rgba(0,0,0,0.22), 0 8px 24px rgba(0,0,0,0.12);
            --shadow-input: 0 2px 8px rgba(0,109,91,0.08);
            --transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            color: var(--gray-700);
            overflow-x: hidden;
        }

        /* ── SLIDESHOW BACKGROUND ── */
        .slideshow-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
        }
        .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.4s ease-in-out;
            transform: scale(1.04);
            transition: opacity 1.4s ease-in-out, transform 8s ease-in-out;
        }
        .slide.active {
            opacity: 1;
            transform: scale(1);
        }
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                160deg,
                rgba(0, 77, 61, 0.55) 0%,
                rgba(0, 30, 20, 0.65) 60%,
                rgba(0, 0, 0, 0.5) 100%
            );
            z-index: -1;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .navbar.scrolled { background: white; box-shadow: var(--shadow-sm); }
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }
        .logo-img {
            height: 40px;
            width: auto;
            max-width: 130px;
            object-fit: contain;
            display: block;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover { color: var(--primary); }
        .user-name {
            color: var(--primary);
            font-weight: 500;
            background: var(--primary-light);
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
        }
        .btn-profiled {
            background: var(--primary);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-profiled:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-logout:hover { background: #b91c1c; transform: translateY(-1px); }
        .btn-login {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover { background: var(--primary); color: white; transform: translateY(-1px); }
        .btn-register {
            background: var(--primary);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-register:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* ── PAGE LAYOUT ── */
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 88px 1.5rem 3rem;
        }

        .profile-layout {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        /* ── LEFT SIDEBAR ── */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .identity-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
        }
        .identity-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 90px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-mid) 100%);
            border-radius: 24px 24px 0 0;
        }

        .avatar-ring {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            z-index: 1;
        }
        .avatar-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            z-index: -1;
        }
        .avatar {
            width: 88px;
            height: 88px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary);
            border: 3px solid white;
        }

        .identity-name {
            font-family: 'Sora', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        .identity-role {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            border-radius: 40px;
            margin-top: 0.5rem;
            letter-spacing: 0.3px;
        }
        .role-admin { background: #FEF3C7; color: #92400E; }
        .role-agent { background: #DBEAFE; color: #1E40AF; }
        .role-citizen { background: var(--primary-light); color: var(--primary-dark); }

        .identity-meta {
            margin-top: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            text-align: left;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.82rem;
            color: var(--gray-500);
            padding: 0.55rem 0.75rem;
            background: var(--gray-50);
            border-radius: 10px;
            border: 1px solid var(--gray-200);
        }
        .meta-item i {
            color: var(--primary);
            width: 14px;
            text-align: center;
            font-size: 0.8rem;
        }
        .meta-item span { font-weight: 500; color: var(--gray-700); }

        .info-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.2rem 1.4rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .info-card-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .info-tip {
            font-size: 0.78rem;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* ── MAIN FORM PANEL ── */
        .form-panel {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }

        .panel-header {
            padding: 1.6rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .panel-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .panel-title i {
            width: 34px;
            height: 34px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .panel-badge {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--gray-500);
            background: var(--gray-100);
            padding: 0.3rem 0.8rem;
            border-radius: 40px;
            letter-spacing: 0.3px;
        }

        .panel-body { padding: 2rem; }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.9rem;
        }
        .alert-success {
            background: var(--success-light);
            border: 1px solid #A7F3D0;
            color: #065F46;
        }
        .alert-success .alert-icon { background: #D1FAE5; color: var(--success); }

        .alert-error {
            background: var(--danger-light);
            border: 1px solid #FECACA;
            color: #991B1B;
        }
        .alert-error .alert-icon { background: #FEE2E2; color: var(--danger); }

        /* Sections */
        .section-block { margin-bottom: 2rem; }
        .section-block:last-child { margin-bottom: 0; }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-200);
        }
        .section-heading-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--primary), var(--primary-mid));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        .section-heading-text {
            font-family: 'Sora', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gray-900);
        }
        .section-heading-sub {
            font-size: 0.75rem;
            color: var(--gray-500);
            font-weight: 400;
            margin-left: auto;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .form-group-full { grid-column: span 2; }

        label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--gray-700);
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        label .required { color: var(--danger); }

        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-300);
            font-size: 0.8rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        input, select {
            width: 100%;
            padding: 0.75rem 0.9rem 0.75rem 2.4rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.875rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--gray-900);
            background: white;
            transition: var(--transition);
            appearance: none;
        }
        input:hover, select:hover { border-color: var(--gray-300); }
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: white;
        }
        input:focus ~ .input-icon,
        select:focus ~ .input-icon { color: var(--primary); }
        .input-wrapper:focus-within .input-icon { color: var(--primary); }

        select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748B' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.9rem center; padding-right: 2.2rem; }

        /* Password strength */
        .password-strength {
            height: 3px;
            background: var(--gray-200);
            border-radius: 2px;
            margin-top: 0.4rem;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            border-radius: 2px;
            transition: width 0.4s ease, background 0.3s;
            width: 0;
        }

        /* Save Button */
        .btn-save {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-mid) 100%);
            color: white;
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            width: 100%;
            margin-top: 1.75rem;
            transition: var(--transition);
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }
        .btn-save::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,109,91,0.4);
        }
        .btn-save:active { transform: translateY(0); }

        /* ── FOOTER ── */
        .footer {
            background: rgba(10,20,15,0.92);
            backdrop-filter: blur(8px);
            color: #64748B;
            text-align: center;
            padding: 1.5rem 2rem;
            font-size: 0.78rem;
            letter-spacing: 0.3px;
        }
        .footer span { color: var(--accent); font-weight: 600; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .profile-layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; }
            .identity-card { flex: 1 1 280px; }
            .info-card { flex: 1 1 200px; }
        }
        @media (max-width: 640px) {
            .page-wrapper { padding: 76px 1rem 2.5rem; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group-full { grid-column: span 1; }
            .panel-body { padding: 1.25rem; }
            .navbar { padding: 0 1rem; }
            .nav-links > a:not([class]) { display: none; }
        }
    </style>
</head>
<body>

<!-- SLIDESHOW BG -->
<div class="slideshow-bg">
    <div class="slide" style="background-image: url('../../assets/images/tunisia1.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia2.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia3.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia4.jpg');"></div>
</div>
<div class="bg-overlay"></div>

<!-- NAVBAR -->
<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <img src="../../assets/images/logo.png" alt="Logo" class="logo-img">
        </a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="#" id="servicesLink">Services</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-name">👋 <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <a href="profil.php" class="btn-profiled">Mon profil</a>
                <a href="logout.php" class="btn-logout">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Connexion</a>
                <a href="register.php" class="btn-register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="page-wrapper">
    <div class="profile-layout">

        <!-- LEFT SIDEBAR -->
        <aside class="sidebar">
            <!-- Identity Card -->
            <div class="identity-card">
                <div class="avatar-ring">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="identity-name"><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></div>
                <?php
                $roleClass = 'role-citizen';
                $roleLabel = 'Citoyen';
                $roleIcon = 'fa-user';
                if($user['role'] === 'admin') { $roleClass='role-admin'; $roleLabel='Administrateur'; $roleIcon='fa-crown'; }
                elseif($user['role'] === 'agent') { $roleClass='role-agent'; $roleLabel='Agent public'; $roleIcon='fa-briefcase'; }
                ?>
                <div class="identity-role <?= $roleClass ?>">
                    <i class="fas <?= $roleIcon ?>"></i> <?= $roleLabel ?>
                </div>
                <div class="identity-meta">
                    <div class="meta-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($user['telephone']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($user['ville']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-id-card"></i>
                        <span>CIN: <?= htmlspecialchars($user['cin']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Tip Card -->
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-shield-alt"></i> Sécurité</div>
                <p class="info-tip">Votre mot de passe actuel est requis pour toute modification. Choisissez un mot de passe d'au moins 8 caractères combinant chiffres et lettres.</p>
            </div>
        </aside>

        <!-- FORM PANEL -->
        <div class="form-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-user-edit"></i>
                    Modifier mon profil
                </div>
                <span class="panel-badge">Compte vérifié</span>
            </div>

            <div class="panel-body">
                <?php if($success): ?>
                <div class="alert alert-success">
                    <div class="alert-icon"><i class="fas fa-check"></i></div>
                    <div><?= htmlspecialchars($success) ?></div>
                </div>
                <?php endif; ?>
                <?php if($error): ?>
                <div class="alert alert-error">
                    <div class="alert-icon"><i class="fas fa-exclamation"></i></div>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Section : Informations personnelles -->
                    <div class="section-block">
                        <div class="section-heading">
                            <div class="section-heading-icon"><i class="fas fa-user"></i></div>
                            <span class="section-heading-text">Informations personnelles</span>
                            <span class="section-heading-sub">Champs obligatoires</span>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required autocomplete="family-name">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prénom <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required autocomplete="given-name">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Adresse email <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required autocomplete="email">
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Téléphone <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required autocomplete="tel">
                                    <i class="fas fa-phone input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Numéro CIN <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" name="cin" value="<?= htmlspecialchars($user['cin']) ?>" required>
                                    <i class="fas fa-id-card input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Ville de résidence</label>
                                <div class="input-wrapper">
                                    <select name="ville">
                                        <?php
                                        $villes = ['Tunis','Sfax','Sousse','Ettadhamen','Kairouan','Gabès','Bizerte','Ariana','La Marsa','Nabeul'];
                                        foreach($villes as $v): ?>
                                        <option value="<?= $v ?>" <?= $user['ville'] == $v ? 'selected' : '' ?>><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section : Sécurité -->
                    <div class="section-block">
                        <div class="section-heading">
                            <div class="section-heading-icon"><i class="fas fa-lock"></i></div>
                            <span class="section-heading-text">Sécurité du compte</span>
                            <span class="section-heading-sub">Requis pour confirmer</span>
                        </div>
                        <div class="form-grid">
                            <div class="form-group form-group-full">
                                <label>Mot de passe actuel <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <input type="password" name="current_password" placeholder="Confirmer votre identité" required>
                                    <i class="fas fa-lock input-icon"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Nouveau mot de passe</label>
                                <div class="input-wrapper">
                                    <input type="password" name="new_password" id="new_password" placeholder="Laisser vide pour conserver">
                                    <i class="fas fa-key input-icon"></i>
                                </div>
                                <div class="password-strength"><div class="strength-bar" id="strength-bar"></div></div>
                            </div>
                            <div class="form-group">
                                <label>Confirmer le nouveau mot de passe</label>
                                <div class="input-wrapper">
                                    <input type="password" name="confirm_password" placeholder="Répéter le mot de passe">
                                    <i class="fas fa-key input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy; 2026 <span>innoGov</span> — Digitaliser aujourd'hui, servir mieux demain</p>
</footer>

<script>
    // SLIDESHOW
    const slides = document.querySelectorAll('.slide');
    let cur = 0;
    if (slides.length) {
        slides[0].classList.add('active');
        setInterval(() => {
            slides[cur].classList.remove('active');
            cur = (cur + 1) % slides.length;
            slides[cur].classList.add('active');
        }, 5500);
    }

    // NAVBAR SCROLL
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });

    // PASSWORD STRENGTH
    const pwdInput = document.getElementById('new_password');
    const bar = document.getElementById('strength-bar');
    if (pwdInput && bar) {
        pwdInput.addEventListener('input', () => {
            const v = pwdInput.value;
            let score = 0;
            if (v.length >= 6) score++;
            if (v.length >= 10) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;
            const w = ['0%','20%','40%','65%','85%','100%'];
            const c = ['#EF4444','#F97316','#EAB308','#22C55E','#10B981'];
            bar.style.width = w[score];
            bar.style.background = c[Math.max(0, score-1)] || '#E2E8F0';
        });
    }
</script>
</body>
</html>