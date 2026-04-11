<?php
require_once __DIR__."/../../MODEL/employer.php";
require_once __DIR__."/../../CONTROLLER/employercontroller.php";

// Démarrer la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client') {
    header("Location: client.php");
    exit();
}

$employeController = new employeController();
$user_id = $_SESSION['user_id'];

// Récupérer les informations complètes de l'utilisateur
$db = Config::getConnexion();
$sql = "SELECT * FROM employe WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $user_id);
$req->execute();
$user = $req->fetch(PDO::FETCH_ASSOC);

// Statistiques
$date_embauche = new DateTime($user['anneEmbauche']);
$now = new DateTime();
$anciennete = $date_embauche->diff($now)->y;

// Gérer la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: client.php");
    exit();
}

echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            background: linear-gradient(145deg, #f6f9fc 0%, #e9f1f8 100%);
            min-height: 100vh;
        }

        /* Barre de navigation */
        .navbar {
            background: white;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .logo i {
            font-size: 28px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            background: #f8fafd;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-info:hover {
            background: #e9f1f8;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-details {
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            color: #333;
        }

        .user-role {
            font-size: 12px;
            color: #999;
        }

        .logout-btn {
            padding: 8px 20px;
            background: #fee;
            color: #f44;
            border-radius: 50px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #fdd;
        }

        /* Conteneur principal */
        .main-container {
            max-width: 1400px;
            margin: 100px auto 30px;
            padding: 0 30px;
        }

        /* En-tête de bienvenue */
        .welcome-header {
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 30px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 20px 40px -10px rgba(102, 126, 234, 0.3);
        }

        .welcome-text h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-text p {
            opacity: 0.9;
            font-size: 16px;
        }

        .date-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
        }

        /* Cartes de statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(145deg, #667eea20, #764ba220);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 28px;
        }

        .stat-info h3 {
            color: #999;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        /* Grille principale */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        /* Carte de profil */
        .profile-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
        }

        .profile-title h2 {
            color: #333;
            margin-bottom: 5px;
        }

        .profile-title p {
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8fafd;
            border-radius: 18px;
        }

        .info-label {
            color: #999;
            font-size: 13px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
            font-weight: 600;
        }

        /* Carte des actions rapides */
        .actions-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .actions-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            color: #333;
        }

        .actions-header i {
            color: #667eea;
            font-size: 24px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .action-btn {
            padding: 20px;
            background: #f8fafd;
            border-radius: 18px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: linear-gradient(145deg, #667eea, #764ba2);
            transform: translateY(-5px);
        }

        .action-btn:hover i,
        .action-btn:hover span {
            color: white;
        }

        .action-btn i {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 10px;
            transition: color 0.3s;
        }

        .action-btn span {
            display: block;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        /* Section des documents */
        .documents-section {
            background: white;
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }

        .view-all {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .document-card {
            padding: 20px;
            background: #f8fafd;
            border-radius: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
        }

        .document-card:hover {
            background: #e9f1f8;
            transform: translateX(5px);
        }

        .document-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 24px;
        }

        .document-info h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .document-info p {
            color: #999;
            font-size: 12px;
        }

        /* Activité récente */
        .activity-section {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .activity-list {
            margin-top: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            background: #4caf50;
            border-radius: 50%;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: #333;
            margin-bottom: 5px;
        }

        .activity-time {
            color: #999;
            font-size: 12px;
        }

        .activity-icon {
            color: #999;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 20px;
            }

            .welcome-header {
                flex-direction: column;
                text-align: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .nav-container {
                flex-direction: column;
                gap: 15px;
            }

            .user-menu {
                width: 100%;
                justify-content: center;
            }
        }

        /* Notifications */
        .notification-badge {
            position: relative;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f44;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
        }

        /* Loading spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f0f0f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 50px auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-building"></i>
                <span>MonEspace</span>
            </div>
            
            <div class="user-menu">
                <div class="user-info" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        ' . strtoupper(substr($user['prenom'], 0, 1)) . substr($user['nom'], 0, 1) . '
                    </div>
                    <div class="user-details">
                        <div class="user-name">' . htmlspecialchars($user['prenom'] . ' ' . $user['nom']) . '</div>
                        <div class="user-role">Membre depuis ' . $user['anneEmbauche'] . '</div>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #999; font-size: 12px;"></i>
                </div>
                
                <a href="?logout=1" class="logout-btn" onclick="return confirm(\'Êtes-vous sûr de vouloir vous déconnecter ?\')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- En-tête de bienvenue -->
        <div class="welcome-header">
            <div class="welcome-text">
                <h1>Bonjour, ' . htmlspecialchars($user['prenom']) . ' ! 👋</h1>
                <p>Bienvenue dans votre espace personnel</p>
            </div>
            <div class="date-badge">
                <i class="fas fa-calendar"></i>
                <span>' . date('d F Y') . '</span>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-info">
                    <h3>Identifiant</h3>
                    <div class="stat-number">#' . $user['id'] . '</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>Ancienneté</h3>
                    <div class="stat-number">' . $anciennete . ' ans</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>Salaire</h3>
                    <div class="stat-number">' . number_format($user['salaire'], 0, ',', ' ') . ' €</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Embauche</h3>
                    <div class="stat-number">' . $user['anneEmbauche'] . '</div>
                </div>
            </div>
        </div>

        <!-- Grille principale -->
        <div class="main-grid">
            <!-- Carte de profil -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-title">
                        <h2>Profil personnel</h2>
                        <p><i class="fas fa-shield-alt"></i> Informations confidentielles</p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Nom
                        </div>
                        <div class="info-value">' . htmlspecialchars($user['nom']) . '</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Prénom
                        </div>
                        <div class="info-value">' . htmlspecialchars($user['prenom']) . '</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-id-badge"></i> ID
                        </div>
                        <div class="info-value">#' . $user['id'] . '</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Année d\'embauche
                        </div>
                        <div class="info-value">' . $user['anneEmbauche'] . '</div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="actions-card">
                <div class="actions-header">
                    <i class="fas fa-bolt"></i>
                    <h2>Actions rapides</h2>
                </div>

                <div class="actions-grid">
                    <a href="modifier_profil.php" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>Modifier profil</span>
                    </a>

                    <a href="documents.php" class="action-btn">
                        <i class="fas fa-file-alt"></i>
                        <span>Mes documents</span>
                    </a>

                    <a href="historique.php" class="action-btn">
                        <i class="fas fa-history"></i>
                        <span>Historique</span>
                    </a>

                    <a href="parametres.php" class="action-btn">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>

                    <a href="messagerie.php" class="action-btn notification-badge">
                        <i class="fas fa-envelope"></i>
                        <span>Messagerie</span>
                        <span class="badge">3</span>
                    </a>

                    <a href="support.php" class="action-btn">
                        <i class="fas fa-headset"></i>
                        <span>Support</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Section documents -->
        <div class="documents-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-folder-open" style="color: #667eea;"></i>
                    Documents récents
                </h2>
                <a href="documents.php" class="view-all">Voir tous <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="documents-grid">
                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="document-info">
                        <h4>Contrat de travail</h4>
                        <p>Ajouté le 15/03/2024</p>
                    </div>
                </div>

                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="document-info">
                        <h4>Fiche de paie Mars</h4>
                        <p>Ajouté le 01/04/2024</p>
                    </div>
                </div>

                <div class="document-card">
                    <div class="document-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="document-info">
                        <h4>Convention collective</h4>
                        <p>Mis à jour récemment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="activity-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-clock" style="color: #667eea;"></i>
                    Activité récente
                </h2>
                <a href="historique.php" class="view-all">Historique <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Connexion à votre espace client</div>
                        <div class="activity-time">Il y a 5 minutes</div>
                    </div>
                    <i class="fas fa-sign-in-alt activity-icon"></i>
                </div>

                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Consultation de votre fiche de paie</div>
                        <div class="activity-time">Il y a 2 heures</div>
                    </div>
                    <i class="fas fa-file-invoice activity-icon"></i>
                </div>

                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Mise à jour des informations personnelles</div>
                        <div class="activity-time">Il y a 3 jours</div>
                    </div>
                    <i class="fas fa-user-edit activity-icon"></i>
                </div>

                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Téléchargement du contrat de travail</div>
                        <div class="activity-time">Il y a 1 semaine</div>
                    </div>
                    <i class="fas fa-download activity-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menu utilisateur
        function toggleUserMenu() {
            // À implémenter pour un menu déroulant
            console.log("Menu utilisateur");
        }

        // Notification toast
        function showNotification(message, type = "success") {
            const notification = document.createElement("div");
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === "success" ? "#4caf50" : "#f44"};
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            notification.innerHTML = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = "slideOut 0.3s ease";
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Animation de bienvenue
        document.addEventListener("DOMContentLoaded", () => {
            showNotification("Bienvenue " + "' . $user['prenom'] . '", "success");
        });

        // Raccourcis clavier
        document.addEventListener("keydown", (e) => {
            // Ctrl + D pour aller aux documents
            if (e.ctrlKey && e.key === "d") {
                e.preventDefault();
                window.location.href = "documents.php";
            }
            
            // Ctrl + P pour aller au profil
            if (e.ctrlKey && e.key === "p") {
                e.preventDefault();
                window.location.href = "modifier_profil.php";
            }
            
            // Ctrl + M pour aller à la messagerie
            if (e.ctrlKey && e.key === "m") {
                e.preventDefault();
                window.location.href = "messagerie.php";
            }
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</body>
</html>
';
?>