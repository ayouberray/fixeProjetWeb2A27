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
.slideshow-bg {
  position: fixed;
  inset: 0;
  z-index: -1;
  overflow: hidden;
}

.slide {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  opacity: 0;
  animation: slideFade 20s infinite;
}

.slide:nth-child(1) {
  animation-delay: 0s;
}

.slide:nth-child(2) {
  animation-delay: 5s;
}

.slide:nth-child(3) {
  animation-delay: 10s;
}

.slide:nth-child(4) {
  animation-delay: 15s;
}

@keyframes slideFade {
  0%, 20% { opacity: 1; }
  25%, 100% { opacity: 0; }
}

.slide-overlay {
  position: fixed;
  inset: 0;
  background: linear-gradient(180deg, rgba(13, 46, 33, 0.6), rgba(13, 46, 33, 0.82));
  z-index: -1;
}

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

.login-wrapper {
  position: relative;
  z-index: 2;
  padding: 120px 1.5rem 3rem;
  max-width: 1080px;
  margin: 0 auto;
}

.login-card {
  background: rgba(255,255,255,0.92);
  border-radius: 30px;
  padding: 2.5rem;
  box-shadow: 0 10px 30px rgba(0, 109, 91, 0.15);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.5);
}

.form-group {
  margin-bottom: 1.25rem;
}

label {
  display: block;
  margin-bottom: 0.55rem;
  font-weight: 600;
  color: #1E293B;
  font-size: 0.95rem;
}

input,
select,
textarea {
  width: 100%;
  padding: 1rem 1rem;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  font-size: 0.95rem;
  color: #1E293B;
  background: white;
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #006D5B;
  box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.12);
}

.btn-submit {
  background: #006D5B;
  color: white;
  border-radius: 999px;
  padding: 0.85rem 1.8rem;
  border: none;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
  width: 100%;
}

.btn-submit:hover {
  background: #004D3D;
  transform: translateY(-2px);
}

.register-link {
  margin-top: 1rem;
  text-align: center;
  color: #475569;
}

.register-link a {
  color: #006D5B;
  font-weight: 700;
}

.footer {
  background: #1a1a1a;
  color: #94a3b8;
  text-align: center;
  padding: 2rem 1.5rem;
  margin-top: 3rem;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 2rem;
  align-items: start;
  max-width: 1200px;
  margin: 0 auto;
}

.footer a {
  color: #94a3b8;
  text-decoration: none;
}

.footer a:hover {
  color: white;
}

.footer-bottom {
  margin-top: 2rem;
  border-top: 1px solid rgba(255,255,255,0.12);
  padding-top: 1.5rem;
}

.alert {
  padding: 1rem 1.25rem;
  border-radius: 16px;
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

@media (max-width: 960px) {
  .navbar {
    padding: 1.5rem;
  }
  .nav-links {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .nav-links {
    flex-direction: column;
    align-items: stretch;
  }
  .btn-login,
  .btn-logout,
  .btn-primary,
  .btn-secondary {
    width: 100%;
  }
}

@media (max-width: 600px) {
  .logo-text {
    font-size: 1.25rem;
  }
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
</script>
</body>
</html>
