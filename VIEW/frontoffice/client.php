
<?php
 
require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';
require_once __DIR__ . '/../../MODEL/Demande.php';
require_once __DIR__ . '/../../MODEL/SuiviReponse.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user_id'] = 2;
$_SESSION['user_nom'] = 'Ben Ali';
$_SESSION['user_prenom'] = 'Mohamed';
$_SESSION['user_role'] = 'citoyen';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'fr';
$_SESSION['lang'] = $lang;

// Chatbot géré entièrement en JavaScript (voir bas de page)

$traductions = [
    'fr' => [
        'title' => 'InnoGov • Portail Citoyen • Mairie',
        'services' => 'Services', 'how_it_works' => 'Comment ça marche', 'conversations' => 'Mes Conversations',
        'my_documents' => 'Mes Documents', 'faq' => 'FAQ', 'hero_title' => 'Votre Mairie,', 'hero_title_span' => 'Partout avec vous.',
        'hero_text' => 'Accédez à une administration moderne, rapide et transparente.',
        'new_request' => 'Nouvelle Demande', 'my_docs_btn' => 'Mes Documents',
        'our_services' => 'Nos Services en Ligne', 'services_subtitle' => 'Sélectionnez le service dont vous avez besoin.',
        'urbanisme' => 'Urbanisme', 'urbanisme_desc' => 'Permis de construire, déclarations de travaux.',
        'etat_civil' => 'État Civil', 'etat_civil_desc' => 'Actes de naissance, mariage, décès.',
        'voirie' => 'Voirie', 'voirie_desc' => 'Signalements de dégradations.',
        'social' => 'Social', 'social_desc' => 'Aides municipales, inscriptions.',
        'how_title' => 'Comment ça marche ?', 'how_subtitle' => 'Trois étapes simples.',
        'step1_title' => 'Déposez', 'step1_desc' => 'Remplissez le formulaire en ligne.',
        'step2_title' => 'Suivez', 'step2_desc' => 'Recevez des notifications en temps réel.',
        'step3_title' => 'Récupérez', 'step3_desc' => 'Téléchargez votre document officiel.',
        'conversations_title' => 'Mes Conversations', 'conversations_subtitle' => 'Échangez avec l\'administration.',
        'no_conversations' => 'Aucune conversation.', 'create_request' => 'Créer une demande →',
        'administration' => '🏛️ Administration', 'you' => '👤 Vous', 'reply' => 'Répondre',
        'your_answer' => 'Votre réponse...', 'send' => 'Envoyer',
        'documents_title' => 'Vos Documents Prêts', 'documents_subtitle' => 'Retrouvez ici tous vos documents.',
        'no_documents' => 'Aucun document prêt.', 'start_process' => 'Commencer une démarche →',
        'download_pdf' => 'Télécharger le PDF', 'faq_title' => 'Questions Fréquentes',
        'faq_subtitle' => 'Tout ce que vous devez savoir.', 'faq_q1' => 'Quels sont les délais ?',
        'faq_a1' => '5 à 10 jours ouvrés selon la complexité.',
        'faq_q2' => 'Les documents sont-ils officiels ?', 'faq_a2' => 'Oui, signature électronique certifiée.',
        'faq_q3' => 'Comment modifier une demande ?', 'faq_a3' => 'Modifiable si statut "En attente".',
        'footer_about' => 'La plateforme numérique au service des citoyens.',
        'navigation' => 'Navigation', 'help' => 'Aide', 'support' => 'Support Technique',
        'legal' => 'Mentions Légales', 'contact' => 'Contact',
        'footer_bottom' => '© 2026 InnoGov • Mairie Digitale • Tous droits réservés.',
        'search_placeholder' => '🔍 Rechercher (titre, n°, description, statut, service)...',
        'demande' => 'Demande', 'validated_on' => 'Validé le',
        'search_results' => 'résultat(s) trouvé(s)', 'search_for' => 'Résultats pour',
        'no_results' => 'Aucun résultat trouvé.', 'clear_search' => 'Effacer', 'search_btn' => 'Rechercher',
        'document_number' => 'Document N°', 'results_table_title' => 'Résultats de la recherche',
        'pending' => 'En attente', 'in_progress' => 'En cours', 'processed' => 'Traité', 'refused' => 'Refusé',
        'all_status' => 'Tous les statuts',
    ],
    'en' => [
        'title' => 'InnoGov • Citizen Portal • City Hall',
        'services' => 'Services', 'how_it_works' => 'How it works', 'conversations' => 'My Conversations',
        'my_documents' => 'My Documents', 'faq' => 'FAQ', 'hero_title' => 'Your City Hall,', 'hero_title_span' => 'Everywhere with you.',
        'hero_text' => 'Access a modern, fast and transparent administration.',
        'new_request' => 'New Request', 'my_docs_btn' => 'My Documents',
        'our_services' => 'Our Online Services', 'services_subtitle' => 'Select the service you need.',
        'urbanisme' => 'Urban Planning', 'urbanisme_desc' => 'Building permits, work declarations.',
        'etat_civil' => 'Civil Status', 'etat_civil_desc' => 'Birth, marriage, death certificates.',
        'voirie' => 'Roads', 'voirie_desc' => 'Damage reports.',
        'social' => 'Social', 'social_desc' => 'Municipal aid, registrations.',
        'how_title' => 'How it works?', 'how_subtitle' => 'Three simple steps.',
        'step1_title' => 'Submit', 'step1_desc' => 'Fill out the online form.',
        'step2_title' => 'Track', 'step2_desc' => 'Receive real-time notifications.',
        'step3_title' => 'Retrieve', 'step3_desc' => 'Download your official document.',
        'conversations_title' => 'My Conversations', 'conversations_subtitle' => 'Exchange with the administration.',
        'no_conversations' => 'No conversations.', 'create_request' => 'Create a request →',
        'administration' => '🏛️ Administration', 'you' => '👤 You', 'reply' => 'Reply',
        'your_answer' => 'Your answer...', 'send' => 'Send',
        'documents_title' => 'Your Ready Documents', 'documents_subtitle' => 'Find all your documents here.',
        'no_documents' => 'No documents ready.', 'start_process' => 'Start a process →',
        'download_pdf' => 'Download PDF', 'faq_title' => 'Frequently Asked Questions',
        'faq_subtitle' => 'Everything you need to know.', 'faq_q1' => 'What are the processing times?',
        'faq_a1' => '5 to 10 working days.', 'faq_q2' => 'Are documents official?', 'faq_a2' => 'Yes, certified signature.',
        'faq_q3' => 'How to modify a request?', 'faq_a3' => 'Modifiable if "Pending".',
        'footer_about' => 'The digital platform serving citizens.',
        'navigation' => 'Navigation', 'help' => 'Help', 'support' => 'Technical Support',
        'legal' => 'Legal Notice', 'contact' => 'Contact',
        'footer_bottom' => '© 2026 InnoGov • Digital City Hall • All rights reserved.',
        'search_placeholder' => '🔍 Search (title, n°, description, status, service)...',
        'demande' => 'Request', 'validated_on' => 'Validated on',
        'search_results' => 'result(s) found', 'search_for' => 'Results for',
        'no_results' => 'No results found.', 'clear_search' => 'Clear', 'search_btn' => 'Search',
        'document_number' => 'Document N°', 'results_table_title' => 'Search Results',
        'pending' => 'Pending', 'in_progress' => 'In progress', 'processed' => 'Processed', 'refused' => 'Refused',
        'all_status' => 'All status',
    ],
    'ar' => [
        'title' => 'InnoGov • بوابة المواطن • البلدية',
        'services' => 'الخدمات', 'how_it_works' => 'كيف يعمل', 'conversations' => 'محادثاتي',
        'my_documents' => 'وثائقي', 'faq' => 'الأسئلة الشائعة', 'hero_title' => 'بلديتك،', 'hero_title_span' => 'معك في كل مكان.',
        'hero_text' => 'الوصول إلى إدارة حديثة وسريعة وشفافة.',
        'new_request' => 'طلب جديد', 'my_docs_btn' => 'وثائقي',
        'our_services' => 'خدماتنا عبر الإنترنت', 'services_subtitle' => 'اختر الخدمة التي تحتاجها.',
        'urbanisme' => 'التعمير', 'urbanisme_desc' => 'رخص البناء والتصريحات.',
        'etat_civil' => 'الحالة المدنية', 'etat_civil_desc' => 'شهادات الميلاد والزواج والوفاة.',
        'voirie' => 'الطرق', 'voirie_desc' => 'التبليغ عن الأضرار.',
        'social' => 'اجتماعي', 'social_desc' => 'المساعدات البلدية والتسجيلات.',
        'how_title' => 'كيف يعمل؟', 'how_subtitle' => 'ثلاث خطوات بسيطة.',
        'step1_title' => 'إيداع', 'step1_desc' => 'املأ الاستمارة عبر الإنترنت.',
        'step2_title' => 'تتبع', 'step2_desc' => 'تلقي إشعارات في الوقت الفعلي.',
        'step3_title' => 'استلام', 'step3_desc' => 'قم بتنزيل وثيقتك الرسمية.',
        'conversations_title' => 'محادثاتي', 'conversations_subtitle' => 'تبادل مع الإدارة.',
        'no_conversations' => 'لا توجد محادثات.', 'create_request' => 'إنشاء طلب →',
        'administration' => '🏛️ الإدارة', 'you' => '👤 أنت', 'reply' => 'رد',
        'your_answer' => 'إجابتك...', 'send' => 'إرسال',
        'documents_title' => 'وثائقك الجاهزة', 'documents_subtitle' => 'تجد هنا جميع وثائقك.',
        'no_documents' => 'لا توجد وثائق.', 'start_process' => 'بدء إجراء →',
        'download_pdf' => 'تحميل PDF', 'faq_title' => 'الأسئلة الشائعة',
        'faq_subtitle' => 'كل ما تحتاج معرفته.', 'faq_q1' => 'ما هي مدة المعالجة؟',
        'faq_a1' => '5 إلى 10 أيام عمل.', 'faq_q2' => 'هل الوثائق رسمية؟', 'faq_a2' => 'نعم، بتوقيع معتمد.',
        'faq_q3' => 'كيف يمكنني تعديل طلب؟', 'faq_a3' => 'قابل للتعديل إذا كان "قيد الانتظار".',
        'footer_about' => 'المنصة الرقمية في خدمة المواطنين.',
        'navigation' => 'التنقل', 'help' => 'مساعدة', 'support' => 'الدعم الفني',
        'legal' => 'إشعار قانوني', 'contact' => 'اتصال',
        'footer_bottom' => '© 2026 InnoGov • البلدية الرقمية • جميع الحقوق محفوظة.',
        'search_placeholder' => '🔍 بحث (عنوان، رقم، وصف، حالة، خدمة)...',
        'demande' => 'طلب', 'validated_on' => 'تم التحقق في',
        'search_results' => 'نتيجة/نتائج', 'search_for' => 'نتائج لـ',
        'no_results' => 'لا توجد نتائج.', 'clear_search' => 'مسح', 'search_btn' => 'بحث',
        'document_number' => 'وثيقة رقم', 'results_table_title' => 'نتائج البحث',
        'pending' => 'قيد الانتظار', 'in_progress' => 'قيد المعالجة', 'processed' => 'تمت', 'refused' => 'مرفوض',
        'all_status' => 'جميع الحالات',
    ]
];

