<?php
header('Content-Type: application/json');

require_once __DIR__."/../MODEL/config.php";
require_once __DIR__."/../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../CONTROLLER/ServiceController.php";

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$query = isset($input['q']) ? trim($input['q']) : (isset($_GET['q']) ? trim($_GET['q']) : '');
$image_b64 = isset($input['image']) && !empty($input['image']) ? $input['image'] : null;

if (empty($query)) {
    echo json_encode([
        'action' => 'message',
        'text' => "Bonjour ! Je suis l'assistant intelligent de la municipalité. Comment puis-je vous aider aujourd'hui ?"
    ]);
    exit;
}

// ==========================================
// CONFIGURATION DE L'IA (DeepSeek ou OpenAI)
// ==========================================
// Remplacer par votre vraie clé API DeepSeek ou OpenAI
$api_key = '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'; 
// URL pour Groq (Compatible OpenAI)
$api_url = 'https://api.groq.com/openai/v1/chat/completions';
$model = 'llama-3.3-70b-versatile'; // Nouveau modèle très performant et gratuit chez Groq

// Si une image est fournie, on bascule obligatoirement sur le modèle Vision
if ($image_b64) {
    $model = 'meta-llama/llama-4-scout-17b-16e-instruct'; // Llama 4 Scout - Modèle Vision officiel Groq
}

// ==========================================
// 1. EXTRACTION DU CONTEXTE DE LA BASE DE DONNÉES
// ==========================================
$db_context = "Voici les informations actuelles de la municipalité extraites de la base de données:\n\n";

// A. Les Services disponibles
$serviceController = new ServiceController();
$services = $serviceController->getAllServices();
$db_context .= "SERVICES DISPONIBLES :\n";
if (!empty($services)) {
    foreach ($services as $srv) {
        $db_context .= "- " . $srv['nom_service'] . " (Durée moyenne: " . $srv['duree_moyenne'] . " min) - " . $srv['description'] . "\n";
    }
} else {
    $db_context .= "Aucun service spécifiquement répertorié pour le moment.\n";
}

// B. Horaires et Contact de base
$db_context .= "\nINFORMATIONS GÉNÉRALES :\n";
$db_context .= "Horaires: Du Lundi au Vendredi, de 08h30 à 15h30.\n";
$db_context .= "Contact: Tél: +216 70 000 000, Email: contact@innogov.tn\n";

// C. Si l'utilisateur demande le statut d'un RDV spécifique (détection de numéro)
preg_match('/\b\d+\b/', $query, $matches);
$id_found = !empty($matches) ? $matches[0] : null;

if ($id_found) {
    $rdvController = new RendezVousController();
    $rdv = $rdvController->getRendezVousById($id_found);
    
    $db_context .= "\nCONTEXTE SPÉCIFIQUE AU RENDEZ-VOUS N°{$id_found} :\n";
    if ($rdv) {
        $date = date('d/m/Y H:i', strtotime($rdv['date_heure']));
        $statut = $rdv['statut'];
        $service = htmlspecialchars($rdv['service_nom'] ?? 'Inconnu');
        $agent = $rdv['agent_nom'] ? htmlspecialchars($rdv['agent_nom']) : 'Non affecté';
        
        $db_context .= "Le rendez-vous n°{$id_found} concerne le service '{$service}', prévu pour le {$date}. Son statut actuel est '{$statut}'. L'agent en charge est '{$agent}'.\n";
    } else {
        $db_context .= "Aucun rendez-vous ne correspond au numéro {$id_found} dans la base de données. Informez l'utilisateur que ce numéro n'existe pas.\n";
    }
}

// ==========================================
// 2. PRÉPARATION DU PROMPT SYSTEM
// ==========================================
$system_prompt = "Tu es l'assistant virtuel intelligent et courtois d'une municipalité (InnoGov), mais tu es aussi une intelligence artificielle capable de répondre à n'importe quelle question générale.
Ton but principal est d'aider les citoyens avec les informations de la municipalité en utilisant le contexte fourni ci-dessous, mais si l'utilisateur te pose une question sur un tout autre sujet (science, histoire, programmation, culture, etc.), tu dois y répondre de manière amicale et complète.
Structure ta réponse en utilisant des balises HTML basiques (comme <strong>, <br>) si tu as besoin de faire des sauts de ligne ou de mettre en gras pour l'interface web. Ne pas utiliser de markdown complexe (pas d'astérisques). Ne mets pas de balise markdown globales comme ```html.

" . $db_context;

// ==========================================
// 3. APPEL À L'API DE L'IA (Multimodal)
// ==========================================
$user_message = [];
if (!empty($query)) {
    $user_message[] = ['type' => 'text', 'text' => $query];
} else {
    $user_message[] = ['type' => 'text', 'text' => "Que vois-tu sur cette image ?"];
}

if ($image_b64) {
    $user_message[] = ['type' => 'image_url', 'image_url' => ['url' => $image_b64]];
}

$data = [
    'model' => $model,
    'messages' => [
        ['role' => 'system', 'content' => $system_prompt]
    ],
    'temperature' => 0.2, // Faible température pour des réponses factuelles
    'max_tokens' => 800
];

if ($image_b64) {
    $data['messages'][] = ['role' => 'user', 'content' => $user_message];
} else {
    $data['messages'][] = ['role' => 'user', 'content' => $query];
}

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

// Si l'utilisateur n'a pas mis de clé API, on simule une réponse (pour éviter que l'app plante pendant les tests)
if ($api_key === 'VOTRE_CLE_API_ICI') {
    $ai_reply = "⚠️ <strong>Attention :</strong> L'intelligence artificielle n'est pas encore activée car la clé API n'a pas été configurée par l'administrateur.<br><br>";
    $ai_reply .= "Pour activer DeepSeek ou ChatGPT, veuillez ouvrir le fichier <strong>api/chatbot_api.php</strong> et insérer votre clé API à la ligne 18.";
    
    // Si on a un ID, on donne au moins le statut en mode de secours
    if ($id_found && isset($rdv) && $rdv) {
         $ai_reply .= "<br><br><strong>Mode Secours - RDV {$id_found} :</strong> Statut: {$rdv['statut']}, Date: " . date('d/m/Y H:i', strtotime($rdv['date_heure'])) . ", Service: " . ($rdv['service_nom'] ?? 'Inconnu');
    }
} else {
    $response_json = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $result = json_decode($response_json, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $ai_reply = $result['choices'][0]['message']['content'];
            // Nettoyage rudimentaire si l'IA renvoie du markdown
            $ai_reply = str_replace("\n", "<br>", $ai_reply); 
            $ai_reply = str_replace("**", "<strong>", $ai_reply); 
        } else {
            $ai_reply = "Désolé, je n'ai pas pu formuler de réponse (Erreur de structure API).";
        }
    } else {
        // En cas d'erreur (ex: 400), on affiche le détail renvoyé par Groq pour comprendre
        $ai_reply = "Désolé, une erreur est survenue (Code {$http_code}). Détails: " . htmlspecialchars($response_json);
    }
}

// ==========================================
// 4. RÉPONSE AU CLIENT
// ==========================================
echo json_encode([
    'action' => 'message',
    'text' => $ai_reply
]);
?>
