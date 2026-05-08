<?php
session_start();
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../CONTROLLER/ReponseController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$id_reclamation = $_GET['id_reclamation'] ?? 0;
$recCtrl = new ReclamationController();
$repCtrl = new ReponseController();

$reclamation = $recCtrl->getReclamationById($id_reclamation);
if(!$reclamation || $reclamation['id_citoyen'] != $_SESSION['user_id']){
    header("Location: ../RECLAMATION/mes-reclamations.php");
    exit();
}

// Gestion de l'envoi d'un nouveau message (toujours possible même si clôturée)
$error = "";
$success = "";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nouveau_message'])){
    $contenu = trim($_POST['contenu']);
    if(!empty($contenu) && strlen($contenu) >= 2){
        $result = $repCtrl->ajouterReponseCitoyen($id_reclamation, $contenu);
        if($result){
            $success = "Message envoyé !";
            // Mettre à jour le statut de la réclamation si elle était clôturée
            if($reclamation['statut'] == 'cloturee' || $reclamation['statut'] == 'rejetee'){
                $recCtrl->modifierStatut($id_reclamation, 'en_cours');
            }
            header("Refresh:0");
            exit();
        } else {
            $error = "Erreur lors de l'envoi du message";
        }
    } else {
        $error = "Veuillez écrire un message (minimum 2 caractères)";
    }
}

