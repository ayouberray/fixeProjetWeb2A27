<?php
require_once __DIR__ . '/../../MODEL/config.php';

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_citoyen = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_citoyen) {
    header('Location: liste_clients.php?error=ID client invalide');
    exit();
}

$db = Config::getConnexion();

// Récupérer les infos du client
$stmt = $db->prepare("SELECT * FROM citoyens WHERE id_citoyen = ?");
$stmt->execute([$id_citoyen]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: liste_clients.php?error=Client introuvable');
    exit();
}

// Récupérer les demandes du client
$stmt = $db->prepare("SELECT d.*, s.nom_service,
                       DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.id_citoyen = ?
                ORDER BY d.date_creation DESC");
$stmt->execute([$id_citoyen]);
$demandes = $stmt->fetchAll();

// Statistiques du client
$stats = [
    'total' => count($demandes),
    'en_attente' => count(array_filter($demandes, fn($d) => $d['statut'] === 'en_attente')),
    'en_cours' => count(array_filter($demandes, fn($d) => $d['statut'] === 'en_cours')),
    'traite' => count(array_filter($demandes, fn($d) => $d['statut'] === 'traite')),
    'refuse' => count(array_filter($demandes, fn($d) => $d['statut'] === 'refuse')),
];

// Récupérer les réponses/conversations
$stmt = $db->prepare("SELECT COUNT(*) FROM reponse_demandes WHERE id_citoyen = ?");
$stmt->execute([$id_citoyen]);
$nbReponses = $stmt->fetchColumn();

// Dernière activité
$stmt = $db->prepare("SELECT MAX(date_creation) FROM demandes WHERE id_citoyen = ?");
$stmt->execute([$id_citoyen]);
$derniereActivite = $stmt->fetchColumn();

$message = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$user_initials = 'AD';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Détail Client #<?= $id_citoyen ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
            --dark: #1A2C3E;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px -2px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            color: var(--dark);
            line-height: 1.6;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #006D5B 0%, #004D3D 100%);
            position: fixed;
            height: 100vh;
            padding: 2rem 1.5rem;
            box-shadow: var(--shadow-lg);
            overflow-y: auto;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .logo-text { font-size: 1.4rem; font-weight: 800; color: white; }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .avatar {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .user-info h4 { font-weight: 600; color: white; font-size: 0.95rem; }
        .user-info p { font-size: 0.8rem; color: rgba(255,255,255,0.7); }

        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: var(--transition-base);
            font-weight: 500;
        }

        .nav-link:hover { background: rgba(255,255,255,0.15); color: white; transform: translateX(5px); }
        .nav-link.active { background: rgba(255,255,255,0.2); color: white; }
        .nav-link i { width: 24px; text-align: center; }

        /* ========== MAIN ========== */
        .main {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        /* ========== TOP BAR ========== */
        .top-bar {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: var(--shadow-md);
            border-bottom: 3px solid var(--primary);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-base);
        }

        .back-btn:hover { color: var(--primary); transform: translateX(-3px); }

        .page-title { font-size: 1.5rem; font-weight: 800; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .btn-primary:hover { transform: translateY(-2px); }

        .btn-sm { padding: 6px 14px; font-size: 12px; }

        /* ========== ALERTES ========== */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .alert-success { background: #D1FAE5; color: #059669; border-left-color: #059669; }
        .alert-danger { background: #FEE2E2; color: #DC2626; border-left-color: #DC2626; }

        /* ========== PROFILE CARD ========== */
        .profile-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: var(--shadow-primary);
        }

        .profile-info {
            flex: 1;
            min-width: 250px;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--gray-700);
        }

        .detail-item i {
            width: 20px;
            color: var(--primary);
            text-align: center;
        }

        /* ========== STATS MINI ========== */
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-mini-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            text-align: center;
            border-bottom: 3px solid var(--primary);
            transition: var(--transition-base);
        }

        .stat-mini-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-mini-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-mini-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        /* ========== TABLES ========== */
        .card-section {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }

        .card-section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid var(--gray-300);
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-wrapper { overflow-x: auto; }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead tr {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-300);
            font-size: 0.85rem;
        }

        .table tbody tr:hover td { background: var(--primary-light); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-en_attente { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traite { background: #D1FAE5; color: #059669; }
        .badge-refuse { background: #FEE2E2; color: #DC2626; }

        .action-icon {
            width: 30px;
            height: 30px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-700);
            background: var(--gray-100);
            transition: var(--transition-base);
        }

        .action-icon:hover { background: var(--primary); color: white; transform: translateY(-2px); }

        .empty-state { text-align: center; padding: 3rem 2rem; }
        .empty-icon { font-size: 3rem; color: var(--gray-300); margin-bottom: 0.5rem; }
        .empty-text { color: var(--gray-500); font-size: 0.9rem; }

        @media (max-width: 1024px) {
            .stats-mini { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .stats-mini { grid-template-columns: repeat(2, 1fr); }
            .profile-card { flex-direction: column; align-items: center; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <a href="index.php" class="logo">
                <div class="logo-icon">IG</div>
                <span class="logo-text">InnoGov</span>
            </a>
            <div class="user-profile">
                <div class="avatar"><?= $user_initials ?></div>
                <div class="user-info">
                    <h4>Administrateur</h4>
                </div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="liste_clients.php" class="nav-link active">
                        <i class="fas fa-users"></i> Liste des clients
                    </a>
                </li>
                <li class="nav-item">
                    <a href="ajouter_demande.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Ajouter une demande
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-alt"></i> Rendez-vous
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN -->
        <main class="main">
            <div class="top-bar">
                <div>
                    <a href="liste_clients.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                    <h1 class="page-title" style="margin-top: 0.5rem;">
                        Détail Client #<?= $id_citoyen ?>
                    </h1>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="mailto:<?= htmlspecialchars($client['email'] ?? '') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-envelope"></i> Contacter
                    </a>
                </div>
            </div>

            <!-- MESSAGES -->
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- PROFIL -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <?= strtoupper(substr($client['prenom'], 0, 1) . substr($client['nom'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2 class="profile-name"><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></h2>
                    <div class="profile-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <span><?= htmlspecialchars($client['email'] ?? 'Non renseigné') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-phone"></i>
                            <span><?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($client['adresse'] ?? 'Non renseignée') ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Inscrit le <?= date('d/m/Y', strtotime($client['date_inscription'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span>Dernière activité : <?= $derniereActivite ? date('d/m/Y', strtotime($derniereActivite)) : 'Aucune' ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-comments"></i>
                            <span><?= $nbReponses ?> messages échangés</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIQUES -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <div class="stat-mini-value"><?= $stats['total'] ?></div>
                    <div class="stat-mini-label"><i class="fas fa-clipboard-list"></i> Total</div>
                </div>
                <div class="stat-mini-card" style="border-bottom-color: #D97706;">
                    <div class="stat-mini-value" style="color: #D97706;"><?= $stats['en_attente'] ?></div>
                    <div class="stat-mini-label">⏳ En attente</div>
                </div>
                <div class="stat-mini-card" style="border-bottom-color: #2563EB;">
                    <div class="stat-mini-value" style="color: #2563EB;"><?= $stats['en_cours'] ?></div>
                    <div class="stat-mini-label">🔄 En cours</div>
                </div>
                <div class="stat-mini-card" style="border-bottom-color: #059669;">
                    <div class="stat-mini-value" style="color: #059669;"><?= $stats['traite'] ?></div>
                    <div class="stat-mini-label">✅ Traitées</div>
                </div>
                <div class="stat-mini-card" style="border-bottom-color: #DC2626;">
                    <div class="stat-mini-value" style="color: #DC2626;"><?= $stats['refuse'] ?></div>
                    <div class="stat-mini-label">❌ Refusées</div>
                </div>
            </div>

            <!-- DEMANDES -->
            <div class="card-section">
                <div class="card-section-header">
                    <i class="fas fa-clipboard-list" style="color: var(--primary);"></i>
                    Demandes (<?= $stats['total'] ?>)
                </div>
                <div class="table-wrapper">
                    <?php if (empty($demandes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p class="empty-text">Aucune demande trouvée pour ce client.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Titre</th>
                                    <th>Service</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($demandes as $d): ?>
                                    <tr>
                                        <td><strong>#<?= str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($d['titre']) ?></div>
                                            <div style="font-size: 0.7rem; color: var(--gray-500);">
                                                <?= substr(htmlspecialchars($d['description']), 0, 60) ?>...
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($d['nom_service'] ?? 'N/A') ?></td>
                                        <td><span style="font-size: 0.8rem;"><?= htmlspecialchars($d['type_demande']) ?></span></td>
                                        <td>
                                            <?php
                                            $badgeClass = match($d['statut']) {
                                                'traite' => 'badge-traite',
                                                'en_cours' => 'badge-en_cours',
                                                'refuse' => 'badge-refuse',
                                                default => 'badge-en_attente'
                                            };
                                            $statutText = match($d['statut']) {
                                                'traite' => '✅ Traité',
                                                'en_cours' => '🔄 En cours',
                                                'refuse' => '❌ Refusé',
                                                default => '⏳ En attente'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $statutText ?></span>
                                        </td>
                                        <td><span style="font-size: 0.8rem;"><?= $d['date_formatee'] ?></span></td>
                                        <td>
                                            <div style="display: flex; gap: 0.25rem; justify-content: center;">
                                                <a href="suivi_demande.php?id=<?= $d['id_demande'] ?>" class="action-icon" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="modifier_demande.php?id=<?= $d['id_demande'] ?>" class="action-icon" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>