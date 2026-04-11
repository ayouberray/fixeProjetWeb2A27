<?php
// VIEW/frontoffice/suivi_demande.php
// Page de suivi détaillé d'une demande

require_once __DIR__ . '/../../CONTROLLER/SuiviDemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: index.php?error=ID de demande invalide');
    exit();
}

$controller = new SuiviDemandeController();
$data = $controller->show($id_demande);

$demande = $data['demande'] ?? null;
$historique = $data['historique'] ?? [];
$delai = $data['delai'] ?? null;

if (!$demande) {
    header('Location: index.php?error=Demande introuvable');
    exit();
}

$user_nom = $_SESSION['user_nom'] ?? 'Ben Ali';
$user_prenom = $_SESSION['user_prenom'] ?? 'Mohamed';

$statut_config = [
    'en_attente' => ['class' => 'warning', 'icon' => '⏳', 'label' => 'En attente'],
    'en_cours' => ['class' => 'info', 'icon' => '🔄', 'label' => 'En cours'],
    'traite' => ['class' => 'success', 'icon' => '✅', 'label' => 'Traité'],
    'refuse' => ['class' => 'danger', 'icon' => '❌', 'label' => 'Refusé']
];

$statut_actuel = $statut_config[$demande['statut']] ?? $statut_config['en_attente'];
$reference = 'DM-' . date('Y') . '-' . str_pad($id_demande, 6, '0', STR_PAD_LEFT);

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Demande #<?= $id_demande ?> • Mairie</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container { max-width: 1000px; margin: 0 auto; }
        
        .page-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover { color: var(--primary); }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .demande-id {
            background: white;
            padding: 0.25rem 1rem;
            border-radius: 100px;
            font-size: 1rem;
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }
        
        .reference {
            background: var(--primary);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 100px;
            font-size: 0.875rem;
        }
        
        .status-banner {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }
        
        .status-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .status-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        
        .status-icon.warning { background: #fef3c7; }
        .status-icon.info { background: #dbeafe; }
        .status-icon.success { background: #d1fae5; }
        .status-icon.danger { background: #fee2e2; }
        
        .status-info h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .status-badge.warning { background: #fef3c7; color: #92400e; }
        .status-badge.info { background: #dbeafe; color: #1e40af; }
        .status-badge.success { background: #d1fae5; color: #065f46; }
        .status-badge.danger { background: #fee2e2; color: #991b1b; }
        
        .progress-container { margin-top: 2rem; }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .progress-step {
            text-align: center;
            flex: 1;
            font-size: 0.75rem;
            color: var(--gray-600);
        }
        
        .progress-step.completed { color: var(--success); font-weight: 600; }
        
        .progress-bar {
            height: 8px;
            background: var(--gray-200);
            border-radius: 100px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 100px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
        }
        
        .info-card-title {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--gray-600);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-item { margin-bottom: 1rem; }
        .info-item:last-child { margin-bottom: 0; }
        .info-label { font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem; }
        .info-value { font-weight: 600; color: var(--gray-900); }
        
        .description-text {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }
        
        .timeline {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }
        
        .timeline-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .timeline-items { position: relative; }
        
        .timeline-items::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--gray-200);
        }
        
        .timeline-item {
            display: flex;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            position: relative;
        }
        
        .timeline-item:last-child { padding-bottom: 0; }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: white;
            border: 2px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            z-index: 1;
        }
        
        .timeline-icon.success { background: #d1fae5; border-color: var(--success); }
        .timeline-icon.warning { background: #fef3c7; border-color: var(--warning); }
        .timeline-icon.info { background: #dbeafe; border-color: var(--info); }
        .timeline-icon.danger { background: #fee2e2; border-color: var(--danger); }
        
        .timeline-content { flex: 1; }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .timeline-action { font-weight: 600; }
        .timeline-date { font-size: 0.75rem; color: var(--gray-600); }
        .timeline-comment { color: var(--gray-600); font-size: 0.875rem; margin-top: 0.5rem; }
        .timeline-agent { font-size: 0.75rem; color: var(--gray-600); margin-top: 0.5rem; }
        
        .actions-bar {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-2px); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; transform: translateY(-2px); }
        .btn-secondary { background: white; color: var(--gray-700); border: 1.5px solid var(--gray-200); }
        .btn-danger { background: white; color: var(--danger); border: 1.5px solid #fee2e2; }
        .btn-danger:hover { background: var(--danger); color: white; }
        .btn-pdf { background: #dc2626; color: white; }
        .btn-pdf:hover { background: #b91c1c; transform: translateY(-2px); }
        
        .empty-state { text-align: center; padding: 2rem; color: var(--gray-600); }
        
        @media print {
            body { background: white; padding: 1cm; }
            .no-print { display: none !important; }
            .status-banner, .info-card, .description-text, .timeline {
                box-shadow: none; border: 1px solid #ddd;
            }
        }
        
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .info-grid { grid-template-columns: 1fr; }
            .status-header { flex-direction: column; text-align: center; }
            .actions-bar { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header no-print">
            <a href="index.php" class="back-link">← Retour au tableau de bord</a>
            <h1 class="page-title">
                Suivi de la demande
                <span class="demande-id">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                <span class="reference">Réf: <?= $reference ?></span>
            </h1>
        </div>
        
        <div class="status-banner">
            <div class="status-header">
                <div class="status-icon <?= $statut_actuel['class'] ?>">
                    <?= $statut_actuel['icon'] ?>
                </div>
                <div class="status-info">
                    <h2><?= htmlspecialchars($demande['titre']) ?></h2>
                    <span class="status-badge <?= $statut_actuel['class'] ?>">
                        <?= $statut_actuel['icon'] ?> <?= $statut_actuel['label'] ?>
                    </span>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-steps">
                    <span class="progress-step completed">📝 Création</span>
                    <span class="progress-step <?= in_array($demande['statut'], ['en_cours', 'traite']) ? 'completed' : '' ?>">🔄 En cours</span>
                    <span class="progress-step <?= $demande['statut'] == 'traite' ? 'completed' : '' ?>">✅ Traité</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 
                        <?php
                        if ($demande['statut'] == 'en_attente') echo '10%';
                        elseif ($demande['statut'] == 'en_cours') echo '50%';
                        elseif ($demande['statut'] == 'traite') echo '100%';
                        elseif ($demande['statut'] == 'refuse') echo '100%';
                        else echo '10%';
                        ?>
                    "></div>
                </div>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-title"><span>📋</span> Informations générales</div>
                <div class="info-item">
                    <div class="info-label">Type de demande</div>
                    <div class="info-value"><?= $types_demandes[$demande['type_demande']] ?? $demande['type_demande'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Service concerné</div>
                    <div class="info-value"><?= htmlspecialchars($demande['nom_service'] ?? 'Non assigné') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date de création</div>
                    <div class="info-value"><?= $demande['date_creation_format'] ?> à <?= $demande['heure_creation'] ?></div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-title"><span>👤</span> Contact et délais</div>
                <div class="info-item">
                    <div class="info-label">Demandeur</div>
                    <div class="info-value"><?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Délai de traitement</div>
                    <div class="info-value">
                        <?php if ($demande['statut'] == 'traite' && $delai): ?>
                            ✅ Traitée en <strong><?= $delai ?> jour(s)</strong>
                        <?php elseif ($demande['statut'] == 'traite'): ?>
                            ✅ Demande traitée
                        <?php else: ?>
                            ⏳ En attente depuis <strong><?= $demande['jours_ecoules'] ?> jour(s)</strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="description-text">
            <div class="info-card-title" style="margin-bottom: 1rem;"><span>📝</span> Description</div>
            <p style="color: var(--gray-700); line-height: 1.6; white-space: pre-wrap;">
                <?= nl2br(htmlspecialchars($demande['description'])) ?>
            </p>
        </div>
        
        <div class="timeline">
            <div class="timeline-title"><span>📅</span> Historique de suivi</div>
            
            <?php if (empty($historique)): ?>
                <div class="empty-state"><p>Aucun suivi disponible.</p></div>
            <?php else: ?>
                <div class="timeline-items">
                    <?php foreach ($historique as $suivi): 
                        $icon_class = 'info'; $icon = '📋';
                        if ($suivi['nouveau_statut'] == 'traite') { $icon_class = 'success'; $icon = '✅'; }
                        elseif ($suivi['nouveau_statut'] == 'en_cours') { $icon_class = 'info'; $icon = '🔄'; }
                        elseif ($suivi['nouveau_statut'] == 'refuse') { $icon_class = 'danger'; $icon = '❌'; }
                        elseif ($suivi['nouveau_statut'] == 'en_attente') { $icon_class = 'warning'; $icon = '⏳'; }
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-icon <?= $icon_class ?>"><?= $icon ?></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-action">
                                    <?php if ($suivi['ancien_statut']): ?>
                                        Statut changé : <?= $statut_config[$suivi['ancien_statut']]['label'] ?? $suivi['ancien_statut'] ?> 
                                        → <?= $statut_config[$suivi['nouveau_statut']]['label'] ?? $suivi['nouveau_statut'] ?>
                                    <?php else: ?>
                                        Demande créée
                                    <?php endif; ?>
                                </span>
                                <span class="timeline-date"><?= $suivi['date_formatee'] ?> à <?= $suivi['heure_formatee'] ?></span>
                            </div>
                            <?php if ($suivi['commentaire']): ?>
                                <div class="timeline-comment"><?= htmlspecialchars($suivi['commentaire']) ?></div>
                            <?php endif; ?>
                            <?php if ($suivi['agent_nom']): ?>
                                <div class="timeline-agent">Par <?= htmlspecialchars($suivi['agent_prenom'] . ' ' . $suivi['agent_nom']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="actions-bar no-print">
            <a href="index.php" class="btn btn-secondary">← Retour aux demandes</a>
            
            <?php if (in_array($demande['statut'], ['en_attente', 'en_cours'])): ?>
                <a href="../backoffice/modifier_demande.php?id=<?= $id_demande ?>" class="btn btn-primary">✏️ Modifier</a>
            <?php endif; ?>
            
            <?php if ($demande['statut'] == 'traite'): ?>
                <button onclick="window.print()" class="btn btn-success">🖨️ Imprimer</button>
                <button onclick="exporterPDF()" class="btn btn-pdf">📄 Exporter PDF</button>
            <?php endif; ?>
            
            <a href="../backoffice/supprimer_demande.php?id=<?= $id_demande ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette demande ?')">🗑️ Supprimer</a>
        </div>
        
        <?php if ($demande['statut'] == 'traite'): ?>
        <div style="margin-top: 2rem; padding: 1rem; background: #d1fae5; border-radius: 12px; color: #065f46; text-align: center;" class="no-print">
            ✅ Cette demande a été traitée. Vous pouvez exporter ou imprimer le récapitulatif.
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function exporterPDF() {
            const contenu = document.querySelector('.container').cloneNode(true);
            contenu.querySelectorAll('.no-print').forEach(el => el.remove());
            
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Suivi Demande #<?= $id_demande ?></title>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        body { font-family: 'Inter', sans-serif; padding: 1.5cm; background: white; }
                        .status-banner, .info-card, .description-text, .timeline {
                            background: white; border: 1px solid #e5e7eb; border-radius: 16px;
                            padding: 1.5rem; margin-bottom: 1.5rem;
                        }
                        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
                        .status-badge { display: inline-block; padding: 0.25rem 1rem; border-radius: 100px; font-weight: 600; }
                        .status-badge.success { background: #d1fae5; color: #065f46; }
                        .status-badge.warning { background: #fef3c7; color: #92400e; }
                        .timeline-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
                        .timeline-icon {
                            width: 40px; height: 40px; border-radius: 12px;
                            display: flex; align-items: center; justify-content: center;
                        }
                        .timeline-icon.success { background: #d1fae5; }
                        .timeline-icon.warning { background: #fef3c7; }
                        .page-header { display: flex; justify-content: space-between; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e5e7eb; }
                        .footer { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class="page-header">
                        <h1>Récapitulatif de demande</h1>
                        <span class="reference">Réf: <?= $reference ?></span>
                    </div>
                    <div style="color: #6b7280; margin-bottom: 1.5rem;">Document généré le <?= date('d/m/Y à H:i') ?></div>
                    ${contenu.innerHTML}
                    <div class="footer">Mairie - Service Municipal<br>Ce document est un récapitulatif officiel.</div>
                    <script>window.onload = function() { window.print(); setTimeout(() => window.close(), 500); };<\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') window.location.href = 'index.php';
        });
    </script>
</body>
</html>