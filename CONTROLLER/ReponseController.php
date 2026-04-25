<?php
require_once __DIR__ . "/../MODEL/config.php";

class ReponseController {
    
    private $db;
    
    public function __construct() {
        $this->db = Config::getConnexion();
    }
    
    /**
     * Ajouter une réponse
     */
    public function ajouter($id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse = 'reponse') {
        $sql = "INSERT INTO reponse_demandes (id_demande, id_citoyen, id_agent, contenu, type_reponse, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse]);
    }
    
    /**
     * Récupérer les réponses d'une demande
     */
    public function getReponsesByDemande($id_demande) {
        $sql = "SELECT r.*, u.nom as nom_agent, u.prenom as prenom_agent
                FROM reponse_demandes r
                LEFT JOIN users u ON r.id_agent = u.id
                WHERE r.id_demande = ?
                ORDER BY r.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_demande]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer une réponse par son ID
     */
    public function getReponseById($id_reponse) {
        $sql = "SELECT r.*, d.titre as titre_demande, c.nom as nom_citoyen, c.prenom as prenom_citoyen 
                FROM reponse_demandes r 
                JOIN demandes d ON r.id_demande = d.id_demande 
                JOIN citoyens c ON r.id_citoyen = c.id_citoyen 
                WHERE r.id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_reponse]);
        return $stmt->fetch();
    }
    
    /**
     * Modifier une réponse
     */
    public function modifier($id_reponse, $contenu, $type_reponse) {
        $sql = "UPDATE reponse_demandes SET contenu = ?, type_reponse = ? WHERE id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$contenu, $type_reponse, $id_reponse]);
    }
    
    /**
     * Supprimer une réponse
     */
    public function supprimer($id_reponse) {
        $sql = "DELETE FROM reponse_demandes WHERE id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_reponse]);
    }
    
    /**
     * Compter les réponses par demande
     */
    public function countReponsesByDemande($id_demande) {
        $sql = "SELECT COUNT(*) FROM reponse_demandes WHERE id_demande = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_demande]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Compter toutes les réponses groupées par demande
     */
    public function countAllReponses() {
        $sql = "SELECT id_demande, COUNT(*) as nb_reponses FROM reponse_demandes GROUP BY id_demande";
        return $this->db->query($sql)->fetchAll();
    }
}
?>