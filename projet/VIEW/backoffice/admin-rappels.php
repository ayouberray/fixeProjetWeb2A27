<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../MODEL/config.php";

$db = Config::getConnexion();

// Statistiques pour affichage
$total_avec_email = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE citoyen_email IS NOT NULL AND citoyen_email != ''")->fetch()['total'];
$total_rappels_envoyes = $db->query("SELECT COUNT(*) as total FROM rappels_log")->fetch()['total'];
$total_a_venir_24h = $db->query("SELECT COUNT(*) as total FROM rendez_vous r WHERE citoyen_email IS NOT NULL AND statut NOT IN ('annule','termine') AND date_heure BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR) AND NOT EXISTS (SELECT 1 FROM rappels_log rl WHERE rl.id_rdv = r.id_rdv AND rl.type_rappel = '1jour')")->fetch()['total'];

// Historique des RDV avec email
$rdvs_email = $db->query("
    SELECT r.id_rdv, r.citoyen_nom, r.citoyen_email, r.date_heure, r.statut, s.nom_service,
           (SELECT COUNT(*) FROM rappels_log rl WHERE rl.id_rdv = r.id_rdv) as nb_rappels
    FROM rendez_vous r
    LEFT JOIN services s ON r.id_service = s.id_service
    WHERE r.citoyen_email IS NOT NULL AND r.citoyen_email != ''
    ORDER BY r.date_heure DESC
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappels Email - InnoGov Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
    <style>
        .reminder-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .reminder-stat-card { background: white; border-radius: 12px; padding: 25px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-top: 4px solid; }
        .reminder-stat-card.blue { border-color: #2563eb; }
        .reminder-stat-card.green { border-color: #10b981; }
        .reminder-stat-card.orange { border-color: #f59e0b; }
        .reminder-stat-card .stat-num { font-size: 2.5rem; font-weight: 700; color: #1e293b; }
        .reminder-stat-card .stat-label { color: #64748b; font-size: 0.9rem; margin-top: 5px; }
        .send-btn { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; padding: 16px 40px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(37,99,235,0.3); }
        .send-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
        .send-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .result-box { margin-top: 20px; padding: 20px; border-radius: 12px; display: none; }
        .result-box.success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .result-box.warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
        .result-box.error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        .badge-sent { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-pending { background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .config-card { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 25px; margin-bottom: 25px; }
        .config-card h3 { color: #1e40af; margin-top: 0; }
        .config-card code { background: #dbeafe; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 0.9em; }
        .config-warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; margin-top: 10px; font-size: 0.9rem; color: #92400e; }
    </style>
</head>
<body>
<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <img src="/projet/assets/images/innogov-logo.png" alt="InnoGov" class="logo-img">
                <div class="logo-text"><p class="logo-subtitle">Municipalité Tunisienne</p></div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link">Accueil</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin RDV</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/projet/VIEW/backoffice/admin-rappels.php" class="nav-link active">Rappels Email</a>
        </div>
    </div>
</nav>

<div class="container" style="padding-top: 40px;">

    <div class="card reveal">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <h2 class="card-title"><i class="fas fa-bell"></i> Gestion des Rappels Email</h2>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour Admin
            </a>
        </div>

        <!-- Statistiques -->
        <div class="reminder-stats">
            <div class="reminder-stat-card blue">
                <div class="stat-num"><?= $total_avec_email ?></div>
                <div class="stat-label"><i class="fas fa-envelope"></i> RDV avec email</div>
            </div>
            <div class="reminder-stat-card orange">
                <div class="stat-num"><?= $total_a_venir_24h ?></div>
                <div class="stat-label"><i class="fas fa-clock"></i> Rappels à envoyer (24h)</div>
            </div>
            <div class="reminder-stat-card green">
                <div class="stat-num"><?= $total_rappels_envoyes ?></div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> Rappels envoyés</div>
            </div>
        </div>

        <!-- Configuration -->
        <div class="config-card">
            <h3><i class="fas fa-cog"></i> Configuration SMTP (Gmail)</h3>
            <p>Pour activer l'envoi réel d'emails, ouvrez le fichier <code>api/send_reminders.php</code> (lignes 18-19) et remplissez :</p>
            <ul style="color: #1e40af; line-height: 2;">
                <li><strong>SMTP_USER</strong> : Votre adresse Gmail (ex: <code>monamed@gmail.com</code>)</li>
                <li><strong>SMTP_PASS</strong> : Votre <strong>mot de passe d'application Gmail</strong> (16 caractères)</li>
            </ul>
            <div class="config-warning">
                <i class="fas fa-info-circle"></i> Pour créer un mot de passe d'application Gmail :
                <strong>Google Account → Sécurité → Validation en 2 étapes → Mots de passe d'application</strong>
            </div>
        </div>

        <!-- Bouton d'envoi -->
        <div style="text-align: center; margin: 30px 0;">
            <button class="send-btn" id="sendBtn" onclick="sendReminders()">
                <i class="fas fa-paper-plane" id="sendIcon"></i>
                <span id="sendText">🔔 Envoyer les rappels maintenant</span>
            </button>
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 12px;">
                Envoie un rappel à tous les citoyens ayant un RDV dans les 24 prochaines heures.
            </p>
        </div>

        <div class="result-box" id="resultBox"></div>

        <!-- Tableau des RDV avec email -->
        <h3 style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #1e293b;">
            <i class="fas fa-list"></i> Historique des RDV avec email
        </h3>

        <?php if (empty($rdvs_email)): ?>
            <div class="alert alert-info">Aucun rendez-vous avec email pour le moment. Les prochains RDV réservés avec une adresse email apparaîtront ici.</div>
        <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Citoyen</th>
                        <th>Email</th>
                        <th>Service</th>
                        <th>Date RDV</th>
                        <th>Statut</th>
                        <th>Rappel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs_email as $r): ?>
                    <tr>
                        <td><strong>#<?= $r['id_rdv'] ?></strong></td>
                        <td><?= htmlspecialchars($r['citoyen_nom']) ?></td>
                        <td><i class="fas fa-envelope" style="color:#2563eb;"></i> <?= htmlspecialchars($r['citoyen_email']) ?></td>
                        <td><?= htmlspecialchars($r['nom_service'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['date_heure'])) ?></td>
                        <td><?= ucfirst($r['statut']) ?></td>
                        <td>
                            <?php if ($r['nb_rappels'] > 0): ?>
                                <span class="badge-sent"><i class="fas fa-check"></i> <?= $r['nb_rappels'] ?> envoyé(s)</span>
                            <?php else: ?>
                                <span class="badge-pending"><i class="fas fa-clock"></i> En attente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov</h4><p>Plateforme de services municipaux</p></div>
        <div class="footer-section"><h4>Contact</h4><p>Tel: +216 70 000 000</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun-Ven: 8h30 - 15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

<script>
function sendReminders() {
    const btn = document.getElementById('sendBtn');
    const icon = document.getElementById('sendIcon');
    const text = document.getElementById('sendText');
    const box  = document.getElementById('resultBox');

    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    text.textContent = 'Envoi en cours...';
    box.style.display = 'none';

    fetch('/projet/api/send_reminders.php')
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            icon.className = 'fas fa-paper-plane';
            text.textContent = '🔔 Envoyer les rappels maintenant';
            box.style.display = 'block';

            if (data.total_found === 0) {
                box.className = 'result-box warning';
                box.innerHTML = `<i class="fas fa-info-circle"></i> <strong>Aucun rappel à envoyer pour le moment.</strong><br>Pas de rendez-vous dans les prochaines 24h avec un email et un rappel non encore envoyé.`;
            } else if (data.errors === 0) {
                box.className = 'result-box success';
                box.innerHTML = `<i class="fas fa-check-circle"></i> <strong>${data.sent} rappel(s) envoyé(s) avec succès !</strong><br>
                    <ul style="margin-top: 10px;">
                        ${data.details.map(d => `<li>✅ RDV #${d.rdv} → ${d.email}</li>`).join('')}
                    </ul>`;
                setTimeout(() => location.reload(), 2500);
            } else {
                box.className = 'result-box error';
                box.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>${data.sent} envoyé(s), ${data.errors} erreur(s).</strong><br>
                    <p>Vérifiez la configuration SMTP dans <code>api/send_reminders.php</code>.</p>
                    <ul style="margin-top: 10px;">
                        ${data.details.map(d => d.status === 'error' ? `<li>❌ RDV #${d.rdv} → ${d.error}</li>` : `<li>✅ RDV #${d.rdv} → ${d.email}</li>`).join('')}
                    </ul>`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            icon.className = 'fas fa-paper-plane';
            text.textContent = '🔔 Envoyer les rappels maintenant';
            box.style.display = 'block';
            box.className = 'result-box error';
            box.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Erreur de connexion : ${err.message}`;
        });
}
</script>
</body>
</html>
