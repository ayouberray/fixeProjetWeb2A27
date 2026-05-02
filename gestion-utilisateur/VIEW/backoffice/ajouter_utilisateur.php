<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit();
}
?>

<div class="top-bar">
    <h1><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>➕ Formulaire d'ajout d'utilisateur</h3>
    </div>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <form action="../../CONTROLLER/UtilisateurController.php" method="POST" class="form">
        <input type="hidden" name="action" value="add">
        
        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="cin">CIN</label>
                <input type="text" id="cin" name="cin" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" required>
            </div>
            <div class="form-group">
                <label for="role">Rôle</label>
                <select id="role" name="role">
                    <option value="citoyen">Citoyen</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter l'utilisateur
            </button>
            <a href="liste_utilisateurs.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </form>
</div>
