<?php

require_once __DIR__ . "/../MODEL/config.php";
require_once __DIR__ . "/../MODEL/Demande.php";
require_once __DIR__ . "/../MODEL/SuiviDemande.php";

class DemandeController {
    
    private $suiviModel;
    
    public function __construct() {
        $this->suiviModel = new SuiviDemande();
    }
    
    public function index() {
        $this->checkAuth();
        $demandes = $this->getDemandes();
        $stats = $this->getAllStatistiques();
        return ['demandes' => $demandes, 'stats' => $stats];
    }
    
    public function ajouter() {
        $this->checkAuth();
        $services = $this->getAllServices();
        $errors = [];
        $form_data = ['titre' => '', 'description' => '', 'id_service' => '', 'type_demande' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form_data = [
                'titre' => trim($_POST['titre'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'id_service' => (int)($_POST['id_service'] ?? 0),
                'type_demande' => $_POST['type_demande'] ?? ''
            ];
            
            if (strlen($form_data['titre']) < 5) $errors['titre'] = 'Titre trop court (min 5 caractères)';
            if (strlen($form_data['description']) < 20) $errors['description'] = 'Description trop courte (min 20 caractères)';
            if (empty($form_data['id_service'])) $errors['id_service'] = 'Service requis';
            if (empty($form_data['type_demande'])) $errors['type_demande'] = 'Type de demande requis';
            
            if (empty($errors)) {
                $demande = new Demande();
                $demande->setIdCitoyen($_SESSION['user_id']);
                $demande->setIdService($form_data['id_service']);
                $demande->setTitre($form_data['titre']);
                $demande->setDescription($form_data['description']);
                $demande->setTypeDemande($form_data['type_demande']);
                $demande->setStatut('en_attente');
                
                if ($this->addDemande($demande)) {
                    $id = Config::getConnexion()->lastInsertId();
                    $this->suiviModel->ajouter($id, null, 'en_attente', 'Demande créée');
                    header('Location: ../backoffice/ajouter_demande.php?success=created');
                    exit();
                } else {
                    $errors['general'] = 'Erreur lors de la création de la demande';
                }
            }
            return ['errors' => $errors, 'form_data' => $form_data, 'services' => $services];
        }
        return ['services' => $services, 'errors' => $errors, 'form_data' => $form_data];
    }
    
    /**
     * MODIFIER UNE DEMANDE + ENVOI SMS SI TRAITÉ
     */
    public function modifier($id) {
        $this->checkAuth();
        
        $demande = $this->getDemandeById($id);
        if (!$demande) {
            return ['errors' => ['general' => 'Demande introuvable']];
        }
        
        $user_role = $_SESSION['user_role'] ?? 'citoyen';
        if ($user_role !== 'admin' && $demande['id_citoyen'] != $_SESSION['user_id']) {
            return ['errors' => ['general' => 'Accès non autorisé']];
        }
        
        $services = $this->getAllServices();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $id_service = (int)($_POST['id_service'] ?? 0);
            $type_demande = $_POST['type_demande'] ?? '';
            $statut = $_POST['statut'] ?? $demande['statut'];
            
            $errors = [];
            
            if (strlen($titre) < 5) $errors['titre'] = 'Le titre doit contenir au moins 5 caractères';
            if (strlen($description) < 20) $errors['description'] = 'La description doit contenir au moins 20 caractères';
            if (empty($id_service)) $errors['id_service'] = 'Veuillez sélectionner un service';
            if (empty($type_demande)) $errors['type_demande'] = 'Veuillez sélectionner un type de demande';
            
            if (empty($errors)) {
                $demandeObj = new Demande();
                $demandeObj->setTitre($titre);
                $demandeObj->setDescription($description);
                $demandeObj->setIdService($id_service);
                $demandeObj->setTypeDemande($type_demande);
                $demandeObj->setStatut($statut);
                
                if ($this->updateDemande($id, $demandeObj)) {
                    // Ajouter un suivi
                    if ($statut !== $demande['statut']) {
                        $this->suiviModel->ajouter($id, $demande['statut'], $statut, 'Statut modifié par administrateur');
                    } else {
                        $this->suiviModel->ajouter($id, $demande['statut'], $statut, 'Demande modifiée');
                    }
                    
                    // ========== ENVOI SMS SI PASSAGE À TRAITÉ ==========
                    $smsEnvoye = false;
                    if ($demande['statut'] !== 'traite' && $statut === 'traite') {
                        $smsEnvoye = $this->envoyerSMSTwilio($id);
                    }
                    
                    return [
                        'success' => true,
                        'id_demande' => $id,
                        'ancien_statut' => $demande['statut'],
                        'nouveau_statut' => $statut,
                        'sms_envoye' => $smsEnvoye,
                        'demande' => $this->getDemandeById($id),
                        'services' => $services,
                        'errors' => []
                    ];
                } else {
                    $errors['general'] = 'Erreur lors de la modification de la demande';
                }
            }
            
            return ['demande' => array_merge($demande, $_POST), 'services' => $services, 'errors' => $errors];
        }
        
        return ['demande' => $demande, 'services' => $services, 'errors' => []];
    }
    
    /**
     * ENVOI SMS VIA TWILIO
     */
    private function envoyerSMSTwilio($id_demande) {
        $sid = 'AC8076693893118ab692d90b6b60aa2456';
        $token = '5c07c38bb732fc025429e90f9fd63806';
        $from = '+19129133693';
        
        try {
            $db = Config::getConnexion();
            $stmt = $db->prepare("SELECT c.telephone FROM demandes d JOIN citoyens c ON d.id_citoyen = c.id_citoyen WHERE d.id_demande = ?");
            $stmt->execute([$id_demande]);
            $tel = $stmt->fetchColumn();
            
            if (!$tel) return false;
            
            // Nettoyer le numéro
            $tel = preg_replace('/[^0-9]/', '', $tel);
            if (!empty($tel) && $tel[0] != '+') $tel = '+' . $tel;
            
            $message = "InnoGov: Demande #" . str_pad($id_demande, 5, '0', STR_PAD_LEFT) . " traitee.";
            
            $ch = curl_init("https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'From' => $from,
                'To' => $tel,
                'Body' => $message
            ]));
            curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Log
            $logDir = __DIR__ . '/../../logs';
            if (!is_dir($logDir)) mkdir($logDir, 0777, true);
            file_put_contents($logDir . '/sms.log', date('Y-m-d H:i:s') . " | #$id_demande | Tel:$tel | HTTP:$httpCode | $response\n", FILE_APPEND);
            
            return ($httpCode == 201);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function supprimer($id) {
        $this->checkAuth();
        $demande = $this->getDemandeById($id);
        if (!$demande) { header('Location: index.php?error=Demande introuvable'); exit(); }
        return ['demande' => $demande];
    }
    
    public function deleteDemandeConfirm($id_demande) {
        $this->checkAuth();
        try {
            $demande = $this->getDemandeById($id_demande);
            if (!$demande) return ['success' => false, 'error' => 'Demande introuvable'];
            $this->suiviModel->supprimerParDemande($id_demande);
            $result = $this->deleteDemande($id_demande);
            return $result ? ['success' => true] : ['success' => false, 'error' => 'Erreur lors de la suppression'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // ========== MÉTHODES PRIVÉES ==========
    
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_nom'] = 'Administrateur';
            $_SESSION['user_prenom'] = 'Admin';
            $_SESSION['user_role'] = 'admin';
        }
    }
    
    private function getAllServices() {
        $db = Config::getConnexion();
        return $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif' ORDER BY nom_service")->fetchAll();
    }
    
    private function getAllStatistiques() {
        $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente, SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours, SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traite, SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuse FROM demandes";
        $db = Config::getConnexion();
        return $db->query($sql)->fetch() ?: ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];
    }
    
    private function getStatistiques($id_citoyen) {
        $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente, SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours, SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traite, SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuse FROM demandes WHERE id_citoyen = ?";
        $db = Config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetch() ?: ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];
    }
    
