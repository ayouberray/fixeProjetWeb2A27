<?php
require_once __DIR__ . '/../MODEL/config.php';

class ChatbotController {
    
    public function getResponse($message) {
        $message = mb_strtolower(trim($message), 'UTF-8');
        
        if (strpos($message, 'salut') !== false || strpos($message, 'bonjour') !== false || strpos($message, 'salam') !== false) {
            return "Bonjour ! Je suis l'assistant InnoGov. Comment puis-je vous aider avec le planning ?";
        }

        if (strpos($message, 'emploi') !== false || strpos($message, 'planning') !== false) {
            $stats = $this->getQuickStats();
            return "Il y a actuellement " . $stats['total'] . " emplois planifiés, dont " . $stats['aujourdhui'] . " pour aujourd'hui. Vous pouvez les gérer dans la section 'Emplois'.";
        }

        if (strpos($message, 'agent') !== false) {
            $stats = $this->getQuickStats();
            return "Nous avons " . $stats['agents'] . " agents actifs dans le système. Vous pouvez voir la liste complète dans le module de gestion des agents.";
        }

        if (strpos($message, 'shift') !== false || strpos($message, 'horaire') !== false) {
            return "Les shifts permettent de définir les plages horaires (Matin, Après-midi, Nuit). Vous pouvez en créer de nouveaux dans la section 'Shifts'.";
        }

        if (strpos($message, 'aide') !== false || strpos($message, 'comment') !== false) {
            return "Je peux vous renseigner sur les statistiques globales, les agents ou les horaires. Essayez de me demander : 'Combien d'emplois aujourd'hui ?'";
        }

        return "Désolé, je ne comprends pas encore cette demande. Essayez de me poser une question sur les emplois, les shifts ou les agents !";
    }

    private function getQuickStats() {
        $db = Config::getConnexion();
        try {
            $req = $db->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN date_travail = CURDATE() THEN 1 ELSE 0 END) as aujourdhui,
                COUNT(DISTINCT id_agent) as agents
                FROM emplois");
            return $req->fetch();
        } catch (Exception $e) {
            return ['total' => 0, 'aujourdhui' => 0, 'agents' => 0];
        }
    }
}

// Handle AJAX request
if (isset($_POST['message'])) {
    $chat = new ChatbotController();
    echo json_encode(['response' => $chat->getResponse($_POST['message'])]);
    exit;
}
