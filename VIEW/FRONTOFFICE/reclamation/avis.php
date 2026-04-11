<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
require_once "../../../CONTROLLER/AvisController.php";
require_once "../../../MODEL/Avis.php";

$recCtrl = new ReclamationController();
$avisCtrl = new AvisController();

$id = $_GET['id'] ?? 0;
$reclamation = $recCtrl->getReclamationById($id);

if(!$reclamation || $reclamation['id_citoyen'] != $_SESSION['user_id'] || $reclamation['statut'] != 'traitee') {
    header("Location: mes-reclamations.php");
    exit();
}

// Vérifier si un avis existe déjà
$existingAvis = $avisCtrl->getAvisByReclamation($id);
if($existingAvis) {
    header("Location: details.php?id=" . $id);
    exit();
}

$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $avis = new Avis(
        $id,
        $_POST['note'],
        $_POST['satisfaction'],
        $_POST['commentaire'] ?? null
    );
    
    if($avisCtrl->ajouterAvis($avis)) {
        $success = "Merci pour votre avis !";
    } else {
        $error = "Erreur lors de l'envoi de votre avis";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Donner mon avis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        .star-rating {
            display: flex;
            gap: 15px;
            font-size: 45px;
            cursor: pointer;
            justify-content: center;
            margin: 20px 0;
        }
        .star-rating i {
            transition: all 0.2s;
            color: #f59e0b;
        }
        .star-rating i:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h1>Donner mon avis</h1>
                <p>Réclamation: <?= htmlspecialchars($reclamation['reference']) ?></p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <div style="text-align: center;">
                    <a href="details.php?id=<?= $id ?>" class="btn btn-primary">Retour à ma réclamation</a>
                </div>
            <?php else: ?>
                <?php if($error): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" id="avisForm">
                    <div class="form-group" style="text-align: center;">
                        <label>Votre note (1 à 5)</label>
                        <div class="star-rating" id="starRating">
                            <i class="far fa-star" data-value="1"></i>
                            <i class="far fa-star" data-value="2"></i>
                            <i class="far fa-star" data-value="3"></i>
                            <i class="far fa-star" data-value="4"></i>
                            <i class="far fa-star" data-value="5"></i>
                        </div>
                        <input type="hidden" name="note" id="noteValue" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Niveau de satisfaction</label>
                        <select name="satisfaction" required>
                            <option value="">Sélectionnez</option>
                            <option value="tres_insatisfait">Très insatisfait</option>
                            <option value="insatisfait">Insatisfait</option>
                            <option value="neutre">Neutre</option>
                            <option value="satisfait">Satisfait</option>
                            <option value="tres_satisfait">Très satisfait</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Commentaire (optionnel)</label>
                        <textarea name="commentaire" rows="5" placeholder="Partagez votre expérience..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Envoyer mon avis</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const stars = document.querySelectorAll('#starRating i');
        const noteInput = document.getElementById('noteValue');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const value = parseInt(this.dataset.value);
                stars.forEach((s, i) => {
                    if(i < value) {
                        s.classList.add('fas');
                        s.classList.remove('far');
                    } else {
                        s.classList.add('far');
                        s.classList.remove('fas');
                    }
                });
            });
            
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                noteInput.value = value;
                
                stars.forEach((s, i) => {
                    if(i < value) {
                        s.classList.add('fas');
                        s.classList.remove('far');
                    } else {
                        s.classList.add('far');
                        s.classList.remove('fas');
                    }
                });
            });
        });
        
        document.getElementById('starRating').addEventListener('mouseleave', function() {
            const currentValue = parseInt(noteInput.value);
            if(currentValue) {
                stars.forEach((s, i) => {
                    if(i < currentValue) {
                        s.classList.add('fas');
                        s.classList.remove('far');
                    } else {
                        s.classList.add('far');
                        s.classList.remove('fas');
                    }
                });
            } else {
                stars.forEach(s => {
                    s.classList.add('far');
                    s.classList.remove('fas');
                });
            }
        });
        
        document.getElementById('avisForm')?.addEventListener('submit', function(e) {
            if(!noteInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner une note');
            }
        });
    </script>
</body>
</html>