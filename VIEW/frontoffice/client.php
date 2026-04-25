<?php
require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';
require_once __DIR__ . '/../../MODEL/Demande.php';
require_once __DIR__ . '/../../MODEL/SuiviReponse.php';

// Initialiser la session SI PAS DÉJÀ FAIT
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FORCER LE PROFIL CITOYEN (pas admin)
$_SESSION['user_id'] = 2;        // ID du citoyen Mohamed Ben Ali
$_SESSION['user_nom'] = 'Ben Ali';
$_SESSION['user_prenom'] = 'Mohamed';
$_SESSION['user_role'] = 'citoyen';

$controller = new DemandeController();

// RÉCUPÉRER LES DEMANDES DU CITOYEN (pas toutes les demandes)
$demandes = $controller->getDemandesByCitoyen($_SESSION['user_id']);

// Filtrer les demandes traitées
$demandes_traitees = array_filter($demandes, function($d) {
    return $d['statut'] === 'traite';
});

// Informations utilisateur
$user_nom = $_SESSION['user_nom'];
$user_prenom = $_SESSION['user_prenom'];
$user_initials = strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1));

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];

$message = $_GET['success'] ?? $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

// Récupérer les conversations DU CITOYEN
$suiviReponse = new SuiviReponse();
$demandesWithReponses = [];
foreach ($demandes as $demande) {
    $reponses = $suiviReponse->getReponsesByDemande($demande['id_demande']);
    if (!empty($reponses)) {
        $demandesWithReponses[$demande['id_demande']] = [
            'demande' => $demande,
            'reponses' => $reponses
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Portail Citoyen • Mairie</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-100);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 1rem 2rem;
            border-bottom: 2px solid var(--primary);
            transition: var(--transition-base);
        }

        .navbar-container {
            max-width: 1400px;
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
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: var(--shadow-primary);
            font-weight: 800;
        }

        .logo-text h1 {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-text p {
            font-size: 11px;
            color: var(--gray-500);
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
            list-style: none;
        }

        .nav-link {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 600;
            transition: var(--transition-base);
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition-base);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid white;
            box-shadow: var(--shadow-sm);
        }

        .user-name {
            font-weight: 600;
            color: var(--gray-700);
        }

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

        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, var(--gray-100), transparent);
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

        .hero h1 span {
            color: #FFB800;
            text-shadow: 2px 2px 8px rgba(255,184,0,0.4);
        }

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
        .bg-light { background: var(--white); }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 40px 30px;
            text-align: center;
            transition: var(--transition-base);
            box-shadow: var(--shadow-sm);
            border: 2px solid transparent;
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
            background: white;
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
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 20px; }
        .empty-state p { font-size: 18px; color: var(--gray-500); margin-bottom: 20px; }
        .empty-state a { color: var(--primary); font-weight: 700; text-decoration: none; font-size: 16px; }
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

        /* ========== CONVERSATIONS ========== */
        .conversation-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
        }

        .conversation-card:hover {
            box-shadow: var(--shadow-md);
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-300);
            flex-wrap: wrap;
            gap: 10px;
        }

        .message-bubble {
            margin-bottom: 16px;
            padding-left: 0;
        }

        .message-bubble.citoyen {
            padding-left: 40px;
        }

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

        .reply-form {
            display: none;
            margin-top: 10px;
            padding-left: 10px;
        }

        .reply-form textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-family: 'Inter', sans-serif;
            resize: vertical;
            min-height: 50px;
        }

        .reply-form textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.1);
        }

        /* ========== FAQ ========== */
        .faq-grid { max-width: 800px; margin: 0 auto; }

        .faq-item {
            background: white;
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
            background: var(--success);
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
            background: linear-gradient(135deg, var(--dark) 0%, var(--gray-900) 100%);
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
            .navbar-container { flex-direction: column; text-align: center; }
            .nav-menu { justify-content: center; }
            .btn { width: 100%; justify-content: center; }
            .services-grid, .export-grid { grid-template-columns: 1fr; }
            .footer-container { grid-template-columns: 1fr; }
            .section { padding: 60px 0; }
            .steps-grid { grid-template-columns: 1fr; gap: 30px; }
            .message-bubble.citoyen { padding-left: 20px; }
        }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="client.php" class="logo">
                <div class="logo-icon">IG</div>
                <div class="logo-text">
                    <h1>InnoGov</h1>
                    <p>Portail Citoyen</p>
                </div>
            </a>
            <ul class="nav-menu">
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#guide" class="nav-link">Comment ça marche</a></li>
                <li><a href="#conversations" class="nav-link">Conversations</a></li>
                <li><a href="#export" class="nav-link">Mes Documents</a></li>
                <li><a href="#faq" class="nav-link">FAQ</a></li>
            </ul>
            <div class="user-nav">
                <div class="avatar"><?= $user_initials ?></div>
                <span class="user-name"><?= htmlspecialchars($user_prenom) ?> <?= htmlspecialchars($user_nom) ?></span>
            </div>
        </div>
    </nav>

    <!-- Alertes -->
    <?php if ($message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="hero">
        <div class="hero-content" data-aos="fade-up">
            <h1>Votre Mairie, <span>Partout avec vous.</span></h1>
            <p>Accédez à une administration moderne, rapide et transparente. Déposez vos demandes en ligne et recevez vos documents officiels sans vous déplacer.</p>
            <div class="hero-buttons">
                <a href="../backoffice/ajouter_demande.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nouvelle Demande
                </a>
                <a href="#export" class="btn btn-outline">
                    <i class="fas fa-file-download"></i> Mes Documents
                </a>
            </div>
        </div>
    </section>

    <section class="section bg-light" id="services">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Nos Services en Ligne</h2>
                <p class="section-subtitle">Plus besoin de faire la queue. Sélectionnez le service dont vous avez besoin et commencez votre démarche immédiatement.</p>
            </div>
            <div class="services-grid">
                <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-icon"><i class="fas fa-building"></i></div>
                    <h3>Urbanisme</h3>
                    <p>Permis de construire, déclarations de travaux et certificats d'urbanisme.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-icon"><i class="fas fa-id-card"></i></div>
                    <h3>État Civil</h3>
                    <p>Actes de naissance, mariage, décès et demandes de cartes d'identité.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-icon"><i class="fas fa-road"></i></div>
                    <h3>Voirie</h3>
                    <p>Signalements de dégradations, demandes d'occupation du domaine public.</p>
                </div>
                <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-icon"><i class="fas fa-users"></i></div>
                    <h3>Social</h3>
                    <p>Aides municipales, inscriptions en crèche et services aux seniors.</p>
                </div>
            </div>
        </div>
    </section>

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

            <?php if (empty($demandesWithReponses)): ?>
                <div class="empty-state" data-aos="zoom-in">
                    <i class="fas fa-comments"></i>
                    <p>Aucune conversation pour le moment.</p>
                    <a href="../backoffice/ajouter_demande.php">Créer une demande →</a>
                </div>
            <?php else: ?>
                <div style="max-width: 900px; margin: 0 auto;">
                    <?php foreach ($demandesWithReponses as $item): ?>
                        <?php $d = $item['demande']; $reponses = $item['reponses']; ?>
                        <div class="conversation-card" data-aos="fade-up">
                            <div class="conversation-header">
                                <div>
                                    <h3 style="font-size: 16px; font-weight: 700;"><?= htmlspecialchars($d['titre']) ?></h3>
                                    <span style="font-size: 12px; color: var(--gray-500);">Demande #<?= str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <span class="badge badge-<?= $d['statut'] ?>" style="font-size: 11px;">
                                    <?php
                                    $statuts = ['en_attente' => '⏳ En attente', 'en_cours' => '🔄 En cours', 'traite' => '✅ Traité', 'refuse' => '❌ Refusé'];
                                    echo $statuts[$d['statut']] ?? $d['statut'];
                                    ?>
                                </span>
                            </div>
                            
                            <?php foreach ($reponses as $rep): ?>
                                <?php 
                                $isAdmin = $rep['expediteur'] === 'admin';
                                $reponsesEnfants = $suiviReponse->getReponsesEnfants($rep['id_reponse']);
                                ?>
                                <div class="message-bubble <?= $isAdmin ? '' : 'citoyen' ?>">
                                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                                        <?php if ($isAdmin): ?>
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
                                            
                                            <?php if ($isAdmin): ?>
                                                <button onclick="toggleReponseForm(<?= $rep['id_reponse'] ?>)" class="reply-btn">
                                                    <i class="fas fa-reply"></i> Répondre
                                                </button>
                                                <div id="reponseForm_<?= $rep['id_reponse'] ?>" class="reply-form">
                                                    <form method="POST" action="envoyer_reponse.php" style="display: flex; gap: 10px; flex-direction: column;">
                                                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                                                        <input type="hidden" name="id_parent" value="<?= $rep['id_reponse'] ?>">
                                                        <input type="hidden" name="redirect" value="client.php#conversations">
                                                        <textarea name="contenu" rows="2" placeholder="Votre réponse..."></textarea>
                                                        <button type="submit" style="align-self: flex-end; padding: 8px 20px; background: var(--success); color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: 600; font-size: 13px;">
                                                            <i class="fas fa-paper-plane"></i> Envoyer
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($reponsesEnfants)): ?>
                                                <div style="margin-top: 10px; padding-left: 20px; border-left: 2px solid #C8E6C9;">
                                                    <?php foreach ($reponsesEnfants as $enfant): ?>
                                                        <div style="margin-bottom: 8px;">
                                                            <div style="background: #FFF8E1; padding: 10px 12px; border-radius: var(--radius-md);">
                                                                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                                                    <span style="font-weight: 600; font-size: 12px; color: #F57F17;">
                                                                        <?= $enfant['expediteur'] === 'citoyen' ? '👤 Vous' : '🏛️ Administration' ?>
                                                                    </span>
                                                                    <span style="font-size: 10px; color: var(--gray-500);">
                                                                        <?= date('d/m/Y H:i', strtotime($enfant['date_creation'])) ?>
                                                                    </span>
                                                                </div>
                                                                <p style="font-size: 13px;"><?= nl2br(htmlspecialchars($enfant['contenu'])) ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!$isAdmin): ?>
                                            <div style="width: 35px; height: 35px; background: #2E7D32; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">MB</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section" id="export">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Vos Documents Prêts</h2>
                <p class="section-subtitle">Retrouvez ici tous vos documents officiels validés et prêts à être téléchargés.</p>
            </div>

            <?php if (empty($demandes_traitees)): ?>
                <div class="empty-state" data-aos="zoom-in">
                    <i class="fas fa-folder-open"></i>
                    <p>Aucun document n'est encore prêt pour l'exportation.</p>
                    <a href="../backoffice/ajouter_demande.php">Commencer une démarche →</a>
                </div>
            <?php else: ?>
                <div class="export-grid">
                    <?php foreach ($demandes_traitees as $d): ?>
                        <div class="export-card" data-aos="fade-up">
                            <div class="card-header">
                                <div class="card-icon-pdf"><i class="fas fa-file-pdf"></i></div>
                                <div class="card-info">
                                    <h3><?= htmlspecialchars($d['titre']) ?></h3>
                                    <p><?= $types_demandes[$d['type_demande']] ?? 'Document Officiel' ?> • Validé le <?= $d['date_formatee'] ?></p>
                                </div>
                            </div>
                            <button class="btn-export" onclick="telechargerPDF('<?= htmlspecialchars(addslashes($d['titre'])) ?>', '<?= htmlspecialchars(addslashes($d['description'])) ?>', '<?= $d['id_demande'] ?>', '<?= $d['date_formatee'] ?>', '<?= htmlspecialchars(addslashes($d['nom_service'] ?? 'Service municipal')) ?>')">
                                <i class="fas fa-cloud-download-alt"></i> Télécharger le PDF
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <div class="logo" style="margin-bottom: 20px;">
                    <div class="logo-icon">IG</div>
                    <div class="logo-text" style="color: white;">
                        <h1 style="color: white; font-size: 20px; -webkit-text-fill-color: white;">InnoGov</h1>
                        <p style="color: rgba(255,255,255,0.6);">Portail Citoyen</p>
                    </div>
                </div>
                <p style="max-width: 300px;">La plateforme numérique au service des citoyens pour une administration plus proche et plus efficace.</p>
            </div>
            <div class="footer-section">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#conversations">Conversations</a></li>
                    <li><a href="#export">Mes Documents</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Aide</h4>
                <ul>
                    <li><a href="#guide">Comment ça marche</a></li>
                    <li><a href="#">Support Technique</a></li>
                    <li><a href="#">Mentions Légales</a></li>
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
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100
        });

        function toggleReponseForm(id) {
            const form = document.getElementById('reponseForm_' + id);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                form.querySelector('textarea').focus();
            } else {
                form.style.display = 'none';
            }
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
                    doc.text('Date d\'émission : ' + dateEmission, 15, 70);
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
                    showToast('❌ Erreur lors de la génération du PDF');
                }
            }, 500);
        }

        function showToast(message) {
            const existingToast = document.querySelector('.toast');
            if (existingToast) existingToast.remove();
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>