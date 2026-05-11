<?php


require_once __DIR__ . '/config.php';

class SuiviDemande {
    private $pdo;
    private $id_suivi;
    private $id_demande;
    private $id_agent;
    private $ancien_statut;
    private $nouveau_statut;
    private $commentaire;
    private $date_changement;
    
    public function __construct() {
        $this->pdo = Config::getConnexion();
    }
    
    
    public function setIdDemande($id) { $this->id_demande = $id; }
    public function setIdAgent($id) { $this->id_agent = $id; }
    public function setAncienStatut($statut) { $this->ancien_statut = $statut; }
    public function setNouveauStatut($statut) { $this->nouveau_statut = $statut; }
    public function setCommentaire($commentaire) { $this->commentaire = $commentaire; }
    
    public function getIdSuivi() { return $this->id_suivi; }
    public function getIdDemande() { return $this->id_demande; }
    public function getAncienStatut() { return $this->ancien_statut; }
    public function getNouveauStatut() { return $this->nouveau_statut; }
    public function getCommentaire() { return $this->commentaire; }
    
    
    public function ajouter($id_demande, $ancien_statut, $nouveau_statut, $commentaire, $id_agent = null) {
        $sql = "INSERT INTO suivi_demandes 
                (id_demande, id_agent, ancien_statut, nouveau_statut, commentaire, date_changement) 
                VALUES (:id_demande, :id_agent, :ancien_statut, :nouveau_statut, :commentaire, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_demande' => $id_demande,
            ':id_agent' => $id_agent,
            ':ancien_statut' => $ancien_statut,
            ':nouveau_statut' => $nouveau_statut,
            ':commentaire' => $commentaire
        ]);
    }
    
    
    public function getHistorique($id_demande) {
        $sql = "SELECT s.*, 
                       u.nom as agent_nom,
                       u.prenom as agent_prenom,
                       DATE_FORMAT(s.date_changement, '%d/%m/%Y') as date_formatee,
                       DATE_FORMAT(s.date_changement, '%H:%i') as heure_formatee
                FROM suivi_demandes s
                LEFT JOIN users u ON s.id_agent = u.id
                WHERE s.id_demande = :id_demande
                ORDER BY s.date_changement DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_demande' => $id_demande]);
        return $stmt->fetchAll();
    }
    
    
    public function getDemandeComplete($id_demande, $id_citoyen) {
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
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_demande' => $id_demande,
            ':id_citoyen' => $id_citoyen
        ]);
        return $stmt->fetch();
    }
    
    
    public function getDelaiTraitement($demande) {
        if (isset($demande['statut']) && $demande['statut'] == 'traite' && !empty($demande['date_modification'])) {
            $date_creation = new DateTime($demande['date_creation']);
            $date_modification = new DateTime($demande['date_modification']);
            $interval = $date_creation->diff($date_modification);
            return $interval->days;
        }
        return null;
    }
    
    
    public function supprimerParDemande($id_demande) {
        $sql = "DELETE FROM suivi_demandes WHERE id_demande = :id_demande";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_demande' => $id_demande]);
    }
}
?>