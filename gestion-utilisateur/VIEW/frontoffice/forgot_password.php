<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Mot de passe oublié</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --gray-600: #a0a0a0;
            --gray-800: #eeeeee;
            --bg-body: #0f0f1a;
            --card-bg: #16213e;
            --border-color: #2c3e50;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        [data-theme="dark"] body {
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
        }

        /* Switch mode */
        .theme-switch-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(0,0,0,0.6);
            padding: 8px 15px;
            border-radius: 40px;
            backdrop-filter: blur(10px);
            z-index: 1000;
        }
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.2s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.2s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(24px); }
        .theme-icon { font-size: 14px; }
        .light-icon, .dark-icon { display: none; }
        [data-theme="light"] .light-icon { display: inline; }
        [data-theme="light"] .dark-icon { display: none; }
        [data-theme="dark"] .light-icon { display: none; }
        [data-theme="dark"] .dark-icon { display: inline; }

        .container {
            max-width: 450px;
            width: 90%;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        [data-theme="dark"] .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .logo {
            margin-bottom: 1.5rem;
        }

        .logo img {
            height: 50px;
        }

        h1 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        [data-theme="dark"] .form-group input {
            background: var(--card-bg);
            border-color: var(--border-color);
            color: var(--gray-800);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,109,91,0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .back-link {
            margin-top: 1.5rem;
            text-align: center;
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #D1FAE5;
            color: #059669;
            border-left: 4px solid #059669;
        }

        .alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border-left: 4px solid #DC2626;
        }

        .alert-info {
            background: #DBEAFE;
            color: #2563EB;
            border-left: 4px solid #2563EB;
        }

        [data-theme="dark"] .alert-success {
            background: #1a4a2a;
            color: #a3e4b7;
        }

        [data-theme="dark"] .alert-error {
            background: #4a1a1a;
            color: #f5a5a5;
        }

        [data-theme="dark"] .alert-info {
            background: #1a3a5a;
            color: #87cefa;
        }
    </style>
</head>
<body>

<div class="theme-switch-wrapper">
    <span class="theme-icon light-icon">☀️</span>
    <label class="theme-switch">
        <input type="checkbox" id="theme-toggle">
        <span class="slider"></span>
    </label>
    <span class="theme-icon dark-icon">🌙</span>
</div>

<div class="container">
    <div class="card">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="Logo" style="height: 60px;">
        </div>
        <h1>Mot de passe oublié ?</h1>
        <p class="subtitle">Entrez votre email pour recevoir un lien de réinitialisation</p>

        <?php if(isset($_SESSION['reset_error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['reset_success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?>
            </div>
        <?php endif; ?>

        <form action="../../CONTROLLER/AuthController.php?action=forgot" method="POST">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Adresse email</label>
                <input type="email" name="email" placeholder="exemple@email.com" required>
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Envoyer le lien
            </button>
        </form>

        <div class="back-link">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>
</div>

<script>
    // Mode sombre/clair
    (function() {
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        const THEME_KEY = 'innogov_theme';
        
        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem(THEME_KEY, theme);
            if (themeToggle) themeToggle.checked = (theme === 'dark');
        }
        
        function initTheme() {
            const savedTheme = localStorage.getItem(THEME_KEY);
            if (savedTheme) {
                setTheme(savedTheme);
            } else {
                setTheme(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            }
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                setTheme(this.checked ? 'dark' : 'light');
            });
        }
        
        initTheme();
    })();
</script>

</body>
</html>