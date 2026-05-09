<?php

class SuiviReponse {
    
    private $db;
    
    public function __construct() {
        $this->db = Config::getConnexion();
    }
    
    /**
     * Récupérer toutes les réponses d'une demande (conversation principale)
     */
    public function getReponsesByDemande($id_demande) {
        $sql = "SELECT r.*, 
                       u.nom as nom_agent, 
                       u.prenom as prenom_agent,
                       d.titre as titre_demande,
                       (SELECT COUNT(*) FROM reponse_demandes WHERE id_parent = r.id_reponse) as nb_reponses_enfants
                FROM reponse_demandes r
                LEFT JOIN users u ON r.id_agent = u.id
                JOIN demandes d ON r.id_demande = d.id_demande
                WHERE r.id_demande = ? AND r.id_parent IS NULL
                ORDER BY r.date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_demande]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer les réponses enfants (réponses à une réponse)
     */
    public function getReponsesEnfants($id_parent) {
        $sql = "SELECT r.*, 
                       u.nom as nom_agent, 
                       u.prenom as prenom_agent
                FROM reponse_demandes r
                LEFT JOIN users u ON r.id_agent = u.id
                WHERE r.id_parent = ?
                ORDER BY r.date_creation ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_parent]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer une réponse par son ID
     */
    public function getReponseById($id_reponse) {
        $sql = "SELECT r.*, 
                       d.titre as titre_demande, 
                       d.id_demande,
                       c.nom as nom_citoyen, 
                       c.prenom as prenom_citoyen 
                FROM reponse_demandes r 
                JOIN demandes d ON r.id_demande = d.id_demande 
                JOIN citoyens c ON r.id_citoyen = c.id_citoyen 
                WHERE r.id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_reponse]);
        return $stmt->fetch();
    }
    
    /**
     * Ajouter une réponse (admin ou citoyen)
     */
    public function ajouter($id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse = 'reponse', $expediteur = 'admin', $id_parent = null) {
        $sql = "INSERT INTO reponse_demandes (id_demande, id_citoyen, id_agent, contenu, type_reponse, expediteur, id_parent, date_creation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse, $expediteur, $id_parent]);
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
     * Supprimer une réponse et ses enfants
     */
    public function supprimer($id_reponse) {
        $sql = "DELETE FROM reponse_demandes WHERE id_reponse = ? OR id_parent = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_reponse, $id_reponse]);
    }
    
    /**
     * Compter les réponses par demande (principales seulement)
     */
    public function countByDemande($id_demande) {
        $sql = "SELECT COUNT(*) FROM reponse_demandes WHERE id_demande = ? AND id_parent IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_demande]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Compter toutes les réponses groupées par demande
     */
    public function countAll() {
        $sql = "SELECT id_demande, COUNT(*) as nb_reponses FROM reponse_demandes WHERE id_parent IS NULL GROUP BY id_demande";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Compter les réponses non lues pour un citoyen
     */
    public function countNonLuesByCitoyen($id_citoyen) {
        $sql = "SELECT COUNT(*) FROM reponse_demandes r
                JOIN demandes d ON r.id_demande = d.id_demande
                WHERE d.id_citoyen = ? AND r.expediteur = 'admin' AND r.est_lu = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_citoyen]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Marquer une réponse comme lue
     */
    public function marquerLue($id_reponse) {
        $sql = "UPDATE reponse_demandes SET est_lu = 1 WHERE id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_reponse]);
    }
}
?>