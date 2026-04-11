<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
$ctrl = new ReclamationController();

$id = $_GET['id'] ?? 0;
$reclamation = $ctrl->getReclamationById($id);

if(!$reclamation) {
    header("Location: lister.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($ctrl->supprimerReclamation($id)) {
        header("Location: lister.php?message=supprime");
        exit();
    } else {
        $error = "Erreur lors de la suppression";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer réclamation</title>
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
            <p style="margin: 20px 0;">Êtes-vous sûr de vouloir supprimer cette réclamation ?</p>
            
            <div style="background: rgba(255,255,255,0.05); border-radius: 15px; padding: 15px; margin: 20px 0; text-align: left;">
                <p><strong>Référence:</strong> <?= htmlspecialchars($reclamation['reference']) ?></p>
                <p><strong>Objet:</strong> <?= htmlspecialchars($reclamation['objet']) ?></p>
                <p><strong>Citoyen:</strong> <?= htmlspecialchars($reclamation['prenom'] . ' ' . $reclamation['nom']) ?></p>
                <p><strong>Date:</strong> <?= date('d/m/Y', strtotime($reclamation['date_soumission'])) ?></p>
            </div>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <form method="POST">
                    <button type="submit" class="btn" style="background: #ef4444; color: white;"><i class="fas fa-trash"></i> Oui, supprimer</button>
                </form>
                <a href="lister.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </div>
    </div>
</body>
</html>