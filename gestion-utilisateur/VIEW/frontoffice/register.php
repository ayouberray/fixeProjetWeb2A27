<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Flatpickr pour le calendrier -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
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

.register-wrapper {
  position: relative;
  z-index: 2;
  padding: 120px 1.5rem 3rem;
  max-width: 1080px;
  margin: 0 auto;
}

.register-card {
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

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.5rem;
}

.form-grid .form-group-full {
  grid-column: span 2;
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
  .form-grid {
    grid-template-columns: 1fr;
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
            <a href="login.php" style="background:#006D5B; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Connexion</a>
        </div>
    </div>
</nav>

<div class="register-wrapper">
    <div class="register-card">
        <h2>📝 Créer un compte</h2>
        
        <?php if(isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="../../CONTROLLER/AuthController.php">
            <input type="hidden" name="action" value="register">
            
            <!-- NOM et PRÉNOM -->
            <div class="form-row">
                <div class="form-group">
                    <label>Nom <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom">
                    <div class="error-message" id="nomError"></div>
                </div>
                <div class="form-group">
                    <label>Prénom <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">
                    <div class="error-message" id="prenomError"></div>
                </div>
            </div>
            
            <!-- SEXE et DATE DE NAISSANCE -->
            <div class="form-row">
                <div class="form-group">
                    <label>Sexe <span style="color:#ef4444;">*</span></label>
                    <select id="sexe" name="sexe">
                        <option value="">Sélectionnez</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                    <div class="error-message" id="sexeError"></div>
                </div>
                <div class="form-group">
                    <label>Date de naissance <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="date_naissance" name="date_naissance" placeholder="Sélectionnez une date" readonly>
                    <div class="error-message" id="dateError"></div>
                </div>
            </div>
            
            <!-- CIN et TÉLÉPHONE -->
            <div class="form-row">
                <div class="form-group">
                    <label>CIN (8 chiffres) <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="cin" name="cin" placeholder="12345678" maxlength="8">
                    <div class="error-message" id="cinError"></div>
                </div>
                <div class="form-group">
                    <label>Téléphone (8 chiffres) <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="telephone" name="telephone" placeholder="50123456" maxlength="8">
                    <div class="error-message" id="telephoneError"></div>
                </div>
            </div>
            
            <!-- PAYS et VILLE -->
            <div class="form-row">
                <div class="form-group">
                    <label>Pays <span style="color:#ef4444;">*</span></label>
                    <select id="pays" name="pays">
                        <option value="">Sélectionnez un pays</option>
                        <option value="Tunisie">🇹🇳 Tunisie</option>
                        <option value="Algérie">🇩🇿 Algérie</option>
                        <option value="Maroc">🇲🇦 Maroc</option>
                        <option value="Libye">🇱🇾 Libye</option>
                        <option value="France">🇫🇷 France</option>
                        <option value="Italie">🇮🇹 Italie</option>
                        <option value="Allemagne">🇩🇪 Allemagne</option>
                        <option value="Canada">🇨🇦 Canada</option>
                        <option value="Turquie">🇹🇷 Turquie</option>
                        <option value="Émirats Arabes Unis">🇦🇪 Émirats Arabes Unis</option>
                        <option value="Qatar">🇶🇦 Qatar</option>
                        <option value="Arabie Saoudite">🇸🇦 Arabie Saoudite</option>
                    </select>
                    <div class="error-message" id="paysError"></div>
                </div>
                <div class="form-group">
                    <label>Ville <span style="color:#ef4444;">*</span></label>
                    <select id="ville" name="ville">
                        <option value="">Sélectionnez d'abord un pays</option>
                    </select>
                    <div class="error-message" id="villeError"></div>
                </div>
            </div>
            
            <!-- TYPE DE COMPTE -->
            <div class="form-group">
                <label>Type de compte <span style="color:#ef4444;">*</span></label>
                <select id="type_compte" name="type_compte">
                    <option value="">Sélectionnez un type de compte</option>
                    <option value="citoyen">👤 Citoyen</option>
                    <option value="professionnel">🏢 Professionnel (Indépendant / Entreprise)</option>
                    <option value="agent_public">🏛️ Agent public (Institution publique)</option>
                </select>
                <div class="error-message" id="typeError"></div>
            </div>
            
            <!-- Champs conditionnels -->
            <div id="organisationField" style="display: none;">
                <div class="form-group">
                    <label id="organisationLabel">Nom de l'organisation / Institution</label>
                    <input type="text" id="nom_organisation" name="nom_organisation" placeholder="Nom de votre entreprise ou institution">
                    <div class="error-message" id="organisationError"></div>
                </div>
            </div>
            
            <div id="professionField" style="display: none;">
                <div class="form-group">
                    <label>Profession / Métier</label>
                    <input type="text" id="profession" name="profession" placeholder="Votre profession">
                    <div class="error-message" id="professionError"></div>
                </div>
            </div>
            
            <!-- EMAIL -->
            <div class="form-group">
                <label>Email <span style="color:#ef4444;">*</span></label>
                <input type="text" id="email" name="email" placeholder="exemple@email.com">
                <div class="error-message" id="emailError"></div>
            </div>
            
            <!-- MOT DE PASSE -->
            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe (min 6 caractères) <span style="color:#ef4444;">*</span></label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                    <div class="error-message" id="passwordError"></div>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe <span style="color:#ef4444;">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                    <div class="error-message" id="confirmError"></div>
                </div>
            </div>
            
            <button type="submit" id="submitBtn" class="btn-submit" disabled>S'inscrire</button>
        </form>
        <div class="login-link">Déjà inscrit ? <a href="login.php">Se connecter</a></div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div><h4>innoGov</h4><p>Digitaliser aujourd'hui, servir mieux demain</p><p>🇹🇳 Tunisie</p></div>
        <div><h4>Liens rapides</h4><p><a href="../../../index.php">Accueil</a></p><p><a href="login.php">Connexion</a></p></div>
        <div><h4>Horaires</h4><p>Lun - Ven: 8h00 - 17h00</p><p>Sam: 9h00 - 13h00</p></div>
        <div><h4>Contact</h4><p><i class="fas fa-phone"></i> +216 70 000 000</p><p><i class="fas fa-envelope"></i> contact@innogov.tn</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 innoGov - Tous droits réservés</p></div>
</footer>

<script>
    // ==================== SLIDESHOW ====================
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

    // ==================== NAVBAR SCROLL ====================
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    // ==================== CALENDRIER FLATPICKR ====================
    flatpickr("#date_naissance", {
        dateFormat: "d/m/Y",
        locale: "fr",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            validateDate();
            checkFormValidity();
        }
    });

    // ==================== VILLES PAR PAYS ====================
    const villesParPays = {
        'Tunisie': ['Tunis', 'Sfax', 'Sousse', 'Ettadhamen', 'Kairouan', 'Gabès', 'Bizerte', 'Ariana', 'La Marsa', 'Nabeul', 'Monastir', 'Ben Arous', 'Manouba', 'Zaghouan', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Kasserine', 'Gafsa', 'Tozeur', 'Kébili', 'Médenine', 'Tataouine', 'Mahdia', 'El Kef'],
        'Algérie': ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida', 'Sétif', 'Tizi Ouzou', 'Béjaïa', 'Tlemcen', 'Skikda'],
        'Maroc': ['Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 'Meknès', 'Oujda', 'Tétouan', 'Essaouira'],
        'Libye': ['Tripoli', 'Benghazi', 'Misrata', 'Bayda', 'Sebha', 'Tobrouk', 'Zliten', 'Ajdabiya'],
        'France': ['Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille'],
        'Italie': ['Rome', 'Milan', 'Naples', 'Turin', 'Palerme', 'Gênes', 'Bologne', 'Florence', 'Venise', 'Vérone'],
        'Allemagne': ['Berlin', 'Hambourg', 'Munich', 'Cologne', 'Francfort', 'Stuttgart', 'Düsseldorf', 'Leipzig', 'Dortmund', 'Essen'],
        'Canada': ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Québec', 'Winnipeg', 'Hamilton', 'Halifax'],
        'Turquie': ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Adana', 'Gaziantep', 'Konya', 'Mersin', 'Diyarbakir'],
        'Émirats Arabes Unis': ['Dubai', 'Abu Dhabi', 'Sharjah', 'Al Ain', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain'],
        'Qatar': ['Doha', 'Al Rayyan', 'Al Wakrah', 'Al Khor', 'Umm Salal', 'Lusail'],
        'Arabie Saoudite': ['Riyad', 'Jeddah', 'La Mecque', 'Médine', 'Dammam', 'Taëf', 'Khamis Mushait', 'Buraydah']
    };

    const paysSelect = document.getElementById('pays');
    const villeSelect = document.getElementById('ville');

    function updateVilles() {
        const pays = paysSelect.value;
        villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';
        
        if (pays && villesParPays[pays]) {
            villesParPays[pays].forEach(ville => {
                const option = document.createElement('option');
                option.value = ville;
                option.textContent = ville;
                villeSelect.appendChild(option);
            });
        }
    }

    paysSelect.addEventListener('change', updateVilles);
    updateVilles();

    // ==================== TYPES DE COMPTE ====================
    const typeCompteSelect = document.getElementById('type_compte');
    const organisationField = document.getElementById('organisationField');
    const professionField = document.getElementById('professionField');
    const organisationLabel = document.getElementById('organisationLabel');

    typeCompteSelect.addEventListener('change', function() {
        const type = this.value;
        
        if (type === 'professionnel') {
            organisationField.style.display = 'block';
            professionField.style.display = 'block';
            organisationLabel.textContent = 'Nom de l\'entreprise / Organisation';
        } else if (type === 'agent_public') {
            organisationField.style.display = 'block';
            professionField.style.display = 'none';
            organisationLabel.textContent = 'Nom de l\'institution publique';
        } else {
            organisationField.style.display = 'none';
            professionField.style.display = 'none';
        }
        checkFormValidity();
    });

    // ==================== VALIDATION JAVASCRIPT ====================
    const nomInput = document.getElementById('nom');
    const prenomInput = document.getElementById('prenom');
    const sexeInput = document.getElementById('sexe');
    const dateInput = document.getElementById('date_naissance');
    const cinInput = document.getElementById('cin');
    const telephoneInput = document.getElementById('telephone');
    const paysField = document.getElementById('pays');
    const villeField = document.getElementById('ville');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');

    function showError(elementId, message) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) errorDiv.innerHTML = message ? `<i class="fas fa-exclamation-circle"></i> ${message}` : '';
    }

    function updateFieldStatus(input, isValid) {
        if (isValid && input.value !== '') {
            input.classList.add('valid');
            input.classList.remove('invalid');
        } else if (!isValid && input.value !== '') {
            input.classList.add('invalid');
            input.classList.remove('valid');
        } else {
            input.classList.remove('valid', 'invalid');
        }
    }

    function validateNom() {
        const nom = nomInput.value.trim();
        const isValid = /^[a-zA-ZÀ-ÿ\s-]{3,}$/.test(nom);
        showError('nomError', isValid ? '' : 'Le nom doit contenir au moins 3 caractères (lettres uniquement)');
        updateFieldStatus(nomInput, isValid);
        return isValid;
    }

    function validatePrenom() {
        const prenom = prenomInput.value.trim();
        const isValid = /^[a-zA-ZÀ-ÿ\s-]{3,}$/.test(prenom);
        showError('prenomError', isValid ? '' : 'Le prénom doit contenir au moins 3 caractères (lettres uniquement)');
        updateFieldStatus(prenomInput, isValid);
        return isValid;
    }

    function validateSexe() {
        const sexe = sexeInput.value;
        const isValid = sexe !== '';
        showError('sexeError', isValid ? '' : 'Veuillez sélectionner votre sexe');
        return isValid;
    }

    function validateDate() {
        const dateStr = dateInput.value;
        if (!dateStr) {
            showError('dateError', 'La date de naissance est requise');
            updateFieldStatus(dateInput, false);
            return false;
        }
        
        const parts = dateStr.split('/');
        if (parts.length !== 3) {
            showError('dateError', 'Format invalide');
            updateFieldStatus(dateInput, false);
            return false;
        }
        
        const jour = parseInt(parts[0]);
        const mois = parseInt(parts[1]);
        const annee = parseInt(parts[2]);
        const birthDate = new Date(annee, mois - 1, jour);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
        
        const isValid = age >= 18;
        showError('dateError', isValid ? '' : 'Vous devez avoir au moins 18 ans');
        updateFieldStatus(dateInput, isValid);
        return isValid;
    }

    function validateCin() {
        const cin = cinInput.value.trim();
        const isValid = /^[0-9]{8}$/.test(cin);
        showError('cinError', isValid ? '' : 'Le CIN doit contenir exactement 8 chiffres');
        updateFieldStatus(cinInput, isValid);
        return isValid;
    }

    function validateTelephone() {
        const tel = telephoneInput.value.trim();
        const isValid = /^[0-9]{8}$/.test(tel);
        showError('telephoneError', isValid ? '' : 'Le téléphone doit contenir 8 chiffres');
        updateFieldStatus(telephoneInput, isValid);
        return isValid;
    }

    function validatePays() {
        const pays = paysField.value;
        const isValid = pays !== '';
        showError('paysError', isValid ? '' : 'Veuillez sélectionner votre pays');
        return isValid;
    }

    function validateVille() {
        const ville = villeField.value;
        const isValid = ville !== '';
        showError('villeError', isValid ? '' : 'Veuillez sélectionner votre ville');
        return isValid;
    }

    function validateEmail() {
        const email = emailInput.value.trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const isValid = emailRegex.test(email);
        showError('emailError', isValid ? '' : 'Veuillez entrer un email valide (exemple@domaine.com)');
        updateFieldStatus(emailInput, isValid);
        return isValid;
    }

    function validatePassword() {
        const password = passwordInput.value;
        const isValid = password.length >= 6;
        showError('passwordError', isValid ? '' : 'Le mot de passe doit contenir au moins 6 caractères');
        updateFieldStatus(passwordInput, isValid);
        return isValid;
    }

    function validateConfirm() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        const isValid = password === confirm;
        showError('confirmError', isValid ? '' : 'Les mots de passe ne correspondent pas');
        updateFieldStatus(confirmInput, isValid);
        return isValid;
    }

    function validateTypeCompte() {
        const type = typeCompteSelect.value;
        const isValid = type !== '';
        showError('typeError', isValid ? '' : 'Veuillez sélectionner un type de compte');
        return isValid;
    }

    function validateConditionalFields() {
        const type = typeCompteSelect.value;
        let isValid = true;
        
        if (type === 'professionnel') {
            const organisation = document.getElementById('nom_organisation').value.trim();
            const profession = document.getElementById('profession').value.trim();
            if (!organisation) {
                showError('organisationError', 'Le nom de l\'entreprise est requis');
                isValid = false;
            } else {
                showError('organisationError', '');
            }
            if (!profession) {
                showError('professionError', 'La profession est requise');
                isValid = false;
            } else {
                showError('professionError', '');
            }
        } else if (type === 'agent_public') {
            const organisation = document.getElementById('nom_organisation').value.trim();
            if (!organisation) {
                showError('organisationError', 'Le nom de l\'institution est requis');
                isValid = false;
            } else {
                showError('organisationError', '');
            }
        } else {
            showError('organisationError', '');
            showError('professionError', '');
        }
        return isValid;
    }

    function checkFormValidity() {
        const isValid = validateNom() && validatePrenom() && validateSexe() && validateDate() &&
                        validateCin() && validateTelephone() && validatePays() && validateVille() &&
                        validateEmail() && validatePassword() && validateConfirm() && validateTypeCompte() &&
                        validateConditionalFields();
        submitBtn.disabled = !isValid;
        return isValid;
    }

    // Écouteurs d'événements
    nomInput.addEventListener('input', () => { validateNom(); checkFormValidity(); });
    prenomInput.addEventListener('input', () => { validatePrenom(); checkFormValidity(); });
    sexeInput.addEventListener('change', () => { validateSexe(); checkFormValidity(); });
    cinInput.addEventListener('input', () => { validateCin(); checkFormValidity(); });
    telephoneInput.addEventListener('input', () => { validateTelephone(); checkFormValidity(); });
    paysField.addEventListener('change', () => { validatePays(); checkFormValidity(); });
    villeField.addEventListener('change', () => { validateVille(); checkFormValidity(); });
    emailInput.addEventListener('input', () => { validateEmail(); checkFormValidity(); });
    passwordInput.addEventListener('input', () => { validatePassword(); validateConfirm(); checkFormValidity(); });
    confirmInput.addEventListener('input', () => { validateConfirm(); checkFormValidity(); });
    typeCompteSelect.addEventListener('change', () => { validateTypeCompte(); validateConditionalFields(); checkFormValidity(); });
    
    document.getElementById('nom_organisation')?.addEventListener('input', () => { validateConditionalFields(); checkFormValidity(); });
    document.getElementById('profession')?.addEventListener('input', () => { validateConditionalFields(); checkFormValidity(); });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (!checkFormValidity()) {
            e.preventDefault();
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
