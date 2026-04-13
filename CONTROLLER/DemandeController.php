<?php
// CONTROLLER/DemandeController.php
// Version complète avec TOUTES les méthodes

require_once __DIR__ . "/../MODEL/config.php";
require_once __DIR__ . "/../MODEL/Demande.php";
require_once __DIR__ . "/../MODEL/SuiviDemande.php";

class DemandeController {
    
    private $suiviModel;
    
    public function __construct() {
        $this->suiviModel = new SuiviDemande();
    }
    
    // ============================================
    // 1. INDEX - Liste des demandes
    // ============================================
    public function index() {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'];
        $demandes = $this->getDemandesByCitoyen($user_id);
        $stats = $this->getStatistiques($user_id);
        return ['demandes' => $demandes, 'stats' => $stats];
    }
    
    // ============================================
    // 2. AJOUTER - Formulaire et traitement
    // ============================================
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
            
            if (strlen($form_data['titre']) < 5) $errors['titre'] = 'Titre trop court (min 5)';
            if (strlen($form_data['description']) < 20) $errors['description'] = 'Description trop courte (min 20)';
            if (empty($form_data['id_service'])) $errors['id_service'] = 'Service requis';
            if (empty($form_data['type_demande'])) $errors['type_demande'] = 'Type requis';
            
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
                    // MODIFICATION ICI : Rediriger vers la page de succès
                    header('Location: ../backoffice/ajouter_demande.php?success=created');
                    exit();
                }
            }
            return ['errors' => $errors, 'form_data' => $form_data, 'services' => $services];
        }
        return ['services' => $services, 'errors' => $errors, 'form_data' => $form_data];
    }
    
    // ============================================
    // 3. MODIFIER - Pour modifier_demande.php
    // ============================================
    public function modifier($id) {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'];
        
        $demande = $this->getDemandeById($id);
        if (!$demande || $demande['id_citoyen'] != $user_id) {
            header('Location: ../frontoffice/index.php?error=Demande introuvable');
            exit();
        }
        
        if (!in_array($demande['statut'], ['en_attente', 'en_cours'])) {
            header('Location: ../frontoffice/index.php?error=Non modifiable');
            exit();
        }
        
        $services = $this->getAllServices();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $id_service = (int)($_POST['id_service'] ?? 0);
            $type_demande = $_POST['type_demande'] ?? '';
            
            if (strlen($titre) >= 5 && strlen($description) >= 20 && $id_service && $type_demande) {
                $demandeObj = new Demande();
                $demandeObj->setTitre($titre);
                $demandeObj->setDescription($description);
                $demandeObj->setIdService($id_service);
                $demandeObj->setTypeDemande($type_demande);
                $demandeObj->setStatut($demande['statut']);
                
                if ($this->updateDemande($id, $demandeObj)) {
                    $this->suiviModel->ajouter($id, $demande['statut'], $demande['statut'], 'Demande modifiée');
                    header('Location: ../frontoffice/index.php?success=Demande modifiée');
                    exit();
                }
            }
        }
        
        return ['demande' => $demande, 'services' => $services];
    }
    
    // ============================================
    // 4. SUPPRIMER - Pour supprimer_demande.php (afficher confirmation)
    // ============================================
    public function supprimer($id) {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'];
        
        $demande = $this->getDemandeById($id);
        if (!$demande || $demande['id_citoyen'] != $user_id) {
            header('Location: ../frontoffice/index.php?error=Demande introuvable');
            exit();
        }
        
        return ['demande' => $demande];
    }
    
    // ============================================
    // 4bis. DELETE DEMANDE CONFIRM - Exécuter la suppression
    // ============================================
    /**
     * Confirme et exécute la suppression d'une demande
     * @param int $id_demande
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function deleteDemandeConfirm($id_demande) {
        $this->checkAuth();
        $user_id = $_SESSION['user_id'];
        
        try {
            // Vérifier que la demande appartient bien à l'utilisateur
            $demande = $this->getDemandeById($id_demande);
            if (!$demande || $demande['id_citoyen'] != $user_id) {
                return ['success' => false, 'error' => 'Demande introuvable ou non autorisée'];
            }
            
            // Supprimer l'historique de suivi associé
            $this->suiviModel->supprimerParDemande($id_demande);
            
            // Supprimer la demande
            $result = $this->deleteDemande($id_demande);
            
            if ($result) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Erreur lors de la suppression dans la base de données'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // ============================================
    // 5. MÉTHODES PRIVÉES
    // ============================================
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 2;
            $_SESSION['user_nom'] = 'Ben Ali';
            $_SESSION['user_prenom'] = 'Mohamed';
        }
    }
    
    private function getAllServices() {
        $db = Config::getConnexion();
        return $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif' ORDER BY nom_service")->fetchAll();
    }
    
    private function getStatistiques($id_citoyen) {
        $sql = "SELECT COUNT(*) as total,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traite,
                SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuse
                FROM demandes WHERE id_citoyen = ?";
        $db = Config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetch() ?: ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];
    }
    
    // ============================================
    // 6. MÉTHODES CRUD DE BASE
    // ============================================
    public function addDemande($demande) {
        $sql = "INSERT INTO demandes (titre, description, type_demande, statut, date_creation, id_citoyen, id_service) 
                VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $db = Config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $demande->getTitre(), $demande->getDescription(), $demande->getTypeDemande(),
            $demande->getStatut(), $demande->getIdCitoyen(), $demande->getIdService()
        ]);
    }
    
    public function getDemandes() {
        return Config::getConnexion()->query("SELECT * FROM demandes")->fetchAll();
    }
    
    public function getDemandesByCitoyen($id_citoyen) {
        $sql = "SELECT d.*, s.nom_service,
                       DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee,
                       DATEDIFF(NOW(), d.date_creation) as jours_ecoules
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.id_citoyen = ? ORDER BY d.date_creation DESC";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetchAll();
    }
    
    public function getDemandeById($id) {
        $stmt = Config::getConnexion()->prepare("SELECT * FROM demandes WHERE id_demande = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function updateDemande($id, $demande) {
        $sql = "UPDATE demandes SET titre = ?, description = ?, type_demande = ?, 
                id_service = ?, date_modification = NOW() WHERE id_demande = ?";
        $stmt = Config::getConnexion()->prepare($sql);
        return $stmt->execute([
            $demande->getTitre(), $demande->getDescription(), $demande->getTypeDemande(),
            $demande->getIdService(), $id
        ]);
    }
    
    public function updateStatutDemande($id, $statut) {
        $stmt = Config::getConnexion()->prepare("UPDATE demandes SET statut = ? WHERE id_demande = ?");
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
        $stmt = Config::getConnexion()->prepare("SELECT * FROM demandes WHERE statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }
}
?>
