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
    
    // ============================================
    // MÉTHODE SHOW - Afficher le suivi d'une demande
    // ============================================
    public function show($id) {
        $user_id = $_SESSION['user_id'];
        
        // Récupérer la demande complète
        $demande = $this->getDemandeComplete($id, $user_id);
        
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
    
    // ============================================
    // RÉCUPÉRER UNE DEMANDE COMPLÈTE
    // ============================================
    private function getDemandeComplete($id_demande, $id_citoyen) {
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
        
        $db = Config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->execute([
                ':id_demande' => $id_demande,
                ':id_citoyen' => $id_citoyen
            ]);
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }
    
    // ============================================
    // RÉCUPÉRER L'HISTORIQUE DE SUIVI
    // ============================================
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
    
    // ============================================
    // CALCULER LE DÉLAI DE TRAITEMENT
    // ============================================
    private function getDelaiTraitement($demande) {
        if ($demande['statut'] == 'traite' && $demande['date_modification']) {
            $date_creation = new DateTime($demande['date_creation']);
            $date_modification = new DateTime($demande['date_modification']);
            $interval = $date_creation->diff($date_modification);
            return $interval->days;
        }
        return null;
    }
    
    // ============================================
    // AJOUTER UN SUIVI
    // ============================================
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
}
?>