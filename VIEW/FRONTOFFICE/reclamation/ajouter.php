<?php
session_start();

if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
    $_SESSION['user_type'] = 'citoyen';
}

require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $ctrl = new ReclamationController();
    
    $reference = $ctrl->genererReference();
    $id_citoyen = $_SESSION['user_id'];
    $id_service = !empty($_POST['id_service']) ? $_POST['id_service'] : null;
    $categorie = $_POST['categorie'];
    $objet = $_POST['objet'];
    $description = $_POST['description'];
    $priorite = $_POST['priorite'];
    $lieu = $_POST['lieu'] ?? null;
    
    $sql = "INSERT INTO reclamation (reference, id_citoyen, id_service, categorie, objet, description, lieu, priorite, statut, date_soumission) 
            VALUES (:reference, :id_citoyen, :id_service, :categorie, :objet, :description, :lieu, :priorite, 'soumise', NOW())";
    
    try {
        $req = $db->prepare($sql);
        $result = $req->execute([
            ':reference' => $reference,
            ':id_citoyen' => $id_citoyen,
            ':id_service' => $id_service,
            ':categorie' => $categorie,
            ':objet' => $objet,
            ':description' => $description,
            ':lieu' => $lieu,
            ':priorite' => $priorite
        ]);
        
        if($result){
            $success = "✅ Réclamation envoyée avec succès !<br>Référence : " . $reference;
        } else {
            $error = "❌ Erreur lors de l'envoi de votre réclamation.";
        }
    } catch(Exception $e) {
        $error = "❌ Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; }

        /* NAVBAR */
        .navbar { background: rgba(255,255,255,0.15); backdrop-filter: blur(16px); position: fixed; top: 0; width: 100%; z-index: 1000; padding: 1rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.2); transition: all 0.3s; }
        .navbar.scrolled { background: rgba(0,60,45,0.92); box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .navbar-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 45px; height: 45px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; }
        .logo-text h1 { font-size: 22px; font-weight: 800; color: white; }
        .logo-text p { font-size: 11px; color: rgba(255,255,255,0.7); }
        .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
        .nav-link { text-decoration: none; color: rgba(255,255,255,0.85); font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { color: white; }
        .lang-toggle { display: flex; gap: 5px; background: rgba(255,255,255,0.15); padding: 5px 10px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.25); }
        .lang-btn { background: none; border: none; padding: 5px 12px; border-radius: 25px; cursor: pointer; font-weight: 600; color: rgba(255,255,255,0.7); }
        .lang-btn.active { background: rgba(255,255,255,0.25); color: white; }

        /* PAGE SLIDESHOW BACKGROUND */
        .page-bg { position: fixed; inset: 0; z-index: -1; }
        .page-bg .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease; }
        .page-bg .slide.active { opacity: 1; }
        .page-bg-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0,77,61,0.88) 0%, rgba(0,20,15,0.93) 100%); }

        /* MAIN LAYOUT */
        .page-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 100px 20px 60px; }

        /* GLASS CARD */
        .glass-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 24px; padding: 36px; width: 100%; max-width: 680px; box-shadow: 0 30px 60px rgba(0,0,0,0.3); }
        .card-header { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.15); }
        .card-title { font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: white; font-family: 'Syne', sans-serif; }

        /* FORM */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: rgba(255,255,255,0.85); font-size: 14px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.25); border-radius: 10px; font-size: 14px; transition: all 0.2s; background: rgba(255,255,255,0.12); color: white; font-family: 'DM Sans', sans-serif; }
        .form-control::placeholder { color: rgba(255,255,255,0.45); }
        .form-control option { background: #1e3a30; color: white; }
        .form-control:focus { outline: none; border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.18); box-shadow: 0 0 0 3px rgba(255,255,255,0.1); }
        textarea.form-control { resize: vertical; min-height: 110px; }

        .btn-submit { width: 100%; background: rgba(255,255,255,0.2); color: white; padding: 14px; border: 1px solid rgba(255,255,255,0.35); border-radius: 30px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; letter-spacing: 0.5px; }
        .btn-submit:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(16,185,129,0.2); color: #6EE7B7; border: 1px solid rgba(16,185,129,0.3); }
        .alert-danger  { background: rgba(239,68,68,0.2);  color: #FCA5A5; border: 1px solid rgba(239,68,68,0.3); }

        .info-user { background: rgba(255,255,255,0.12); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.18); }

        .scanner-section { margin-bottom: 22px; padding: 15px; border: 2px dashed rgba(255,255,255,0.3); border-radius: 15px; background: rgba(255,255,255,0.06); text-align: center; }
        .scanner-section h3 { font-size: 15px; margin-bottom: 8px; color: white; }
        .scanner-section p  { font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 14px; }
        .btn-scan { background: rgba(255,255,255,0.18); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 30px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-scan:hover { background: rgba(255,255,255,0.28); transform: translateY(-2px); }
        #reader { width: 100%; max-width: 500px; margin: 15px auto; display: none; border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.2); }

        .error-message { color: #FCA5A5; font-size: 12px; margin-top: 5px; display: none; }
        .error-message.show { display: block; }

        .footer { background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); color: rgba(255,255,255,0.6); padding: 20px 2rem; text-align: center; }

        @media (max-width: 768px) { .navbar-container { flex-direction: column; } .nav-menu { justify-content: center; } }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="../../../index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text">
                <h1>InnoGov</h1>
                <p>Municipalité Tunisienne</p>
            </div>
        </a>
        <div class="nav-menu">
            <a href="../../../index.php" class="nav-link">Accueil</a>
            <a href="mes-reclamations.php" class="nav-link">Mes réclamations</a>
            <a href="ajouter.php" class="nav-link">Déposer</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">عربي</button>
            </div>
        </div>
    </div>
</nav>

<!-- FULL PAGE SLIDESHOW BACKGROUND -->
<div class="page-bg" id="pageBg">
    <div class="slide active" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia1.jpg');"></div>
    <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia2.jpg');"></div>
    <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia3.jpg');"></div>
    <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia4.jpg');"></div>
    <div class="page-bg-overlay"></div>
</div>

<div class="page-wrapper">
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-pen-alt"></i> Nouvelle Réclamation</h2>
        </div>
        
        <div class="info-user">
            <i class="fas fa-user-circle"></i>
            <span>Bienvenue, <strong><?= $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] ?></strong></span>
        </div>
        
        <div id="alertContainer">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
        </div>
        
        <div class="scanner-section">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #1A2E2A;"><i class="fas fa-qrcode"></i> Vous êtes devant un équipement défectueux ?</h3>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 15px;">Scannez son code-barres (ou QR Code) pour pré-remplir la réclamation instantanément.</p>
            <button type="button" class="btn-scan" id="btn-start-scan">
                <i class="fas fa-camera"></i> Scanner un équipement
            </button>
            <div id="reader"></div>
        </div>
        
        <form id="reclamationForm" method="POST" novalidate>
            <div class="form-group">
                <label class="form-label">Service concerné</label>
                <select name="id_service" class="form-control" id="service">
                    <option value="">-- Non spécifié --</option>
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['id_service'] ?>"><?= $s['nom_service'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Catégorie *</label>
                <select name="categorie" class="form-control" id="categorie">
                    <option value="">-- Choisir --</option>
                    <option value="administrative">📋 Administrative</option>
                    <option value="sociale">🤝 Sociale</option>
                    <option value="infrastructure">🏗️ Infrastructure</option>
                    <option value="sante">🏥 Santé</option>
                    <option value="education">📚 Éducation</option>
                    <option value="transport">🚌 Transport</option>
                    <option value="environnement">🌳 Environnement</option>
                    <option value="autre">📌 Autre</option>
                </select>
                <div class="error-message" id="categorieError">Veuillez sélectionner une catégorie</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Objet *</label>
                <input type="text" name="objet" class="form-control" id="objet" placeholder="Titre clair et concis">
                <div class="error-message" id="objetError">L'objet doit contenir au moins 5 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" id="description" placeholder="Décrivez votre problème en détail"></textarea>
                <div class="error-message" id="descriptionError">La description doit contenir au moins 20 caractères</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Priorité *</label>
                <select name="priorite" class="form-control" id="priorite">
                    <option value="faible">🟢 Faible</option>
                    <option value="normale" selected>🟡 Normale</option>
                    <option value="haute">🟠 Haute</option>
                    <option value="urgente">🔴 Urgente</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Lieu</label>
                <input type="text" name="lieu" class="form-control" id="lieu" placeholder="Adresse, quartier...">
            </div>
            
            <button type="submit" class="btn-submit" id="submitBtn"><i class="fas fa-paper-plane"></i> Envoyer ma réclamation</button>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2024 InnoGov &mdash; Municipalité Tunisienne</p>
