<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innogov - Plateforme de modernisation administrative</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #0B1120 0%, #1A1F2E 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Animation de fond */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(118, 75, 162, 0.15) 0%, transparent 40%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Particules */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: floatParticle 15s infinite linear;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100vh) translateX(50px);
                opacity: 0;
            }
        }

        /* Conteneur principal */
        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* En-tête */
        .hero {
            text-align: center;
            margin-bottom: 60px;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 50px;
            color: white;
            box-shadow: 0 20px 40px -10px rgba(102, 126, 234, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 20px 40px -10px rgba(102, 126, 234, 0.5);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 30px 50px -10px rgba(102, 126, 234, 0.7);
            }
        }

        h1 {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(145deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Cartes d'accès */
        .access-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
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

        .card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: left 0.5s ease;
        }

        .card:hover::before {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-10px);
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 30px 50px -20px rgba(102, 126, 234, 0.4);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(145deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.1));
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 35px;
            color: #667eea;
            border: 2px solid rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }

        .card:hover .card-icon {
            background: linear-gradient(145deg, #667eea, #764ba2);
            color: white;
            transform: scale(1.1) rotate(5deg);
            border-color: transparent;
        }

        .card h2 {
            color: white;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .card p {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .card-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(102, 126, 234, 0.15);
            padding: 8px 20px;
            border-radius: 30px;
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        /* Section fonctionnalités */
        .features {
            text-align: center;
            margin-top: 60px;
            animation: fadeInUp 1s ease-out;
        }

        .features h3 {
            color: white;
            font-size: 28px;
            margin-bottom: 40px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-5px);
        }

        .feature-item i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .feature-item h4 {
            color: white;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .feature-item p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 80px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 36px;
            }
            
            .subtitle {
                font-size: 16px;
            }
            
            .access-cards {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Message de bienvenue si connecté */
        .welcome-banner {
            background: linear-gradient(145deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.1));
            border-radius: 20px;
            padding: 15px 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .welcome-text {
            color: white;
        }

        .welcome-text i {
            color: #10b981;
            margin-right: 8px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 8px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    <div class="particles" id="particles"></div>

    <div class="container">
        <!-- Bannière de bienvenue si connecté -->
        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_type'])): ?>
            <div class="welcome-banner">
                <div class="welcome-text">
                    <i class="fas fa-user-circle"></i>
                    Bienvenue, <strong><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Utilisateur') ?></strong>
                    <span style="margin-left: 10px; font-size: 12px; background: rgba(102,126,234,0.3); padding: 2px 10px; border-radius: 20px;">
                        <?= $_SESSION['user_type'] == 'admin' ? 'Administrateur' : 'Citoyen' ?>
                    </span>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <div class="hero">
            <div class="logo">
                <i class="fas fa-landmark"></i>
            </div>
            <h1>Innogov</h1>
            <p class="subtitle">
                Plateforme de modernisation administrative pour une gouvernance 
                digitale au service des citoyens tunisiens
            </p>
        </div>

        <!-- Cartes d'accès -->
        <div class="access-cards">
            <!-- Carte Citoyen -->
            <div class="card" onclick="window.location.href='VIEW/frontoffice/login.php'">
                <div class="card-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2>Espace Citoyen</h2>
                <p>Accédez à vos services en ligne, déposez vos réclamations, suivez vos demandes et recevez des réponses rapidement.</p>
                <span class="card-badge">
                    <i class="fas fa-arrow-right"></i> Se connecter
                </span>
            </div>

            <!-- Carte Administrateur -->
            <div class="card" onclick="window.location.href='VIEW/backoffice/login.php'">
                <div class="card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2>Espace Administrateur</h2>
                <p>Gérez les réclamations, les services, les utilisateurs et assurez le suivi des demandes citoyennes.</p>
                <span class="card-badge">
                    <i class="fas fa-arrow-right"></i> Administration
                </span>
            </div>

            <!-- Carte Agent -->
            <div class="card" onclick="window.location.href='VIEW/backoffice/login.php'">
                <div class="card-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h2>Espace Agent</h2>
                <p>Traitement des réclamations, réponse aux citoyens et gestion des dossiers en cours.</p>
                <span class="card-badge">
                    <i class="fas fa-arrow-right"></i> Accès agent
                </span>
            </div>
        </div>

        <!-- Section fonctionnalités -->
        <div class="features">
            <h3>Nos services</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-comment-dots"></i>
                    <h4>Réclamations</h4>
                    <p>Déposez et suivez vos réclamations en ligne</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <h4>Suivi en temps réel</h4>
                    <p>Suivez l'évolution de vos demandes</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-bell"></i>
                    <h4>Notifications</h4>
                    <p>Soyez alerté des réponses à vos demandes</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-pie"></i>
                    <h4>Statistiques</h4>
                    <p>Tableaux de bord pour l'administration</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-file-alt"></i>
                    <h4>Documents</h4>
                    <p>Gestion des pièces jointes</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-star"></i>
                    <h4>Évaluations</h4>
                    <p>Notez la qualité des services</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Innogov - Plateforme de modernisation administrative</p>
            <p style="margin-top: 10px; font-size: 12px;">Tunisie - Vers une administration digitale au service des citoyens</p>
        </div>
    </div>

    <script>
        // Création des particules
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Position aléatoire
                particle.style.left = Math.random() * 100 + '%';
                
                // Taille aléatoire
                const size = Math.random() * 6 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Animation personnalisée
                const duration = 10 + Math.random() * 20;
                const delay = Math.random() * 10;
                particle.style.animation = `floatParticle ${duration}s infinite linear`;
                particle.style.animationDelay = delay + 's';
                
                container.appendChild(particle);
            }
        }

        // Animation des cartes au scroll
        function animateOnScroll() {
            const cards = document.querySelectorAll('.card, .feature-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            animateOnScroll();
        });
    </script>
</body>
</html>