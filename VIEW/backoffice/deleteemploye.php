<?php
require_once __DIR__."/../../MODEL/employer.php";
require_once __DIR__."/../../CONTROLLER/employercontroller.php";
require_once __DIR__."/../../model/config.php";

$x = new employeController();
$error = "";
$success = "";
$employeInfo = null;

// Vérifier s'il y a un message de succès dans l'URL
if(isset($_GET['message'])) {
    $success = htmlspecialchars($_GET['message']);
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $identifiant = $_POST['identifiant'];
    
    if(isset($identifiant) && !empty($identifiant)){
        $db = Config::getConnexion();
        $sql = "SELECT * FROM employe WHERE id = :id"; 
        $req = $db->prepare($sql);
        $req->bindValue('id', $identifiant);
        $req->execute();
        
        if($req->rowCount() > 0){
            $employeInfo = $req->fetch(PDO::FETCH_ASSOC);
            $x->deleteEmploye($identifiant);
            header("Location: deleteemploye.php?message=" . urlencode("Employé #$identifiant supprimé avec succès"));
            exit();
        } else {
            $error = "Aucun employé trouvé avec l'identifiant : " . htmlspecialchars($identifiant);
        }
    } else {
        $error = "Veuillez saisir un identifiant";
    }
}

echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression d\'employé - Administration</title>
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
                radial-gradient(circle at 20% 50%, rgba(239, 68, 68, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.15) 0%, transparent 50%);
            z-index: 0;
        }

        .floating-shape {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(145deg, #EF4444, #7C3AED);
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
        .delete-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 600px;
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

        /* Carte de suppression */
        .delete-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 40px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* En-tête */
        .delete-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(145deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #EF4444;
            font-size: 45px;
            border: 2px solid rgba(239, 68, 68, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 0 30px 10px rgba(239, 68, 68, 0.3);
            }
        }

        .delete-header h1 {
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .delete-header p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 16px;
        }

        /* Messages d\'alerte */
        .success-message {
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
            border-left: 4px solid #10B981;
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
            animation: slideIn 0.5s ease;
        }

        .error-message {
            background: linear-gradient(145deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
            border-left: 4px solid #EF4444;
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
            animation: shake 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .success-message i {
            color: #10B981;
            font-size: 24px;
        }

        .error-message i {
            color: #EF4444;
            font-size: 24px;
        }

        .message-content {
            flex: 1;
        }

        .message-title {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .message-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        /* Formulaire */
        .delete-form {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 20px;
            transition: color 0.3s ease;
            z-index: 1;
        }

        .input-field {
            width: 100%;
            height: 65px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 20px;
            padding: 0 20px 0 60px;
            color: white;
            font-size: 18px;
            font-weight: 500;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-field:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(239, 68, 68, 0.4);
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: #EF4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
        }

        .input-field:focus + .input-icon {
            color: #EF4444;
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.2);
            font-weight: 400;
        }

        /* Boutons */
        .buttons-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .delete-btn {
            flex: 1;
            min-width: 200px;
            height: 60px;
            background: linear-gradient(145deg, #EF4444, #DC2626);
            border: none;
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);
            position: relative;
            overflow: hidden;
        }

        .delete-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .delete-btn:hover::before {
            left: 100%;
        }

        .delete-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -5px rgba(239, 68, 68, 0.6);
        }

        .delete-btn i {
            font-size: 18px;
        }

        .return-btn {
            flex: 1;
            min-width: 200px;
            height: 60px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .return-btn:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: #4F46E5;
            transform: translateY(-3px);
        }

        .return-btn i {
            color: #4F46E5;
            transition: transform 0.3s ease;
        }

        .return-btn:hover i {
            transform: translateX(-5px);
        }

        /* Aperçu de l\'employé (si trouvé) */
        .employee-preview {
            margin-top: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: none;
        }

        .employee-preview.show {
            display: block;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .preview-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .preview-item {
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .preview-label {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
            margin-bottom: 5px;
        }

        .preview-value {
            color: white;
            font-size: 16px;
            font-weight: 600;
        }

        /* Pied de page */
        .delete-footer {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .delete-card {
                padding: 30px 20px;
            }

            .delete-header h1 {
                font-size: 28px;
            }

            .buttons-container {
                flex-direction: column;
            }

            .delete-btn, .return-btn {
                width: 100%;
            }

            .preview-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Éléments d\'arrière-plan -->
    <div class="background-gradient"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>

    <div class="delete-container">
        <div class="delete-card">
            <!-- En-tête -->
            <div class="delete-header">
                <div class="header-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h1>Suppression d\'employé</h1>
                <p>Cette action est irréversible</p>
            </div>';

// Message de succès
if(!empty($success)) {
    echo '
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <div class="message-content">
                    <div class="message-title">Succès !</div>
                    <div class="message-text">' . $success . '</div>
                </div>
            </div>';
}

// Message d\'erreur
if(!empty($error)) {
    echo '
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div class="message-content">
                    <div class="message-title">Erreur</div>
                    <div class="message-text">' . $error . '</div>
                </div>
            </div>';
}

echo '
            <!-- Formulaire -->
            <form class="delete-form" action="" method="POST" id="deleteForm">
                <div class="form-group">
                    <label class="form-label" for="identifiant">
                        <i class="fas fa-id-card" style="margin-right: 6px;"></i>
                        Identifiant de l\'employé
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-hashtag input-icon"></i>
                        <input 
                            type="text" 
                            id="identifiant"
                            name="identifiant" 
                            class="input-field" 
                            placeholder="ex: 12345"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="buttons-container">
                    <button type="submit" class="delete-btn" id="submitBtn">
                        <i class="fas fa-trash-alt"></i>
                        <span>Supprimer l\'employé</span>
                        <span class="spinner" style="display: none;"></span>
                    </button>
                    
                    <button type="button" class="return-btn" onclick="window.location.href=\'../frontoffice/adminpanel.php\'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour à l\'accueil</span>
                    </button>
                </div>
            </form>

            <!-- Aperçu de l\'employé (affiché si des données sont trouvées) -->';

if($employeInfo) {
    echo '
            <div class="employee-preview show">
                <div class="preview-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Aperçu de l\'employé à supprimer</span>
                </div>
                <div class="preview-grid">
                    <div class="preview-item">
                        <div class="preview-label">ID</div>
                        <div class="preview-value">#' . htmlspecialchars($employeInfo['id']) . '</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Nom complet</div>
                        <div class="preview-value">' . htmlspecialchars($employeInfo['nom'] . ' ' . $employeInfo['prenom']) . '</div>
                    </div>
                </div>
            </div>';
}

echo '
            <!-- Pied de page -->
            <div class="delete-footer">
                <p><i class="fas fa-shield-alt" style="margin-right: 5px;"></i> Opération sécurisée - Toutes les actions sont journalisées</p>
            </div>
        </div>
    </div>

    <script>
        // Animation de confirmation
        document.getElementById("deleteForm").addEventListener("submit", function(e) {
            const identifiant = document.getElementById("identifiant").value;
            
            if(identifiant) {
                const confirmation = confirm("⚠️ Êtes-vous ABSOLUMENT sûr de vouloir supprimer l\'employé #" + identifiant + " ?\\n\\nCette action est irréversible et supprimera définitivement toutes les données associées.");
                
                if(!confirmation) {
                    e.preventDefault();
                    return false;
                }
                
                // Afficher le spinner
                const btn = document.getElementById("submitBtn");
                const btnText = btn.querySelector("span:not(.spinner)");
                const spinner = btn.querySelector(".spinner");
                
                btnText.style.opacity = "0.5";
                spinner.style.display = "inline-block";
                btn.disabled = true;
            }
        });

        // Validation en temps réel
        document.getElementById("identifiant").addEventListener("input", function(e) {
            // Ne permettre que les chiffres (optionnel)
            // this.value = this.value.replace(/[^0-9]/g, "");
            
            if(this.value.length > 0) {
                this.style.borderColor = "#EF4444";
            } else {
                this.style.borderColor = "rgba(239, 68, 68, 0.2)";
            }
        });

        // Raccourcis clavier
        document.addEventListener("keydown", function(e) {
            // Ctrl + Enter pour soumettre
            if(e.ctrlKey && e.key === "Enter") {
                e.preventDefault();
                document.getElementById("deleteForm").requestSubmit();
            }
            
            // Échap pour retour
            if(e.key === "Escape") {
                window.location.href = "../frontoffice/adminpanel.php";
            }
        });

        // Effet de focus
        const input = document.querySelector(".input-field");
        input.addEventListener("focus", function() {
            this.parentElement.style.transform = "scale(1.02)";
        });
        
        input.addEventListener("blur", function() {
            this.parentElement.style.transform = "scale(1)";
        });

        // Animation du header
        const headerIcon = document.querySelector(".header-icon");
        setInterval(() => {
            headerIcon.style.transform = "scale(1.1)";
            setTimeout(() => {
                headerIcon.style.transform = "scale(1)";
            }, 200);
        }, 3000);
    </script>
</body>
</html>';
?>