</footer>

<script src="../../../ASSETS/JS/script.js"></script>
<script>
    // Page Background Slideshow
    const pageBgSlides = document.querySelectorAll('#pageBg .slide');
    if (pageBgSlides.length > 0) {
        let bgCurrent = 0;
        setInterval(() => {
            pageBgSlides[bgCurrent].classList.remove('active');
            bgCurrent = (bgCurrent + 1) % pageBgSlides.length;
            pageBgSlides[bgCurrent].classList.add('active');
        }, 3000);
    }

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 50) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        }
    });

    // Scanner Logic - Direct API for better UX
    let html5QrCode = null;
    const btnScan = document.getElementById('btn-start-scan');
    const readerDiv = document.getElementById('reader');
    let isScanning = false;

    function resetScannerBtn() {
        readerDiv.style.display = 'none';
        btnScan.innerHTML = '<i class="fas fa-camera"></i> Scanner un équipement';
        btnScan.style.background = '#006D5B';
        isScanning = false;
    }

    btnScan.addEventListener('click', () => {
        if (!isScanning) {
            readerDiv.style.display = 'block';
            btnScan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initialisation...';
            
            if(!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Try to find back camera
                    let cameraId = devices[0].id;
                    for (let i = 0; i < devices.length; i++) {
                        if (devices[i].label.toLowerCase().includes('back') || devices[i].label.toLowerCase().includes('arrière')) {
                            cameraId = devices[i].id;
                            break;
                        }
                    }
                    
                    html5QrCode.start(
                        cameraId, 
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText, decodedResult) => {
                            // On Success
                            html5QrCode.stop().then(() => {
                                readerDiv.style.display = 'none';
                                btnScan.innerHTML = '<i class="fas fa-check-circle"></i> Équipement détecté !';
                                btnScan.style.background = '#10B981';
                                isScanning = false;
                                
                                // Remplissage du formulaire
                                document.getElementById('objet').value = "Panne équipement : " + decodedText;
                                document.getElementById('categorie').value = "infrastructure";
                                document.getElementById('lieu').value = "Position équip. " + decodedText;
                                
                                // Effets visuels
                                document.getElementById('objet').style.borderColor = '#10B981';
                                document.getElementById('categorie').style.borderColor = '#10B981';
                                document.getElementById('lieu').style.borderColor = '#10B981';
                                
                                setTimeout(() => {
                                    btnScan.innerHTML = '<i class="fas fa-camera"></i> Scanner un autre';
                                    btnScan.style.background = '#006D5B';
                                }, 3000);
                            }).catch(err => console.error("Erreur arrêt scanner", err));
                        },
                        (errorMessage) => {
                            // Ignorer les erreurs de scan continu
                        }
                    ).then(() => {
                        isScanning = true;
                        btnScan.innerHTML = '<i class="fas fa-times"></i> Annuler le scan';
                        btnScan.style.background = '#DC2626';
                    }).catch((err) => {
                        alert("Erreur de démarrage de la caméra : " + err);
                        resetScannerBtn();
                    });
                } else {
                    alert("Aucune caméra trouvée sur cet appareil.");
                    resetScannerBtn();
                }
            }).catch(err => {
                alert("Impossible d'accéder à la caméra. Vérifiez que vous utilisez 'localhost' ou 'HTTPS', et que les permissions sont accordées.");
                resetScannerBtn();
            });
        } else {
            // Stop scanning
            if(html5QrCode) {
                html5QrCode.stop().then(() => {
                    resetScannerBtn();
                }).catch(err => {
                    console.error("Erreur lors de l'arrêt", err);
                    resetScannerBtn();
                });
            }
        }
    });
</script>
</body>
</html>