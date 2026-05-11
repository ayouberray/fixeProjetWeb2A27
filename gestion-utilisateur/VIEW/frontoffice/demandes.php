<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

// Simulation de données (à remplacer par vos vraies données BDD)
$demandes = [
    ['id_demande' => 1001, 'titre' => 'Demande de permis de construire', 'description' => 'Construction d\'une villa R+1 au lotissement El Amir', 'type_demande' => 'urbanisme', 'statut' => 'traite', 'date_formatee' => '15/03/2026', 'nom_service' => 'Direction d\'Urbanisme'],
    ['id_demande' => 1002, 'titre' => 'Demande d\'acte de naissance', 'description' => 'Acte de naissance pour mon fils Amine Ben Ali', 'type_demande' => 'etat_civil', 'statut' => 'en_cours', 'date_formatee' => '20/03/2026', 'nom_service' => 'Service État Civil'],
    ['id_demande' => 1003, 'titre' => 'Signalement nid-de-poule', 'description' => 'Trou dangereux sur l\'avenue du 1er Novembre', 'type_demande' => 'voirie', 'statut' => 'traite', 'date_formatee' => '10/03/2026', 'nom_service' => 'Service Voirie'],
    ['id_demande' => 1004, 'titre' => 'Demande de certificat de résidence', 'description' => 'Certificat pour inscription scolaire', 'type_demande' => 'autre', 'statut' => 'en_attente', 'date_formatee' => '25/04/2026', 'nom_service' => 'Service Administratif']
];

$reponses = [
    ['id_reponse' => 1, 'id_demande' => 1001, 'id_parent' => null, 'expediteur' => 'admin', 'contenu' => 'Bonjour Monsieur, votre dossier a été examiné. Le permis est approuvé. Vous pouvez télécharger le document ci-dessous.', 'date_creation' => '2026-03-16 10:30:00'],
    ['id_reponse' => 2, 'id_demande' => 1001, 'id_parent' => 1, 'expediteur' => 'citoyen', 'contenu' => 'Merci beaucoup pour votre retour rapide !', 'date_creation' => '2026-03-16 14:20:00'],
    ['id_reponse' => 3, 'id_demande' => 1002, 'id_parent' => null, 'expediteur' => 'admin', 'contenu' => 'Votre demande est en cours de traitement. Nous reviendrons vers vous sous 48h.', 'date_creation' => '2026-03-21 09:15:00'],
    ['id_reponse' => 4, 'id_demande' => 1003, 'id_parent' => null, 'expediteur' => 'admin', 'contenu' => 'Signalement pris en compte. Une équipe interviendra sous 3 jours.', 'date_creation' => '2026-03-11 08:45:00']
];

$user_nom = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : 'Visiteur';
$user_prenom = $isLoggedIn ? ($_SESSION['user_prenom'] ?? '') : '';
$user_initials = $isLoggedIn ? strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) : 'MB';

$types_labels = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];

