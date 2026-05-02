<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit();
}

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$user = $utilisateur->getById($_GET['id']);
?>

<div class="top-bar">
    <h1><i class="fas fa-user-edit"></i> Modifier un utilisateur</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>✏️ Formulaire de modification d'utilisateur</h3>
    </div>
    
    <form action="../../CONTROLLER/UtilisateurController.php" method="POST" class="form">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="cin">CIN</label>
                <input type="text" id="cin" name="cin" value="<?= $user['cin'] ?>" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= $user['telephone'] ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="role">Rôle</label>
                <select id="role" name="role">
                    <option value="citoyen" <?= $user['role'] === 'citoyen' ? 'selected' : '' ?>>Citoyen</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="liste_utilisateurs.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </form>
</div>