$t = $traductions[$lang];
$dir = $lang == 'ar' ? 'rtl' : 'ltr';

$controller = new DemandeController();
$demandes = $controller->getDemandesByCitoyen($_SESSION['user_id']);

$searchTerm = trim($_GET['search'] ?? '');
$filterStatus = $_GET['status'] ?? '';

if (!empty($filterStatus)) {
    $demandes = array_filter($demandes, function($d) use ($filterStatus) {
        return $d['statut'] === $filterStatus;
    });
}

if (!empty($searchTerm)) {
    $demandes = array_filter($demandes, function($d) use ($searchTerm) {
        $demandeId = str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT);
        return stripos($d['titre'], $searchTerm) !== false ||
               stripos($d['description'], $searchTerm) !== false ||
               stripos($d['nom_service'] ?? '', $searchTerm) !== false ||
               stripos($d['type_demande'], $searchTerm) !== false ||
               stripos($d['statut'], $searchTerm) !== false ||
               stripos($demandeId, $searchTerm) !== false ||
               stripos('#' . $demandeId, $searchTerm) !== false ||
               stripos((string)$d['id_demande'], $searchTerm) !== false ||
               stripos($d['date_formatee'] ?? '', $searchTerm) !== false;
    });
}

$demandes = array_values($demandes);
$documentCounter = 1;

$demandes_traitees = array_filter($demandes, function($d) {
    return $d['statut'] === 'traite';
});

$user_nom = $_SESSION['user_nom'];
$user_prenom = $_SESSION['user_prenom'];
$user_initials = strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1));

$types_demandes = [
    'urbanisme' => '🏗️ ' . $t['urbanisme'],
    'voirie' => '🛣️ ' . $t['voirie'],
    'etat_civil' => '📜 ' . $t['etat_civil'],
    'culture' => '🎭 Culture',
    'social' => '🤝 ' . $t['social'],
    'autre' => '📌 Autre'
];

