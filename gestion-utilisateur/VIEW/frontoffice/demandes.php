<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Mes demandes</title>
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
    <h1>Mes demandes administratives</h1>
    <p>Suivez l'état d'avancement de vos demandes</p>
</div>

<div class="container">
    <div class="form-card">
        <h3>📋 Nouvelle demande</h3>
        <form action="#" method="POST" onsubmit="alert('Fonctionnalité à venir. Veuillez vous connecter.'); return false;">
            <div class="form-group">
                <label>Type de demande</label>
                <select>
                    <option>Passeport</option>
                    <option>Carte d'identité nationale (CIN)</option>
                    <option>Extrait de naissance</option>
                    <option>Certificat de résidence</option>
                    <option>Permis de conduire</option>
                </select>
            </div>
            <div class="form-group">
                <label>Informations complémentaires</label>
                <textarea rows="3" placeholder="Précisez toute information utile..."></textarea>
            </div>
            <button type="submit" class="btn-submit">Soumettre la demande</button>
        </form>
    </div>

    <div class="table-card">
        <h3>📜 Historique des demandes</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr><th>N°</th><th>Type</th><th>Date</th><th>Statut</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td data-label="N°">#DEM-001</td><td data-label="Type">Passeport</td><td data-label="Date">10/04/2026</td><td data-label="Statut"><span class="badge badge-warning">En cours</span></td><td data-label="Action"><a href="#" style="color:#2e7d32;">Suivre</a></td></tr>
                    <tr><td data-label="N°">#DEM-002</td><td data-label="Type">CIN</td><td data-label="Date">12/04/2026</td><td data-label="Statut"><span class="badge badge-success">Approuvé</span></td><td data-label="Action"><a href="#" style="color:#2e7d32;">Télécharger</a></td></tr>
                    <tr><td data-label="N°">#DEM-003</td><td data-label="Type">Extrait Naissance</td><td data-label="Date">14/04/2026</td><td data-label="Statut"><span class="badge badge-info">En attente</span></td><td data-label="Action"><a href="#" style="color:#2e7d32;">Suivre</a></td></tr>
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
