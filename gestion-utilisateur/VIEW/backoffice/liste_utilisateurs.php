<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit();
}

require_once '../../MODEL/Utilisateur.php';

$utilisateur = new Utilisateur();

// Paramètres de tri
$sort = $_GET['sort'] ?? 'date_creation';
$order = strtoupper($_GET['order'] ?? 'DESC');
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$typeFilter = $_GET['type'] ?? '';

// Colonnes valides pour le tri
$validSortColumns = [
    'id' => 'u.id',
    'nom' => 'u.nom',
    'prenom' => 'u.prenom',
    'email' => 'u.email',
    'cin' => 'u.cin',
    'telephone' => 'u.telephone',
    'type_compte' => 'u.type_compte',
    'role' => 'u.role',
    'date_creation' => 'u.date_creation',
    'last_login' => 'u.last_login'
];

$sortColumn = $validSortColumns[$sort] ?? 'u.date_creation';
$order = ($order === 'ASC') ? 'ASC' : 'DESC';

// Récupération des données avec filtres
$users = $utilisateur->getAllOrdered($sort, $order, $search, $roleFilter, $typeFilter);
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');

// URL de tri
function sortUrl($column) {
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = ($_GET['sort'] ?? '') === $column && ($_GET['order'] ?? 'DESC') === 'ASC' ? 'DESC' : 'ASC';
    return '?' . http_build_query($params);
}

// Icône de tri
function sortIcon($column) {
    if (($_GET['sort'] ?? '') !== $column) return 'fa-sort';
    return ($_GET['order'] ?? 'DESC') === 'ASC' ? 'fa-sort-up' : 'fa-sort-down';
}
?>

<div class="top-bar">
    <h1><i class="fas fa-users-cog"></i> Gestion des utilisateurs</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<!-- Mini statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total utilisateurs</div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Citoyens</div>
        <div class="stat-value"><?= number_format($totalCitoyens) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Agents</div>
        <div class="stat-value"><?= number_format($totalAgents) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Administrateurs</div>
        <div class="stat-value"><?= number_format($totalAdmins) ?></div>
    </div>
</div>

<!-- Tableau -->
<div class="card">
    <div class="card-header">
        <h3>📋 Liste des utilisateurs</h3>
        <div class="card-actions">
            <a href="ajouter_utilisateur.php" class="btn-add">
                <i class="fas fa-plus"></i> Ajouter un utilisateur
            </a>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <input type="hidden" name="page" value="liste_utilisateurs">
            <div class="filter-row">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Rechercher par nom, email, CIN..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="role" onchange="this.form.submit()">
                    <option value="">Tous les rôles</option>
                    <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                    <option value="agent" <?= $roleFilter === 'agent' ? 'selected' : '' ?>>Agents</option>
                    <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>Citoyens</option>
                </select>
                <select name="type" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="citoyen" <?= $typeFilter === 'citoyen' ? 'selected' : '' ?>>Citoyen standard</option>
                    <option value="professionnel" <?= $typeFilter === 'professionnel' ? 'selected' : '' ?>>Professionnel</option>
                    <option value="agent_public" <?= $typeFilter === 'agent_public' ? 'selected' : '' ?>>Agent public</option>
                </select>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <?php if($search || $roleFilter || $typeFilter): ?>
                    <a href="?page=liste_utilisateurs" class="btn-clear">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                <?php endif; ?>
            </div>
        </form>
        
        <?php if($search || $roleFilter || $typeFilter): ?>
            <div class="active-filters">
                <i class="fas fa-filter"></i> Filtres actifs : 
                <?= $search ? "Recherche: \"$search\" " : '' ?>
                <?= $roleFilter ? "Rôle: $roleFilter " : '' ?>
                <?= $typeFilter ? "Type: $typeFilter" : '' ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="<?= sortUrl('id') ?>" class="sort-link">
                            ID
                            <i class="fas <?= sortIcon('id') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('nom') ?>" class="sort-link">
                            Nom
                            <i class="fas <?= sortIcon('nom') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('email') ?>" class="sort-link">
                            Email
                            <i class="fas <?= sortIcon('email') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('cin') ?>" class="sort-link">
                            CIN
                            <i class="fas <?= sortIcon('cin') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>Téléphone</th>
                    <th>
                        <a href="<?= sortUrl('type_compte') ?>" class="sort-link">
                            Type
                            <i class="fas <?= sortIcon('type_compte') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('role') ?>" class="sort-link">
                            Rôle
                            <i class="fas <?= sortIcon('role') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('date_creation') ?>" class="sort-link">
                            Inscription
                            <i class="fas <?= sortIcon('date_creation') ?> sort-icon"></i>
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="9" class="no-results">
                            <i class="fas fa-search"></i>
                            <p>Aucun utilisateur trouvé</p>
                            <?php if($search || $roleFilter || $typeFilter): ?>
                                <a href="?page=liste_utilisateurs" class="clear-link">Effacer les filtres</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><strong>#<?= $user['id'] ?></strong></td>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-avatar <?= $user['role'] ?>">
                                    <?= strtoupper(substr($user['prenom'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['cin'] ?></td>
                        <td><?= $user['telephone'] ?></td>
                        <td>
                            <?php 
                            $typeLabels = [
                                'citoyen' => '<span class="badge badge-citoyen">Citoyen</span>',
                                'professionnel' => '<span class="badge badge-professionnel">Professionnel</span>',
                                'agent_public' => '<span class="badge badge-agent-public">Agent public</span>'
                            ];
                            echo $typeLabels[$user['type_compte']] ?? '<span class="badge badge-user">Standard</span>';
                            ?>
                        </td>
                        <td>
                            <?php
                            if($user['role'] == 'admin') {
                                echo '<span class="badge badge-admin">Admin</span>';
                            } elseif($user['role'] == 'agent') {
                                echo '<span class="badge badge-agent">Agent</span>';
                            } else {
                                echo '<span class="badge badge-user">Citoyen</span>';
                            }
                            ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                        <td>
                            <div class="actions">
                                <a href="modifier_utilisateur.php?id=<?= $user['id'] ?>" class="btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="../../CONTROLLER/UtilisateurController.php?action=delete&id=<?= $user['id'] ?>" 
                                       class="btn-delete" 
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="results-info">
        <div class="results-count">
            Affichage de <strong><?= count($users) ?></strong> utilisateur(s)
            <?php if($search || $roleFilter || $typeFilter): ?>
                (filtré)
            <?php endif; ?>
        </div>
    </div>
</div>
