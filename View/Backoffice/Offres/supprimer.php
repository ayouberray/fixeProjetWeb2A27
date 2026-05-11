<?php
// supprimer.php
require_once __DIR__ . "/../../../CONTROLLER/OffreController.php";
$ctrl = new OffreController();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $offre = $ctrl->offreModel->getById($id); // il faut rendre la propriété accessible ou ajouter une méthode
    if (!$offre) {
        die("Offre introuvable.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $result = $ctrl->offreModel->delete($id);
    if ($result) {
        header("Location: lister.php?msg=supprime");
        exit;
    } else {
        $error = "Erreur lors de la suppression.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer une offre</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <h1>Supprimer une offre</h1>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <p>Êtes-vous sûr de vouloir supprimer l'offre : <strong><?= htmlspecialchars($offre['titre']) ?></strong> ?</p>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $offre['id_offre'] ?>">
        <button type="submit">Oui, supprimer</button>
        <a href="lister.php">Annuler</a>
    </form>
</body>
</html>