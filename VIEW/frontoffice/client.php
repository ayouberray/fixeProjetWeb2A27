<?php
require_once __DIR__."/../../MODEL/employer.php";
require_once __DIR__."/../../model/config.php";
$error="";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $identifiant=$_POST['identifiant'];
    $pass=$_POST['pass'];
    
    $db=Config::getConnexion();
    $sql="SELECT id, nom, prenom FROM employe WHERE id = :id "; 
    $req=$db->prepare($sql);
    $req->bindValue('id',$identifiant);
    
    $req->execute();
    
    if($req->rowCount() > 0 && $pass == "client"){
        $user = $req->fetch(PDO::FETCH_ASSOC);
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_type'] = 'client';
        
        header("Location: espace.php");
        exit();
    } else {
        $error = "Identifiant ou mot de passe incorrect";
    }
}
echo'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #0B4F6C 0%, #145C9E 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animation de fond */
        body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
            z-index: 0;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Particules animées */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(-30px) translateX(-10px); }
            75% { transform: translateY(-10px) translateX(20px); }
        }

        /* Conteneur principal */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
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

        /* Carte de connexion */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 32px;
            padding: 40px 32px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 1px 1px rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 30px 60px -12px rgba(0, 80, 100, 0.3),
                inset 0 1px 1px rgba(255, 255, 255, 0.8);
        }

        /* En-tête */
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .icon-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: linear-gradient(145deg, #0B4F6C, #145C9E);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(255, 255, 255, 0.3);
            animation: pulse 2s infinite;
            box-shadow: 0 10px 30px -5px #0B4F6C;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 30px -5px #0B4F6C; }
            50% { transform: scale(1.05); box-shadow: 0 20px 40px -5px #145C9E; }
        }

        .icon-container i {
            font-size: 48px;
            color: #fff;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
        }

        .login-header h1 {
            color: #0B4F6C;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #145C9E;
            font-size: 16px;
            font-weight: 500;
        }

        /* Message d\'erreur */
        .error-message {
            background: linear-gradient(145deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border-left: 4px solid #EF4444;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
            animation: shake 0.5s ease-in-out;
            background-color: #fff;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .error-message i {
            color: #EF4444;
            font-size: 20px;
        }

        .error-message span {
            color: #EF4444;
            font-size: 14px;
            font-weight: 500;
        }

        /* Message de bienvenue */
        .welcome-message {
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border-left: 4px solid #10B981;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #fff;
        }

        .welcome-message i {
            color: #10B981;
            font-size: 20px;
        }

        .welcome-message span {
            color: #10B981;
            font-size: 14px;
            font-weight: 500;
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: #0B4F6C;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #145C9E;
            font-size: 18px;
            transition: color 0.3s ease;
            z-index: 1;
        }

        .input-field {
            width: 100%;
            height: 56px;
            background: rgba(11, 79, 108, 0.03);
            border: 2px solid rgba(11, 79, 108, 0.1);
            border-radius: 16px;
            padding: 0 20px 0 50px;
            color: #0B4F6C;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-field:hover {
            background: rgba(11, 79, 108, 0.05);
            border-color: rgba(11, 79, 108, 0.2);
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #145C9E;
            box-shadow: 0 0 0 4px rgba(11, 79, 108, 0.1);
        }

        .input-field:focus + .input-icon {
            color: #145C9E;
        }

        .input-field::placeholder {
            color: rgba(11, 79, 108, 0.3);
            font-weight: 400;
        }

        /* Options supplémentaires */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            background: rgba(11, 79, 108, 0.03);
            border: 2px solid rgba(11, 79, 108, 0.2);
            border-radius: 5px;
            display: inline-block;
            position: relative;
            transition: all 0.2s ease;
        }

        input[type="checkbox"] {
            display: none;
        }

        input[type="checkbox"]:checked + .checkbox-custom {
            background: #145C9E;
            border-color: #145C9E;
        }

        input[type="checkbox"]:checked + .checkbox-custom::after {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #fff;
            font-size: 11px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .remember-me span {
            color: #0B4F6C;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-link {
            color: #145C9E;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            color: #0B4F6C;
            text-decoration: underline;
        }

        /* Bouton de connexion */
        .login-btn {
            width: 100%;
            height: 56px;
            background: linear-gradient(145deg, #0B4F6C, #145C9E);
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px -5px rgba(11, 79, 108, 0.3);
        }

        .login-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(11, 79, 108, 0.5);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .login-btn:hover i {
            transform: translateX(5px);
        }

        /* Lien d\'inscription */
        .signup-link {
            margin-top: 20px;
            text-align: center;
            color: #0B4F6C;
            font-size: 14px;
        }

        .signup-link a {
            color: #145C9E;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .signup-link a:hover {
            color: #0B4F6C;
            text-decoration: underline;
        }

        /* Pied de page */
        .login-footer {
            margin-top: 32px;
            text-align: center;
            color: rgba(11, 79, 108, 0.4);
            font-size: 13px;
            font-weight: 500;
        }

        .login-footer a {
            color: #145C9E;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-footer a:hover {
            color: #0B4F6C;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Particules animées -->
    <div id="particles"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="icon-container">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Espace Client</h1>
                <p>Connectez-vous à votre espace personnel</p>
            </div>

            '.(!empty($error) ? '
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span>'.htmlspecialchars($error).'</span>
            </div>' : '
            <div class="welcome-message">
                <i class="fas fa-info-circle"></i>
                <span>Utilisez votre identifiant et mot de passe pour vous connecter</span>
            </div>').'

            <form action="" method="POST" autocomplete="off">
                <div class="form-group">
                    <label class="form-label" for="identifiant">
                        <i class="fas fa-id-card" style="margin-right: 6px;"></i>
                        Identifiant
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            id="identifiant"
                            name="identifiant" 
                            class="input-field" 
                            placeholder="Entrez votre identifiant"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pass">
                        <i class="fas fa-lock" style="margin-right: 6px;"></i>
                        Mot de passe
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            type="password" 
                            id="pass"
                            name="pass" 
                            class="input-field" 
                            placeholder="Entrez votre mot de passe"
                            required
                        >
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span class="checkbox-custom"></span>
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="mot_de_passe_oublie.php" class="forgot-link">Mot de passe oublié?</a>
                </div>

                <button type="submit" class="login-btn">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="signup-link">
                    Pas encore de compte ? <a href="inscription.php">Créer un compte</a>
                </div>

                <div class="login-footer">
                    &copy; 2024 Espace Client. Tous droits réservés.
                    <br>
                    <a href="conditions_utilisation.php">Conditions d\'utilisation</a> | 
                    <a href="politique_confidentialite.php">Politique de confidentialité</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Création des particules animées
        function createParticles() {
            const particlesContainer = document.getElementById("particles");
            const numberOfParticles = 50;

            for (let i = 0; i < numberOfParticles; i++) {
                const particle = document.createElement("div");
                particle.className = "particle";
                
                // Position aléatoire
                particle.style.left = Math.random() * 100 + "%";
                particle.style.top = Math.random() * 100 + "%";
                
                // Taille aléatoire
                const size = Math.random() * 6 + 2;
                particle.style.width = size + "px";
                particle.style.height = size + "px";
                
                // Opacité aléatoire
                particle.style.opacity = Math.random() * 0.5 + 0.2;
                
                // Animation personnalisée
                const duration = Math.random() * 20 + 10;
                const delay = Math.random() * 5;
                particle.style.animation = `float ${duration}s infinite ease-in-out`;
                particle.style.animationDelay = delay + "s";
                
                particlesContainer.appendChild(particle);
            }
        }

        // Animation du message d\'erreur
        function animateError() {
            const errorMessage = document.querySelector(".error-message");
            if (errorMessage) {
                errorMessage.style.animation = "shake 0.5s ease-in-out";
                setTimeout(() => {
                    errorMessage.style.animation = "";
                }, 500);
            }
        }

        // Validation en temps réel
        function validateForm() {
            const identifiant = document.getElementById("identifiant");
            const pass = document.getElementById("pass");
            
            if(identifiant.value.length < 3) {
                identifiant.style.borderColor = "#EF4444";
            } else {
                identifiant.style.borderColor = "#10B981";
            }
            
            if(pass.value.length < 4) {
                pass.style.borderColor = "#EF4444";
            } else {
                pass.style.borderColor = "#10B981";
            }
        }

        // Initialisation
        document.addEventListener("DOMContentLoaded", () => {
            createParticles();
            
            // Animation des champs de saisie
            const inputs = document.querySelectorAll(".input-field");
            inputs.forEach(input => {
                input.addEventListener("focus", () => {
                    input.parentElement.querySelector(".input-icon").style.color = "#145C9E";
                });
                
                input.addEventListener("blur", () => {
                    if (!input.value) {
                        input.parentElement.querySelector(".input-icon").style.color = "#0B4F6C";
                    }
                });
                
                input.addEventListener("input", validateForm);
            });
        });

        // Si erreur, animation
        if (document.querySelector(".error-message")) {
            document.addEventListener("DOMContentLoaded", animateError);
        }

        // Empêcher la soumission si les champs sont vides
        document.querySelector("form").addEventListener("submit", function(e) {
            const identifiant = document.getElementById("identifiant").value;
            const pass = document.getElementById("pass").value;
            
            if(!identifiant || !pass) {
                e.preventDefault();
                alert("Veuillez remplir tous les champs");
            }
        });
    </script>
</body>
</html>
';
?>