<?php
// CONTROLLER/DemandeController.php

require_once __DIR__ . "/../MODEL/config.php";
require_once __DIR__ . "/../MODEL/Demande.php";
require_once __DIR__ . "/../MODEL/SuiviDemande.php";

class DemandeController {
    
    // ============================================
    // MÉTHODE INDEX (NOUVELLE)
    // ============================================
    public function index() {
        // Vérifier l'authentification
        $this->checkAuth();
        
        $user_id = $_SESSION['user_id'];
        
        // Récupérer les demandes du citoyen
        $demandes = $this->getDemandesByCitoyen($user_id);
        
        // Calculer les statistiques
        $stats = $this->getStatistiques($user_id);
        
        return [
            'demandes' => $demandes,
            'stats' => $stats
        ];
    }
    
    // ============================================
    // MÉTHODE POUR LES STATISTIQUES
    // ============================================
    private function getStatistiques($id_citoyen) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traite,
                    SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuse
                FROM demandes 
                WHERE id_citoyen = :id_citoyen";
        
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':id_citoyen', $id_citoyen);
            $req->execute();
            return $req->fetch();
        } catch(Exception $e) {
            return ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];
        }
    }
    
    // ============================================
    // VÉRIFICATION AUTHENTIFICATION
    // ============================================
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 2;
            $_SESSION['user_nom'] = 'Ben Ali';
            $_SESSION['user_prenom'] = 'Mohamed';
            $_SESSION['user_role'] = 'citoyen';
        }
    }
    
    // ============================================
    // MÉTHODES EXISTANTES
    // ============================================
    
    function addDemande($demande) {
        $sql = "INSERT INTO demandes VALUES(NULL, :titre, :description, :type_demande, :statut, NOW(), :id_citoyen)";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'titre' => $demande->getTitre(),
                'description' => $demande->getDescription(),
                'type_demande' => $demande->getTypeDemande(),
                'statut' => $demande->getStatut(),
                'id_citoyen' => $demande->getIdCitoyen()
            ]);
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function getDemandes() {
        $sql = "SELECT * FROM demandes";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function getDemandesByCitoyen($id_citoyen) {
        $sql = "SELECT d.*, s.nom_service,
                       DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(d.date_creation, '%H:%i') as heure_formatee
                FROM demandes d
                LEFT JOIN services s ON d.id_service = s.id_service
                WHERE d.id_citoyen = :id_citoyen 
                ORDER BY d.date_creation DESC";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue('id_citoyen', $id_citoyen);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
            return [];
        }
    }
    
    function getDemandeById($id) {
        $sql = "SELECT * FROM demandes WHERE id_demande = :id";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue('id', $id);
            $req->execute();
            return $req->fetch();
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function updateDemande($id, $demande) {
        $sql = "UPDATE demandes SET 
                titre = :titre,
                description = :description,
                type_demande = :type_demande,
                statut = :statut 
                WHERE id_demande = :id";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id,
                'titre' => $demande->getTitre(),
                'description' => $demande->getDescription(),
                'type_demande' => $demande->getTypeDemande(),
                'statut' => $demande->getStatut()
            ]);
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function updateStatutDemande($id, $statut) {
        $sql = "UPDATE demandes SET statut = :statut WHERE id_demande = :id";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id,
                'statut' => $statut
            ]);
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function deleteDemande($id) {
        $sql = "DELETE FROM demandes WHERE id_demande = :id";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue('id', $id);
            $req->execute();
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function countDemandesByStatut($statut) {
        $sql = "SELECT COUNT(*) as total FROM demandes WHERE statut = :statut";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue('statut', $statut);
            $req->execute();
            $result = $req->fetch();
            return $result['total'];
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
    
    function getDemandesByStatut($statut) {
        $sql = "SELECT * FROM demandes WHERE statut = :statut";
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue('statut', $statut);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "erreur" . $e->getMessage();
        }
    }
}
?>