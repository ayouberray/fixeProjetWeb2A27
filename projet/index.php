<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// ==========================================
// PSEUDO-CRON : Rappels Email Automatiques
// Vérifie et envoie les rappels maximum 1 fois toutes les 30 minutes
// ==========================================
$lock_file = __DIR__ . '/api/.reminder_last_run';
$should_run = true;
if (file_exists($lock_file)) {
    $last_run = (int)file_get_contents($lock_file);
    if (time() - $last_run < 1800) { // 1800 secondes = 30 minutes
        $should_run = false;
    }
}
if ($should_run) {
    file_put_contents($lock_file, time());
    // Exécution non-bloquante en arrière-plan
    register_shutdown_function(function() {
        @include __DIR__ . '/api/send_reminders.php';
    });
}

require_once __DIR__."/MODEL/config.php";

$db = Config::getConnexion();

$totalCitoyens = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'citoyen'")->fetch();
$totalServices = $db->query("SELECT COUNT(*) as total FROM services WHERE statut = 'actif'")->fetch();
$totalRdvs = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut = 'termine'")->fetch();
$services = $db->query("SELECT * FROM services WHERE statut = 'actif' LIMIT 6")->fetchAll();

$parService = $db->query("
    SELECT s.nom_service, COUNT(r.id_rdv) as total 
    FROM services s
    LEFT JOIN rendez_vous r ON r.id_service = s.id_service
    WHERE s.statut = 'actif'
    GROUP BY s.id_service, s.nom_service
    ORDER BY total DESC
    LIMIT 4
")->fetchAll();

$news = [
    ['title' => 'Lancement de la plateforme InnoGov', 'date' => '10 Avril 2024', 'excerpt' => 'Nouvelle plateforme pour faciliter les démarches administratives...', 'image' => '/projet/assets/images/news/news1.jpg'],
    ['title' => 'Réunion du conseil municipal', 'date' => '05 Avril 2024', 'excerpt' => 'Discussion sur les projets de développement de la ville...', 'image' => '/projet/assets/images/news/news2.jpg'],
    ['title' => 'Nouveau service en ligne', 'date' => '01 Avril 2024', 'excerpt' => 'Découvrez notre nouveau service de prise de rendez-vous en ligne...', 'image' => '/projet/assets/images/news/news3.jpg']
];
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Services Municipaux Digitalisés</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <img src="/projet/assets/images/innogov-logo.png" alt="InnoGov" class="logo-img">
                <div class="logo-text">
                    <p class="logo-subtitle">Municipalité Tunisienne</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link active">Accueil</a>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link">Mes RDV</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">عربي</button>
            </div>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION AVEC VIDÉO DYNAMIQUE -->
<section class="hero">
    <video class="hero-video" autoplay loop muted playsinline>
        <source src="/projet/assets/video/background.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Chiffres Clés</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center;">
            <div class="stats-grid" style="flex: 1; min-width: 300px;">
                <div class="stat-card reveal">
                    <i class="fas fa-users"></i>
                    <div class="number" data-target="<?= (int)$totalCitoyens['total'] ?>">0</div>
                    <div class="label">Citoyens</div>
                </div>
                <div class="stat-card reveal">
                    <i class="fas fa-concierge-bell"></i>
                    <div class="number" data-target="<?= (int)$totalServices['total'] ?>">0</div>
                    <div class="label">Services</div>
                </div>
                <div class="stat-card reveal">
                    <i class="fas fa-calendar-check"></i>
                    <div class="number" data-target="<?= (int)$totalRdvs['total'] ?>">0</div>
                    <div class="label">RDV traités</div>
                </div>
                <div class="stat-card reveal">
                    <i class="fas fa-award"></i>
                    <div class="number" data-target="<?= date('Y') - 2019 ?>">0</div>
                    <div class="label">Années d'expérience</div>
                </div>
            </div>
            
            <div class="card reveal" style="flex: 1; min-width: 300px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
                <h3 style="text-align: center; margin-bottom: 20px; font-size: 1.1rem; color: #333;"><i class="fas fa-chart-pie" style="color: #3b82f6;"></i> Top Services Demandés</h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="homeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>



<section class="section">
    <div class="container">
        <h2 class="section-title">Actualités</h2>
        <div class="news-grid">
            <?php foreach($news as $item): ?>
            <div class="news-card reveal">
                <div class="news-image">
                    <img src="<?= $item['image'] ?>" alt="Actualité">
                </div>
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> <?= $item['date'] ?></span>
                    <h3 class="news-title"><?= $item['title'] ?></h3>
                    <p class="news-excerpt"><?= $item['excerpt'] ?></p>
                    <a href="#" class="btn btn-outline btn-sm" style="margin-top:15px;">Lire la suite</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4><i class="fas fa-building"></i> InnoGov</h4>
            <p>Plateforme de services municipaux<br>Modernisation de l'administration tunisienne</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-phone"></i> Contact</h4>
            <p><i class="fas fa-phone-alt"></i> +216 70 000 000</p>
            <p><i class="fas fa-envelope"></i> contact@innogov.tn</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-clock"></i> Horaires</h4>
            <p>Lundi - Vendredi: 8h30 - 15h30</p>
            <p>Samedi - Dimanche: Fermé</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 InnoGov - Tous droits réservés</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const homeLabels = <?= json_encode(array_column($parService, 'nom_service')) ?>;
    const homeData = <?= json_encode(array_column($parService, 'total')) ?>;

    if (document.getElementById('homeChart')) {
        Chart.defaults.font.family = "'Inter', sans-serif";
        new Chart(document.getElementById('homeChart'), {
            type: 'polarArea',
            data: {
                labels: homeLabels,
                datasets: [{
                    data: homeData,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    }
});
</script>
<!-- CHATBOT -->
<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-header">
        <div class="chatbot-title">
            <i class="fas fa-robot"></i> InnoBot
        </div>
        <button id="chatbot-close" class="chatbot-close"><i class="fas fa-times"></i></button>
    </div>
    <div id="chatbot-messages" class="chatbot-messages">
        <!-- Messages will be injected here -->
    </div>
    <div id="chatbot-options" class="chatbot-options">
        <!-- Quick reply buttons will be injected here -->
    </div>
    <div class="chatbot-input-area">
        <button id="chatbot-upload-btn" title="Importer une photo"><i class="fas fa-paperclip"></i></button>
        <input type="file" id="chatbot-file-input" accept="image/*" style="display: none;">
        <input type="text" id="chatbot-input" placeholder="Posez votre question (ex: rdv 12)..." autocomplete="off">
        <button id="chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
<button id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
</button>

<style>
/* Chatbot CSS */
.chatbot-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #2563eb;
    color: white;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    font-size: 24px;
    cursor: pointer;
    z-index: 9999;
    transition: transform 0.3s ease, background-color 0.3s ease;
}
.chatbot-toggle:hover {
    transform: scale(1.1);
    background-color: var(--primary-dark);
}
.chatbot-container {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    max-height: 80vh;
    height: 600px;
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    overflow: hidden;
    transform: translateY(20px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
}
.chatbot-container.active {
    transform: translateY(0);
    opacity: 1;
    pointer-events: all;
}
.chatbot-header {
    background-color: #2563eb;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.chatbot-title {
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
}
.chatbot-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
    background-color: #f8fafc;
    min-height: 100px;
}
.chat-msg {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 0.95rem;
    line-height: 1.4;
    position: relative;
    animation: fadeIn 0.3s ease;
}
.chat-msg.bot {
    background-color: white;
    color: #333;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.chat-msg.user {
    background-color: #2563eb;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
    box-shadow: 0 2px 5px rgba(37, 99, 235, 0.2);
}
.chatbot-options {
    padding: 15px;
    background-color: white;
    border-top: 1px solid #eee;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
    max-height: 200px;
    overflow-y: auto;
}
.chatbot-input-area {
    display: flex;
    padding: 10px;
    background-color: white;
    border-top: 1px solid #eee;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}
.chatbot-input-area input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    outline: none;
    font-family: inherit;
    font-size: 0.9rem;
}
.chatbot-input-area button {
    background-color: #2563eb;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.2s;
}
.chatbot-input-area button:hover {
    background-color: var(--primary-dark);
}
#chatbot-upload-btn {
    background-color: #10b981;
}
#chatbot-upload-btn:hover {
    background-color: #059669;
}
.chat-btn {
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: var(--primary-color);
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    text-align: left;
}
.chat-btn:hover {
    background-color: #e2e8f0;
    transform: translateY(-1px);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Chatbot Logic
document.addEventListener('DOMContentLoaded', () => {
    const botToggle = document.getElementById('chatbot-toggle');
    const botContainer = document.getElementById('chatbot-container');
    const botClose = document.getElementById('chatbot-close');
    const messagesContainer = document.getElementById('chatbot-messages');
    const optionsContainer = document.getElementById('chatbot-options');
    const inputField = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send-btn');
    const uploadBtn = document.getElementById('chatbot-upload-btn');
    const fileInput = document.getElementById('chatbot-file-input');

    botToggle.addEventListener('click', () => {
        botContainer.classList.add('active');
        if(messagesContainer.children.length === 0) {
            showMenu();
        }
    });

    botClose.addEventListener('click', () => {
        botContainer.classList.remove('active');
    });

    sendBtn.addEventListener('click', handleUserText);
    let selectedImageBase64 = null;

    inputField.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') handleUserText();
    });
    uploadBtn.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = function(event) {
                selectedImageBase64 = event.target.result;
                addMessage(`Photo prête à être envoyée : <strong>${file.name}</strong>`, 'bot', true);
            };
            reader.readAsDataURL(file);
        }
    });

    function addMessage(text, sender = 'bot', isHtml = false, id = null) {
        const div = document.createElement('div');
        div.className = `chat-msg ${sender}`;
        if(id) div.id = id;
        if(isHtml) div.innerHTML = text;
        else div.textContent = text;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function removeMessage(id) {
        const el = document.getElementById(id);
        if(el) el.remove();
    }

    function handleUserText() {
        const text = inputField.value.trim();
        if(!text && !selectedImageBase64) return;
        
        let msg = text;
        if (selectedImageBase64) {
            msg += ` <br><img src="${selectedImageBase64}" style="max-width:100%; border-radius:10px; margin-top:5px;">`;
        }
        if (text || selectedImageBase64) {
            addMessage(msg || '📸 Photo envoyée', 'user', true);
        }
        
        inputField.value = '';
        clearOptions();

        const typingId = 'typing-' + Date.now();
        addMessage("<i class='fas fa-ellipsis-h'></i>", 'bot', true, typingId);

        sendApiRequest(text, selectedImageBase64, typingId);
        selectedImageBase64 = null; // reset
        fileInput.value = ''; // reset file input
    }

    function sendApiRequest(text, imageBase64, typingId) {
        let url = '/projet/api/chatbot_api.php';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ q: text, image: imageBase64 })
        })
            .then(res => res.json())
            .then(data => {
                removeMessage(typingId);
                addMessage(data.text, 'bot', true);
                addBackOption();
            })
            .catch(err => {
                removeMessage(typingId);
                addMessage("❌ Erreur de connexion au serveur.", 'bot');
                addBackOption();
            });
    }

    function clearOptions() {
        optionsContainer.innerHTML = '';
    }

    function addOption(text, callback) {
        const btn = document.createElement('button');
        btn.className = 'chat-btn';
        btn.textContent = text;
        btn.addEventListener('click', () => {
            addMessage(text, 'user');
            clearOptions();
            setTimeout(callback, 500); // Simulate bot typing delay
        });
        optionsContainer.appendChild(btn);
    }

    // Scenarios
    function showMenu() {
        addMessage("Bonjour ! 👋 Je suis InnoBot. Comment puis-je vous aider aujourd'hui ?");
        addOption("📅 Prendre un rendez-vous", showPrendreRdv);
        addOption("⚙️ Gérer mes rendez-vous (Consulter/Modifier)", showGererRdv);
        addOption("ℹ️ Horaires & Contact", showInfos);
    }

    function showPrendreRdv() {
        addMessage("Pour prendre un rendez-vous, vous devez vous rendre sur notre espace de réservation. Vous pourrez choisir le service et la date souhaitée.", false);
        addMessage("<a href='/projet/VIEW/frontoffice/citoyen-reserver-rdv.php' class='btn btn-primary btn-sm' style='display:inline-block; margin-top:10px; color:white; text-decoration:none;'>Prendre RDV maintenant</a>", 'bot', true);
        addBackOption();
    }

    function showGererRdv() {
        addMessage("Dans votre espace 'Mes RDV', vous pouvez consulter la liste de vos rendez-vous, les modifier ou les annuler à tout moment.", false);
        addMessage("<a href='/projet/VIEW/frontoffice/citoyen-mes-rdv.php' class='btn btn-outline btn-sm' style='display:inline-block; margin-top:10px; text-decoration:none;'>Accéder à Mes RDV</a>", 'bot', true);
        addBackOption();
    }

    function showInfos() {
        addMessage("🏛️ <strong>Horaires d'ouverture :</strong><br>Du Lundi au Vendredi<br>De 08h30 à 15h30.<br><br>📞 <strong>Contact :</strong><br>Tél : +216 70 000 000<br>Email : contact@innogov.tn", 'bot', true);
        addBackOption();
    }

    function addBackOption() {
        addOption("🔙 Retour au menu principal", showMenu);
    }

    // --- INITIALISATION STATISTIQUES ---
    const homeLabels = <?= json_encode(array_column($parService, 'nom_service')) ?>;
    const homeData = <?= json_encode(array_column($parService, 'total')) ?>;

    new Chart(document.getElementById('homeChart'), {
        type: 'doughnut',
        data: {
            labels: homeLabels,
            datasets: [{
                data: homeData,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });

    // Animation des nombres
    document.querySelectorAll('.number').forEach(num => {
        const target = +num.getAttribute('data-target');
        const update = () => {
            const current = +num.innerText;
            const step = target / 30;
            if (current < target) {
                num.innerText = Math.ceil(current + step);
                setTimeout(update, 40);
            } else {
                num.innerText = target;
            }
        };
        update();
    });
});
</script>
</body>
</html>