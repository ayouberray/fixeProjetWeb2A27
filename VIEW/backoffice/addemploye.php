<?php
// Simulons quelques données pour les statistiques (à remplacer par vos vraies données)
$totalEmployes = 25; // À récupérer de votre base de données
$nouveauxEmployes = 3; // Employés récents
$departements = 5; // Nombre de départements

echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
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

        /* Animation de fond améliorée */
        .background-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            z-index: 0;
        }

        /* Grille de fond */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
        }

        /* Particules animées */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            pointer-events: none;
        }

        @keyframes floatParticle {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-30px) translateX(15px); }
            50% { transform: translateY(-50px) translateX(-15px); }
            75% { transform: translateY(-20px) translateX(25px); }
        }

        /* Formes flottantes */
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
        .shape-3 { top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; height: 400px; animation-delay: -10s; }

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
            position: relative;
        }

        .header-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 45px;
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.5);
            transform: rotate(10deg);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .header-icon::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            20%, 100% { transform: translateX(100%) rotate(45deg); }
        }

        .header-icon:hover {
            transform: rotate(0deg) scale(1.1);
            box-shadow: 0 30px 50px -10px rgba(79, 70, 229, 0.7);
        }

        .dashboard-header h1 {
            color: white;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            background: linear-gradient(145deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dashboard-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
            font-weight: 500;
        }

        /* Cartes de statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(79, 70, 229, 0.3);
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.3);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(145deg, rgba(79, 70, 229, 0.2), rgba(124, 58, 237, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4F46E5;
            font-size: 28px;
            border: 2px solid rgba(79, 70, 229, 0.2);
        }

        .stat-info h3 {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info .stat-value {
            color: white;
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-info .stat-change {
            color: #10B981;
            font-size: 13px;
            font-weight: 500;
        }

        /* Grille des cartes principales */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Cartes d\'administration */
        .admin-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .admin-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: left 0.5s ease;
        }

        .admin-card:hover::before {
            left: 100%;
        }

        .admin-card:hover {
            transform: translateY(-10px);
            border-color: rgba(79, 70, 229, 0.3);
            box-shadow: 0 30px 60px -20px rgba(79, 70, 229, 0.6);
        }

        .card-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(145deg, rgba(79, 70, 229, 0.2), rgba(124, 58, 237, 0.1));
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #4F46E5;
            font-size: 45px;
            border: 2px solid rgba(79, 70, 229, 0.2);
            transition: all 0.4s ease;
        }

        .admin-card:hover .card-icon {
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            color: white;
            border-color: transparent;
            transform: scale(1.1) rotate(8deg);
            box-shadow: 0 20px 30px -10px rgba(79, 70, 229, 0.5);
        }

        .card-title {
            color: white;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .card-description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .card-badges {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .badge {
            background: rgba(79, 70, 229, 0.15);
            color: #4F46E5;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid rgba(79, 70, 229, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .badge i {
            font-size: 13px;
        }

        .admin-card:hover .badge {
            background: rgba(79, 70, 229, 0.25);
            transform: scale(1.05);
        }

        /* Badge spécial pour suppression */
        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .badge-danger:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        /* Carte de retour */
        .return-section {
            display: flex;
            justify-content: center;
            margin: 40px 0;
        }

        .return-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.03);
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.4s ease;
            text-align: center;
        }

        .return-card .card-icon {
            width: 70px;
            height: 70px;
            font-size: 30px;
            color: #10B981;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .return-card:hover .card-icon {
            background: linear-gradient(145deg, #10B981, #059669);
            color: white;
        }

        .return-card:hover {
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 30px 50px -20px rgba(16, 185, 129, 0.3);
        }

        .return-card .card-title {
            font-size: 22px;
            margin-bottom: 10px;
        }

        /* Activité récente */
        .recent-activity {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 30px;
            padding: 30px;
            margin-top: 40px;
        }

        .activity-title {
            color: white;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-title i {
            color: #4F46E5;
            font-size: 24px;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(5px);
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            background: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 15px #10B981;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: white;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .activity-time {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }

        /* Pied de page */
        .dashboard-footer {
            text-align: center;
            margin-top: 60px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            padding: 20px 0;
        }

        /* Loading spinner */
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top-color: #4F46E5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Notification */
        .floating-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(16, 185, 129, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 16px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
            animation: slideInRight 0.5s ease;
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .floating-notification i {
            font-size: 22px;
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
        }

        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            white-space: nowrap;
            margin-bottom: 10px;
            z-index: 100;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 36px;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-card {
                padding: 30px 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .floating-notification {
                left: 20px;
                right: 20px;
                bottom: 20px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Éléments d\'arrière-plan -->
    <div class="background-gradient"></div>
    <div class="grid-overlay"></div>
    <div class="particles" id="particles"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <div class="header-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h1>Tableau de Bord Administrateur</h1>
            <p>Gérez efficacement votre espace d\'administration</p>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total employés</h3>
                    <div class="stat-value">' . $totalEmployes . '</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +12% ce mois</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <h3>Nouveaux</h3>
                    <div class="stat-value">' . $nouveauxEmployes . '</div>
                    <div class="stat-change">Cette semaine</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <h3>Départements</h3>
                    <div class="stat-value">' . $departements . '</div>
                    <div class="stat-change">Actifs</div>
                </div>
            </div>
        </div>

        <!-- Grille des cartes principales -->
        <div class="cards-grid">
            <!-- Carte Affichage -->
            <div class="admin-card" onclick="window.location.href=\'listemploye.php\'" data-tooltip="Voir tous les employés">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="card-title">Liste des employés</h3>
                <p class="card-description">Consultez et gérez tous les employés de votre entreprise</p>
                <div class="card-badges">
                    <span class="badge"><i class="fas fa-eye"></i>Visualiser</span>
                    <span class="badge"><i class="fas fa-chart-bar"></i>Détails</span>
                    <span class="badge"><i class="fas fa-download"></i>Export</span>
                </div>
            </div>

            <!-- Carte Ajout -->
            <div class="admin-card" onclick="window.location.href=\'../backoffice/addemploye.php\'" data-tooltip="Ajouter un nouvel employé">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="card-title">Ajouter un employé</h3>
                <p class="card-description">Intégrez un nouveau membre à votre équipe facilement</p>
                <div class="card-badges">
                    <span class="badge"><i class="fas fa-plus-circle"></i>Nouveau</span>
                    <span class="badge"><i class="fas fa-user-graduate"></i>Onboarding</span>
                    <span class="badge"><i class="fas fa-file"></i>Formulaire</span>
                </div>
            </div>

            <!-- Carte Suppression -->
            <div class="admin-card" onclick="window.location.href=\'../backoffice/deleteemploye.php\'" data-tooltip="Supprimer un employé">
                <div class="card-icon">
                    <i class="fas fa-user-minus"></i>
                </div>
                <h3 class="card-title">Supprimer un employé</h3>
                <p class="card-description">Retirez un employé de la base de données</p>
                <div class="card-badges">
                    <span class="badge badge-danger"><i class="fas fa-trash-alt"></i>Supprimer</span>
                    <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i>Attention</span>
                </div>
            </div>
        </div>

        <!-- Section retour -->
        <div class="return-section">
            <div class="return-card" onclick="window.location.href=\'../frontoffice/adminpanel.php\'" data-tooltip="Retour à l\'accueil">
                <div class="card-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h3 class="card-title">Retour à l\'accueil</h3>
                <p class="card-description">Revenir au tableau de bord principal</p>
                <div class="card-badges">
                    <span class="badge"><i class="fas fa-arrow-left"></i>Retour</span>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="recent-activity">
            <div class="activity-title">
                <i class="fas fa-clock"></i>
                <span>Activité récente</span>
            </div>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Nouvel employé ajouté : Jean Dupont</div>
                        <div class="activity-time">Il y a 5 minutes</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Mise à jour des informations de Marie Martin</div>
                        <div class="activity-time">Il y a 2 heures</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <div class="activity-text">Export de la liste des employés</div>
                        <div class="activity-time">Il y a 3 heures</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="dashboard-footer">
            <p>&copy; 2024 Administration. Tous droits réservés. | Version 2.0</p>
        </div>
    </div>

    <!-- Notification flottante -->
    <div class="floating-notification" id="welcomeNotification">
        <i class="fas fa-bell"></i>
        <div>
            <div style="font-weight: 700;">Bienvenue !</div>
            <div style="font-size: 13px; opacity: 0.9;">Dernière connexion : ' . date('d/m/Y H:i') . '</div>
        </div>
    </div>

    <script>
        // Création des particules
        function createParticles() {
            const particlesContainer = document.getElementById("particles");
            const numberOfParticles = 50;

            for (let i = 0; i < numberOfParticles; i++) {
                const particle = document.createElement("div");
                particle.className = "particle";
                
                particle.style.left = Math.random() * 100 + "%";
                particle.style.top = Math.random() * 100 + "%";
                
                const size = Math.random() * 6 + 2;
                particle.style.width = size + "px";
                particle.style.height = size + "px";
                
                particle.style.opacity = Math.random() * 0.5 + 0.2;
                
                const duration = Math.random() * 20 + 15;
                const delay = Math.random() * 5;
                particle.style.animation = `floatParticle ${duration}s infinite ease-in-out`;
                particle.style.animationDelay = delay + "s";
                
                particlesContainer.appendChild(particle);
            }
        }

        // Animation d\'apparition des cartes
        document.addEventListener("DOMContentLoaded", function() {
            createParticles();
            
            const cards = document.querySelectorAll(".admin-card, .stat-card, .activity-item");
            
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                
                setTimeout(() => {
                    card.style.transition = "all 0.6s ease";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 100 * (index + 1));
            });

            // Notification de bienvenue
            setTimeout(() => {
                const notification = document.getElementById("welcomeNotification");
                notification.style.opacity = "0";
                notification.style.transform = "translateX(100%)";
                setTimeout(() => {
                    notification.style.display = "none";
                }, 500);
            }, 5000);
        });

        // Raccourcis clavier
        document.addEventListener("keydown", function(e) {
            if (e.ctrlKey && e.key === "h") {
                e.preventDefault();
                window.location.href = "../frontoffice/adminpanel.php";
            }
            if (e.ctrlKey && e.key === "l") {
                e.preventDefault();
                window.location.href = "listemploye.php";
            }
            if (e.ctrlKey && e.key === "a") {
                e.preventDefault();
                window.location.href = "../backoffice/addemploye.php";
            }
            if (e.ctrlKey && e.key === "d") {
                e.preventDefault();
                window.location.href = "../backoffice/deleteemploye.php";
            }
        });

        // Confirmation pour la suppression
        const deleteCard = document.querySelector(".admin-card[onclick*=\'deleteemploye\']");
        if (deleteCard) {
            deleteCard.addEventListener("click", function(e) {
                if (!confirm("⚠️ Êtes-vous sûr de vouloir accéder à la page de suppression ?")) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }

        // Effet de ripple au clic
        document.addEventListener("click", function(e) {
            const ripple = document.createElement("div");
            ripple.style.position = "fixed";
            ripple.style.left = e.clientX + "px";
            ripple.style.top = e.clientY + "px";
            ripple.style.width = "20px";
            ripple.style.height = "20px";
            ripple.style.background = "radial-gradient(circle, #4F46E5, transparent)";
            ripple.style.borderRadius = "50%";
            ripple.style.pointerEvents = "none";
            ripple.style.zIndex = "9999";
            ripple.style.transform = "translate(-50%, -50%)";
            ripple.style.animation = "ripple 1s ease-out";
            document.body.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 1000);
        });

        // Style pour l\'animation de ripple
        const style = document.createElement("style");
        style.textContent = `
            @keyframes ripple {
                0% {
                    transform: translate(-50%, -50%) scale(0);
                    opacity: 1;
                }
                100% {
                    transform: translate(-50%, -50%) scale(20);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
';
?>