$message = $_GET['success'] ?? $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$suiviReponse = new SuiviReponse();
$demandesWithReponses = [];
foreach ($demandes as $demande) {
    $reponses = $suiviReponse->getReponsesByDemande($demande['id_demande']);
    if (!empty($reponses)) {
        $demandesWithReponses[$demande['id_demande']] = [
            'demande' => $demande,
            'reponses' => $reponses
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {--primary:#006D5B;--primary-dark:#004D3D;--primary-light:#E6F4F0;--secondary:#2E7D32;--success:#00A86B;--warning:#FFB800;--danger:#E31E24;--dark:#1A2C3E;--gray-700:#4A5A6E;--gray-500:#8A99B0;--gray-300:#D1D9E6;--gray-100:#F5FCF9;--white:#FFFFFF;--shadow-sm:0 2px 4px rgba(0,0,0,0.05);--shadow-md:0 4px 8px -2px rgba(0,0,0,0.08);--shadow-lg:0 12px 24px -8px rgba(0,0,0,0.12);--shadow-xl:0 20px 40px -12px rgba(0,0,0,0.2);--shadow-primary:0 8px 20px -6px rgba(0,109,91,0.4);--transition-base:0.3s cubic-bezier(0.4,0,0.2,1);--radius-sm:0.5rem;--radius-md:0.75rem;--radius-lg:1rem;}
        *{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth}body{font-family:'Inter',sans-serif;background:var(--gray-100);color:var(--dark);line-height:1.6;overflow-x:hidden}
        .navbar{background:rgba(255,255,255,0.98);backdrop-filter:blur(10px);box-shadow:var(--shadow-lg);position:sticky;top:0;z-index:1000;padding:1rem 2rem;border-bottom:2px solid var(--primary)}
        .navbar-container{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
        .logo{display:flex;align-items:center;gap:12px;text-decoration:none}
        .logo-icon{width:45px;height:45px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:white;font-size:22px;box-shadow:var(--shadow-primary);font-weight:800}
        .logo-text h1{font-size:22px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--primary-dark));-webkit-background-clip:text;background-clip:text;color:transparent}
        .logo-text p{font-size:11px;color:var(--gray-500)}
        .nav-menu{display:flex;gap:1.5rem;align-items:center;flex-wrap:wrap;list-style:none}
        .nav-link{text-decoration:none;color:var(--gray-700);font-weight:600;transition:var(--transition-base);position:relative}
        .nav-link::after{content:'';position:absolute;bottom:-5px;left:0;width:0;height:2px;background:var(--primary);transition:var(--transition-base)}
        .nav-link:hover::after,.nav-link.active::after{width:100%}.nav-link:hover{color:var(--primary)}
        .user-nav{display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
        .avatar{width:40px;height:40px;background:var(--primary-light);color:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid white;box-shadow:var(--shadow-sm)}
        .user-name{font-weight:600;color:var(--gray-700)}
        .lang-switcher{display:flex;gap:4px}
        .lang-btn{padding:5px 8px;border:2px solid var(--gray-300);border-radius:20px;background:white;cursor:pointer;font-weight:600;font-size:11px;transition:var(--transition-base);text-decoration:none;color:var(--gray-700)}
        .lang-btn.active{background:var(--primary);color:white;border-color:var(--primary)}.lang-btn:hover{border-color:var(--primary)}
        .search-section{max-width:900px;margin:20px auto;padding:20px;background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-md)}
        .search-form{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
        .search-input{flex:1;min-width:200px;padding:12px 20px;border:2px solid var(--gray-300);border-radius:50px;font-size:15px;font-family:'Inter',sans-serif;transition:var(--transition-base)}
        .search-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(0,109,91,0.15)}
        .filter-select{padding:12px 16px;border:2px solid var(--gray-300);border-radius:50px;font-size:14px;font-family:'Inter',sans-serif;background:white;cursor:pointer;transition:var(--transition-base)}
        .filter-select:focus{outline:none;border-color:var(--primary)}
        .search-btn{padding:12px 24px;background:var(--primary);color:white;border:none;border-radius:50px;cursor:pointer;font-weight:600;transition:var(--transition-base);white-space:nowrap}
        .search-btn:hover{background:var(--primary-dark)}
        .clear-btn{padding:12px 20px;background:var(--gray-300);color:var(--gray-700);border:none;border-radius:50px;cursor:pointer;font-weight:600;text-decoration:none;transition:var(--transition-base);white-space:nowrap;font-size:14px}
        .clear-btn:hover{background:var(--gray-500);color:white}
        .search-info{margin-top:15px;padding:10px 15px;background:var(--primary-light);border-radius:var(--radius-md);font-size:14px;color:var(--primary);font-weight:600}
        .no-results{text-align:center;padding:30px;color:var(--gray-500)}.no-results i{font-size:48px;margin-bottom:10px;display:block}
        .results-table-section{max-width:900px;margin:20px auto;background:white;border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-md)}
        .results-table-header{padding:15px 20px;background:var(--primary);color:white;font-weight:700;display:flex;align-items:center;gap:8px}
        .results-table{width:100%;border-collapse:collapse}
        .results-table th{padding:12px 15px;text-align:left;font-size:11px;text-transform:uppercase;color:var(--gray-700);background:var(--gray-100);font-weight:700}
        .results-table td{padding:12px 15px;border-bottom:1px solid var(--gray-300);font-size:13px}
        .results-table tbody tr{transition:var(--transition-base);cursor:pointer}
        .results-table tbody tr:hover{background:var(--primary-light)}
        .section{padding:80px 0}.container{max-width:1400px;margin:0 auto;padding:0 2rem}
        .section-header{text-align:center;margin-bottom:50px}.section-title{font-size:36px;font-weight:700;margin-bottom:16px;color:var(--dark)}.section-subtitle{font-size:18px;color:var(--gray-500);max-width:600px;margin:0 auto}
        .hero{position:relative;min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:6rem 2rem;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);color:white;overflow:hidden}
        .hero::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(255,255,255,0.1) 0%,transparent 70%);border-radius:50%}
        .hero::after{content:'';position:absolute;bottom:0;left:0;right:0;height:100px;background:linear-gradient(to top,var(--gray-100),transparent)}
        .hero-content{position:relative;z-index:2;max-width:800px}
        .hero h1{font-size:52px;font-weight:800;margin-bottom:20px;text-shadow:2px 2px 4px rgba(0,0,0,0.3)}
        .hero h1 span{color:#FFB800;text-shadow:2px 2px 8px rgba(255,184,0,0.4)}.hero p{font-size:20px;margin-bottom:40px;opacity:0.95}
        .hero-buttons{display:flex;gap:20px;justify-content:center;flex-wrap:wrap}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:14px 32px;border-radius:var(--radius-md);text-decoration:none;font-weight:600;font-size:15px;transition:var(--transition-base);border:none;cursor:pointer}
        .btn-primary{background:white;color:var(--primary);box-shadow:var(--shadow-lg)}.btn-primary:hover{transform:translateY(-3px);box-shadow:var(--shadow-xl)}
        .btn-outline{background:transparent;border:2px solid white;color:white}.btn-outline:hover{background:white;color:var(--primary);transform:translateY(-3px)}
        .btn-sm{padding:8px 16px;font-size:13px}.bg-light{background:var(--white)}
        .services-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:30px}
        .service-card{background:white;border-radius:var(--radius-lg);padding:40px 30px;text-align:center;transition:var(--transition-base);box-shadow:var(--shadow-sm);border:2px solid transparent}
        .service-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-xl);border-color:var(--primary)}
        .service-icon{width:80px;height:80px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--primary);font-size:36px;transition:var(--transition-base)}
        .service-card:hover .service-icon{background:var(--primary);color:white;transform:scale(1.1) rotate(360deg)}
        .service-card h3{font-size:20px;font-weight:700;margin-bottom:12px;color:var(--dark)}.service-card p{color:var(--gray-500);font-size:15px;line-height:1.6}
        .steps-section{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);padding:80px 0;color:white}
        .steps-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:40px;margin-top:50px}
        .step{text-align:center;padding:30px}
        .step-number{width:70px;height:70px;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border:3px solid white;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;margin:0 auto 24px;transition:var(--transition-base)}
        .step:hover .step-number{background:white;color:var(--primary);transform:scale(1.1)}.step h3{font-size:22px;font-weight:700;margin-bottom:12px}.step p{font-size:15px;opacity:0.9;line-height:1.6}
        .export-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:30px}
        .export-card{background:white;border-radius:var(--radius-lg);padding:30px;box-shadow:var(--shadow-sm);transition:var(--transition-base);border-left:4px solid var(--success);position:relative}
        .export-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-lg)}
        .document-number-badge{position:absolute;top:15px;right:15px;background:var(--primary);color:white;padding:6px 14px;border-radius:50px;font-size:12px;font-weight:700}
        .card-header{display:flex;gap:20px;margin-bottom:24px;align-items:flex-start}
        .card-icon-pdf{width:60px;height:60px;background:#FEE2E2;color:var(--danger);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:28px;flex-shrink:0}
        .card-info h3{font-size:18px;font-weight:700;color:var(--dark);margin-bottom:6px}.card-info p{font-size:14px;color:var(--gray-500)}
        .btn-export{width:100%;background:var(--success);color:white;padding:14px;border-radius:var(--radius-md);text-decoration:none;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px;transition:var(--transition-base);border:none;cursor:pointer;font-size:0.95rem}
        .btn-export:hover{background:#008f5a;transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,168,107,0.3)}
        .btn-export:disabled{background:var(--gray-300);cursor:not-allowed;transform:none}
        .empty-state{text-align:center;padding:60px 20px;background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)}.empty-state i{font-size:64px;color:var(--gray-300);margin-bottom:20px}.empty-state p{font-size:18px;color:var(--gray-500);margin-bottom:20px}.empty-state a{color:var(--primary);font-weight:700;text-decoration:none;font-size:16px}
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:50px;font-size:12px;font-weight:600}
        .badge-en_attente{background:#FEF3C7;color:#D97706}.badge-en_cours{background:#DBEAFE;color:#2563EB}.badge-traite{background:#D1FAE5;color:#059669}.badge-refuse{background:#FEE2E2;color:#DC2626}
        .conversation-card{background:white;border-radius:var(--radius-lg);padding:24px;margin-bottom:24px;box-shadow:var(--shadow-sm);transition:var(--transition-base)}.conversation-card:hover{box-shadow:var(--shadow-md)}
        .conversation-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid var(--gray-300);flex-wrap:wrap;gap:10px}
        .message-bubble{margin-bottom:16px;padding-left:0}.message-bubble.citoyen{padding-left:40px}
        .message-content{background:var(--primary-light);padding:12px 16px;border-radius:var(--radius-md)}.message-content.citoyen-message{background:#E8F5E9;border:2px solid #C8E6C9}
        .reply-btn{margin-top:8px;padding:6px 14px;background:var(--primary-light);color:var(--primary);border:none;border-radius:20px;cursor:pointer;font-size:12px;font-weight:600;transition:var(--transition-base)}.reply-btn:hover{background:var(--primary);color:white}
        .reply-form{display:none;margin-top:10px;padding-left:10px}.reply-form textarea{width:100%;padding:10px;border:2px solid var(--gray-300);border-radius:var(--radius-md);font-family:'Inter',sans-serif;resize:vertical;min-height:50px}.reply-form textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(0,109,91,0.1)}
        .faq-grid{max-width:800px;margin:0 auto}.faq-item{background:white;border-radius:var(--radius-lg);padding:24px 30px;margin-bottom:16px;box-shadow:var(--shadow-sm);transition:var(--transition-base);cursor:pointer}.faq-item:hover{box-shadow:var(--shadow-md)}
        .faq-item h4{font-size:17px;font-weight:700;color:var(--dark);display:flex;justify-content:space-between;align-items:center;gap:20px}.faq-item h4 i{transition:var(--transition-base);color:var(--primary)}.faq-item.active h4 i{transform:rotate(180deg)}.faq-item p{margin-top:16px;color:var(--gray-500);font-size:15px;line-height:1.7;display:none}.faq-item.active p{display:block}
        .toast{position:fixed;bottom:30px;right:30px;background:var(--success);color:white;padding:1rem 1.5rem;border-radius:50px;box-shadow:var(--shadow-lg);z-index:9999;font-weight:500;animation:slideUp 0.3s ease;display:flex;align-items:center;gap:0.5rem}
        @keyframes slideUp{from{transform:translateY(100px);opacity:0}to{transform:translateY(0);opacity:1}}
        .footer{background:linear-gradient(135deg,var(--dark) 0%,#2D3A4B 100%);color:white;padding:60px 2rem 30px;margin-top:60px}
        .footer-container{max-width:1400px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:40px}
        .footer-section h4{color:white;margin-bottom:20px;font-size:18px;font-weight:700}
        .footer-section p,.footer-section a{color:rgba(255,255,255,0.6);text-decoration:none;line-height:2;font-size:14px;transition:0.15s}
        .footer-section a:hover{color:#FFB800;padding-left:4px}.footer-section ul{list-style:none}
        .footer-bottom{text-align:center;padding-top:30px;border-top:1px solid rgba(255,255,255,0.1);font-size:13px;color:rgba(255,255,255,0.4);max-width:1400px;margin:0 auto}
        @media(max-width:1024px){.footer-container{grid-template-columns:1fr 1fr}.hero h1{font-size:40px}}
        @media(max-width:768px){.hero h1{font-size:32px}.hero p{font-size:16px}.section-title{font-size:28px}.navbar-container{flex-direction:column;text-align:center}.nav-menu{justify-content:center}.btn{width:100%;justify-content:center}.services-grid,.export-grid{grid-template-columns:1fr}.footer-container{grid-template-columns:1fr}.section{padding:60px 0}.steps-grid{grid-template-columns:1fr;gap:30px}.message-bubble.citoyen{padding-left:20px}.search-form{flex-direction:column}.search-input,.filter-select{width:100%}}
        ::-webkit-scrollbar{width:10px}::-webkit-scrollbar-track{background:var(--gray-100)}::-webkit-scrollbar-thumb{background:var(--primary);border-radius:10px}

        /* ===== CHATBOT & NOTIFICATIONS ===== */
        .chatbot-container{position:fixed;bottom:80px;right:30px;z-index:9999}
        .chatbot-bubble{width:60px;height:60px;background:linear-gradient(135deg,#006D5B,#00A86B);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 25px rgba(0,109,91,0.4);transition:all 0.3s;animation:pulse 2s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(0,109,91,0.4)}70%{box-shadow:0 0 0 15px rgba(0,109,91,0)}100%{box-shadow:0 0 0 0 rgba(0,109,91,0)}}
        .chatbot-bubble i{font-size:28px;color:white}.chatbot-bubble:hover{transform:scale(1.1)}
        .chatbot-window{display:none;position:fixed;bottom:100px;right:30px;width:400px;height:550px;background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;z-index:9999;flex-direction:column}
        .chatbot-window.active{display:flex}.chatbot-header{background:linear-gradient(135deg,#006D5B,#00A86B);color:white;padding:20px;display:flex;align-items:center;gap:12px}
        .chatbot-header h3{margin:0;font-size:16px}.chatbot-close{margin-left:auto;cursor:pointer;font-size:20px}
        .chatbot-messages{flex:1;padding:20px;overflow-y:auto;background:#f8fafc}
        .chatbot-message{margin-bottom:15px;max-width:85%;animation:fadeIn 0.3s ease}.chatbot-message.user{margin-left:auto}.chatbot-message.bot{margin-right:auto}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .chatbot-message .message-bubble{padding:12px 16px;border-radius:15px;font-size:14px;line-height:1.5;white-space:pre-wrap;margin:0}
        .chatbot-message.user .message-bubble{background:linear-gradient(135deg,#006D5B,#00A86B);color:white;border-bottom-right-radius:5px}
        .chatbot-message.bot .message-bubble{background:white;border:1px solid #e2e8f0;color:#1a202c;border-bottom-left-radius:5px}
        .chatbot-input{padding:15px;background:white;border-top:1px solid #e2e8f0;display:flex;gap:10px}
        .chatbot-input input{flex:1;padding:12px 16px;border:2px solid #e2e8f0;border-radius:25px;font-family:'Inter',sans-serif;font-size:14px;outline:none}.chatbot-input input:focus{border-color:#006D5B}
        .chatbot-input button{width:45px;height:45px;background:linear-gradient(135deg,#006D5B,#00A86B);color:white;border:none;border-radius:50%;cursor:pointer;font-size:16px}

        .notif-bell{position:fixed;bottom:150px;right:30px;width:60px;height:60px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 25px rgba(0,0,0,0.2);z-index:9998;transition:all 0.3s;font-size:24px;color:var(--primary)}
        .notif-bell:hover{transform:scale(1.1)}
        .notif-count{position:absolute;top:-5px;right:-5px;background:var(--danger);color:white;width:24px;height:24px;border-radius:50%;font-size:12px;display:flex;align-items:center;justify-content:center;font-weight:700}
        .notif-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:700px;max-height:70vh;background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);z-index:10000;overflow:hidden;flex-direction:column}
        .notif-modal.active{display:flex}
        .notif-modal-header{background:var(--primary);color:white;padding:20px;display:flex;justify-content:space-between;align-items:center}
        .notif-modal-body{flex:1;overflow-y:auto;padding:20px}
        .notif-modal-close{cursor:pointer;font-size:24px;color:white}
        .notif-item{background:var(--gray-100);border-radius:var(--radius-md);padding:15px;margin-bottom:10px;border-left:4px solid var(--success)}
        .notif-item h4{font-size:16px;font-weight:700;color:var(--dark);margin-bottom:5px}
        .notif-item p{font-size:14px;color:var(--gray-500);margin:0}
        .notif-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999}
        .notif-overlay.active{display:block}
        @media(max-width:768px){.chatbot-window{width:100vw;height:100vh;bottom:0;right:0;border-radius:0}.notif-modal{width:95%;max-height:80vh}}
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="client.php" class="logo"><div class="logo-icon">IG</div><div class="logo-text"><h1>InnoGov</h1><p>Portail Citoyen</p></div></a>
            <ul class="nav-menu">
                <li><a href="#services" class="nav-link"><?= $t['services'] ?></a></li>
                <li><a href="#guide" class="nav-link"><?= $t['how_it_works'] ?></a></li>
                <li><a href="#conversations" class="nav-link"><?= $t['conversations'] ?></a></li>
                <li><a href="#export" class="nav-link"><?= $t['my_documents'] ?></a></li>
                <li><a href="#faq" class="nav-link"><?= $t['faq'] ?></a></li>
            </ul>
            <div class="user-nav">
                <div class="lang-switcher">
                    <a href="?lang=fr<?= $searchTerm?'&search='.urlencode($searchTerm):'' ?><?= $filterStatus?'&status='.$filterStatus:'' ?>" class="lang-btn <?= $lang=='fr'?'active':'' ?>">🇫🇷 FR</a>
                    <a href="?lang=en<?= $searchTerm?'&search='.urlencode($searchTerm):'' ?><?= $filterStatus?'&status='.$filterStatus:'' ?>" class="lang-btn <?= $lang=='en'?'active':'' ?>">🇬🇧 EN</a>
                    <a href="?lang=ar<?= $searchTerm?'&search='.urlencode($searchTerm):'' ?><?= $filterStatus?'&status='.$filterStatus:'' ?>" class="lang-btn <?= $lang=='ar'?'active':'' ?>">🇸🇦 AR</a>
                </div>
                <div class="avatar"><?= $user_initials ?></div><span class="user-name"><?= htmlspecialchars($user_prenom) ?> <?= htmlspecialchars($user_nom) ?></span>
            </div>
        </div>
    </nav>

    <?php if($message):?><div class="container" style="padding-top:20px"><div style="background:#D1FAE5;color:#059669;padding:16px 24px;border-radius:var(--radius-md);border-left:4px solid #059669"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div></div><?php endif?>
    <?php if($error):?><div class="container" style="padding-top:20px"><div style="background:#FEE2E2;color:#DC2626;padding:16px 24px;border-radius:var(--radius-md);border-left:4px solid #DC2626"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div></div><?php endif?>

    <div class="container" style="padding-top:20px">
        <div class="search-section" data-aos="fade-up">
            <form method="GET" action="client.php" class="search-form">
                <input type="hidden" name="lang" value="<?= $lang ?>">
                <input type="text" name="search" class="search-input" placeholder="<?= $t['search_placeholder'] ?>" value="<?= htmlspecialchars($searchTerm) ?>">
                <select name="status" class="filter-select">
                    <option value=""><?= $t['all_status'] ?></option>
                    <option value="en_attente" <?= $filterStatus=='en_attente'?'selected':'' ?>>⏳ <?= $t['pending'] ?></option>
                    <option value="en_cours" <?= $filterStatus=='en_cours'?'selected':'' ?>>🔄 <?= $t['in_progress'] ?></option>
                    <option value="traite" <?= $filterStatus=='traite'?'selected':'' ?>>✅ <?= $t['processed'] ?></option>
                    <option value="refuse" <?= $filterStatus=='refuse'?'selected':'' ?>>❌ <?= $t['refused'] ?></option>
                </select>
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> <?= $t['search_btn'] ?></button>
                <?php if(!empty($searchTerm)||!empty($filterStatus)):?><a href="client.php?lang=<?= $lang ?>" class="clear-btn"><i class="fas fa-times"></i> <?= $t['clear_search'] ?></a><?php endif?>
            </form>
            <?php if(!empty($searchTerm)||!empty($filterStatus)):?>
                <div class="search-info">
                    <?php if(count($demandes)>0):?><?= $t['search_for'] ?> "<strong><?= htmlspecialchars($searchTerm?:($t[$filterStatus]??$filterStatus)) ?></strong>" : <strong><?= count($demandes) ?></strong> <?= $t['search_results'] ?>
                    <?php else:?><div class="no-results"><i class="fas fa-search"></i><?= $t['no_results'] ?></div><?php endif?>
                </div>
            <?php endif?>
        </div>
    </div>

    <?php if((!empty($searchTerm)||!empty($filterStatus))&&count($demandes)>0):?>
        <div class="results-table-section" data-aos="fade-up">
            <div class="results-table-header"><i class="fas fa-list"></i> <?= $t['results_table_title'] ?> (<?= count($demandes) ?>)</div>
            <div style="overflow-x:auto">
                <table class="results-table">
                    <thead><tr><th>N°</th><th><?= $t['demande'] ?></th><th>Titre</th><th>Service</th><th>Statut</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach($demandes as $d):?>
                            <tr onclick="document.getElementById('export').scrollIntoView({behavior:'smooth'})">
                                <td style="font-weight:700">#<?= str_pad($d['id_demande'],5,'0',STR_PAD_LEFT) ?></td>
                                <td><span style="font-size:11px;background:#E0E7FF;color:#3730A3;padding:3px 10px;border-radius:50px;font-weight:600"><?= $types_demandes[$d['type_demande']]??$d['type_demande'] ?></span></td>
                                <td style="font-weight:600"><?= htmlspecialchars($d['titre']) ?></td>
                                <td style="font-size:13px"><?= htmlspecialchars($d['nom_service']??'N/A') ?></td>
                                <td><span class="badge badge-<?= $d['statut'] ?>"><?php $sts=['en_attente'=>'⏳ '.$t['pending'],'en_cours'=>'🔄 '.$t['in_progress'],'traite'=>'✅ '.$t['processed'],'refuse'=>'❌ '.$t['refused']];echo $sts[$d['statut']]??$d['statut'] ?></span></td>
                                <td style="font-size:13px;color:var(--gray-500)"><?= $d['date_formatee'] ?></td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif?>

    <section class="hero"><div class="hero-content" data-aos="fade-up"><h1><?= $t['hero_title'] ?> <span><?= $t['hero_title_span'] ?></span></h1><p><?= $t['hero_text'] ?></p><div class="hero-buttons"><a href="../backoffice/ajouter_demande.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?= $t['new_request'] ?></a><a href="#export" class="btn btn-outline"><i class="fas fa-file-download"></i> <?= $t['my_docs_btn'] ?></a></div></div></section>

    <section class="section bg-light" id="services"><div class="container"><div class="section-header" data-aos="fade-up"><h2 class="section-title"><?= $t['our_services'] ?></h2><p class="section-subtitle"><?= $t['services_subtitle'] ?></p></div><div class="services-grid"><?php foreach([['fa-building',$t['urbanisme'],$t['urbanisme_desc']],['fa-id-card',$t['etat_civil'],$t['etat_civil_desc']],['fa-road',$t['voirie'],$t['voirie_desc']],['fa-users',$t['social'],$t['social_desc']]] as $i=>$s):?><div class="service-card" data-aos="fade-up" data-aos-delay="<?=($i+1)*100?>"><div class="service-icon"><i class="fas <?=$s[0]?>"></i></div><h3><?=$s[1]?></h3><p><?=$s[2]?></p></div><?php endforeach?></div></div></section>

    <section class="steps-section" id="guide"><div class="container"><div class="section-header" data-aos="fade-up"><h2 class="section-title" style="color:white"><?= $t['how_title'] ?></h2><p class="section-subtitle" style="color:rgba(255,255,255,0.9)"><?= $t['how_subtitle'] ?></p></div><div class="steps-grid"><?php for($i=1;$i<=3;$i++):?><div class="step" data-aos="<?=['fade-right','fade-up','fade-left'][$i-1]?>"><div class="step-number"><?=$i?></div><h3><?=$t['step'.$i.'_title']?></h3><p><?=$t['step'.$i.'_desc']?></p></div><?php endfor?></div></div></section>

    <section class="section" id="conversations"><div class="container"><div class="section-header" data-aos="fade-up"><h2 class="section-title"><?= $t['conversations_title'] ?></h2><p class="section-subtitle"><?= $t['conversations_subtitle'] ?></p></div><?php if(empty($demandesWithReponses)):?><div class="empty-state" data-aos="zoom-in"><i class="fas fa-comments"></i><p><?= $t['no_conversations'] ?></p><a href="../backoffice/ajouter_demande.php"><?= $t['create_request'] ?></a></div><?php else:?><div style="max-width:900px;margin:0 auto"><?php foreach($demandesWithReponses as $item):$d=$item['demande'];$reponses=$item['reponses']?><div class="conversation-card" data-aos="fade-up"><div class="conversation-header"><div><h3 style="font-size:16px;font-weight:700"><?= htmlspecialchars($d['titre']) ?></h3><span style="font-size:12px;color:var(--gray-500)"><?= $t['demande'] ?> #<?= str_pad($d['id_demande'],5,'0',STR_PAD_LEFT) ?></span></div><span class="badge badge-<?= $d['statut'] ?>"><?php $sts=['en_attente'=>'⏳ '.$t['pending'],'en_cours'=>'🔄 '.$t['in_progress'],'traite'=>'✅ '.$t['processed'],'refuse'=>'❌ '.$t['refused']];echo $sts[$d['statut']]??$d['statut']?></span></div><?php foreach($reponses as $rep):$isAdmin=$rep['expediteur']==='admin';$enfants=$suiviReponse->getReponsesEnfants($rep['id_reponse'])?><div class="message-bubble <?=$isAdmin?'':'citoyen'?>"><div style="display:flex;gap:10px;align-items:flex-start"><?php if($isAdmin):?><div style="width:35px;height:35px;background:var(--primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0">AD</div><?php endif?><div style="flex:1"><div class="message-content <?=$isAdmin?'admin-message':'citoyen-message'?>"><div style="display:flex;justify-content:space-between;margin-bottom:5px"><span style="font-weight:600;font-size:13px;color:<?=$isAdmin?'var(--primary)':'#2E7D32'?>"><?=$isAdmin?$t['administration']:$t['you']?></span><span style="font-size:11px;color:var(--gray-500)"><?=date('d/m/Y H:i',strtotime($rep['date_creation']))?></span></div><p style="font-size:14px"><?=nl2br(htmlspecialchars($rep['contenu']))?></p></div><?php if($isAdmin):?><button onclick="toggleReponseForm(<?=$rep['id_reponse']?>)" class="reply-btn"><i class="fas fa-reply"></i> <?=$t['reply']?></button><div id="reponseForm_<?=$rep['id_reponse']?>" class="reply-form"><form method="POST" action="envoyer_reponse.php" style="display:flex;gap:10px;flex-direction:column"><input type="hidden" name="id_demande" value="<?=$d['id_demande']?>"><input type="hidden" name="id_parent" value="<?=$rep['id_reponse']?>"><input type="hidden" name="redirect" value="client.php?lang=<?=$lang?>#conversations"><textarea name="contenu" rows="2" placeholder="<?=$t['your_answer']?>"></textarea><button type="submit" style="align-self:flex-end;padding:8px 20px;background:var(--success);color:white;border:none;border-radius:20px;cursor:pointer;font-weight:600;font-size:13px"><i class="fas fa-paper-plane"></i> <?=$t['send']?></button></form></div><?php endif?><?php if(!empty($enfants)):?><div style="margin-top:10px;padding-left:20px;border-left:2px solid #C8E6C9"><?php foreach($enfants as $enfant):?><div style="margin-bottom:8px;background:#FFF8E1;padding:10px 12px;border-radius:var(--radius-md)"><div style="display:flex;justify-content:space-between;margin-bottom:3px"><span style="font-weight:600;font-size:12px;color:#F57F17"><?=$enfant['expediteur']==='citoyen'?$t['you']:$t['administration']?></span><span style="font-size:10px;color:var(--gray-500)"><?=date('d/m/Y H:i',strtotime($enfant['date_creation']))?></span></div><p style="font-size:13px"><?=nl2br(htmlspecialchars($enfant['contenu']))?></p></div><?php endforeach?></div><?php endif?></div><?php if(!$isAdmin):?><div style="width:35px;height:35px;background:#2E7D32;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0">MB</div><?php endif?></div></div><?php endforeach?></div><?php endforeach?></div><?php endif?></div></section>

    <section class="section" id="export"><div class="container"><div class="section-header" data-aos="fade-up"><h2 class="section-title"><?= $t['documents_title'] ?></h2><p class="section-subtitle"><?= $t['documents_subtitle'] ?></p></div><?php if(empty($demandes_traitees)):?><div class="empty-state" data-aos="zoom-in"><i class="fas fa-folder-open"></i><p><?= $t['no_documents'] ?></p><a href="../backoffice/ajouter_demande.php"><?= $t['start_process'] ?></a></div><?php else:?><div class="export-grid"><?php foreach($demandes_traitees as $d):?><div class="export-card" data-aos="fade-up"><div class="document-number-badge"><?= $t['document_number'] ?><?= $documentCounter++ ?></div><div class="card-header"><div class="card-icon-pdf"><i class="fas fa-file-pdf"></i></div><div class="card-info"><h3><?= htmlspecialchars($d['titre']) ?></h3><p><?= $types_demandes[$d['type_demande']]??'Document Officiel' ?> • <?= $t['validated_on'] ?> <?= $d['date_formatee'] ?></p></div></div><button class="btn-export" onclick="telechargerPDF('<?= htmlspecialchars(addslashes($d['titre'])) ?>','<?= htmlspecialchars(addslashes($d['description'])) ?>','<?= $d['id_demande'] ?>','<?= $d['date_formatee'] ?>','<?= htmlspecialchars(addslashes($d['nom_service']??'Service municipal')) ?>','<?= $lang ?>',<?= $documentCounter-1 ?>)"><i class="fas fa-cloud-download-alt"></i> <?= $t['download_pdf'] ?></button></div><?php endforeach?></div><?php endif?></div></section>

    <section class="section bg-light" id="faq"><div class="container"><div class="section-header" data-aos="fade-up"><h2 class="section-title"><?= $t['faq_title'] ?></h2><p class="section-subtitle"><?= $t['faq_subtitle'] ?></p></div><div class="faq-grid"><?php for($i=1;$i<=3;$i++):?><div class="faq-item" data-aos="fade-up" data-aos-delay="<?=($i-1)*100?>" onclick="this.classList.toggle('active')"><h4><?=$t['faq_q'.$i]?> <i class="fas fa-chevron-down"></i></h4><p><?=$t['faq_a'.$i]?></p></div><?php endfor?></div></div></section>

    <footer class="footer"><div class="footer-container"><div class="footer-section"><div class="logo" style="margin-bottom:20px"><div class="logo-icon">IG</div><div class="logo-text" style="color:white"><h1 style="color:white;font-size:20px;-webkit-text-fill-color:white">InnoGov</h1></div></div><p style="max-width:300px"><?= $t['footer_about'] ?></p></div><div class="footer-section"><h4><?= $t['navigation'] ?></h4><ul><li><a href="#">Accueil</a></li><li><a href="#services"><?= $t['services'] ?></a></li><li><a href="#conversations"><?= $t['conversations'] ?></a></li><li><a href="#export"><?= $t['my_documents'] ?></a></li><li><a href="#faq"><?= $t['faq'] ?></a></li></ul></div><div class="footer-section"><h4><?= $t['help'] ?></h4><ul><li><a href="#guide"><?= $t['how_it_works'] ?></a></li><li><a href="#"><?= $t['support'] ?></a></li><li><a href="#"><?= $t['legal'] ?></a></li></ul></div><div class="footer-section"><h4><?= $t['contact'] ?></h4><ul><li><i class="fas fa-envelope"></i> contact@innogov.dz</li><li><i class="fas fa-phone"></i> +213 23 45 67 89</li></ul></div></div><div class="footer-bottom"><p><?= $t['footer_bottom'] ?></p></div></footer>

    <!-- ========== CHATBOT UI ========== -->
    <div class="chatbot-container">
        <div class="chatbot-bubble" id="chatbotBubble" onclick="toggleChatbot()" title="Assistant IA">
            <i class="fas fa-robot"></i>
        </div>
        <div class="chatbot-window" id="chatbotWindow">
            <div class="chatbot-header">
                <i class="fas fa-robot" style="font-size:24px;"></i>
                <div><h3>Assistant IA InnoGov</h3><small style="opacity:0.8;">Propulsé par Groq • 24/7</small></div>
                <span class="chatbot-close" onclick="toggleChatbot()">✕</span>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="chatbot-message bot">
                    <div class="message-bubble">👋 Bonjour <?= htmlspecialchars($user_prenom) ?> ! Je suis InnoBot, propulsé par l'IA Groq. Posez-moi n'importe quelle question concernant vos démarches administratives, je suis là pour vous aider !</div>
                </div>
            </div>
            <div class="chatbot-input">
                <input type="text" id="chatbotInput" placeholder="Votre question..." onkeypress="if(event.key==='Enter')sendMessage()">
                <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <!-- ========== NOTIFICATION BELL ========== -->
    <div class="notif-bell" id="notifBell" onclick="toggleNotifications()">
        <i class="fas fa-bell"></i>
        <?php $nbTraitees = count($demandes_traitees); if($nbTraitees > 0): ?>
        <span class="notif-count"><?= $nbTraitees ?></span>
        <?php endif; ?>
    </div>
    <div class="notif-overlay" id="notifOverlay" onclick="toggleNotifications()"></div>
    <div class="notif-modal" id="notifModal">
        <div class="notif-modal-header">
            <h3><i class="fas fa-check-circle"></i> Demandes traitées (<?= count($demandes_traitees) ?>)</h3>
            <span class="notif-modal-close" onclick="toggleNotifications()">✕</span>
        </div>
        <div class="notif-modal-body">
            <?php if(empty($demandes_traitees)): ?>
                <p style="text-align:center;color:var(--gray-500);">Aucune demande traitée pour le moment.</p>
            <?php else: foreach($demandes_traitees as $d): ?>
                <div class="notif-item">
                    <h4>#<?= str_pad($d['id_demande'],5,'0',STR_PAD_LEFT) ?> - <?= htmlspecialchars($d['titre']) ?></h4>
                    <p>
                        <strong>Service :</strong> <?= htmlspecialchars($d['nom_service'] ?? 'N/A') ?><br>
                        <strong>Type :</strong> <?= $types_demandes[$d['type_demande']] ?? $d['type_demande'] ?><br>
                        <strong>Date :</strong> <?= $d['date_formatee'] ?><br>
                        <strong>Description :</strong> <?= substr(htmlspecialchars($d['description']),0,100) ?>...
                    </p>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <script>
        // Données PHP injectées pour le chatbot IA
        const CHATBOT_CONTEXT = {
            prenom: <?= json_encode($user_prenom) ?>,
            nom: <?= json_encode($user_nom) ?>,
            lang: <?= json_encode($lang) ?>,
            demandes: <?= json_encode(array_map(function($d) {
                return [
                    'id'      => str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT),
                    'titre'   => $d['titre'],
                    'statut'  => $d['statut'],
                    'type'    => $d['type_demande'],
                    'service' => $d['nom_service'] ?? 'N/A',
                    'date'    => $d['date_formatee'] ?? ''
                ];
            }, $controller->getDemandesByCitoyen($_SESSION['user_id']))) ?>
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({duration:800,easing:'ease-out-cubic',once:true,offset:100});
        function toggleReponseForm(id){const f=document.getElementById('reponseForm_'+id);if(f.style.display==='none'||f.style.display===''){f.style.display='block';f.querySelector('textarea').focus()}else{f.style.display='none'}}
        function telechargerPDF(titre,description,id,date,service,lang,docNum){
            const btn=event.target.closest('.btn-export');const orig=btn.innerHTML;
            const L={fr:['Génération...','Document Officiel','Mairie Digitale','RÉFÉRENCE','TITRE','SERVICE','STATUT','TRAITÉE','DESCRIPTION','Généré automatiquement','Signature certifiée','✅ PDF téléchargé!','❌ Erreur'],en:['Generating...','Official Document','Digital City Hall','REFERENCE','TITLE','SERVICE','STATUS','PROCESSED','DESCRIPTION','Auto-generated','Certified signature','✅ Downloaded!','❌ Error'],ar:['جاري التوليد...','وثيقة رسمية','البلدية الرقمية','مرجع','العنوان','الخدمة','الحالة','تمت','الوصف','تم إنشاؤه تلقائياً','توقيع معتمد','✅ تم!','❌ خطأ']};
            const l=L[lang]||L.fr;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> '+l[0];btn.disabled=true;
            setTimeout(()=>{try{const{jsPDF}=window.jspdf;const doc=new jsPDF();const p=[0,109,91],d=[26,44,62],g=[138,153,176];
            doc.setFillColor(p[0],p[1],p[2]);doc.rect(0,0,210,40,'F');doc.setTextColor(255,255,255);doc.setFontSize(22);doc.setFont('helvetica','bold');doc.text('INNOGOV',15,20);doc.setFontSize(10);doc.setFont('helvetica','normal');doc.text(l[1],15,28);doc.text(l[2],15,34);
            doc.setTextColor(d[0],d[1],d[2]);doc.setFontSize(11);doc.setFont('helvetica','bold');doc.text(l[3],15,55);doc.setFont('helvetica','normal');doc.setFontSize(10);doc.text('DEM-'+String(id).padStart(5,'0'),15,62);
            doc.text('Date: '+new Date().toLocaleDateString(lang==='ar'?'ar-TN':'fr-FR'),15,70);doc.setDrawColor(p[0],p[1],p[2]);doc.setLineWidth(0.5);doc.line(15,78,195,78);
            doc.setFontSize(14);doc.setFont('helvetica','bold');doc.text(l[4],15,91);doc.setFontSize(11);doc.setFont('helvetica','normal');doc.text(titre,15,101);
            doc.setFontSize(11);doc.setFont('helvetica','bold');doc.text(l[5],15,115);doc.setFont('helvetica','normal');doc.setFontSize(10);doc.text(service,15,122);
            doc.setFontSize(11);doc.setFont('helvetica','bold');doc.text(l[6],15,136);doc.setTextColor(0,168,107);doc.setFont('helvetica','bold');doc.text(l[7],15,143);
            doc.setTextColor(d[0],d[1],d[2]);doc.setDrawColor(p[0],p[1],p[2]);doc.line(15,150,195,150);
            doc.setFontSize(11);doc.setFont('helvetica','bold');doc.text(l[8],15,163);doc.setFont('helvetica','normal');doc.setFontSize(9);doc.text(doc.splitTextToSize(description,180),15,170);
            doc.setDrawColor(p[0],p[1],p[2]);doc.line(15,235,195,235);doc.setFontSize(9);doc.setFont('helvetica','italic');doc.setTextColor(g[0],g[1],g[2]);doc.text(l[9],15,245);doc.text(l[10]+' - '+new Date().toLocaleDateString(),15,251);
            doc.setFillColor(p[0],p[1],p[2]);doc.rect(0,275,210,22,'F');doc.setTextColor(255,255,255);doc.setFontSize(8);doc.setFont('helvetica','normal');doc.text('InnoGov | contact@innogov.dz | +213 23 45 67 89',15,288);
            doc.save('Document_'+docNum+'_'+id+'.pdf');btn.innerHTML=orig;btn.disabled=false;showToast(l[11])}catch(e){btn.innerHTML=orig;btn.disabled=false;showToast(l[12]+': '+e.message)}},500)}
        function showToast(m){const t=document.querySelector('.toast');if(t)t.remove();const d=document.createElement('div');d.className='toast';d.innerHTML=m;document.body.appendChild(d);setTimeout(()=>{d.style.opacity='0';d.style.transition='opacity 0.3s ease';setTimeout(()=>d.remove(),300)},3000)}

        // ========== CHATBOT SCRIPTS (Groq API) ==========
        function toggleChatbot() {
            const w = document.getElementById('chatbotWindow');
            const b = document.getElementById('chatbotBubble');
            w.classList.toggle('active');
            b.style.display = w.classList.contains('active') ? 'none' : 'flex';
            if(w.classList.contains('active')) document.getElementById('chatbotInput').focus();
        }

        function addMessage(type, text) {
            const div = document.getElementById('chatbotMessages');
            const msg = document.createElement('div');
            msg.className = 'chatbot-message ' + type;
            msg.innerHTML = '<div class="message-bubble">' + text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') + '</div>';
            div.appendChild(msg);
            div.scrollTop = div.scrollHeight;
        }

        function buildSystemPrompt() {
            const langMap = {fr:'français', en:'anglais', ar:'arabe'};
            const langue = langMap[CHATBOT_CONTEXT.lang] || 'français';
            let contexte = '';
            if (CHATBOT_CONTEXT.demandes.length > 0) {
                contexte = "Demandes du citoyen :\n";
                CHATBOT_CONTEXT.demandes.forEach(d => {
                    contexte += `- #${d.id} | Titre: ${d.titre} | Statut: ${d.statut} | Type: ${d.type} | Service: ${d.service} | Date: ${d.date}\n`;
                });
            } else {
                contexte = "Le citoyen n'a aucune demande en cours.";
            }
            return `Tu es InnoBot, l'assistant intelligent du portail citoyen InnoGov d'une mairie.
Tu aides les citoyens avec leurs démarches administratives : suivi de demandes, délais, documents, procédures.
Réponds toujours en ${langue}, de façon claire, sympathique et concise (3-5 phrases max sauf si l'utilisateur demande plus de détail).
Utilise des emojis avec modération pour rendre les réponses plus lisibles.

Contexte de l'utilisateur :
Nom : ${CHATBOT_CONTEXT.prenom} ${CHATBOT_CONTEXT.nom}
${contexte}

Délais moyens de traitement :
- État civil : 5-7 jours
- Urbanisme : 10-15 jours
- Voirie : 3-5 jours
- Social : 7-10 jours

Si on te demande de créer une demande, dis à l'utilisateur de cliquer sur 'Nouvelle Demande'.
Si on te demande un document, indique qu'il est téléchargeable dans la section 'Mes Documents' si la demande est traitée.
Tu peux répondre à toutes les questions liées aux services municipaux.`;
        }

        async function sendMessage() {
            const input = document.getElementById('chatbotInput');
            const msg = input.value.trim();
            if (!msg) return;

            addMessage('user', msg);
            input.value = '';

            const typing = document.createElement('div');
            typing.className = 'chatbot-message bot';
            typing.id = 'typing';
            typing.innerHTML = '<div class="message-bubble"><i class="fas fa-spinner fa-spin"></i> InnoBot réfléchit...</div>';
            document.getElementById('chatbotMessages').appendChild(typing);
            document.getElementById('chatbotMessages').scrollTop = document.getElementById('chatbotMessages').scrollHeight;

            try {
                // ⚡ Remplace par ta clé Groq
                const GROQ_API_KEY = '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';

                const response = await fetch('https://api.groq.com/openai/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${GROQ_API_KEY}`
                    },
                    body: JSON.stringify({
                        model: 'llama-3.3-70b-versatile', // tu peux aussi utiliser 'mixtral-8x7b-32768'
                        temperature: 0.7,
                        max_tokens: 800,
                        messages: [
                            { role: 'system', content: buildSystemPrompt() },
                            { role: 'user', content: msg }
                        ]
                    })
                });

                const data = await response.json();
                document.getElementById('typing')?.remove();
                const reponse = data.choices?.[0]?.message?.content ?? '❌ Réponse vide.';
                addMessage('bot', reponse);
            } catch(e) {
                document.getElementById('typing')?.remove();
                addMessage('bot', '❌ Erreur de connexion. Veuillez réessayer.');
            }
        }

        // ========== NOTIFICATIONS SCRIPTS ==========
        function toggleNotifications() {
            document.getElementById('notifOverlay').classList.toggle('active');
            document.getElementById('notifModal').classList.toggle('active');
        }
    </script>
</body>
</html>