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

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem 2rem;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 700px;
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 0.6s ease;
        }
        .register-card h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.85rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
            background: white;
        }
        .form-group input:focus, .form-group select:focus {
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
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
            margin-top: 0.5rem;
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
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .login-link a { color: var(--primary); text-decoration: none; font-weight: 600; }
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
            .form-row { grid-template-columns: 1fr; }
            .register-card { padding: 1.5rem; margin: 1rem; }
            .footer-container { grid-template-columns: 1fr; gap: 1.5rem; }
        }

        /* Style du calendrier Flatpickr */
        .flatpickr-calendar {
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
        }
        .flatpickr-day.selected {
            background: var(--primary);
            border-color: var(--primary);
        }
        .flatpickr-day.today {
            border-color: var(--secondary);
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