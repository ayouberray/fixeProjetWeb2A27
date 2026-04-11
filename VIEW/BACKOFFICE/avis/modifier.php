<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/AvisController.php";
$ctrl = new AvisController();

$id = $_GET['id'] ?? 0;
$avis = $ctrl->getAvisById($id);

if(!$avis) {
    header("Location: lister.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($ctrl->modifierAvis($id, $_POST['note'], $_POST['satisfaction'], $_POST['commentaire'])) {
        $success = "Avis modifié avec succès";
        $avis = $ctrl->getAvisById($id);
    } else {
        $error = "Erreur lors de la modification";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier avis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        .star-rating { display: flex; gap: 10px; font-size: 30px; cursor: pointer; }
        .star-rating i { transition: all 0.2s; }
        .star-rating i:hover { transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h1>Modifier l'avis</h1>
                <p>Réclamation: <?= htmlspecialchars($avis['reference']) ?></p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Note (1-5)</label>
                    <div class="star-rating" id="starRating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fa<?= $i <= $avis['note'] ? 's' : 'r' ?> fa-star" data-value="<?= $i ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="note" id="noteValue" value="<?= $avis['note'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Satisfaction</label>
                    <select name="satisfaction" required>
                        <option value="tres_insatisfait" <?= $avis['satisfaction'] == 'tres_insatisfait' ? 'selected' : '' ?>>Très insatisfait</option>
                        <option value="insatisfait" <?= $avis['satisfaction'] == 'insatisfait' ? 'selected' : '' ?>>Insatisfait</option>
                        <option value="neutre" <?= $avis['satisfaction'] == 'neutre' ? 'selected' : '' ?>>Neutre</option>
                        <option value="satisfait" <?= $avis['satisfaction'] == 'satisfait' ? 'selected' : '' ?>>Satisfait</option>
                        <option value="tres_satisfait" <?= $avis['satisfaction'] == 'tres_satisfait' ? 'selected' : '' ?>>Très satisfait</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" rows="5"><?= htmlspecialchars($avis['commentaire'] ?? '') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="lister.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </form>
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
                        s.style.color = '#f59e0b';
                    } else {
                        s.classList.add('far');
                        s.classList.remove('fas');
                        s.style.color = '';
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
        
        document.querySelector('#starRating').addEventListener('mouseleave', function() {
            const currentValue = parseInt(noteInput.value);
            stars.forEach((s, i) => {
                if(i < currentValue) {
                    s.classList.add('fas');
                    s.classList.remove('far');
                } else {
                    s.classList.add('far');
                    s.classList.remove('fas');
                }
            });
        });
    </script>
</body>
</html>