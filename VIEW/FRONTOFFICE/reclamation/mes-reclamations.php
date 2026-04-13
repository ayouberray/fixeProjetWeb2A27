<?php
require_once "C:/xampp/htdocs/PROJETFIXE/config.php";
require_once CONTROLLER_PATH . "ReclamationController.php";

$recController = new ReclamationController();
$list = $recController->getReclamationByCitoyen($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #F5FCF9;
            overflow-x: hidden;
        }
        
        /* ========== NAVBAR ========== */
        .navbar {
            background: rgba(245, 252, 249, 0.85);
            backdrop-filter: blur(16px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            transition: all 0.3s;
        }
        .navbar.scrolled { background: rgba(245, 252, 249, 0.98); box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .navbar-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 45px; height: 45px; background: linear-gradient(135deg, #006D5B, #004D3D); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; }
        .logo-text h1 { font-size: 22px; font-weight: 800; background: linear-gradient(135deg, #006D5B, #004D3D); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .logo-text p { font-size: 11px; color: #5C8B7E; }
        .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
        .nav-link { text-decoration: none; color: #2C5A4F; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { color: #006D5B; }
        .btn-primary { background: linear-gradient(135deg, #006D5B, #004D3D); color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,109,91,0.3); }
        
        /* ========== HERO AVEC EFFET PARALLAXE ========== */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
            background-image: url('/PROJETFIXE/assets/images/tunisia1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: 0;
            transform: scale(1.05);
            transition: transform 0.1s ease-out;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.9) 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 2rem;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero h1 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            animation: bounce 2s infinite;
            cursor: pointer;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }
        
        .scroll-indicator i {
            font-size: 30px;
            color: white;
            opacity: 0.8;
        }
        
        /* ========== SECTION LISTE ========== */
        .list-section {
            background: #F5FCF9;
            padding: 80px 2rem;
            position: relative;
            z-index: 5;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1A2E2A;
            margin-bottom: 15px;
        }
        
        .section-header p {
            color: #5C8B7E;
            font-size: 18px;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 24px 48px rgba(0,77,61,0.1);
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #006D5B, #004D3D);
            color: white;
        }
        
        .card-header h3 {
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 24px 28px;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            text-align: left;
            padding: 14px 16px;
            background: #F0FDF4;
            color: #006D5B;
            font-weight: 600;
            font-size: 13px;
        }
        
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #E5E7EB;
            color: #1A2E2A;
            font-size: 14px;
        }
        
        .table tr:hover td {
            background: #F9FAFB;
        }
        
        .badge {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-soumise { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traitee { background: #D1FAE5; color: #006D5B; }
        .badge-rejetee { background: #FEE2E2; color: #DC2626; }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-info { background: #3B82F6; color: white; }
        .btn-info:hover { background: #2563EB; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .empty-state {
            text-align: center;
            padding: 60px;
        }
        .empty-state i { font-size: 64px; color: #C1E0D6; margin-bottom: 20px; }
        .empty-state p { color: #5C8B7E; font-size: 16px; }
        
        .footer {
            background: linear-gradient(180deg, #0D3328, #0A281E);
            color: white;
            padding: 40px 2rem 20px;
            text-align: center;
        }
        
        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            width: 550px;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalFadeIn 0.3s ease;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #E5E7EB;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        .modal-header h3 { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #1A2E2A; }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280; }
        .close-modal:hover { color: #DC2626; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; background: #F9FAFB; border-radius: 0 0 20px 20px; }
        
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #F0F0F0; }
        .detail-label { width: 110px; font-weight: 600; color: #374151; font-size: 13px; }
        .detail-value { flex: 1; color: #1A2E2A; font-size: 13px; word-break: break-word; }
        
        @media (max-width: 768px) {
            .navbar-container { flex-direction: column; text-align: center; }
            .nav-menu { justify-content: center; }
            .hero h1 { font-size: 36px; }
            .hero p { font-size: 16px; }
            .section-header h2 { font-size: 28px; }
            .detail-label { width: 100px; }
        }
    </style>
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="/PROJETFIXE/index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text"><h1>InnoGov</h1><p>Espace Citoyen</p></div>
        </a>
        <div class="nav-menu">
            <a href="/PROJETFIXE/index.php" class="nav-link">Accueil</a>
            <a href="/PROJETFIXE/VIEW/FRONTOFFICE/RECLAMATION/mes-reclamations.php" class="nav-link">Mes réclamations</a>
            <a href="/PROJETFIXE/VIEW/BACKOFFICE/RECLAMATION/lister.php" class="nav-link">Admin</a>
            <a href="/PROJETFIXE/VIEW/FRONTOFFICE/RECLAMATION/ajouter.php" class="btn-primary">Déposer</a>
        </div>
    </div>
</nav>

<!-- HERO AVEC EFFET PARALLAXE -->
<section class="hero">
    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Mes réclamations</h1>
        <p>Suivez l'état de vos demandes et interventions</p>
        <a href="ajouter.php" class="btn-primary" style="display: inline-block;">📝 Déposer une réclamation</a>
    </div>
    <div class="scroll-indicator" onclick="scrollToList()">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- SECTION LISTE DES RÉCLAMATIONS -->
<section class="list-section" id="listSection">
    <div class="container">
        <div class="section-header">
            <h2>📋 Historique de mes réclamations</h2>
            <p>Retrouvez ici toutes vos demandes et leur état d'avancement</p>
        </div>
        
        <div class="card" id="reclamationsCard">
            <div class="card-header">
                <h3><i class="fas fa-list-alt"></i> Liste de mes réclamations</h3>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Réf.</th>
                                <th>Service</th>
                                <th>Objet</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($list)): ?>
                                <?php foreach($list as $rec): ?>
                                <tr>
                                    <td>#<?= $rec['id_reclamation'] ?></td>
                                    <td><strong><?= htmlspecialchars($rec['reference']) ?></strong></td>
                                    <td><?= htmlspecialchars($rec['nom_service'] ?? 'Non spécifié') ?></td>
                                    <td><?= htmlspecialchars(substr($rec['objet'], 0, 35)) ?>...</td>
                                    <td><span class="badge badge-<?= $rec['statut'] ?>"><?= $rec['statut'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($rec['date_soumission'])) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick='showDetails(<?= json_encode($rec, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'><i class="fas fa-eye"></i> Détails</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align:center; padding: 60px;">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Aucune réclamation trouvée</p>
                                        <a href="ajouter.php" class="btn-primary" style="display: inline-block; margin-top: 20px;">Déposer une réclamation</a>
                                    </div>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer-container">
        <p>&copy; 2024 InnoGov - Tous droits réservés</p>
    </div>
</footer>

<!-- MODAL DÉTAILS -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Détails de la réclamation</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn btn-info" onclick="closeModal()">Fermer</button>
        </div>
    </div>
</div>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        const heroBg = document.getElementById('heroBg');
        
        if(window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Effet parallaxe sur l'image de fond
        if(heroBg) {
            heroBg.style.transform = `scale(1.05) translateY(${window.scrollY * 0.3}px)`;
        }
    });
    
    // Animation de la carte au scroll
    const observerOptions = {
        threshold: 0.2,
        rootMargin: "0px 0px -50px 0px"
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const card = document.getElementById('reclamationsCard');
    if(card) observer.observe(card);
    
    // Scroll vers la liste
    function scrollToList() {
        document.getElementById('listSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
    
    // Modal functions
    const modal = document.getElementById('detailsModal');
    
    function showDetails(reclamation) {
        let statusBadge = '';
        switch(reclamation.statut) {
            case 'soumise': statusBadge = 'badge-soumise'; break;
            case 'en_cours': statusBadge = 'badge-en_cours'; break;
            case 'traitee': statusBadge = 'badge-traitee'; break;
            case 'rejetee': statusBadge = 'badge-rejetee'; break;
            default: statusBadge = 'badge-soumise';
        }
        
        const dateSoumission = new Date(reclamation.date_soumission).toLocaleDateString('fr-FR') + ' ' + new Date(reclamation.date_soumission).toLocaleTimeString('fr-FR');
        
        document.getElementById('modalBody').innerHTML = `
            <div class="detail-row"><div class="detail-label">Référence</div><div class="detail-value">${escapeHtml(reclamation.reference)}</div></div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">${escapeHtml(reclamation.nom_service || 'Non spécifié')}</div></div>
            <div class="detail-row"><div class="detail-label">Catégorie</div><div class="detail-value">${escapeHtml(reclamation.categorie)}</div></div>
            <div class="detail-row"><div class="detail-label">Priorité</div><div class="detail-value">${escapeHtml(reclamation.priorite)}</div></div>
            <div class="detail-row"><div class="detail-label">Statut</div><div class="detail-value"><span class="badge ${statusBadge}">${escapeHtml(reclamation.statut)}</span></div></div>
            <div class="detail-row"><div class="detail-label">Objet</div><div class="detail-value">${escapeHtml(reclamation.objet)}</div></div>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${escapeHtml(reclamation.description).replace(/\n/g, '<br>')}</div></div>
            <div class="detail-row"><div class="detail-label">Lieu</div><div class="detail-value">${escapeHtml(reclamation.lieu || 'Non précisé')}</div></div>
            <div class="detail-row"><div class="detail-label">Date soumission</div><div class="detail-value">${dateSoumission}</div></div>
        `;
        modal.style.display = 'flex';
    }
    
    function closeModal() { modal.style.display = 'none'; }
    modal.addEventListener('click', function(e) { if(e.target === modal) closeModal(); });
    
    function escapeHtml(text) {
        if(!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

</body>
</html>