// Récupérer TOUTES les réponses (historique complet)
$reponses = $repCtrl->getReponsesByReclamation($id_reclamation);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Conversation #<?= htmlspecialchars($reclamation['reference']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5FCF9; color: #1A2E2A; }
        
        /* SLIDESHOW BACKGROUND */
        .hero { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .hero-slideshow { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .hero-slideshow .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease; }
        .hero-slideshow .slide.active { opacity: 1; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.95) 100%); z-index: 1; }
        
        .navbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .navbar-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .navbar a { text-decoration: none; color: #006D5B; font-weight: 600; }
        .logo { font-size: 22px; font-weight: 800; }
        
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; position: relative; z-index: 2; }
        
        .chat-header { background: linear-gradient(135deg, #006D5B, #004D3D); color: white; padding: 25px; border-radius: 20px 20px 0 0; }
        .chat-header h2 { font-size: 22px; margin-bottom: 5px; }
        .chat-header p { opacity: 0.8; font-size: 14px; }
        
        .chat-body { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 25px; border-radius: 0 0 20px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-height: 500px; overflow-y: auto; }
        
        .message { margin-bottom: 20px; display: flex; flex-direction: column; max-width: 80%; }
        .message.user { align-self: flex-end; margin-left: auto; }
        .message.admin { align-self: flex-start; }
        
        .message-bubble { padding: 12px 18px; border-radius: 18px; font-size: 14px; line-height: 1.5; }
        .message.user .message-bubble { background: #006D5B; color: white; border-bottom-right-radius: 4px; }
        .message.admin .message-bubble { background: #F3F4F6; color: #374151; border-bottom-left-radius: 4px; }
        
        .message-info { font-size: 11px; color: #9CA3AF; margin-top: 5px; display: flex; gap: 10px; flex-wrap: wrap; }
        .message.user .message-info { justify-content: flex-end; }
        
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-traitee { background: #D1FAE5; color: #065F46; }
        .status-soumise { background: #FEF3C7; color: #92400E; }
        .status-en_cours { background: #DBEAFE; color: #1E40AF; }
        .status-rejetee { background: #FEE2E2; color: #991B1B; }
        .status-cloturee { background: #F3E8FF; color: #6B21A5; }
        
        .chat-input { background: white; padding: 20px; border-radius: 20px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .chat-input textarea { width: 100%; padding: 12px; border: 1px solid #E5E7EB; border-radius: 12px; font-size: 14px; resize: vertical; font-family: inherit; }
        .chat-input textarea:focus { outline: none; border-color: #006D5B; }
        .chat-input button { margin-top: 10px; background: #006D5B; color: white; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .chat-input button:hover { background: #004D3D; transform: translateY(-2px); }
        
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 15px; }
        .alert-success { background: #D1FAE5; color: #065F46; border-left: 4px solid #10B981; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border-left: 4px solid #EF4444; }
        
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #006D5B; text-decoration: none; font-size: 14px; margin-bottom: 15px; }
        .btn-back:hover { text-decoration: underline; }
        
        .info-message { text-align: center; padding: 15px; background: #E6F4F0; border-radius: 12px; margin: 20px 0; color: #006D5B; font-size: 13px; }
        
        .barcode-container { background: white; padding: 10px; border-radius: 10px; text-align: center; display: inline-flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .barcode-container #qrcode img { border-radius: 4px; }
        
        .tts-btn { background: none; border: none; color: #006D5B; cursor: pointer; font-size: 16px; margin-left: 10px; padding: 5px; border-radius: 50%; transition: all 0.2s; }
        .tts-btn:hover { background: rgba(0,109,91,0.1); transform: scale(1.1); }
        .message.admin .tts-btn { color: #006D5B; }
        
        @media (max-width: 768px) { 
            .navbar-container { flex-direction: column; text-align: center; } 
            .message { max-width: 95%; } 
        }
    </style>
</head>
<body>

<!-- SLIDESHOW BACKGROUND -->
<section class="hero">
    <div class="hero-slideshow">
        <div class="slide active" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia1.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia2.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia3.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia4.jpg');"></div>
    </div>
    <div class="hero-overlay"></div>
</section>

<nav class="navbar">
    <div class="navbar-container">
        <a href="../../../index.php" class="logo">🏛️ InnoGov</a>
        <a href="../RECLAMATION/mes-reclamations.php">📋 Mes réclamations</a>
    </div>
</nav>

<div class="container">
    <a href="../RECLAMATION/mes-reclamations.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    
    <div class="chat-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2>💬 Conversation #<?= htmlspecialchars($reclamation['reference']) ?></h2>
            <p><?= htmlspecialchars($reclamation['objet']) ?></p>
        </div>
        <div class="barcode-container" title="QR Code à scanner en agence">
            <div id="qrcode"></div>
            <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 5px;">QR TICKET</div>
        </div>
    </div>
    
    <div class="chat-body" id="chatBody">
        <!-- Message initial du citoyen -->
        <div class="message user">
            <div class="message-bubble">
                <strong>📝 Ma réclamation initiale</strong><br>
                <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
            </div>
            <div class="message-info">
                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($reclamation['date_soumission'])) ?></span>
                <span class="status-badge status-<?= $reclamation['statut'] ?>"><?= ucfirst($reclamation['statut']) ?></span>
            </div>
        </div>
        
        <!-- Messages de la conversation -->
        <?php foreach($reponses as $rep): ?>
            <?php if($rep['envoyeur'] == 'citoyen'): ?>
                <div class="message user">
                    <div class="message-bubble">
                        <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
                    </div>
                    <div class="message-info">
                        <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="message admin">
                    <div class="message-bubble">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong><i class="fas fa-user-tie"></i> <?= htmlspecialchars($rep['nom_agent']) ?></strong>
                            <button class="tts-btn" onclick="lireMessage(`<?= addslashes(htmlspecialchars($rep['contenu'])) ?>`)" title="Écouter la réponse">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </div>
                        <div style="margin-top: 8px;">
                            <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
                        </div>
                        <?php if($rep['decision']): ?>
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #D1D5DB;">
                                📋 Décision : <?= htmlspecialchars($rep['decision']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="message-info">
                        <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if(empty($reponses)): ?>
            <div class="info-message">
                <i class="fas fa-clock"></i> Aucune réponse pour le moment. Notre équipe vous répondra dans les plus brefs délais.
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Formulaire d'envoi de message (toujours disponible) -->
    <div class="chat-input">
        <form method="POST" id="messageForm">
            <textarea name="contenu" id="contenu" rows="3" placeholder="Écrivez votre message ici... (min. 2 caractères)"></textarea>
            <button type="submit" name="nouveau_message"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </form>
        
        <?php if($error): ?>
            <div class="alert alert-danger" style="margin-top: 10px;"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success" style="margin-top: 10px;"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="info-message" style="margin-top: 15px; background: #F9FAFB; font-size: 12px;">
            <i class="fas fa-info-circle"></i> Vous pouvez continuer la conversation même après la clôture. L'administration vous répondra.
        </div>
    </div>
</div>

<script>
    // Auto-scroll vers le bas
    const chatBody = document.getElementById('chatBody');
    if(chatBody) {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    
    // Validation du formulaire
    const messageForm = document.getElementById('messageForm');
    if(messageForm) {
        const contenu = document.getElementById('contenu');
        
        contenu.addEventListener('input', function() {
            if(this.value.trim().length >= 2) {
                this.style.borderColor = '#D1D5DB';
            }
        });
        
        messageForm.addEventListener('submit', function(e) {
            if(!contenu.value.trim() || contenu.value.trim().length < 2) {
                e.preventDefault();
                contenu.style.borderColor = '#EF4444';
                alert('Veuillez écrire un message (minimum 2 caractères)');
            }
        });
    }
    
    // Slideshow
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if(slides.length > 0) {
        let currentSlide = 0;
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.opacity = i === index ? '1' : '0';
            });
        }
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 3000);
    }

    // Génération du QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= htmlspecialchars($reclamation['reference']) ?>",
        width: 64,
        height: 64,
        colorDark : "#006D5B",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Lecture Vocale (Text-to-Speech)
    function lireMessage(texte) {
        // Arrêter la lecture en cours s'il y en a une
        window.speechSynthesis.cancel();
        
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(texte);
            utterance.lang = 'fr-FR'; // Langue française
            utterance.rate = 1; // Vitesse normale
            utterance.pitch = 1; // Tonalité normale
            
            // Lancer la lecture
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Votre navigateur ne supporte pas la lecture vocale.");
        }
    }
</script>

</body>
</html>