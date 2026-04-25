<?php
// CONTROLLER/SuiviDemandeController.php

require_once __DIR__ . '/../MODEL/config.php';
require_once __DIR__ . '/../MODEL/SuiviDemande.php';

class SuiviDemandeController {
    
    private $suiviModel;
    
    public function __construct() {
        $this->suiviModel = new SuiviDemande();
        $this->checkAuth();
    }
    
    
    public function show($id) {
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_role'] ?? 'citoyen';
        
        // ✅ Récupérer la demande (admin voit tout, citoyen voit seulement ses demandes)
        $demande = $this->getDemandeComplete($id, $user_id, $user_role);
        
        if (!$demande) {
            header('Location: index.php?error=Demande introuvable');
            exit();
        }
        
        // Récupérer l'historique
        $historique = $this->getHistorique($id);
        
        // Calculer le délai
        $delai = $this->getDelaiTraitement($demande);
        
        return [
            'demande' => $demande,
            'historique' => $historique,
            'delai' => $delai
        ];
    }
    
  
    private function getDemandeComplete($id_demande, $id_citoyen, $user_role = 'citoyen') {
        $db = Config::getConnexion();
        
        // ✅ Si admin, pas de filtre par id_citoyen
        if ($user_role === 'admin') {
            $sql = "SELECT d.*, 
                           s.nom_service,
                           s.description as service_description,
                           c.nom as citoyen_nom,
                           c.prenom as citoyen_prenom,
                           DATEDIFF(NOW(), d.date_creation) as jours_ecoules,
                           DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_creation_format,
                           DATE_FORMAT(d.date_creation, '%H:%i') as heure_creation,
                           DATE_FORMAT(d.date_modification, '%d/%m/%Y') as date_modification_format
                    FROM demandes d
                    LEFT JOIN services s ON d.id_service = s.id_service
                    LEFT JOIN citoyens c ON d.id_citoyen = c.id_citoyen
                    WHERE d.id_demande = :id_demande";
            
            $req = $db->prepare($sql);
            $req->execute([':id_demande' => $id_demande]);
        } else {
            // Citoyen : filtrer par id_citoyen
            $sql = "SELECT d.*, 
                           s.nom_service,
                           s.description as service_description,
                           DATEDIFF(NOW(), d.date_creation) as jours_ecoules,
                           DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_creation_format,
                           DATE_FORMAT(d.date_creation, '%H:%i') as heure_creation,
                           DATE_FORMAT(d.date_modification, '%d/%m/%Y') as date_modification_format
                    FROM demandes d
                    LEFT JOIN services s ON d.id_service = s.id_service
                    WHERE d.id_demande = :id_demande AND d.id_citoyen = :id_citoyen";
            
            $req = $db->prepare($sql);
            $req->execute([
                ':id_demande' => $id_demande,
                ':id_citoyen' => $id_citoyen
            ]);
        }
        
        try {
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }
    
   
    private function getHistorique($id_demande) {
        $sql = "SELECT s.*, 
                       u.nom as agent_nom,
                       u.prenom as agent_prenom,
                       DATE_FORMAT(s.date_changement, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(s.date_changement, '%H:%i') as heure_formatee
                FROM suivi_demandes s
                LEFT JOIN users u ON s.id_agent = u.id
                WHERE s.id_demande = :id_demande
                ORDER BY s.date_changement DESC";
        
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute([':id_demande' => $id_demande]);
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
    
  
    private function getDelaiTraitement($demande) {
        if ($demande['statut'] == 'traite' && $demande['date_modification']) {
            $date_creation = new DateTime($demande['date_creation']);
            $date_modification = new DateTime($demande['date_modification']);
            $interval = $date_creation->diff($date_modification);
            return $interval->days;
        }
        return null;
    }
    
    
    public function ajouterSuivi($id_demande, $ancien_statut, $nouveau_statut, $commentaire, $id_agent = null) {
        $sql = "INSERT INTO suivi_demandes 
                (id_demande, id_agent, ancien_statut, nouveau_statut, commentaire, date_changement) 
                VALUES (:id_demande, :id_agent, :ancien_statut, :nouveau_statut, :commentaire, NOW())";
        
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            return $req->execute([
                ':id_demande' => $id_demande,
                ':id_agent' => $id_agent,
                ':ancien_statut' => $ancien_statut,
                ':nouveau_statut' => $nouveau_statut,
                ':commentaire' => $commentaire
            ]);
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
            return false;
        }
    }
    
    
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            // Par défaut, connecter en tant qu'admin pour le backoffice
            $_SESSION['user_id'] = 1;
            $_SESSION['user_nom'] = 'Administrateur';
            $_SESSION['user_prenom'] = 'Admin';
            $_SESSION['user_role'] = 'admin';
        }
    }
}
?>