    // ========== MÉTHODES DATABASE ==========
    
    public function addDemande($demande) {
        $sql = "INSERT INTO demandes (titre, description, type_demande, statut, date_creation, id_citoyen, id_service) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $db = Config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([$demande->getTitre(), $demande->getDescription(), $demande->getTypeDemande(), $demande->getStatut(), $demande->getIdCitoyen(), $demande->getIdService()]);
    }
    
    public function getDemandes() {
        $sql = "SELECT d.*, s.nom_service, DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee, DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee, DATE_FORMAT(d.date_modification, '%d/%m/%Y %H:%i') as date_modification_formatee FROM demandes d LEFT JOIN services s ON d.id_service = s.id_service ORDER BY d.date_creation DESC";
        return Config::getConnexion()->query($sql)->fetchAll();
    }
    
    public function getDemandesByCitoyen($id_citoyen) {
        $sql = "SELECT d.*, s.nom_service, DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee, DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee, DATEDIFF(NOW(), d.date_creation) as jours_ecoules FROM demandes d LEFT JOIN services s ON d.id_service = s.id_service WHERE d.id_citoyen = ? ORDER BY d.date_creation DESC";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetchAll();
    }
    
    public function getDemandeById($id) {
        $sql = "SELECT d.*, s.nom_service FROM demandes d LEFT JOIN services s ON d.id_service = s.id_service WHERE d.id_demande = ?";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function updateDemande($id, $demande) {
        $sql = "UPDATE demandes SET titre=?, description=?, type_demande=?, id_service=?, statut=?, date_modification=NOW() WHERE id_demande=?";
        $stmt = Config::getConnexion()->prepare($sql);
        return $stmt->execute([$demande->getTitre(), $demande->getDescription(), $demande->getTypeDemande(), $demande->getIdService(), $demande->getStatut(), $id]);
    }
    
    public function updateStatutDemande($id, $statut) {
        $sql = "UPDATE demandes SET statut=?, date_modification=NOW() WHERE id_demande=?";
        $stmt = Config::getConnexion()->prepare($sql);
        return $stmt->execute([$statut, $id]);
    }
    
    public function deleteDemande($id) {
        $stmt = Config::getConnexion()->prepare("DELETE FROM demandes WHERE id_demande = ?");
        return $stmt->execute([$id]);
    }
    
    public function countDemandesByStatut($statut) {
        $stmt = Config::getConnexion()->prepare("SELECT COUNT(*) FROM demandes WHERE statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchColumn();
    }
    
    public function getDemandesByStatut($statut) {
        $sql = "SELECT d.*, s.nom_service FROM demandes d LEFT JOIN services s ON d.id_service = s.id_service WHERE d.statut = ? ORDER BY d.date_creation DESC";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }
}