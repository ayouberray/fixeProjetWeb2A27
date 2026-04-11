<?php
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
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
            max-width: 1200px;
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
            margin-bottom: 50px;
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

        /* Grille des cartes */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
            box-shadow: 0 30px 50px -20px rgba(79, 70, 229, 0.5);
        }

        .card-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(145deg, rgba(79, 70, 229, 0.2), rgba(124, 58, 237, 0.1));
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #4F46E5;
            font-size: 40px;
            border: 2px solid rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
        }

        .admin-card:hover .card-icon {
            background: linear-gradient(145deg, #4F46E5, #7C3AED);
            color: white;
            border-color: transparent;
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .card-description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .card-stats {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .stat-badge {
            background: rgba(79, 70, 229, 0.2);
            color: #4F46E5;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid rgba(79, 70, 229, 0.3);
        }

        /* Carte spéciale pour le retour */
        .return-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.03);
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
        }

        .return-card .card-icon {
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

        /* Pied de page */
        .dashboard-footer {
            text-align: center;
            margin-top: 60px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Animation de chargement */
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

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 32px;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-card {
                padding: 30px 20px;
            }
        }

        /* Tooltip personnalisé */
        .tooltip {
            position: relative;
        }

        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 12px;
            white-space: nowrap;
            margin-bottom: 10px;
            z-index: 100;
        }

        /* Notification flottante */
        .floating-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(16, 185, 129, 0.9);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
            animation: slideInRight 0.5s ease;
            z-index: 1000;
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
            font-size: 20px;
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
                <i class="fas fa-crown"></i>
            </div>
            <h1>Tableau de Bord</h1>
            <p>Gérez efficacement votre espace d\'administration</p>
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
                <div class="card-stats">
                    <span class="stat-badge"><i class="fas fa-eye" style="margin-right: 5px;"></i>Visualiser</span>
                    <span class="stat-badge"><i class="fas fa-chart-bar" style="margin-right: 5px;"></i>Détails</span>
                </div>
            </div>

            <!-- Carte Ajout -->
            <div class="admin-card" onclick="window.location.href=\'../backoffice/addemploye.php\'" data-tooltip="Ajouter un nouvel employé">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="card-title">Ajouter un employé</h3>
                <p class="card-description">Intégrez un nouveau membre à votre équipe facilement</p>
                <div class="card-stats">
                    <span class="stat-badge"><i class="fas fa-plus-circle" style="margin-right: 5px;"></i>Nouveau</span>
                    <span class="stat-badge"><i class="fas fa-user-graduate" style="margin-right: 5px;"></i>Onboarding</span>
                </div>
            </div>

            <!-- Carte Suppression -->
            <div class="admin-card" onclick="window.location.href=\'../backoffice/deleteemploye.php\'" data-tooltip="Supprimer un employé">
                <div class="card-icon">
                    <i class="fas fa-user-minus"></i>
                </div>
                <h3 class="card-title">Supprimer un employé</h3>
                <p class="card-description">Retirez un employé de la base de données</p>
                <div class="card-stats">
                    <span class="stat-badge" style="background: rgba(239, 68, 68, 0.2); color: #EF4444; border-color: rgba(239, 68, 68, 0.3);">
                        <i class="fas fa-trash-alt" style="margin-right: 5px;"></i>Suppression
                    </span>
                </div>
            </div>
        </div>

        <!-- Carte Retour spéciale -->
        <div class="admin-card return-card" onclick="window.location.href=\'../frontoffice/adminpanel.php\'">
            <div class="card-icon">
                <i class="fas fa-home"></i>
            </div>
            <h3 class="card-title">Retour à l\'accueil</h3>
            <p class="card-description">Revenir au tableau de bord principal</p>
            <div class="card-stats">
                <span class="stat-badge"><i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Retour</span>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div style="display: flex; justify-content: center; gap: 20px; margin-top: 40px; flex-wrap: wrap;">
            <div style="background: rgba(255, 255, 255, 0.02); padding: 15px 25px; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.05);">
                <span style="color: rgba(255, 255, 255, 0.6);"><i class="fas fa-clock" style="margin-right: 8px; color: #4F46E5;"></i>Dernière connexion : </span>
                <span style="color: white; font-weight: 600;">' . date('d/m/Y H:i') . '</span>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="dashboard-footer">
            <p>&copy; 2024 Administration. Tous droits réservés.</p>
        </div>
    </div>

    <!-- Notification flottante (optionnelle) -->
    <div class="floating-notification" id="welcomeNotification">
        <i class="fas fa-bell"></i>
        <span>Bienvenue dans votre espace d\'administration</span>
    </div>

    <script>
        // Animation d\'apparition des cartes
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".admin-card");
            
            // Animation séquentielle des cartes
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                
                setTimeout(() => {
                    card.style.transition = "all 0.6s ease";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 200 * (index + 1));
            });

            // Notification de bienvenue
            setTimeout(() => {
                const notification = document.getElementById("welcomeNotification");
                notification.style.display = "none";
            }, 5000);

            // Effet de survol amélioré
            cards.forEach(card => {
                card.addEventListener("mouseenter", function() {
                    this.style.transition = "all 0.3s ease";
                });
            });
        });

        // Raccourcis clavier
        document.addEventListener("keydown", function(e) {
            // Ctrl + H pour retour à l\'accueil
            if (e.ctrlKey && e.key === "h") {
                e.preventDefault();
                window.location.href = "../frontoffice/adminpanel.php";
            }
            
            // Ctrl + L pour liste des employés
            if (e.ctrlKey && e.key === "l") {
                e.preventDefault();
                window.location.href = "listemploye.php";
            }
            
            // Ctrl + A pour ajouter
            if (e.ctrlKey && e.key === "a") {
                e.preventDefault();
                window.location.href = "../backoffice/addemploye.php";
            }
            
            // Ctrl + D pour suppression
            if (e.ctrlKey && e.key === "d") {
                e.preventDefault();
                window.location.href = "../backoffice/deleteemploye.php";
            }
        });

        // Gestionnaire de clic sur les cartes avec confirmation pour la suppression
        const deleteCard = document.querySelector(".admin-card[onclick*=\'deleteemploye\']");
        if (deleteCard) {
            const originalClick = deleteCard.onclick;
            deleteCard.onclick = function(e) {
                if (confirm("Êtes-vous sûr de vouloir accéder à la page de suppression ?")) {
                    window.location.href = "../backoffice/deleteemploye.php";
                }
            };
        }

        // Effet de particules au clic
        document.addEventListener("click", function(e) {
            const particle = document.createElement("div");
            particle.style.position = "fixed";
            particle.style.left = e.clientX + "px";
            particle.style.top = e.clientY + "px";
            particle.style.width = "10px";
            particle.style.height = "10px";
            particle.style.background = "radial-gradient(circle, #4F46E5, transparent)";
            particle.style.borderRadius = "50%";
            particle.style.pointerEvents = "none";
            particle.style.zIndex = "9999";
            particle.style.animation = "ripple 1s ease-out";
            document.body.appendChild(particle);

            setTimeout(() => {
                particle.remove();
            }, 1000);
        });

        // Style pour l\'animation de ripple
        const style = document.createElement("style");
        style.textContent = `
            @keyframes ripple {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }
                100% {
                    transform: scale(20);
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