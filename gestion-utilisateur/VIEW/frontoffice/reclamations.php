<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fas fa-leaf"></i></div>
        <div class="logo-text">inno<span>Gov</span></div>
    </a>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <a href="emploi.php">Emploi</a>
        <a href="concours.php">Concours</a>
        <a href="rendez_vous.php">Rendez-vous</a>
        <a href="demandes.php">Demandes</a>
        <a href="reclamations.php">Réclamations</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span style="color: #2e7d32;">👋 <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
            <a href="profil.php" style="background:#2e7d32; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Mon profil</a>
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Connexion</a>
            <a href="register.php" style="background:#4caf50; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<div class="page-hero">
    <h1>Réclamations</h1>
    <p>Faites-nous part de vos préoccupations</p>
</div>

<div class="container">
    <div class="form-card">
        <h3>⚠️ Nouvelle réclamation</h3>
        <form action="#" method="POST" onsubmit="alert('Fonctionnalité à venir. Veuillez vous connecter.'); return false;">
            <div class="form-group">
                <label>Sujet</label>
                <select>
                    <option>Délai trop long</option>
                    <option>Erreur dans mon dossier</option>
                    <option>Problème technique</option>
                    <option>Comportement d'un agent</option>
                    <option>Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description détaillée</label>
                <textarea rows="4" placeholder="Expliquez votre problème en détail..."></textarea>
            </div>
            <div class="form-group">
                <label>Numéro de dossier (optionnel)</label>
                <input type="text" placeholder="Si vous avez un numéro de dossier">
            </div>
            <button type="submit" class="btn-submit">Envoyer la réclamation</button>
        </form>
    </div>

    <div class="table-card">
        <h3>📋 Mes réclamations</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr><th>Sujet</th><th>Date</th><th>Statut</th><th>Réponse</th></tr>
                </thead>
                <tbody>
                    <tr><td data-label="Sujet">Délai trop long</td><td data-label="Date">05/04/2026</td><td data-label="Statut"><span class="badge badge-warning">En cours</span></td><td data-label="Réponse">-</td></tr>
                    <tr><td data-label="Sujet">Erreur dans document</td><td data-label="Date">08/04/2026</td><td data-label="Statut"><span class="badge badge-success">Résolu</span></td><td data-label="Réponse"><a href="#" style="color:#2e7d32;">Voir réponse</a></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

</body>
</html>
