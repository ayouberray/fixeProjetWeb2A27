<?php
session_start();

$token = $_GET['token'] ?? '';
if (empty($token)) {
    header('Location: forgot_password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Réinitialisation du mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --gray-600: #475569;
            --gray-800: #1E293B;
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

        .logo img {
            height: 50px;
            margin-bottom: 1.5rem;
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

        .password-strength {
            margin-top: 8px;
            font-size: 0.75rem;
        }

        .strength-weak { color: #dc2626; }
        .strength-medium { color: #f59e0b; }
        .strength-strong { color: #10b981; }

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

        .alert {
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border-left: 4px solid #DC2626;
        }

        [data-theme="dark"] .alert-error {
            background: #4a1a1a;
            color: #f5a5a5;
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
        <h1>Nouveau mot de passe</h1>
        <p class="subtitle">Créez un nouveau mot de passe sécurisé</p>

        <?php if(isset($_SESSION['reset_error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?>
            </div>
        <?php endif; ?>

        <form action="../../CONTROLLER/AuthController.php?action=reset" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required>
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                <input type="password" name="password_confirm" required>
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Réinitialiser le mot de passe
            </button>
        </form>
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

    // Force du mot de passe
    const passwordInput = document.getElementById('password');
    const strengthDiv = document.getElementById('passwordStrength');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        if (password.length === 0) {
            strengthDiv.textContent = '';
            strengthDiv.className = 'password-strength';
        } else if (strength <= 2) {
            strengthDiv.textContent = '🔴 Mot de passe faible';
            strengthDiv.className = 'password-strength strength-weak';
        } else if (strength <= 4) {
            strengthDiv.textContent = '🟠 Mot de passe moyen';
            strengthDiv.className = 'password-strength strength-medium';
        } else {
            strengthDiv.textContent = '🟢 Mot de passe fort';
            strengthDiv.className = 'password-strength strength-strong';
        }
    });
</script>

</body>
</html>