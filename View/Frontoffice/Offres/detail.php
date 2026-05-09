<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($offre['titre']) ?> - INNOC@V</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
    <style>
        .toast-notif {
            position: fixed; bottom: 30px; right: 30px; background: #006D5B;
            color: white; padding: 1rem 1.5rem; border-radius: 0.5rem;
            display: flex; align-items: center; gap: 0.75rem; z-index: 1000;
            animation: slideInRight 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toast-notif.error { background: #dc3545; }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .error-message {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }
        .form-control.is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="loader"><div class="spinner"></div></div>
    <div class="hero-slideshow">
        <img src="assets/images/tunisia1.png" class="slide active" alt="Tunisia">
        <img src="assets/images/tunisia2.png" class="slide" alt="Tunisia">
        <img src="assets/images/tunisia3.png" class="slide" alt="Tunisia">
        <img src="assets/images/tunisia4.png" class="slide" alt="Tunisia">
        <div class="hero-overlay"></div>
    </div>
  <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="logo">
                <img src="/ProjettWeb/assets/images/logo.png" alt="INNOGOV" style="height: 60px; object-fit: contain;">
            </a>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link" data-i18n="home">Accueil</a></li>
                <li><a href="index.php?controller=offre&action=lister" class="nav-link active" data-i18n="services">Services</a></li>
            </ul>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
            </div>
        </div>
    </nav>
    <main class="container" style="padding-top: 6rem;">
        <div class="card" style="margin-bottom: 2rem;">
            <h1><?= htmlspecialchars($offre['titre']) ?></h1>
            <p><strong><i class="fas fa-building"></i> Entité :</strong> <?= htmlspecialchars($offre['entite']) ?></p>
            <p><strong>Description :</strong></p>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
            <p><strong><i class="fas fa-users"></i> Nombre de postes :</strong> <?= $offre['nombre_postes'] ?></p>
            <p><strong><i class="fas fa-calendar-alt"></i> Date limite :</strong> <?= $offre['date_limite'] ?></p>
        </div>
        <div class="card">
            <?php 
            $isFull = ($offre['nb_candidats'] ?? 0) >= ($offre['nombre_postes'] ?? 0);
            if ($isFull): ?>
                <div style="text-align: center; padding: 2rem;">
                    <div style="font-size: 4rem; color: #dc3545; margin-bottom: 1rem;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2 style="color: #dc3545;">Cette offre est désormais complète</h2>
                    <p style="color: #666; margin-bottom: 2rem;">Le nombre maximum de candidatures pour ce poste a été atteint. Nous vous remercions de votre intérêt.</p>
                    <a href="index.php?controller=offre&action=lister" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Voir d'autres offres
                    </a>
                </div>
            <?php else: ?>
                <h2 data-i18n="applyTitle">Postuler</h2>
                <form id="candidatureForm" novalidate enctype="multipart/form-data">
                    <input type="hidden" name="offre_id" value="<?= $offre['id_offre'] ?>">
                    
                    <div class="form-group">
                        <label class="form-label" data-i18n="lastName">Nom *</label>
                        <input type="text" name="nom" id="nom" class="form-control">
                        <div class="error-message" id="error-nom"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="firstName">Prénom *</label>
                        <input type="text" name="prenom" id="prenom" class="form-control">
                        <div class="error-message" id="error-prenom"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="email">Email *</label>
                        <input type="email" name="email" id="email" class="form-control">
                        <div class="error-message" id="error-email"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="phone">Téléphone *</label>
                        <input type="text" name="num_tel" id="tel" class="form-control">
                        <div class="error-message" id="error-tel"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="cv">CV (PDF/DOCX, max 2Mo) *</label>
                        <input type="file" name="cv" id="cv" class="form-control" accept=".pdf,.docx">
                        <div class="error-message" id="error-cv"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> <span data-i18n="submitBtn">Envoyer ma candidature</span>
                    </button>
                    <a href="index.php?controller=offre&action=lister" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><h4>INNOC@V</h4><p data-i18n="footerDesc">Solutions numériques pour les citoyens</p></div>
            <div class="footer-section"><h4 data-i18n="contactTitle">Contact</h4><p><i class="fas fa-envelope"></i> contact@innocv.gov.ma</p></div>
        </div>
        <div class="footer-bottom"><p>&copy; 2025 INNOC@V - <span data-i18n="allRights">Tous droits réservés</span></p></div>
    </footer>

    <script>
        function showToast(msg, type) {
            const toast = document.createElement('div');
            toast.className = `toast-notif ${type === 'error' ? 'error' : ''}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
        const nomInput = document.getElementById('nom');
        const prenomInput = document.getElementById('prenom');
        const emailInput = document.getElementById('email');
        const telInput = document.getElementById('tel');
        const cvInput = document.getElementById('cv');
        function setError(id, msg) {
            document.getElementById(id).innerHTML = msg;
            document.getElementById(id.replace('error-', '')).classList.add('is-invalid');
        }
        function clearError(id) {
            document.getElementById(id).innerHTML = '';
            document.getElementById(id.replace('error-', '')).classList.remove('is-invalid');
        }
        function validateNom() {
            const val = nomInput.value.trim();
            if (!val) { setError('error-nom', 'Le nom est requis.'); return false; }
            if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/.test(val)) { setError('error-nom', 'Lettres et tirets uniquement.'); return false; }
            clearError('error-nom');
            return true;
        }
        function validatePrenom() {
            const val = prenomInput.value.trim();
            if (!val) { setError('error-prenom', 'Le prénom est requis.'); return false; }
            if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/.test(val)) { setError('error-prenom', 'Lettres et tirets uniquement.'); return false; }
            clearError('error-prenom');
            return true;
        }
        function validateEmail() {
            const val = emailInput.value.trim();
            if (!val) { setError('error-email', 'L\'email est requis.'); return false; }
            if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(val)) { setError('error-email', 'Email invalide (ex: nom@domaine.fr).'); return false; }
            clearError('error-email');
            return true;
        }
        function validateTel() {
            const val = telInput.value.trim();
            if (!val) { setError('error-tel', 'Le téléphone est requis.'); return false; }
            if (!/^[\+\-\s0-9]{8,15}$/.test(val)) { setError('error-tel', '8-15 chiffres, + - espaces autorisés.'); return false; }
            clearError('error-tel');
            return true;
        }
        function validateCV() {
            const file = cvInput.files[0];
            if (!file) { setError('error-cv', 'Veuillez sélectionner un CV.'); return false; }
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['pdf','docx'].includes(ext)) { setError('error-cv', 'Format non autorisé (PDF ou DOCX).'); return false; }
            if (file.size > 2 * 1024 * 1024) { setError('error-cv', 'Le CV ne doit pas dépasser 2 Mo.'); return false; }
            clearError('error-cv');
            return true;
        }
        nomInput.addEventListener('blur', validateNom);
        prenomInput.addEventListener('blur', validatePrenom);
        emailInput.addEventListener('blur', validateEmail);
        telInput.addEventListener('blur', validateTel);
        cvInput.addEventListener('change', validateCV);
        const form = document.getElementById('candidatureForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const isNomOk = validateNom();
            const isPrenomOk = validatePrenom();
            const isEmailOk = validateEmail();
            const isTelOk = validateTel();
            const isCvOk = validateCV();

            if (!isNomOk || !isPrenomOk || !isEmailOk || !isTelOk || !isCvOk) {
                showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                return;
            }

            const formData = new FormData(form);
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

            try {
                const res = await fetch('index.php?controller=candidature&action=postuler', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast('✅ Candidature envoyée ! Votre badge QR est prêt.', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=candidature&action=badge&id=' + data.candidature_id;
                    }, 1500);
                } else {
                    showToast(data.message || 'Envoi avec succe', 'success');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
                }
            } catch (err) {
                showToast('Erreur de connexion', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
            }
        };
    </script>
</body>
</html> 