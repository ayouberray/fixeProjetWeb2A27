
<?php
// Démarrer la session
session_start();

// SIMULATION DE LOGIN POUR TEST - À ENLEVER EN PRODUCTION
$_SESSION['user_id'] = 1;
$_SESSION['user_nom'] = 'Test';
$_SESSION['user_prenom'] = 'Utilisateur';
$_SESSION['user_type'] = 'client';

// Inclure votre classe Config
require_once __DIR__ . "/../../MODEL/config.php";

// Récupérer la connexion
$pdo = Config::getConnexion();

$user_id = $_SESSION['user_id'];
$user_nom = $_SESSION['user_nom'] ?? '';
$user_prenom = $_SESSION['user_prenom'] ?? '';
$nom_complet = trim($user_prenom . ' ' . $user_nom);

$message = $_GET['message'] ?? '';

echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes - Municipalité</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #0B1120 0%, #1A1F2E 100%);
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        /* Animation de fond */
        .background-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(79, 70, 229, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.1) 0%, transparent 50%);
            z-index: 0;
        }

        .floating-shape {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s infinite alternate;
            z-index: 0;
        }

        .shape-1 { top: -100px; left: -100px; }
        .shape-2 { bottom: -100px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.2); }
        }

        /* Conteneur principal */
        .dashboard-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* En-tête */
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 40px;
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.5);
            transform: rotate(10deg);
            transition: transform 0.3s ease;
        }

        .header-icon:hover {
            transform: rotate(0deg) scale(1.1);
        }

        .dashboard-header h1 {
            color: white;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .dashboard-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            font-weight: 500;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(79, 70, 229, 0.2);
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 15px;
            border: 1px solid rgba(79, 70, 229, 0.3);
        }

        .user-badge i {
            color: #4F46E5;
        }

        .user-badge span {
            color: white;
            font-weight: 600;
        }

        .test-mode-badge {
            background: #ffc107;
            color: #000;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        /* Barre d\'actions */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px -8px rgba(79, 70, 229, 0.5);
        }

        .btn-success {
            background: linear-gradient(145deg, #10B981, #059669);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px -8px rgba(16, 185, 129, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Filtres */
        .filters-section {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .filter-label i {
            color: #4F46E5;
        }

        .filter-select {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            min-width: 200px;
        }

        .filter-select option {
            background: #1A1F2E;
        }

        .stats-badge {
            margin-left: auto;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }

        /* Table des demandes */
        .table-wrapper {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .demandes-table {
            width: 100%;
            border-collapse: collapse;
        }

        .demandes-table thead tr {
            background: rgba(79, 70, 229, 0.1);
            border-bottom: 2px solid rgba(79, 70, 229, 0.3);
        }

        .demandes-table th {
            padding: 20px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .demandes-table td {
            padding: 20px;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .demandes-table tbody tr {
            transition: all 0.3s ease;
        }

        .demandes-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.05);
            transform: scale(1.01);
        }

        /* Badges de statut */
        .statut-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .statut-en_attente {
            background: rgba(245, 158, 11, 0.15);
            color: #F59E0B;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .statut-en_cours {
            background: rgba(59, 130, 246, 0.15);
            color: #3B82F6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .statut-traite {
            background: rgba(16, 185, 129, 0.15);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .statut-refuse {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Type badge */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Boutons d\'action */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 16px;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
        }

        .btn-view:hover {
            background: #3B82F6;
            color: white;
            border-color: #3B82F6;
        }

        .btn-edit:hover {
            background: #F59E0B;
            color: white;
            border-color: #F59E0B;
        }

        .btn-delete:hover {
            background: #EF4444;
            color: white;
            border-color: #EF4444;
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #4F46E5;
            font-size: 50px;
            border: 2px solid rgba(79, 70, 229, 0.2);
        }

        .empty-state h3 {
            color: white;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 30px;
        }

        /* Alert */
        .alert {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            padding: 18px 25px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #10B981;
            backdrop-filter: blur(10px);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert i {
            font-size: 24px;
        }

        /* Footer */
        .dashboard-footer {
            text-align: center;
            margin-top: 40px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 32px;
            }
            
            .actions-bar {
                flex-direction: column;
            }
            
            .btn-group {
                width: 100%;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
            
            .demandes-table {
                font-size: 14px;
            }
            
            .demandes-table th,
            .demandes-table td {
                padding: 15px 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Éléments d\'arrière-plan -->
    <div class="background-gradient"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <div class="header-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h1>Gestion des Demandes</h1>
            <p>Suivez et gérez vos demandes municipales</p>
            <div class="user-badge">
                <i class="fas fa-user-circle"></i>
                <span>Bienvenue, ' . htmlspecialchars($nom_complet ?: "Utilisateur") . '</span>
                <span class="test-mode-badge">
                    <i class="fas fa-flask"></i> MODE TEST
                </span>
            </div>
        </div>

        ' . ($message ? '
        <div class="alert">
            <i class="fas fa-check-circle"></i>
            <span>' . htmlspecialchars($message) . '</span>
        </div>
        ' : '') . '

        <!-- Barre d\'actions -->
        <div class="actions-bar">
            <div class="btn-group">
                <a href="ajouter_demande.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Nouvelle demande
                </a>
                <button onclick="window.location.reload()" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i>
                    Actualiser
                </button>
            </div>
            <a href="../backoffice/adminpanel.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour au tableau de bord
            </a>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="filter-label">
                <i class="fas fa-filter"></i>
                <span>Filtrer par statut :</span>
            </div>
            <select id="filtre-statut" class="filter-select" onchange="filtrerDemandes()">
                <option value="">Tous les statuts</option>
                <option value="en_attente">⏳ En attente</option>
                <option value="en_cours">🔄 En cours</option>
                <option value="traite">✅ Traité</option>
                <option value="refuse">❌ Refusé</option>
            </select>
            <div class="stats-badge" id="stats-demandes"></div>
        </div>

        <!-- Table des demandes -->
        <div class="table-wrapper">
            <table class="demandes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="demandes-tbody">';

// Récupérer les demandes
try {
    $sql = "SELECT * FROM demandes WHERE id_citoyen = :user_id ORDER BY date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $demandes = $stmt->fetchAll();
    
    if (empty($demandes)) {
        echo '
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h3>Aucune demande trouvée</h3>
                                <p>Commencez par créer votre première demande municipale</p>
                                <a href="ajouter_demande.php" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i>
                                    Créer une demande
                                </a>
                            </div>
                        </td>
                    </tr>';
    } else {
        foreach ($demandes as $demande) {
            $types = [
                'urbanisme' => '🏗️ Urbanisme',
                'voirie' => '🛣️ Voirie',
                'etat_civil' => '📜 État civil',
                'culture' => '🎭 Culture',
                'autre' => '📌 Autre'
            ];
            $type_display = $types[$demande['type_demande']] ?? ucfirst(str_replace('_', ' ', $demande['type_demande']));
            
            $statuts = [
                'en_attente' => '⏳ En attente',
                'en_cours' => '🔄 En cours',
                'traite' => '✅ Traité',
                'refuse' => '❌ Refusé'
            ];
            $statut_display = $statuts[$demande['statut']] ?? ucfirst(str_replace('_', ' ', $demande['statut']));
            
            echo '
                    <tr data-statut="' . $demande['statut'] . '">
                        <td><strong>#' . $demande['id'] . '</strong></td>
                        <td>' . htmlspecialchars($demande['titre']) . '</td>
                        <td>
                            <span class="type-badge">' . $type_display . '</span>
                        </td>
                        <td>
                            <span class="statut-badge statut-' . $demande['statut'] . '">' . $statut_display . '</span>
                        </td>
                        <td>' . date('d/m/Y H:i', strtotime($demande['date_creation'])) . '</td>
                        <td>
                            <div class="action-buttons">
                                <a href="suivi_demande.php?id=' . $demande['id'] . '" class="btn-icon btn-view" title="Voir le suivi">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="modifier_demande.php?id=' . $demande['id'] . '" class="btn-icon btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="supprimer_demande.php?id=' . $demande['id'] . '" 
                                   class="btn-icon btn-delete" 
                                   onclick="return confirm(\'⚠️ Confirmer la suppression de cette demande ?\')"
                                   title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>';
        }
    }
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') {
        echo '
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h3>Table "demandes" non trouvée</h3>
                                <p>La table des demandes n\'existe pas encore</p>
                                <button onclick="creerTableTest()" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i>
                                    Créer la table demandes
                                </button>
                            </div>
                        </td>
                    </tr>';
    } else {
        echo '
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h3>Erreur de base de données</h3>
                                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                            </div>
                        </td>
                    </tr>';
    }
}

echo '
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="dashboard-footer">
            <p>&copy; ' . date('Y') . ' Municipalité. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        function filtrerDemandes() {
            const filtre = document.getElementById("filtre-statut").value;
            const lignes = document.querySelectorAll("tbody tr[data-statut]");
            let visibleCount = 0;
            
            lignes.forEach(ligne => {
                if (!filtre || ligne.dataset.statut === filtre) {
                    ligne.style.display = "";
                    visibleCount++;
                } else {
                    ligne.style.display = "none";
                }
            });
            
            updateStats(visibleCount, lignes.length);
        }
        
        function updateStats(visible, total) {
            const statsDiv = document.getElementById("stats-demandes");
            if (total > 0) {
                statsDiv.innerHTML = `<i class="fas fa-chart-bar"></i> ${visible} demande(s) affichée(s) sur ${total}`;
            }
        }
        
        function creerTableTest() {
            if (confirm("Voulez-vous créer la table \'demandes\' avec des données de test ?")) {
                window.location.href = "creer_table_demandes.php";
            }
        }
        
        // Initialisation
        document.addEventListener("DOMContentLoaded", function() {
            const lignes = document.querySelectorAll("tbody tr[data-statut]");
            if (lignes.length > 0) {
                updateStats(lignes.length, lignes.length);
            }
            
            // Animation des cartes
            const cards = document.querySelectorAll(".table-wrapper, .filters-section");
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                
                setTimeout(() => {
                    card.style.transition = "all 0.6s ease";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 100 * (index + 1));
            });
        });
        
        // Raccourcis clavier
        document.addEventListener("keydown", function(e) {
            // Ctrl + N pour nouvelle demande
            if (e.ctrlKey && e.key === "n") {
                e.preventDefault();
                window.location.href = "ajouter_demande.php";
            }
            
            // Ctrl + R pour actualiser
            if (e.ctrlKey && e.key === "r") {
                e.preventDefault();
                window.location.reload();
            }
            
            // Échap pour retour
            if (e.key === "Escape") {
                window.location.href = "../backoffice/adminpanel.php";
            }
        });
    </script>
</body>
</html>
';
?>