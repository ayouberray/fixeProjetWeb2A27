<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReponseController.php";
$ctrl = new ReponseController();

$id = $_GET['id'] ?? 0;
$reponse = $ctrl->getReponseById($id);

if(!$reponse) {
    header("Location: lister.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($ctrl->supprimerReponse($id)) {
        header("Location: lister.php?message=supprime");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer réponse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 500px; margin: 0 auto; text-align: center;">
            <div class="header-icon" style="background: linear-gradient(145deg, #ef4444, #dc2626);">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h1>Confirmer la suppression</h1>
            <p>Réclamation: <?= htmlspecialchars($reponse['reference']) ?></p>
            <p>Agent: <?= htmlspecialchars($reponse['nom_agent']) ?></p>
            
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <form method="POST">
                    <button type="submit" class="btn" style="background: #ef4444;"><i class="fas fa-trash"></i> Supprimer</button>
                </form>
                <a href="lister.php" class="btn btn-secondary">Annuler</a>
            </div>
        </div>
    </div>
</body>
</html>