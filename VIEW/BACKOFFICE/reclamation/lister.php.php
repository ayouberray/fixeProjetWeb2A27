<?php
require_once "C:/xampp/htdocs/PROJETFIXE/config.php";
require_once CONTROLLER_PATH . "ReclamationController.php";

$ctrl = new ReclamationController();
$reclamations = $ctrl->getAllReclamations();
$stats = $ctrl->getStatistiques();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Gestion des réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #0D3328 0%, #0A281E 100%); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; font-family: 'Syne', sans-serif; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-subtitle { font-size: 12px; color: rgba(255,255,255,0.4); margin-top: 8px; }
        .sidebar-nav { padding: 0 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-link i { width: 20px; font-size: 16px; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        .sidebar-divider { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); padding: 16px 16px 8px; margin-top: 8px; }
        
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-topbar { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 99; }
        .menu-toggle-btn { display: none; background: none; border: none; font-size: 20px; cursor: pointer; color: #006D5B; }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 8px 16px; background: #F3F4F6; border-radius: 30px; font-size: 14px; font-weight: 500; }
        .user-info i { font-size: 20px; color: #006D5B; }
        .admin-content { padding: 24px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-info h3 { font-size: 13px; color: #6B7280; margin-bottom: 8px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #006D5B; font-family: 'Syne', sans-serif; }
        .stat-icon { width: 48px; height: 48px; background: #E6F4F0; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #006D5B; font-size: 24px; }
        
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .card-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 20px 24px; }
        .table-wrapper { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 16px; background: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 13px; }
        .table td { padding: 12px 16px; border-bottom: 1px solid #F0F0F0; font-size: 14px; }
        .table tr:hover td { background: #F9FAFB; }
        
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .badge-soumise { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traitee { background: #D1FAE5; color: #006D5B; }
        .badge-rejetee { background: #FEE2E2; color: #DC2626; }
        
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; }
        .btn-info { background: #3B82F6; color: white; }
        .btn-info:hover { background: #2563EB; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 550px; max-width: 90%; max-height: 80vh; overflow-y: auto; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; }
        .modal-header h3 { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #1A2E2A; }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280; }
        .close-modal:hover { color: #DC2626; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; background: #F9FAFB; }
        
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #F0F0F0; }
        .detail-label { width: 110px; font-weight: 600; color: #374151; font-size: 13px; }
        .detail-value { flex: 1; color: #1A2E2A; font-size: 13px; word-break: break-word; }
        
        @media (max-width: 768px) { 
            .admin-sidebar { transform: translateX(-100%); } 
            .admin-sidebar.open { transform: translateX(0); } 
            .admin-main { margin-left: 0; } 
            .menu-toggle-btn { display: block; }
            .detail-label { width: 100px; }
        }
    </style>
</head>
<body>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div>
            <p class="sidebar-subtitle">Administration</p>
        </div>
        <nav class="sidebar-nav">
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            <div class="sidebar-divider">GESTION</div>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <div class="sidebar-divider">SYSTÈME</div>
            <a href="/PROJETFIXE/index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
        </div>
        
        <div class="admin-content">
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-info"><h3>Total réclamations</h3><div class="stat-number"><?= $stats['total'] ?></div></div><div class="stat-icon"><i class="fas fa-comment-dots"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>En attente</h3><div class="stat-number"><?= $stats['soumise'] ?></div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>En cours</h3><div class="stat-number"><?= $stats['en_cours'] ?></div></div><div class="stat-icon"><i class="fas fa-spinner"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>Traitées</h3><div class="stat-number"><?= $stats['traitee'] ?></div></div><div class="stat-icon"><i class="fas fa-check-circle"></i></div></div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-list"></i> Liste des réclamations</h2>
                    <a href="ajouter.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nouvelle réclamation</a>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr><th>ID</th><th>Réf.</th><th>Citoyen</th><th>Objet</th><th>Statut</th><th>Date</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($reclamations as $r): ?>
                                <tr>
                                    <td>#<?= $r['id_reclamation'] ?></td>
                                    <td><?= htmlspecialchars($r['reference']) ?></td>
                                    <td><?= htmlspecialchars($r['citoyen']) ?></td>
                                    <td><?= htmlspecialchars(substr($r['objet'], 0, 40)) ?>...</td>
                                    <td><span class="badge badge-<?= $r['statut'] ?>"><?= $r['statut'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick='showDetails(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'><i class="fas fa-eye"></i> Détails</button>
                                        <a href="modifier.php?id=<?= $r['id_reclamation'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                                        <a href="supprimer.php?id=<?= $r['id_reclamation'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Détails de la réclamation</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal()">Fermer</button>
        </div>
    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => { sidebar.classList.toggle('open'); });
    }
    
    const modal = document.getElementById('detailsModal');
    
    function showDetails(reclamation) {
        console.log(reclamation); // Pour debug
        
        let statusBadge = '';
        switch(reclamation.statut) {
            case 'soumise': statusBadge = 'badge-soumise'; break;
            case 'en_cours': statusBadge = 'badge-en_cours'; break;
            case 'traitee': statusBadge = 'badge-traitee'; break;
            case 'rejetee': statusBadge = 'badge-rejetee'; break;
            default: statusBadge = 'badge-soumise';
        }
        
        const dateSoumission = new Date(reclamation.date_soumission).toLocaleDateString('fr-FR') + ' ' + new Date(reclamation.date_soumission).toLocaleTimeString('fr-FR');
        const dateModification = reclamation.date_modification ? new Date(reclamation.date_modification).toLocaleDateString('fr-FR') : 'Non modifiée';
        
        document.getElementById('modalBody').innerHTML = `
            <div class="detail-row"><div class="detail-label">ID</div><div class="detail-value">#${reclamation.id_reclamation}</div></div>
            <div class="detail-row"><div class="detail-label">Référence</div><div class="detail-value">${escapeHtml(reclamation.reference)}</div></div>
            <div class="detail-row"><div class="detail-label">Citoyen</div><div class="detail-value">${escapeHtml(reclamation.citoyen)}</div></div>
            <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value">${escapeHtml(reclamation.citoyen_email || 'Non renseigné')}</div></div>
            <div class="detail-row"><div class="detail-label">Téléphone</div><div class="detail-value">${escapeHtml(reclamation.citoyen_telephone || 'Non renseigné')}</div></div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">${escapeHtml(reclamation.nom_service || 'Non spécifié')}</div></div>
            <div class="detail-row"><div class="detail-label">Catégorie</div><div class="detail-value">${escapeHtml(reclamation.categorie)}</div></div>
            <div class="detail-row"><div class="detail-label">Priorité</div><div class="detail-value">${escapeHtml(reclamation.priorite)}</div></div>
            <div class="detail-row"><div class="detail-label">Statut</div><div class="detail-value"><span class="badge ${statusBadge}">${escapeHtml(reclamation.statut)}</span></div></div>
            <div class="detail-row"><div class="detail-label">Objet</div><div class="detail-value">${escapeHtml(reclamation.objet)}</div></div>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${escapeHtml(reclamation.description).replace(/\n/g, '<br>')}</div></div>
            <div class="detail-row"><div class="detail-label">Lieu</div><div class="detail-value">${escapeHtml(reclamation.lieu || 'Non précisé')}</div></div>
            <div class="detail-row"><div class="detail-label">Date soumission</div><div class="detail-value">${dateSoumission}</div></div>
            <div class="detail-row"><div class="detail-label">Dernière modif.</div><div class="detail-value">${dateModification}</div></div>
        `;
        modal.style.display = 'flex';
    }
    
    function closeModal() { 
        modal.style.display = 'none'; 
    }
    
    modal.addEventListener('click', function(e) { 
        if(e.target === modal) closeModal(); 
    });
    
    function escapeHtml(text) {
        if(!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

</body>
</html>
