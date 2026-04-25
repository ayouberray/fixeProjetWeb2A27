<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-sm: 0 2px 8px rgba(0, 109, 91, 0.08);
            --shadow-md: 0 4px 15px rgba(0, 109, 91, 0.12);
            --shadow-lg: 0 10px 30px rgba(0, 109, 91, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
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

        /* NAVBAR */
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
        .navbar.scrolled { background: white; box-shadow: var(--shadow-md); }
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

        /* FORMULAIRE */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 2rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 0.6s ease;
        }
        .login-card h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        .form-group input {
            width: 100%;
            padding: 0.85rem;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.1);
        }
        .form-group input.valid {
            border-color: #22c55e;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2322c55e"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
        }
        .form-group input.invalid {
            border-color: #ef4444;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ef4444"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
        }
        .error-message {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .register-link a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }
        .alert-success {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #16A34A;
        }

        /* FOOTER */
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
        .footer-bottom { text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #334155; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .login-card { padding: 1.5rem; margin: 1rem; }
            .footer-container { grid-template-columns: 1fr; gap: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="slideshow-bg">
    <div class="slide" style="background-image: url('../../assets/images/tunisia1.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia2.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia3.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia4.jpg');"></div>
    <div class="slide-overlay"></div>
</div>

<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="../../../index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <div class="logo-text">inno<span>Gov</span></div>
        </a>
        <div class="nav-links">
            <a href="../../../index.php">Accueil</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="register.php" style="background:#2E7D32; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Inscription</a>
        </div>
    </div>
</nav>

<div class="login-wrapper">
    <div class="login-card">
        <h2>🔐 Connexion</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="../../CONTROLLER/AuthController.php">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Email</label>
                <input type="text" id="email" name="email" placeholder="exemple@email.com">
                <div class="error-message" id="emailError"></div>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="text" id="password" name="password" placeholder="••••••••">
                <div class="error-message" id="passwordError"></div>
            </div>
            <button type="submit" id="submitBtn" class="btn-submit" disabled>Se connecter</button>
        </form>
        <div class="register-link">
            Pas encore de compte ? <a href="register.php">Créer un compte</a>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div><h4>innoGov</h4><p>Digitaliser aujourd'hui, servir mieux demain</p><p>🇹🇳 Tunisie</p></div>
        <div><h4>Liens rapides</h4><p><a href="../../../index.php">Accueil</a></p><p><a href="register.php">Inscription</a></p></div>
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

    // NAVBAR SCROLL
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    // ==================== VALIDATION JS PROFESSIONNELLE ====================
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    // Fonctions de validation
    function validateEmail(email) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!email) return 'L\'email est requis';
        if (!emailRegex.test(email)) return 'Veuillez entrer un email valide (exemple@domaine.com)';
        return null;
    }

    function validatePassword(password) {
        if (!password) return 'Le mot de passe est requis';
        if (password.length < 6) return 'Le mot de passe doit contenir au moins 6 caractères';
        return null;
    }

    // Mettre à jour l'UI du champ
    function updateFieldStatus(input, errorElement, isValid, errorMessage) {
        if (isValid) {
            input.classList.add('valid');
            input.classList.remove('invalid');
            errorElement.textContent = '';
        } else if (errorMessage) {
            input.classList.add('invalid');
            input.classList.remove('valid');
            errorElement.textContent = errorMessage;
        } else {
            input.classList.remove('valid', 'invalid');
            errorElement.textContent = '';
        }
    }

    // Valider l'email
    function checkEmail() {
        const email = emailInput.value.trim();
        const error = validateEmail(email);
        const isValid = !error;
        updateFieldStatus(emailInput, emailError, isValid, error);
        return isValid;
    }

    // Valider le mot de passe
    function checkPassword() {
        const password = passwordInput.value;
        const error = validatePassword(password);
        const isValid = !error;
        updateFieldStatus(passwordInput, passwordError, isValid, error);
        return isValid;
    }

    // Vérifier si le formulaire est valide
    function checkFormValidity() {
        const isEmailValid = checkEmail();
        const isPasswordValid = checkPassword();
        submitBtn.disabled = !(isEmailValid && isPasswordValid);
    }

    // Écouteurs d'événements
    emailInput.addEventListener('input', () => {
        checkEmail();
        checkFormValidity();
    });

    passwordInput.addEventListener('input', () => {
        checkPassword();
        checkFormValidity();
    });

    // Soumission du formulaire
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const isEmailValid = checkEmail();
        const isPasswordValid = checkPassword();
        
        if (!isEmailValid || !isPasswordValid) {
            e.preventDefault();
            // Animation de shake sur le bouton
            submitBtn.style.transform = 'translateX(5px)';
            setTimeout(() => { submitBtn.style.transform = ''; }, 200);
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