$statuts_labels = [
    'en_attente' => ['class' => 'badge-en_attente', 'text' => '⏳ En attente'],
    'en_cours' => ['class' => 'badge-en_cours', 'text' => '🔄 En cours'],
    'traite' => ['class' => 'badge-traite', 'text' => '✅ Traité'],
    'refuse' => ['class' => 'badge-refuse', 'text' => '❌ Refusé']
];
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>InnoGov | Mes demandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --secondary-dark: #1B5E20;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
            --dark: #1A2C3E;
            --gray-900: #2D3A4B;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px -2px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-xl: 0 20px 40px -12px rgba(0,0,0,0.2);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --bg-gradient-start: #f0fdf4;
            --bg-gradient-end: #dcfce7;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --modal-bg: #ffffff;
            --toast-bg: #1e293b;
            --footer-bg: linear-gradient(135deg, #1A2C3E 0%, #2D3A4B 100%);
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --secondary-dark: #1B5E20;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
            --dark: #eeeeee;
            --gray-900: #e2e8f0;
            --gray-700: #cbd5e1;
            --gray-500: #94a3b8;
            --gray-300: #475569;
            --gray-100: #1e293b;
            --white: #1a1a2e;
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
            --footer-bg: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
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

        /* ========== NAVBAR ========== */
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
            color: var(--gray-700);
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

        /* ========== ALERTES ========== */
        .alert {
            padding: 16px 24px;
            border-radius: var(--radius-md);
            margin: 20px auto;
            max-width: 800px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-left: 4px solid;
            font-weight: 500;
        }
        .alert-success { background: #D1FAE5; color: #059669; border-left-color: #059669; }
        .alert-danger { background: #FEE2E2; color: #DC2626; border-left-color: #DC2626; }
        .alert-info { background: #DBEAFE; color: #2563EB; border-left-color: #2563EB; }
        [data-theme="dark"] .alert-success { background: #1a4a2a; color: #a3e4b7; }
        [data-theme="dark"] .alert-danger { background: #4a1a1a; color: #f5a5a5; }
        [data-theme="dark"] .alert-info { background: #1a3a5a; color: #87cefa; }

        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6rem 2rem;
            color: white;
            overflow: hidden;
        }
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            animation: fadeInUp 1s ease-out;
        }
        .hero h1 {
            font-size: 52px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero h1 span { color: #FFB800; text-shadow: 2px 2px 8px rgba(255,184,0,0.4); }
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ========== BOUTONS ========== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .btn-primary {
            background: white;
            color: var(--primary);
            box-shadow: var(--shadow-lg);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        .btn-outline:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* ========== SECTIONS ========== */
        .section {
            padding: 80px 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--dark);
        }
        .section-subtitle {
            font-size: 18px;
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto;
        }

        /* ========== SERVICES CARDS ========== */
        .bg-light { background: var(--card-bg); }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .service-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 40px 30px;
            text-align: center;
            transition: var(--transition-base);
            box-shadow: var(--shadow-sm);
            border: 2px solid transparent;
            cursor: pointer;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary);
            font-size: 36px;
            transition: var(--transition-base);
        }
        .service-card:hover .service-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1) rotate(360deg);
        }
        .service-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--dark);
        }
        .service-card p {
            color: var(--gray-500);
            font-size: 15px;
            line-height: 1.6;
        }

        /* ========== STEPS SECTION ========== */
        .steps-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 80px 0;
            color: white;
        }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }
        .step { text-align: center; padding: 30px; }
        .step-number {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 3px solid white;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            margin: 0 auto 24px;
            transition: var(--transition-base);
        }
        .step:hover .step-number {
            background: white;
            color: var(--primary);
            transform: scale(1.1);
        }
        .step h3 { font-size: 22px; font-weight: 700; margin-bottom: 12px; }
        .step p { font-size: 15px; opacity: 0.9; line-height: 1.6; }

        /* ========== EXPORT SECTION ========== */
        .export-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
        }
        .export-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
            border-left: 4px solid var(--success);
        }
        .export-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .card-header {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            align-items: flex-start;
        }
        .card-icon-pdf {
            width: 60px;
            height: 60px;
            background: #FEE2E2;
            color: var(--danger);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }
        [data-theme="dark"] .card-icon-pdf { background: #4a1a1a; color: #f5a5a5; }
        .card-info h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }
        .card-info p { font-size: 14px; color: var(--gray-500); }
        .btn-export {
            width: 100%;
            background: var(--success);
            color: white;
            padding: 14px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-export:hover {
            background: #008f5a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,168,107,0.3);
        }
        .btn-export:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
            transform: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 20px; }
        .empty-state p { font-size: 18px; color: var(--gray-500); margin-bottom: 20px; }
        .empty-state a { color: var(--primary); font-weight: 700; text-decoration: none; font-size: 16px; cursor: pointer; }
        .empty-state a:hover { text-decoration: underline; }

        /* ========== BADGES ========== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-en_attente { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traite { background: #D1FAE5; color: #059669; }
        .badge-refuse { background: #FEE2E2; color: #DC2626; }
        [data-theme="dark"] .badge-en_attente { background: #4a3a1a; color: #f5d742; }
        [data-theme="dark"] .badge-en_cours { background: #1a3a5a; color: #87cefa; }
        [data-theme="dark"] .badge-traite { background: #1a4a2a; color: #a3e4b7; }
        [data-theme="dark"] .badge-refuse { background: #4a1a1a; color: #f5a5a5; }

        /* ========== CONVERSATIONS ========== */
        .conversation-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
        }
        .conversation-card:hover { box-shadow: var(--shadow-md); }
        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border-color);
            flex-wrap: wrap;
            gap: 10px;
        }
        .message-bubble { margin-bottom: 16px; padding-left: 0; }
        .message-bubble.citoyen { padding-left: 40px; }
        .message-content {
            background: var(--primary-light);
            padding: 12px 16px;
            border-radius: var(--radius-md);
        }
        .message-content.citoyen-message {
            background: #E8F5E9;
            border: 2px solid #C8E6C9;
        }
        .message-content.admin-message {
            background: var(--primary-light);
        }
        [data-theme="dark"] .message-content.citoyen-message {
            background: #1a3a2a;
            border-color: var(--primary);
        }
        .reply-btn {
            margin-top: 8px;
            padding: 6px 14px;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: var(--transition-base);
        }
        .reply-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* ========== MODAL ========== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: var(--modal-bg);
            border-radius: var(--radius-lg);
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--border-color);
        }
        .modal-content h3 {
            margin-bottom: 20px;
            color: var(--primary);
        }
        .modal-content textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-family: 'Inter', sans-serif;
            margin-bottom: 20px;
            resize: vertical;
            background: var(--card-bg);
            color: var(--dark);
        }
        .modal-content input, .modal-content select {
            background: var(--card-bg);
            color: var(--dark);
            border: 2px solid var(--border-color);
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* ========== FAQ ========== */
        .faq-grid { max-width: 800px; margin: 0 auto; }
        .faq-item {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px 30px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
            cursor: pointer;
        }
        .faq-item:hover { box-shadow: var(--shadow-md); }
        .faq-item h4 {
            font-size: 17px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .faq-item h4 i { transition: var(--transition-base); color: var(--primary); }
        .faq-item.active h4 i { transform: rotate(180deg); }
        .faq-item p {
            margin-top: 16px;
            color: var(--gray-500);
            font-size: 15px;
            line-height: 1.7;
            display: none;
        }
        .faq-item.active p { display: block; }

        /* ========== TOAST ========== */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--toast-bg);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            font-weight: 500;
            animation: slideUp 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes slideUp {
            from { transform: translateY(100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== FOOTER ========== */
        .footer {
            background: var(--footer-bg);
            color: white;
            padding: 60px 2rem 30px;
            margin-top: 60px;
        }
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 40px;
        }
        .footer-section h4 { color: white; margin-bottom: 20px; font-size: 18px; font-weight: 700; }
        .footer-section p, .footer-section a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            line-height: 2;
            font-size: 14px;
            transition: var(--transition-fast);
        }
        .footer-section a:hover { color: #FFB800; padding-left: 4px; }
        .footer-section ul { list-style: none; }
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 1024px) {
            .footer-container { grid-template-columns: 1fr 1fr; }
            .hero h1 { font-size: 40px; }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .hero p { font-size: 16px; }
            .section-title { font-size: 28px; }
            .navbar { padding: 0.5rem 1rem; }
            .logo-img { height: 32px; }
            .nav-links { gap: 0.8rem; }
            .btn { width: 100%; justify-content: center; }
            .services-grid, .export-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; gap: 30px; }
            .footer-container { grid-template-columns: 1fr; }
            .section { padding: 60px 0; }
            .message-bubble.citoyen { padding-left: 20px; }
            .container { padding: 0 1rem; }
        }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    </style>
</head>
<body>
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
                    <span class="user-name">👋 <?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></span>
                    <a href="profil.php" class="btn-profiled">Mon profil</a>
                    <a href="logout.php" class="btn-logout">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Connexion</a>
                    <a href="register.php" class="btn-register">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Alertes -->
    <div id="alertContainer"></div>

    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="../../assets/video/background.mp4" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-content" data-aos="fade-up">
            <h1>Mes <span>Demandes</span></h1>
            <p>Suivez l'avancement de vos demandes et échangez avec l'administration.</p>
            <div class="hero-buttons">
                <button class="btn btn-primary" onclick="showNewDemandeModal()">
                    <i class="fas fa-plus-circle"></i> Nouvelle Demande
                </button>
                <button class="btn btn-outline" onclick="scrollToSection('export')">
                    <i class="fas fa-file-download"></i> Mes Documents
                </button>
            </div>
        </div>
    </section>

    <!-- SECTION NOS SERVICES EN LIGNE -->
    <section class="section bg-light" id="services">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Nos Services en Ligne</h2>
                <p class="section-subtitle">Plus besoin de faire la queue. Sélectionnez le service dont vous avez besoin et commencez votre démarche immédiatement.</p>
            </div>
            <div class="services-grid">
                <div class="service-card" data-aos="fade-up" data-aos-delay="100" onclick="showNewDemandeModal('urbanisme')">
                    <div class="service-icon"><i class="fas fa-building"></i></div>
                    <h3>Urbanisme</h3>
                    <p>Permis de construire, déclarations de travaux et certificats d'urbanisme.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="200" onclick="showNewDemandeModal('etat_civil')">
                    <div class="service-icon"><i class="fas fa-id-card"></i></div>
                    <h3>État Civil</h3>
                    <p>Actes de naissance, mariage, décès et demandes de cartes d'identité.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="300" onclick="showNewDemandeModal('voirie')">
                    <div class="service-icon"><i class="fas fa-road"></i></div>
                    <h3>Voirie</h3>
                    <p>Signalements de dégradations, demandes d'occupation du domaine public.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="400" onclick="showNewDemandeModal('social')">
                    <div class="service-icon"><i class="fas fa-users"></i></div>
                    <h3>Social</h3>
                    <p>Aides municipales, inscriptions en crèche et services aux seniors.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION COMMENT ÇA MARCHE -->
    <section class="steps-section" id="guide">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title" style="color: white;">Comment ça marche ?</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.9);">Trois étapes simples pour obtenir vos documents officiels en toute sérénité.</p>
            </div>
            <div class="steps-grid">
                <div class="step" data-aos="fade-right">
                    <div class="step-number">1</div>
                    <h3>Déposez</h3>
                    <p>Remplissez le formulaire en ligne et joignez les pièces justificatives nécessaires.</p>
                </div>
                <div class="step" data-aos="fade-up">
                    <div class="step-number">2</div>
                    <h3>Suivez</h3>
                    <p>Recevez des notifications en temps réel sur l'avancement de votre dossier par nos agents.</p>
                </div>
                <div class="step" data-aos="fade-left">
                    <div class="step-number">3</div>
                    <h3>Récupérez</h3>
                    <p>Une fois validé, téléchargez votre document officiel directement depuis votre espace.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION CONVERSATIONS -->
    <section class="section" id="conversations">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Mes Conversations</h2>
                <p class="section-subtitle">Échangez avec l'administration concernant vos demandes.</p>
            </div>
            <div id="conversationsContainer">
                <?php
                $demandesWithReponses = [];
                foreach($demandes as $d) {
                    $hasReponses = false;
                    foreach($reponses as $r) {
                        if($r['id_demande'] == $d['id_demande']) {
                            $hasReponses = true;
                            break;
                        }
                    }
                    if($hasReponses) $demandesWithReponses[] = $d;
                }
                
                if(empty($demandesWithReponses)): ?>
                    <div class="empty-state" data-aos="zoom-in">
                        <i class="fas fa-comments"></i>
                        <p>Aucune conversation pour le moment.</p>
                        <a onclick="showNewDemandeModal()">Créer une demande →</a>
                    </div>
                <?php else: ?>
                    <div style="max-width: 900px; margin: 0 auto;">
                        <?php foreach($demandesWithReponses as $demande): ?>
                            <div class="conversation-card" data-aos="fade-up">
                                <div class="conversation-header">
                                    <div>
                                        <h3 style="font-size: 16px; font-weight: 700;"><?= htmlspecialchars($demande['titre']) ?></h3>
                                        <span style="font-size: 12px; color: var(--gray-500);">Demande #<?= str_pad($demande['id_demande'], 5, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                    <span class="badge <?= $statuts_labels[$demande['statut']]['class'] ?? 'badge-en_attente' ?>">
                                        <?= $statuts_labels[$demande['statut']]['text'] ?? $demande['statut'] ?>
                                    </span>
                                </div>
                                <?php
                                $demandeReponses = array_filter($reponses, function($r) use ($demande) {
                                    return $r['id_demande'] == $demande['id_demande'] && $r['id_parent'] === null;
                                });
                                foreach($demandeReponses as $rep):
                                    $isAdmin = $rep['expediteur'] === 'admin';
                                    $replies = array_filter($reponses, function($r) use ($rep) {
                                        return $r['id_parent'] == $rep['id_reponse'];
                                    });
                                ?>
                                    <div class="message-bubble <?= $isAdmin ? '' : 'citoyen' ?>">
                                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                                            <?php if($isAdmin): ?>
                                                <div style="width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">AD</div>
                                            <?php endif; ?>
                                            <div style="flex: 1;">
                                                <div class="message-content <?= $isAdmin ? 'admin-message' : 'citoyen-message' ?>">
                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                                        <span style="font-weight: 600; font-size: 13px; color: <?= $isAdmin ? 'var(--primary)' : '#2E7D32' ?>;">
                                                            <?= $isAdmin ? '🏛️ Administration' : '👤 Vous' ?>
                                                        </span>
                                                        <span style="font-size: 11px; color: var(--gray-500);">
                                                            <?= date('d/m/Y H:i', strtotime($rep['date_creation'])) ?>
                                                        </span>
                                                    </div>
                                                    <p style="font-size: 14px; color: var(--dark);"><?= nl2br(htmlspecialchars($rep['contenu'])) ?></p>
                                                </div>
                                                <?php if($isAdmin && $isLoggedIn): ?>
                                                    <button onclick="showReponseModal(<?= $demande['id_demande'] ?>, <?= $rep['id_reponse'] ?>)" class="reply-btn">
                                                        <i class="fas fa-reply"></i> Répondre
                                                    </button>
                                                <?php endif; ?>
                                                <?php if(!empty($replies)): ?>
                                                    <div style="margin-top: 10px; padding-left: 20px; border-left: 2px solid #C8E6C9;">
                                                        <?php foreach($replies as $reply): ?>
                                                            <div style="margin-bottom: 8px;">
                                                                <div style="background: #FFF8E1; padding: 10px 12px; border-radius: var(--radius-md);">
                                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                                                        <span style="font-weight: 600; font-size: 12px; color: #F57F17;">
                                                                            <?= $reply['expediteur'] === 'citoyen' ? '👤 Vous' : '🏛️ Administration' ?>
                                                                        </span>
                                                                        <span style="font-size: 10px; color: var(--gray-500);">
                                                                            <?= date('d/m/Y H:i', strtotime($reply['date_creation'])) ?>
                                                                        </span>
                                                                    </div>
                                                                    <p style="font-size: 13px;"><?= nl2br(htmlspecialchars($reply['contenu'])) ?></p>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if(!$isAdmin): ?>
                                                <div style="width: 35px; height: 35px; background: #2E7D32; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;"><?= $user_initials ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section" id="export">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Vos Documents Prêts</h2>
                <p class="section-subtitle">Retrouvez ici tous vos documents officiels validés et prêts à être téléchargés.</p>
            </div>
            <div id="exportContainer">
                <?php
                $demandesTraitees = array_filter($demandes, function($d) {
                    return $d['statut'] === 'traite';
                });
                
                if(empty($demandesTraitees)): ?>
                    <div class="empty-state" data-aos="zoom-in">
                        <i class="fas fa-folder-open"></i>
                        <p>Aucun document n'est encore prêt pour l'exportation.</p>
                        <a onclick="showNewDemandeModal()">Commencer une démarche →</a>
                    </div>
                <?php else: ?>
                    <div class="export-grid">
                        <?php foreach($demandesTraitees as $d): ?>
                            <div class="export-card" data-aos="fade-up">
                                <div class="card-header">
                                    <div class="card-icon-pdf"><i class="fas fa-file-pdf"></i></div>
                                    <div class="card-info">
                                        <h3><?= htmlspecialchars($d['titre']) ?></h3>
                                        <p><?= $types_labels[$d['type_demande']] ?? 'Document Officiel' ?> • Validé le <?= $d['date_formatee'] ?></p>
                                    </div>
                                </div>
                                <button class="btn-export" onclick="telechargerPDF('<?= addslashes($d['titre']) ?>', '<?= addslashes($d['description']) ?>', <?= $d['id_demande'] ?>, '<?= $d['date_formatee'] ?>', '<?= addslashes($d['nom_service'] ?? 'Service municipal') ?>')">
                                    <i class="fas fa-cloud-download-alt"></i> Télécharger le PDF
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section bg-light" id="faq">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Questions Fréquentes</h2>
                <p class="section-subtitle">Tout ce que vous devez savoir sur l'utilisation de votre portail citoyen.</p>
            </div>
            <div class="faq-grid">
                <div class="faq-item" data-aos="fade-up" onclick="this.classList.toggle('active')">
                    <h4>Quels sont les délais de traitement ? <i class="fas fa-chevron-down"></i></h4>
                    <p>Les délais varient selon le type de demande, mais nous nous engageons à traiter chaque dossier sous 5 à 10 jours ouvrés selon la complexité.</p>
                </div>
                <div class="faq-item" data-aos="fade-up" data-aos-delay="100" onclick="this.classList.toggle('active')">
                    <h4>Les documents téléchargés sont-ils officiels ? <i class="fas fa-chevron-down"></i></h4>
                    <p>Oui, tous les documents exportés via InnoGov comportent une signature électronique certifiée par nos services et ont la même valeur légale qu'un document papier.</p>
                </div>
                <div class="faq-item" data-aos="fade-up" data-aos-delay="200" onclick="this.classList.toggle('active')">
                    <h4>Comment puis-je modifier une demande en cours ? <i class="fas fa-chevron-down"></i></h4>
                    <p>Tant que votre demande est au statut "En attente", vous pouvez la modifier directement depuis votre tableau de bord. Une fois en cours de traitement, contactez le service concerné.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Nouvelle Demande -->
    <div id="demandeModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-file-alt"></i> Nouvelle Demande</h3>
            <select id="demandeType" style="width:100%; padding:12px; margin-bottom:15px; border:2px solid var(--border-color); border-radius:var(--radius-md); font-family:inherit; background:var(--card-bg); color:var(--dark);">
                <option value="urbanisme">🏗️ Urbanisme</option>
                <option value="voirie">🛣️ Voirie</option>
                <option value="etat_civil">📜 État Civil</option>
                <option value="culture">🎭 Culture</option>
                <option value="social">🤝 Social</option>
                <option value="autre">📌 Autre</option>
            </select>
            <input type="text" id="demandeTitre" placeholder="Titre de la demande" style="width:100%; padding:12px; margin-bottom:15px; border:2px solid var(--border-color); border-radius:var(--radius-md); font-family:inherit; background:var(--card-bg); color:var(--dark);">
            <textarea id="demandeDesc" rows="4" placeholder="Description détaillée de votre demande..."></textarea>
            <div class="modal-buttons">
                <button class="btn btn-secondary btn-sm" onclick="closeModal()">Annuler</button>
                <button class="btn btn-primary btn-sm" onclick="ajouterDemande()">Envoyer la demande</button>
            </div>
        </div>
    </div>

    <!-- Modal Réponse -->
    <div id="reponseModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-reply"></i> Répondre à l'administration</h3>
            <input type="hidden" id="reponseDemandeId">
            <input type="hidden" id="reponseParentId">
            <textarea id="reponseContenu" rows="4" placeholder="Votre réponse..."></textarea>
            <div class="modal-buttons">
                <button class="btn btn-secondary btn-sm" onclick="closeReponseModal()">Annuler</button>
                <button class="btn btn-primary btn-sm" onclick="envoyerReponse()">Envoyer</button>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <div class="logo" style="margin-bottom: 20px;" onclick="window.location.href='index.php'">
                    <div class="logo-icon">IG</div>
                    <div class="logo-text" style="color: white;">
                        <h1 style="color: white; font-size: 20px;">InnoGov</h1>
                        <p style="color: rgba(255,255,255,0.6);">Portail Citoyen</p>
                    </div>
                </div>
                <p style="max-width: 300px;">La plateforme numérique au service des citoyens pour une administration plus proche et plus efficace.</p>
            </div>
            <div class="footer-section">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a onclick="scrollToSection('services')">Services</a></li>
                    <li><a onclick="scrollToSection('guide')">Comment ça marche</a></li>
                    <li><a href="demandes.php">Mes demandes</a></li>
                    <li><a href="reclamations.php">Réclamations</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Aide</h4>
                <ul>
                    <li><a onclick="scrollToSection('guide')">Comment ça marche</a></li>
                    <li><a>Support Technique</a></li>
                    <li><a>Mentions Légales</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul>
                    <li><i class="fas fa-envelope"></i> contact@innogov.dz</li>
                    <li><i class="fas fa-phone"></i> +213 23 45 67 89</li>
                    <li><i class="fas fa-map-marker-alt"></i> Siège de la Mairie, Alger</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 InnoGov • Mairie Digitale • Tous droits réservés • Conçu pour le futur de l'administration.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
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

        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100
        });

        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }

        let demandes = <?= json_encode($demandes) ?>;
        let reponses = <?= json_encode($reponses) ?>;
        let nextId = <?= max(array_column($demandes, 'id_demande')) + 1 ?>;
        let nextReponseId = <?= max(array_column($reponses, 'id_reponse')) + 1 ?>;

        const isLoggedIn = <?= json_encode($isLoggedIn) ?>;

        function showToast(message, isError = false) {
            const container = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = 'alert ' + (isError ? 'alert-danger' : 'alert-success');
            alert.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ' + message;
            container.appendChild(alert);
            setTimeout(() => alert.remove(), 4000);
        }

        function showNewDemandeModal(type = 'urbanisme') {
            if (!isLoggedIn) {
                showToast("Veuillez vous connecter pour effectuer une demande", true);
                setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                return;
            }
            document.getElementById('demandeType').value = type;
            document.getElementById('demandeTitre').value = '';
            document.getElementById('demandeDesc').value = '';
            document.getElementById('demandeModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('demandeModal').classList.remove('active');
        }

        function ajouterDemande() {
            const type = document.getElementById('demandeType').value;
            const titre = document.getElementById('demandeTitre').value.trim();
            const desc = document.getElementById('demandeDesc').value.trim();

            if (!titre || !desc) {
                showToast("Veuillez remplir tous les champs", true);
                return;
            }

            const newDemande = {
                id_demande: nextId++,
                titre: titre,
                description: desc,
                type_demande: type,
                statut: "en_attente",
                date_formatee: new Date().toLocaleDateString('fr-FR'),
                nom_service: "Service Municipal"
            };
            demandes.unshift(newDemande);
            closeModal();
            renderConversations();
            renderExports();
            showToast("✅ Demande créée avec succès !");
        }

        function showReponseModal(idDemande, parentId = null) {
            if (!isLoggedIn) {
                showToast("Veuillez vous connecter pour répondre", true);
                setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                return;
            }
            document.getElementById('reponseDemandeId').value = idDemande;
            document.getElementById('reponseParentId').value = parentId || '';
            document.getElementById('reponseContenu').value = '';
            document.getElementById('reponseModal').classList.add('active');
        }

        function closeReponseModal() {
            document.getElementById('reponseModal').classList.remove('active');
        }

        function envoyerReponse() {
            const idDemande = parseInt(document.getElementById('reponseDemandeId').value);
            const parentId = document.getElementById('reponseParentId').value ? parseInt(document.getElementById('reponseParentId').value) : null;
            const contenu = document.getElementById('reponseContenu').value.trim();

            if (!contenu || contenu.length < 5) {
                showToast("Votre réponse doit contenir au moins 5 caractères", true);
                return;
            }

            const nouvelleReponse = {
                id_reponse: nextReponseId++,
                id_demande: idDemande,
                id_parent: parentId,
                expediteur: "citoyen",
                contenu: contenu,
                date_creation: new Date().toISOString().slice(0, 19).replace('T', ' ')
            };
            reponses.push(nouvelleReponse);
            closeReponseModal();
            renderConversations();
            showToast("✅ Réponse envoyée avec succès !");
        }

        function getRepliesForResponse(responseId) {
            return reponses.filter(r => r.id_parent === responseId);
        }

        function renderConversations() {
            const container = document.getElementById('conversationsContainer');
            const demandesWithReponses = demandes.filter(d => reponses.some(r => r.id_demande === d.id_demande));
            
            if (demandesWithReponses.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" data-aos="zoom-in">
                        <i class="fas fa-comments"></i>
                        <p>Aucune conversation pour le moment.</p>
                        <a onclick="showNewDemandeModal()">Créer une demande →</a>
                    </div>
                `;
                return;
            }

            const statutLabels = {
                'en_attente': '⏳ En attente',
                'en_cours': '🔄 En cours',
                'traite': '✅ Traité',
                'refuse': '❌ Refusé'
            };
            
            container.innerHTML = '<div style="max-width: 900px; margin: 0 auto;">';
            
            demandesWithReponses.forEach(demande => {
                const demandeReponses = reponses.filter(r => r.id_demande === demande.id_demande && !r.id_parent);
                
                container.innerHTML += `
                    <div class="conversation-card" data-aos="fade-up">
                        <div class="conversation-header">
                            <div>
                                <h3 style="font-size: 16px; font-weight: 700;">${escapeHtml(demande.titre)}</h3>
                                <span style="font-size: 12px; color: var(--gray-500);">Demande #${String(demande.id_demande).padStart(5, '0')}</span>
                            </div>
                            <span class="badge badge-${demande.statut}">
                                ${statutLabels[demande.statut] || demande.statut}
                            </span>
                        </div>
                `;
                
                demandeReponses.forEach(rep => {
                    const isAdmin = rep.expediteur === 'admin';
                    const replies = getRepliesForResponse(rep.id_reponse);
                    
                    container.innerHTML += `
                        <div class="message-bubble ${isAdmin ? '' : 'citoyen'}">
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                ${isAdmin ? '<div style="width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">AD</div>' : ''}
                                <div style="flex: 1;">
                                    <div class="message-content ${isAdmin ? 'admin-message' : 'citoyen-message'}">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="font-weight: 600; font-size: 13px; color: ${isAdmin ? 'var(--primary)' : '#2E7D32'};">
                                                ${isAdmin ? '🏛️ Administration' : '👤 Vous'}
                                            </span>
                                            <span style="font-size: 11px; color: var(--gray-500);">
                                                ${formatDate(rep.date_creation)}
                                            </span>
                                        </div>
                                        <p style="font-size: 14px; color: var(--dark);">${escapeHtml(rep.contenu)}</p>
                                    </div>
                                    ${isAdmin && isLoggedIn ? `<button onclick="showReponseModal(${demande.id_demande}, ${rep.id_reponse})" class="reply-btn"><i class="fas fa-reply"></i> Répondre</button>` : ''}
                                    ${replies.length > 0 ? `
                                        <div style="margin-top: 10px; padding-left: 20px; border-left: 2px solid #C8E6C9;">
                                            ${replies.map(reply => `
                                                <div style="margin-bottom: 8px;">
                                                    <div style="background: #FFF8E1; padding: 10px 12px; border-radius: var(--radius-md);">
                                                        <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                                            <span style="font-weight: 600; font-size: 12px; color: #F57F17;">${reply.expediteur === 'citoyen' ? '👤 Vous' : '🏛️ Administration'}</span>
                                                            <span style="font-size: 10px; color: var(--gray-500);">${formatDate(reply.date_creation)}</span>
                                                        </div>
                                                        <p style="font-size: 13px;">${escapeHtml(reply.contenu)}</p>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                                ${!isAdmin ? `<div style="width: 35px; height: 35px; background: #2E7D32; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">${getInitials()}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML += `</div>`;
            });
            
            container.innerHTML += '</div>';
        }

        function renderExports() {
            const container = document.getElementById('exportContainer');
            const demandesTraitees = demandes.filter(d => d.statut === 'traite');
            
            const typesLabels = {
                'urbanisme': '🏗️ Urbanisme',
                'voirie': '🛣️ Voirie',
                'etat_civil': '📜 État Civil',
                'culture': '🎭 Culture',
                'social': '🤝 Social',
                'autre': '📌 Autre'
            };
            
            if (demandesTraitees.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" data-aos="zoom-in">
                        <i class="fas fa-folder-open"></i>
                        <p>Aucun document n'est encore prêt pour l'exportation.</p>
                        <a onclick="showNewDemandeModal()">Commencer une démarche →</a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = '<div class="export-grid">';
            demandesTraitees.forEach(d => {
                container.innerHTML += `
                    <div class="export-card" data-aos="fade-up">
                        <div class="card-header">
                            <div class="card-icon-pdf"><i class="fas fa-file-pdf"></i></div>
                            <div class="card-info">
                                <h3>${escapeHtml(d.titre)}</h3>
                                <p>${typesLabels[d.type_demande] || 'Document Officiel'} • Validé le ${d.date_formatee}</p>
                            </div>
                        </div>
                        <button class="btn-export" onclick="telechargerPDF('${escapeJs(d.titre)}', '${escapeJs(d.description)}', ${d.id_demande}, '${d.date_formatee}', '${escapeJs(d.nom_service)}')">
                            <i class="fas fa-cloud-download-alt"></i> Télécharger le PDF
                        </button>
                    </div>
                `;
            });
            container.innerHTML += '</div>';
        }

        function telechargerPDF(titre, description, id, date, service) {
            const btn = event.target.closest('.btn-export');
            const originalHTML = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération du PDF...';
            btn.disabled = true;
            
            setTimeout(() => {
                try {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    const primaryColor = [0, 109, 91];
                    const darkColor = [26, 44, 62];
                    const grayColor = [138, 153, 176];
                    
                    doc.setFillColor(...primaryColor);
                    doc.rect(0, 0, 210, 40, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(22);
                    doc.setFont('helvetica', 'bold');
                    doc.text('INNOGOV', 15, 20);
                    doc.setFontSize(10);
                    doc.setFont('helvetica', 'normal');
                    doc.text('Document Officiel', 15, 28);
                    doc.text('Mairie Digitale', 15, 34);
                    doc.setTextColor(...darkColor);
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'bold');
                    doc.text('REFERENCE', 15, 55);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(10);
                    doc.text('DEM-' + String(id).padStart(5, '0'), 15, 62);
                    const dateEmission = new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
                    doc.text("Date d'émission : " + dateEmission, 15, 70);
                    doc.text('Date de validation : ' + date, 15, 77);
                    doc.setDrawColor(...primaryColor);
                    doc.setLineWidth(0.5);
                    doc.line(15, 85, 195, 85);
                    doc.setFontSize(14);
                    doc.setFont('helvetica', 'bold');
                    doc.text('TITRE DE LA DEMANDE', 15, 98);
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'normal');
                    doc.text(titre, 15, 108);
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'bold');
                    doc.text('SERVICE', 15, 122);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(10);
                    doc.text(service, 15, 129);
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'bold');
                    doc.text('STATUT', 15, 143);
                    doc.setTextColor(0, 168, 107);
                    doc.setFont('helvetica', 'bold');
                    doc.text('TRAITEE', 15, 150);
                    doc.setTextColor(...darkColor);
                    doc.setDrawColor(...primaryColor);
                    doc.line(15, 158, 195, 158);
                    doc.setFontSize(11);
                    doc.setFont('helvetica', 'bold');
                    doc.text('DESCRIPTION', 15, 171);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(9);
                    const splitDescription = doc.splitTextToSize(description, 180);
                    doc.text(splitDescription, 15, 178);
                    const ySignature = 240;
                    doc.setDrawColor(...primaryColor);
                    doc.setLineWidth(0.5);
                    doc.line(15, ySignature, 195, ySignature);
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'italic');
                    doc.setTextColor(...grayColor);
                    doc.text('Ce document est genere automatiquement par la plateforme InnoGov.', 15, ySignature + 10);
                    doc.text('Signature electronique certifiee - ' + dateEmission, 15, ySignature + 16);
                    doc.setFillColor(...primaryColor);
                    doc.rect(0, 275, 210, 22, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(8);
                    doc.setFont('helvetica', 'normal');
                    doc.text('InnoGov - Mairie Digitale | contact@innogov.dz | +213 23 45 67 89 | Siege de la Mairie, Alger', 15, 288);
                    
                    doc.save('Demande_' + id + '_' + titre.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 30) + '.pdf');
                    
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    showToast('✅ PDF téléchargé avec succès !');
                } catch (error) {
                    console.error('Erreur PDF:', error);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    showToast('❌ Erreur lors de la génération du PDF', true);
                }
            }, 500);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function escapeJs(str) {
            if (!str) return '';
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        function getInitials() {
            return '<?= $user_initials ?>';
        }

        // Fermer les modals en cliquant en dehors
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Initialisation
        renderConversations();
        renderExports();
    </script>
</body>
</html>