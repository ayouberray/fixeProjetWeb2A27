<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
$ctrl = new ReclamationController();

$filtre_statut = $_GET['statut'] ?? null;
$filtre_categorie = $_GET['categorie'] ?? null;
$reclamations = $ctrl->getAllReclamations($filtre_statut, $filtre_categorie);
$stats = $ctrl->getStatistiques();

$statuts = ['soumise', 'en_cours', 'traitee', 'rejetee', 'cloturee'];
$categories = ['administrative', 'sociale', 'infrastructure', 'sante', 'education', 'transport', 'environnement', 'autre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des réclamations - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        .actions { display: flex; gap: 8px; }
        .actions a { padding: 6px 10px; border-radius: 8px; color: white; text-decoration: none; transition: all 0.3s; }
        .actions a:hover { transform: translateY(-2px); }
        .btn-view { background: #3b82f6; }
        .btn-edit { background: #f59e0b; }
        .btn-delete { background: #ef4444; }
        .btn-reply { background: #10b981; }
        .filters-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 12px;
            color: white;
        }
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-mini {
            background: rgba(255,255,255,0.03);
            padding: 15px;
            border-radius: 20px;
            text-align: center;
        }
        .stat-mini .number { font-size: 24px; font-weight: bold; color: #667eea; }
        .stat-mini .label { font-size: 12px; color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <h1>Gestion des réclamations</h1>
            <p style="color: rgba(255,255,255,0.6);">Administration des réclamations citoyennes</p>
        </div>

        <div class="stats-mini">
            <div class="stat-mini"><div class="number"><?= $stats['total'] ?></div><div class="label">Total</div></div>
            <div class="stat-mini"><div class="number"><?= $stats['soumise'] ?></div><div class="label">En attente</div></div>
            <div class="stat-mini"><div class="number"><?= $stats['en_cours'] ?></div><div class="label">En cours</div></div>
            <div class="stat-mini"><div class="number"><?= $stats['traitee'] ?></div><div class="label">Traitée</div></div>
            <div class="stat-mini"><div class="number"><?= $stats['haute_priorite'] ?></div><div class="label">Haute priorité</div></div>
        </div>

        <div class="filters-bar">
            <select class="filter-select" id="filtreStatut" onchange="window.location.href='?statut='+this.value+'&categorie=<?= $_GET['categorie'] ?? '' ?>'">
                <option value="">Tous les statuts</option>
                <?php foreach($statuts as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['statut'] ?? '') == $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select class="filter-select" id="filtreCategorie" onchange="window.location.href='?statut=<?= $_GET['statut'] ?? '' ?>&categorie='+this.value">
                <option value="">Toutes les catégories</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c ?>" <?= ($_GET['categorie'] ?? '') == $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                <?php endforeach; ?>
            </select>
            
            <a href="ajouter.php" class="btn btn-primary" style="margin-left: auto;"><i class="fas fa-plus"></i> Nouvelle réclamation</a>
            <a href="../../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Citoyen</th>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($reclamations)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 60px;">Aucune réclamation trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach($reclamations as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['reference']) ?></strong></td>
                            <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
                            <td><?= htmlspecialchars(substr($r['objet'], 0, 40)) ?>...</td>
                            <td><?= ucfirst($r['categorie']) ?></td>
                            <td class="priority-<?= $r['priorite'] == 'urgente' || $r['priorite'] == 'haute' ? 'high' : ($r['priorite'] == 'normale' ? 'normal' : 'low') ?>">
                                <?= ucfirst($r['priorite']) ?>
                            </td>
                            <td><span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst(str_replace('_', ' ', $r['statut'])) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                            <td class="actions">
                                <a href="details.php?id=<?= $r['id_reclamation'] ?>" class="btn-view" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="modifier.php?id=<?= $r['id_reclamation'] ?>" class="btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="supprimer.php?id=<?= $r['id_reclamation'] ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Confirmer la suppression ?')"><i class="fas fa-trash"></i></a>
                                <a href="../../reponse/ajouter.php?id_rec=<?= $r['id_reclamation'] ?>" class="btn-reply" title="Répondre"><i class="fas fa-reply"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Animation des particules
        function createParticles() {
            const container = document.createElement('div');
            container.className = 'particles';
            document.body.appendChild(container);
            
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = 15 + Math.random() * 20 + 's';
                container.appendChild(particle);
            }
        }
        createParticles();
    </script>
</body>
</html>