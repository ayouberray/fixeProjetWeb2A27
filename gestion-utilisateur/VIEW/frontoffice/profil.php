<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$user = $utilisateur->getById($_SESSION['user_id']);

$success = '';
$error = '';

// Traitement de la mise à jour
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
    
    // Vérifier le mot de passe actuel
    if (!password_verify($current_password, $user['password'])) {
        $error = "❌ Mot de passe actuel incorrect";
    } else {
        // Préparer les données à mettre à jour
        $updateData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'cin' => $cin,
            'ville' => $ville,
            'role' => $user['role']
        ];
        
        // Si nouveau mot de passe, le hasher
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $error = "❌ Le nouveau mot de passe doit contenir au moins 6 caractères";
            } elseif ($new_password !== $confirm_password) {
                $error = "❌ Les nouveaux mots de passe ne correspondent pas";
            } else {
                $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }
        
        if (empty($error)) {
            if ($utilisateur->update($user['id'], $updateData)) {
                $success = "✅ Vos informations ont été mises à jour avec succès !";
                // Recharger les données
                $user = $utilisateur->getById($_SESSION['user_id']);
                $_SESSION['user_nom'] = $user['nom'] . ' ' . $user['prenom'];
            } else {
                $error = "❌ Erreur lors de la mise à jour";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Mon profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ============================================
           INNOGOV - DESIGN SYSTEM (MÊME QUE INDEX.PHP)
        ============================================ */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-sm: 0 2px 8px rgba(0, 109, 91, 0.08);
            --shadow-md: 0 4px 15px rgba(0, 109, 91, 0.12);
            --shadow-lg: 0 10px 30px rgba(0, 109, 91, 0.15);
            --shadow-hover: 0 20px 35px rgba(0, 109, 91, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--gray-800);
            min-height: 100vh;
            position: relative;
        }

        /* SLIDESHOW BACKGROUND */
        .slideshow-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .slide.active { opacity: 1; }
        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        /* ===== LOADER ===== */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        .loader.hide { opacity: 0; pointer-events: none; }
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--primary-light);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: white;
            box-shadow: var(--shadow-md);
        }
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
            gap: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .logo:hover { transform: scale(1.02); }
        .logo-icon {
            width: 42px;
            height: 42px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        .logo-text span { font-weight: 400; color: var(--secondary); }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: var(--primary); }
        .lang-toggle {
            display: flex;
            gap: 0.5rem;
            background: var(--gray-100);
            padding: 0.3rem;
            border-radius: 30px;
        }
        .lang-btn {
            padding: 0.3rem 0.8rem;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .lang-btn.active { background: var(--primary); color: white; }
        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        /* ===== PROFIL ===== */
        .profile-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 2rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 0.6s ease;
        }
        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2rem;
            color: white;
            text-align: center;
        }
        .avatar {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
        }
        .profile-body { padding: 2rem; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group-full { grid-column: span 2; }
        label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        input, select {
            padding: 0.85rem 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: #f8fafc;
        }
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.1);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--gray-200);
        }
        .btn-save {
            background: var(--primary);
            color: white;
            padding: 0.85rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-save:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--gray-800);
            color: #94a3b8;
            padding: 2rem 2rem 1.5rem;
            position: relative;
            z-index: 10;
        }
        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }
        .footer h4 { color: white; margin-bottom: 1rem; }
        .footer p { margin-bottom: 0.5rem; font-size: 0.9rem; }
        .footer a { color: #94a3b8; text-decoration: none; }
        .footer a:hover { color: var(--primary); }
        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #334155;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group-full { grid-column: span 1; }
            .profile-card { margin: 1rem; }
            .navbar { padding: 1rem; }
            .nav-links { justify-content: center; }
            .footer-container { grid-template-columns: 1fr; gap: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- SLIDESHOW BACKGROUND -->
<div class="slideshow-bg">
    <div class="slide" style="background-image: url('../../assets/images/tunisia1.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia2.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia3.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia4.jpg');"></div>
    <div class="slide-overlay"></div>
</div>

<!-- LOADER -->
<div id="loader" class="loader">
    <div class="spinner"></div>
</div>

<!-- NAVBAR -->
<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <div class="logo-text">inno<span>Gov</span></div>
        </a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="#services">Services</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <span style="color: var(--primary);">👋 <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></h2>
            <p><?= $user['role'] === 'admin' ? 'Administrateur' : ($user['role'] === 'agent' ? 'Agent public' : 'Citoyen') ?></p>
        </div>

        <div class="profile-body">
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CIN</label>
                        <input type="text" name="cin" value="<?= htmlspecialchars($user['cin']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Ville</label>
                        <select name="ville">
                            <option value="Tunis" <?= $user['ville'] == 'Tunis' ? 'selected' : '' ?>>Tunis</option>
                            <option value="Sfax" <?= $user['ville'] == 'Sfax' ? 'selected' : '' ?>>Sfax</option>
                            <option value="Sousse" <?= $user['ville'] == 'Sousse' ? 'selected' : '' ?>>Sousse</option>
                            <option value="Ettadhamen" <?= $user['ville'] == 'Ettadhamen' ? 'selected' : '' ?>>Ettadhamen</option>
                            <option value="Kairouan" <?= $user['ville'] == 'Kairouan' ? 'selected' : '' ?>>Kairouan</option>
                            <option value="Gabès" <?= $user['ville'] == 'Gabès' ? 'selected' : '' ?>>Gabès</option>
                            <option value="Bizerte" <?= $user['ville'] == 'Bizerte' ? 'selected' : '' ?>>Bizerte</option>
                            <option value="Ariana" <?= $user['ville'] == 'Ariana' ? 'selected' : '' ?>>Ariana</option>
                            <option value="La Marsa" <?= $user['ville'] == 'La Marsa' ? 'selected' : '' ?>>La Marsa</option>
                            <option value="Nabeul" <?= $user['ville'] == 'Nabeul' ? 'selected' : '' ?>>Nabeul</option>
                        </select>
                    </div>
                </div>

                <h3 class="section-title"><i class="fas fa-lock"></i> Sécurité</h3>
                <div class="form-grid">
                    <div class="form-group-full">
                        <label>Mot de passe actuel <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="current_password" placeholder="Entrez votre mot de passe actuel" required>
                    </div>
                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" placeholder="Laissez vide pour ne pas changer">
                    </div>
                    <div class="form-group">
                        <label>Confirmer le nouveau mot de passe</label>
                        <input type="password" name="confirm_password" placeholder="Confirmez le nouveau mot de passe">
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">
        <div><h4>innoGov</h4><p>Digitaliser aujourd'hui, servir mieux demain</p><p>🇹🇳 Tunisie</p></div>
        <div><h4>Liens rapides</h4><p><a href="index.php">Accueil</a></p><p><a href="profil.php">Mon profil</a></p></div>
        <div><h4>Horaires</h4><p>Lun - Ven: 8h00 - 17h00</p><p>Sam: 9h00 - 13h00</p></div>
        <div><h4>Contact</h4><p><i class="fas fa-phone"></i> +216 70 000 000</p><p><i class="fas fa-envelope"></i> contact@innogov.tn</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 innoGov - Tous droits réservés</p></div>
</footer>

<script>
    // SLIDESHOW
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    if (slides.length > 0) {
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) slide.classList.add('active');
            });
        }
        showSlide(0);
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);
    }

    // LOADER
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            setTimeout(() => loader.classList.add('hide'), 500);
        }
    });

    // NAVBAR SCROLL
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            if (window.scrollY > 50) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        }
    });

    // TOGGLE LANGUE
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
            langBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (lang === 'ar') {
                document.body.style.direction = 'rtl';
                document.body.style.textAlign = 'right';
            } else {
                document.body.style.direction = 'ltr';
                document.body.style.textAlign = 'left';
            }
        });
    });
</script>
</body>
</html>