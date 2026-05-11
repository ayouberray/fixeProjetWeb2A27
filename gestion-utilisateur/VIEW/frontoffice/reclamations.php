<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$user_nom = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : 'Visiteur';
$user_prenom = $isLoggedIn ? ($_SESSION['user_prenom'] ?? '') : '';
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>innoGov | Mes réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --bg-gradient-start: #f0fdf4;
            --bg-gradient-end: #dcfce7;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #475569;
            --modal-bg: #ffffff;
            --toast-bg: #1e293b;
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --white: #1a1a2e;
            --gray-100: #1a1a2e;
            --gray-200: #16213e;
            --gray-600: #a0a0a0;
            --gray-700: #cbd5e1;
            --gray-800: #eeeeee;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --bg-gradient-start: #0f0f1a;
            --bg-gradient-end: #1a1a2e;
            --card-bg: #16213e;
            --border-color: #2c3e50;
            --text-muted: #a0a0a0;
            --modal-bg: #16213e;
            --toast-bg: #2c3e50;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            transition: background 0.2s ease;
        }

        /* ===== SWITCH MODE ===== */
        .theme-switch-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 0.5rem;
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

        /* VIDEO BACKGROUND */
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            object-fit: cover;
        }
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            z-index: -1;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        [data-theme="dark"] .navbar {
            background: rgba(26, 26, 46, 0.98);
        }
        .navbar.scrolled { background: var(--card-bg); box-shadow: var(--shadow-sm); }
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

        /* PAGE HEADER */
        .page-header {
            text-align: center;
            padding: 7rem 2rem 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .page-header p {
            color: var(--gray-600);
        }

        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: 1rem auto 2rem;
            padding: 0 2rem;
        }

        /* BANDEAU INFO CONNEXION */
        .info-banner {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid rgba(0, 109, 91, 0.2);
        }
        [data-theme="dark"] .info-banner {
            background: rgba(26, 26, 46, 0.95);
        }
        .info-banner i { color: var(--primary); margin-right: 0.5rem; font-size: 1.2rem; }
        .info-banner a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .info-banner a:hover { text-decoration: underline; }

        /* TABLEAU MODERNE */
        .reclamations-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            overflow-x: auto;
            margin-bottom: 2rem;
        }
        .reclamations-card h2 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .reclamations-card h2 i {
            color: var(--primary);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background: var(--gray-100);
            font-weight: 600;
            color: var(--gray-800);
        }
        td {
            color: var(--gray-600);
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-en-cours {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-soumise {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-traitee {
            background: #dcfce7;
            color: #166534;
        }
        [data-theme="dark"] .badge-en-cours {
            background: #4a3a1a;
            color: #f5d742;
        }
        [data-theme="dark"] .badge-soumise {
            background: #1a3a5a;
            color: #87cefa;
        }
        [data-theme="dark"] .badge-traitee {
            background: #1a4a2a;
            color: #a3e4b7;
        }
        .btn-link {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            transition: all 0.2s;
        }
        .btn-link:hover {
            background: var(--primary-light);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-600);
        }

        /* NOUVELLE RÉCLAMATION */
        .new-reclamation-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: var(--shadow-md);
        }
        .new-reclamation-card h3 {
            margin-bottom: 1rem;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group-full {
            grid-column: span 2;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: var(--gray-700);
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
            background: var(--card-bg);
            color: var(--gray-800);
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.1);
        }
        .btn-submit {
            background: var(--primary);
            color: white;
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* MODAL */
        #responseModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--modal-bg);
            border-radius: 24px;
            max-width: 500px;
            width: 90%;
            padding: 2rem;
            border: 1px solid var(--border-color);
        }
        .modal-content h3 {
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .modal-content p {
            margin-bottom: 1.5rem;
            color: var(--gray-600);
        }
        .modal-content button {
            background: var(--gray-600);
        }

        /* FOOTER */
        .footer {
            background: var(--card-bg);
            color: var(--gray-500);
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        /* TOAST */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--toast-bg);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 40px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            box-shadow: var(--shadow-md);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.4rem 1rem; }
            .logo-img { height: 32px; }
            .nav-links { gap: 0.8rem; }
            .page-header { padding: 6rem 1rem 1rem; }
            .page-header h1 { font-size: 1.5rem; }
            .container { padding: 0 1rem; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group-full { grid-column: span 1; }
            th, td { padding: 0.6rem; font-size: 0.85rem; }
        }
    </style>
</head>
<body>

<!-- VIDEO BACKGROUND -->
<video class="video-bg" autoplay muted loop playsinline>
    <source src="../../assets/video/background.mp4" type="video/mp4">
</video>
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
            
            <!-- SWITCH MODE SOMBRE/CLAIR -->
            <div class="theme-switch-wrapper">
                <span class="theme-icon light-icon">☀️</span>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider"></span>
                </label>
                <span class="theme-icon dark-icon">🌙</span>
            </div>
            
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

<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Mes réclamations</h1>
    <p>Suivez l'état de vos demandes en temps réel</p>
</div>

<!-- CONTENU PRINCIPAL -->
<div class="container">
    
    <?php if(!$isLoggedIn): ?>
        <!-- BANDEAU POUR UTILISATEUR NON CONNECTÉ -->
        <div class="info-banner">
            <i class="fas fa-info-circle"></i> Vous n'êtes pas connecté. 
            <a href="login.php">Connectez-vous</a> pour voir l'historique de vos réclamations.
        </div>
    <?php endif; ?>

    <?php if($isLoggedIn): ?>
        <!-- TABLEAU DES RÉCLAMATIONS (UNIQUEMENT POUR CONNECTÉ) -->
        <div class="reclamations-card">
            <h2><i class="fas fa-list"></i> Liste de mes réclamations</h2>
            <div style="overflow-x: auto;">
                <table id="reclamationsTable">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Objet</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reclamationsBody">
                        <!-- Les réclamations seront chargées ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- FORMULAIRE DE DÉPÔT DE RÉCLAMATION (TOUT LE MONDE PEUT VOIR) -->
    <div class="new-reclamation-card">
        <h3><i class="fas fa-plus-circle"></i> Déposer une nouvelle réclamation</h3>
        <form id="newReclamationForm">
            <div class="form-grid">
                <div class="form-group form-group-full">
                    <label>Objet</label>
                    <input type="text" id="reclamationObjet" placeholder="Ex: Lampadaire cassé" required>
                </div>
                <div class="form-group form-group-full">
                    <label>Description</label>
                    <textarea id="reclamationDescription" rows="3" placeholder="Décrivez votre problème en détail..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select id="reclamationCategorie">
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Voirie">Voirie</option>
                        <option value="Hygiène">Hygiène</option>
                        <option value="Administratif">Administratif</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lieu (optionnel)</label>
                    <input type="text" id="reclamationLieu" placeholder="Adresse ou lieu">
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Déposer la réclamation</button>
        </form>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

<!-- MODAL POUR VOIR LA RÉPONSE -->
<div id="responseModal">
    <div class="modal-content">
        <h3><i class="fas fa-reply-all"></i> Réponse à la réclamation</h3>
        <p id="modalResponseText"></p>
        <button onclick="closeModal()" class="btn-submit" style="background: var(--gray-600);">Fermer</button>
    </div>
</div>

<script>
    // ===== MODE SOMBRE/CLAIR =====
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

    // Données statiques des réclamations (simulation) - UNIQUEMENT POUR CONNECTÉ
    let reclamations = [
        { reference: "REC-20260413-001", objet: "Lampadaire cassé rue Habib Bourguiba", statut: "en_cours", date: "13/04/2026", reponse: "Votre réclamation a été prise en charge. Un technicien interviendra sous 48h." },
        { reference: "REC-20260413-002", objet: "Poubelles non ramassées depuis 5 jours", statut: "soumise", date: "13/04/2026", reponse: "Votre réclamation a bien été enregistrée et sera traitée prochainement." },
        { reference: "REC-20260413-003", objet: "Demande d'extrait d'acte de naissance", statut: "traitee", date: "13/04/2026", reponse: "Votre document est disponible. Veuillez vous rendre au guichet pour le récupérer." }
    ];

    const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
    
    // Mapping des statuts pour l'affichage
    const statutLabels = {
        'en_cours': { class: 'badge-en-cours', text: 'En cours' },
        'soumise': { class: 'badge-soumise', text: 'Soumise' },
        'traitee': { class: 'badge-traitee', text: 'Traitée' }
    };

    // Fonction pour afficher le tableau (uniquement si connecté)
    function renderTable() {
        if (!isLoggedIn) return;
        
        const tbody = document.getElementById('reclamationsBody');
        if (!tbody) return;
        
        if (reclamations.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Aucune réclamation trouvée</td></tr>';
            return;
        }
        
        tbody.innerHTML = reclamations.map(rec => {
            const statutInfo = statutLabels[rec.statut] || { class: 'badge-soumise', text: rec.statut };
            return `
                <tr>
                    <td><strong>${rec.reference}</strong></td>
                    <td>${rec.objet}</td>
                    <td><span class="badge ${statutInfo.class}">${statutInfo.text}</span></td>
                    <td>${rec.date}</td>
                    <td><button class="btn-link" onclick="showReponse('${rec.reference.replace(/'/g, "\\'")}', '${rec.reponse.replace(/'/g, "\\'")}')"><i class="fas fa-eye"></i> Voir réponse</button></td>
                </tr>
            `;
        }).join('');
    }

    // Fonction pour afficher la réponse dans le modal
    function showReponse(reference, reponse) {
        document.getElementById('modalResponseText').innerHTML = `<strong>Réclamation ${reference}</strong><br><br>${reponse}`;
        document.getElementById('responseModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('responseModal').style.display = 'none';
    }

    // Ajouter une nouvelle réclamation
    document.getElementById('newReclamationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const objet = document.getElementById('reclamationObjet').value;
        const description = document.getElementById('reclamationDescription').value;
        const categorie = document.getElementById('reclamationCategorie').value;
        const lieu = document.getElementById('reclamationLieu').value;
        
        if (!objet || !description) {
            showToast('Veuillez remplir l\'objet et la description');
            return;
        }
        
        // Générer une nouvelle référence
        const now = new Date();
        const dateStr = now.toISOString().slice(0,10).replace(/-/g, '');
        const newRef = `REC-${dateStr}-${String(reclamations.length + 1).padStart(3, '0')}`;
        const dateFr = now.toLocaleDateString('fr-FR');
        
        const nouvelleReclamation = {
            reference: newRef,
            objet: objet,
            statut: 'soumise',
            date: dateFr,
            reponse: `Votre réclamation "${objet}" a bien été enregistrée. Nous vous répondrons dans les plus brefs délais.`
        };
        
        // Si l'utilisateur est connecté, on ajoute au tableau
        if (isLoggedIn) {
            reclamations.unshift(nouvelleReclamation);
            renderTable();
        }
        
        // Reset form
        document.getElementById('newReclamationForm').reset();
        
        // Afficher un toast de confirmation
        showToast("Votre réclamation a été déposée avec succès !");
        
        // Message supplémentaire pour non connecté
        if (!isLoggedIn) {
            showToast("Pour suivre votre réclamation, veuillez vous connecter.");
        }
    });
    
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    // Fermer le modal en cliquant en dehors
    document.getElementById('responseModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    
    // Initialisation du tableau (uniquement si connecté)
    if (isLoggedIn) {
        renderTable();
    }
    
    // NAVBAR SCROLL
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });
</script>

</body>
</html>