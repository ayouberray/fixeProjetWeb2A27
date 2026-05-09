<?php
require_once __DIR__ . '/../../MODEL/config.php';

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = Config::getConnexion();

// Récupérer tous les citoyens avec leurs statistiques
$sql = "SELECT 
            c.*,
            COUNT(d.id_demande) as nb_demandes,
            SUM(CASE WHEN d.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN d.statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
            SUM(CASE WHEN d.statut = 'traite' THEN 1 ELSE 0 END) as traite,
            SUM(CASE WHEN d.statut = 'refuse' THEN 1 ELSE 0 END) as refuse,
            MAX(d.id_demande) as derniere_demande_id,
            MAX(d.date_creation) as derniere_demande,
            (SELECT COUNT(*) FROM reponse_demandes r WHERE r.id_citoyen = c.id_citoyen AND r.est_lu = 0 AND r.expediteur = 'citoyen') as reponses_non_lues,
            (SELECT COUNT(*) FROM reponse_demandes r WHERE r.id_citoyen = c.id_citoyen) as total_reponses
        FROM citoyens c
        LEFT JOIN demandes d ON c.id_citoyen = d.id_citoyen
        GROUP BY c.id_citoyen
        ORDER BY nb_demandes DESC";

$clients = $db->query($sql)->fetchAll();

// Stats globales
$totalClients = count($clients);
$totalDemandes = array_sum(array_column($clients, 'nb_demandes'));
$message = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

$user_initials = 'AD';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Liste des Clients</title>
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
            --gray-900: #2D3A4B;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .avatar {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-info h4 { font-weight: 600; color: white; font-size: 0.95rem; }
        .user-info p { font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); }

        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: var(--transition-base);
            font-weight: 500;
        }

        .nav-link:hover { background: rgba(255, 255, 255, 0.15); color: white; transform: translateX(5px); }
        .nav-link.active { background: rgba(255, 255, 255, 0.2); color: white; }
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

        .page-title { font-size: 1.8rem; font-weight: 800; color: var(--dark); }
        .page-subtitle { color: var(--gray-500); margin-top: 0.25rem; font-size: 0.9rem; }

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

        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -8px rgba(0,109,91,0.5); }
        .btn-sm { padding: 6px 14px; font-size: 12px; }

        /* ========== STATS ========== */
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-mini-card {
            background: var(--white);
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            text-align: center;
            border-bottom: 3px solid var(--primary);
        }

        .stat-mini-value { font-size: 2rem; font-weight: 800; color: var(--primary); }
        .stat-mini-label { font-size: 0.85rem; color: var(--gray-500); margin-top: 0.25rem; }

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

        /* ========== CLIENTS TABLE ========== */
        .clients-section {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .section-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid var(--gray-300);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 { font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }

        .search-input {
            padding: 0.6rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            background: var(--white);
            transition: var(--transition-base);
            font-family: 'Inter', sans-serif;
            width: 250px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.15);
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
            padding: 1rem 1.2rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--gray-300);
            font-size: 0.9rem;
        }

        .table tbody tr:hover td { background: var(--primary-light); }

        /* ========== BADGES ========== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success { background: #D1FAE5; color: #059669; }
        .badge-warning { background: #FEF3C7; color: #D97706; }
        .badge-info { background: #DBEAFE; color: #2563EB; }
        .badge-danger { background: #FEE2E2; color: #DC2626; }

        .action-group { display: flex; gap: 0.5rem; justify-content: center; }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-700);
            background: var(--gray-100);
            transition: var(--transition-base);
            position: relative;
        }

        .action-icon:hover { background: var(--primary); color: white; transform: translateY(-2px); }

        .action-icon.reply-icon {
            color: #059669;
            background: #D1FAE5;
        }

        .action-icon.reply-icon:hover {
            background: #059669;
            color: white;
        }

        .empty-state { text-align: center; padding: 4rem 2rem; }
        .empty-icon { font-size: 4rem; color: var(--gray-300); margin-bottom: 1rem; }
        .empty-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--dark); }
        .empty-text { color: var(--gray-500); font-size: 0.9rem; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .stats-mini { grid-template-columns: 1fr; }
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
                    <h1 class="page-title">Liste des Clients</h1>
                    <p class="page-subtitle">Gérez l'ensemble des citoyens inscrits sur la plateforme</p>
                </div>
            </div>

            <!-- STATS MINI -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <div class="stat-mini-value"><?= $totalClients ?></div>
                    <div class="stat-mini-label"><i class="fas fa-users"></i> Clients inscrits</div>
                </div>
                <div class="stat-mini-card">
                    <div class="stat-mini-value"><?= $totalDemandes ?></div>
                    <div class="stat-mini-label"><i class="fas fa-clipboard-list"></i> Total demandes</div>
                </div>
                <div class="stat-mini-card">
                    <div class="stat-mini-value"><?= $totalClients > 0 ? round($totalDemandes / $totalClients, 1) : 0 ?></div>
                    <div class="stat-mini-label"><i class="fas fa-chart-bar"></i> Moy. demandes/client</div>
                </div>
            </div>

            <!-- MESSAGES -->
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- TABLE CLIENTS -->
            <div class="clients-section">
                <div class="section-header">
                    <h2><i class="fas fa-users" style="color: var(--primary);"></i> Clients (<?= $totalClients ?>)</h2>
                    <input type="text" id="searchInput" class="search-input" placeholder="🔍 Rechercher un client...">
                </div>

                <div class="table-wrapper">
                    <?php if (empty($clients)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <h3 class="empty-title">Aucun client trouvé</h3>
                            <p class="empty-text">Aucun citoyen n'est encore inscrit sur la plateforme.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Demandes</th>
                                    <th>En attente</th>
                                    <th>En cours</th>
                                    <th>Traitees</th>
                                    <th>Dernière demande</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="clientsTableBody">
                                <?php foreach ($clients as $client): ?>
                                    <tr data-search="<?= strtolower($client['nom'] . ' ' . $client['prenom'] . ' ' . ($client['email'] ?? '')) ?>">
                                        <td><strong>#<?= $client['id_citoyen'] ?></strong></td>
                                        <td>
                                            <div style="font-weight: 600;">
                                                <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                                            </div>
                                            <?php if ($client['adresse']): ?>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($client['adresse']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($client['email']): ?>
                                                <div style="font-size: 0.85rem;">
                                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($client['email']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($client['telephone']): ?>
                                                <div style="font-size: 0.85rem;">
                                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($client['telephone']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!$client['email'] && !$client['telephone']): ?>
                                                <span style="color: var(--gray-500); font-size: 0.85rem;">Non renseigné</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <i class="fas fa-clipboard-list"></i> <?= $client['nb_demandes'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($client['en_attente'] > 0): ?>
                                                <span class="badge badge-warning">⏳ <?= $client['en_attente'] ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500);">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($client['en_cours'] > 0): ?>
                                                <span class="badge badge-info">🔄 <?= $client['en_cours'] ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500);">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($client['traite'] > 0): ?>
                                                <span class="badge badge-success">✅ <?= $client['traite'] ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500);">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($client['derniere_demande']): ?>
                                                <span style="font-size: 0.85rem;">
                                                    <?= date('d/m/Y', strtotime($client['derniere_demande'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--gray-500);">Aucune</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-group">
                                                <!-- VOIR DÉTAILS -->
                                                <a href="detail_client.php?id=<?= $client['id_citoyen'] ?>" class="action-icon" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- VOIR RÉPONSES (redirige vers suivi_reponses avec la dernière demande) -->
                                                <?php if ($client['derniere_demande_id']): ?>
                                                    <a href="suivi_reponses.php?id_demande=<?= $client['derniere_demande_id'] ?>" class="action-icon reply-icon" title="Voir les réponses">
                                                        <i class="fas fa-reply"></i>
                                                        <?php if ($client['total_reponses'] > 0): ?>
                                                            <span style="position: absolute; top: -6px; right: -6px; background: #059669; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 9px; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                                                <?= $client['total_reponses'] ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="action-icon" title="Aucune demande" style="opacity: 0.4; cursor: not-allowed;">
                                                        <i class="fas fa-reply"></i>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <!-- ENVOYER EMAIL -->
                                                <a href="mailto:<?= htmlspecialchars($client['email'] ?? '') ?>" class="action-icon" title="Envoyer email">
                                                    <i class="fas fa-envelope"></i>
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

    <script>
        // Recherche en temps réel
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#clientsTableBody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            rows.forEach(row => {
                const searchText = row.dataset.search || '';
                row.style.display = searchText.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>