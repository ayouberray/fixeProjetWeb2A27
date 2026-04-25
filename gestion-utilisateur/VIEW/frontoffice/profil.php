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
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  padding: 1rem 2rem;
  box-shadow: 0 2px 8px rgba(0, 109, 91, 0.08);
  transition: all 0.3s ease;
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
  text-decoration: none;
  color: #006D5B;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.02);
}

.logo-icon {
  width: 42px;
  height: 42px;
  background: #006D5B;
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
}

.logo-text span {
  font-weight: 400;
  color: #2E7D32;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex-wrap: wrap;
}

.nav-links a {
  text-decoration: none;
  color: #475569;
  font-weight: 500;
  transition: color 0.3s ease;
}

.nav-links a:hover {
  color: #006D5B;
}

.lang-toggle {
  display: flex;
  gap: 0.5rem;
  background: #F1F5F9;
  padding: 0.3rem;
  border-radius: 30px;
}

.lang-btn {
  padding: 0.3rem 0.8rem;
  border: none;
  background: transparent;
  border-radius: 20px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.lang-btn.active {
  background: #006D5B;
  color: white;
}

.btn-login,
.btn-logout,
.btn-secondary,
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.8rem 1.5rem;
  border-radius: 999px;
  border: none;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.3s ease;
  color: white;
}

.btn-login,
.btn-primary {
  background: #006D5B;
}

.btn-logout,
.btn-secondary {
  background: #dc2626;
}

.btn-login:hover,
.btn-primary:hover,
.btn-logout:hover,
.btn-secondary:hover {
  transform: translateY(-2px);
}

.profile-wrapper {
  position: relative;
  z-index: 2;
  padding: 120px 1.5rem 3rem;
  max-width: 1080px;
  margin: 0 auto;
}

.profile-card {
  background: rgba(255,255,255,0.92);
  border-radius: 30px;
  padding: 2.5rem;
  box-shadow: 0 10px 30px rgba(0, 109, 91, 0.15);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.5);
}

.profile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 2rem;
}

.profile-header .avatar {
  width: 96px;
  height: 96px;
  border-radius: 30px;
  display: grid;
  place-items: center;
  color: white;
  background: linear-gradient(135deg, #006D5B, #2E7D32);
  font-size: 2.5rem;
}

.profile-body {
  display: grid;
  gap: 2rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.5rem;
}

.form-grid .form-group-full {
  grid-column: span 2;
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

.btn-save {
  background: #2E7D32;
  color: white;
  border-radius: 999px;
  padding: 0.95rem 1.8rem;
  border: none;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-save:hover {
  background: #006D5B;
  transform: translateY(-1px);
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
  .form-grid {
    grid-template-columns: 1fr;
  }
  .profile-header {
    flex-direction: column;
    align-items: stretch;
  }
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
