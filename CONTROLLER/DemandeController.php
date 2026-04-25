<?php

require_once __DIR__ . "/../MODEL/config.php";
require_once __DIR__ . "/../MODEL/Demande.php";
require_once __DIR__ . "/../MODEL/SuiviDemande.php";

class DemandeController {
    
    private $suiviModel;
    
    public function __construct() {
        $this->suiviModel = new SuiviDemande();
    }
    
    /**
     * Dashboard - Liste toutes les demandes (Admin)
     */
    public function index() {
        $this->checkAuth();
        
        // Récupérer toutes les demandes (pas seulement celles du citoyen)
        $demandes = $this->getDemandes();
        $stats = $this->getAllStatistiques();
        
        return ['demandes' => $demandes, 'stats' => $stats];
    }
    
    /**
     * Ajouter une nouvelle demande
     */
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
            
            // Validation
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
                    
                    // Redirection vers la page de succès
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
     * Modifier une demande existante
     */
    public function modifier($id) {
        $this->checkAuth();
        
        $demande = $this->getDemandeById($id);
        if (!$demande) {
            header('Location: index.php?error=Demande introuvable');
            exit();
        }
        
        // ✅ MODIFIÉ : Admin peut tout modifier, citoyen seulement ses demandes
        $user_role = $_SESSION['user_role'] ?? 'citoyen';
        if ($user_role !== 'admin' && $demande['id_citoyen'] != $_SESSION['user_id']) {
            header('Location: index.php?error=Accès non autorisé');
            exit();
        }
        
        $services = $this->getAllServices();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $id_service = (int)($_POST['id_service'] ?? 0);
            $type_demande = $_POST['type_demande'] ?? '';
            $statut = $_POST['statut'] ?? $demande['statut'];
            
            $errors = [];
            
            // Validation
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
                    // Ajouter un suivi si le statut a changé
                    if ($statut !== $demande['statut']) {
                        $this->suiviModel->ajouter($id, $demande['statut'], $statut, 'Statut modifié par administrateur');
                    } else {
                        $this->suiviModel->ajouter($id, $demande['statut'], $statut, 'Demande modifiée');
                    }
                    
                    // Redirection vers le backoffice
                    header('Location: index.php?success=Demande modifiée avec succès');
                    exit();
                } else {
                    $errors['general'] = 'Erreur lors de la modification de la demande';
                }
            }
            
            return [
                'demande' => array_merge($demande, $_POST),
                'services' => $services,
                'errors' => $errors
            ];
        }
        
        return ['demande' => $demande, 'services' => $services, 'errors' => []];
    }
    
    /**
     * Afficher la page de confirmation de suppression
     */
    public function supprimer($id) {
        $this->checkAuth();
        
        $demande = $this->getDemandeById($id);
        if (!$demande) {
            header('Location: index.php?error=Demande introuvable');
            exit();
        }
        
        return ['demande' => $demande];
    }
    
    /**
     * Supprimer définitivement une demande
     */
    public function deleteDemandeConfirm($id_demande) {
        $this->checkAuth();
        
        try {
            $demande = $this->getDemandeById($id_demande);
            if (!$demande) {
                return ['success' => false, 'error' => 'Demande introuvable'];
            }
            
            // Supprimer les suivis liés
            $this->suiviModel->supprimerParDemande($id_demande);
            
            // Supprimer la demande
            $result = $this->deleteDemande($id_demande);
            
            if ($result) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Erreur lors de la suppression'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    // ========== MÉTHODES PRIVÉES ==========
    
    /**
     * Vérifier l'authentification
     */
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Pour le développement : connexion automatique en tant qu'admin
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_nom'] = 'Administrateur';
            $_SESSION['user_prenom'] = 'Admin';
            $_SESSION['user_role'] = 'admin';
        }
    }
    
    /**
     * Récupérer tous les services actifs
     */
    private function getAllServices() {
        $db = Config::getConnexion();
        return $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif' ORDER BY nom_service")->fetchAll();
    }
    
    /**
     * Statistiques globales (toutes les demandes)
     */
    private function getAllStatistiques() {
        $sql = "SELECT COUNT(*) as total,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traite,
                SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuse
                FROM demandes";
        $db = Config::getConnexion();
        return $db->query($sql)->fetch() ?: ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];
    }
    
    /**
     * Statistiques pour un citoyen spécifique
     */
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
    
    // ========== MÉTHODES DATABASE ==========
    
    /**
     * Ajouter une demande
     */
    public function addDemande($demande) {
        $sql = "INSERT INTO demandes (titre, description, type_demande, statut, date_creation, id_citoyen, id_service) 
                VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $db = Config::getConnexion();
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getTypeDemande(),
            $demande->getStatut(),
            $demande->getIdCitoyen(),
            $demande->getIdService()
        ]);
    }
    
    /**
     * Récupérer toutes les demandes (pour l'admin)
     */
    public function getDemandes() {
        $sql = "SELECT d.*, s.nom_service,
                       DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee,
                       DATE_FORMAT(d.date_modification, '%d/%m/%Y %H:%i') as date_modification_formatee
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                ORDER BY d.date_creation DESC";
        return Config::getConnexion()->query($sql)->fetchAll();
    }
    
    /**
     * Récupérer les demandes d'un citoyen
     */
    public function getDemandesByCitoyen($id_citoyen) {
        $sql = "SELECT d.*, s.nom_service,
                       DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee,
                       DATEDIFF(NOW(), d.date_creation) as jours_ecoules
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.id_citoyen = ?
                ORDER BY d.date_creation DESC";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer une demande par son ID
     */
    public function getDemandeById($id) {
        $sql = "SELECT d.*, s.nom_service
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.id_demande = ?";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mettre à jour une demande
     */
    public function updateDemande($id, $demande) {
        $sql = "UPDATE demandes 
                SET titre = ?, 
                    description = ?, 
                    type_demande = ?, 
                    id_service = ?, 
                    statut = ?,
                    date_modification = NOW() 
                WHERE id_demande = ?";
        $stmt = Config::getConnexion()->prepare($sql);
        return $stmt->execute([
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getTypeDemande(),
            $demande->getIdService(),
            $demande->getStatut(),
            $id
        ]);
    }
    
    /**
     * Mettre à jour uniquement le statut d'une demande
     */
    public function updateStatutDemande($id, $statut) {
        $sql = "UPDATE demandes SET statut = ?, date_modification = NOW() WHERE id_demande = ?";
        $stmt = Config::getConnexion()->prepare($sql);
        return $stmt->execute([$statut, $id]);
    }
    
    /**
     * Supprimer une demande
     */
    public function deleteDemande($id) {
        $stmt = Config::getConnexion()->prepare("DELETE FROM demandes WHERE id_demande = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Compter les demandes par statut
     */
    public function countDemandesByStatut($statut) {
        $stmt = Config::getConnexion()->prepare("SELECT COUNT(*) FROM demandes WHERE statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Récupérer les demandes par statut
     */
    public function getDemandesByStatut($statut) {
        $sql = "SELECT d.*, s.nom_service FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.statut = ?
                ORDER BY d.date_creation DESC";
        $stmt = Config::getConnexion()->prepare($sql);
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }
}
?>