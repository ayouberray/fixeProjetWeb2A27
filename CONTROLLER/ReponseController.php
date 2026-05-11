<?php
require_once __DIR__ . "/../MODEL/config.php";

if (file_exists(__DIR__ . "/../MODEL/Reponse.php")) {
    require_once __DIR__ . "/../MODEL/Reponse.php";
}

class ReponseController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    private function isReclamationContext() {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        return strpos($script, '/reponse/') !== false || strpos($script, '/reclamation/') !== false;
    }

    public function ajouter($id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse = 'reponse') {
        $sql = "INSERT INTO reponse_demandes (id_demande, id_citoyen, id_agent, contenu, type_reponse, date_creation)
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_demande, $id_citoyen, $id_agent, $contenu, $type_reponse]);
    }

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

    public function getReponseById($id_reponse) {
        if ($this->isReclamationContext()) {
            $reclamation = $this->getReponseReclamationById($id_reponse);
            if ($reclamation) {
                return $reclamation;
            }
        }

        $demande = $this->getReponseDemandeById($id_reponse);
        if ($demande) {
            return $demande;
        }

        return $this->getReponseReclamationById($id_reponse);
    }

    public function getReponseDemandeById($id_reponse) {
        $sql = "SELECT r.*, d.titre as titre_demande, c.nom as nom_citoyen, c.prenom as prenom_citoyen
                FROM reponse_demandes r
                JOIN demandes d ON r.id_demande = d.id_demande
                JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                WHERE r.id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_reponse]);
        return $stmt->fetch();
    }

    public function modifier($id_reponse, $contenu, $type_reponse) {
        $sql = "UPDATE reponse_demandes SET contenu = ?, type_reponse = ? WHERE id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$contenu, $type_reponse, $id_reponse]);
    }

    public function supprimer($id_reponse) {
        $sql = "DELETE FROM reponse_demandes WHERE id_reponse = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_reponse]);
    }

    public function countReponsesByDemande($id_demande) {
        $sql = "SELECT COUNT(*) FROM reponse_demandes WHERE id_demande = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_demande]);
        return $stmt->fetchColumn();
    }

    public function countAllReponses() {
        $sql = "SELECT id_demande, COUNT(*) as nb_reponses FROM reponse_demandes GROUP BY id_demande";
        return $this->db->query($sql)->fetchAll();
    }

    public function ajouterReponse($reponse) {
        $sql = "INSERT INTO reponse (id_reclamation, nom_agent, service_agent, type_reponse, contenu, decision, date_reponse, envoyeur)
                VALUES (:id_reclamation, :nom_agent, :service_agent, :type_reponse, :contenu, :decision, NOW(), 'admin')";

        try {
            $this->db->beginTransaction();
            $req = $this->db->prepare($sql);
            $req->execute([
                ':id_reclamation' => $reponse->getIdReclamation(),
                ':nom_agent' => $reponse->getNomAgent(),
                ':service_agent' => $reponse->getServiceAgent(),
                ':type_reponse' => $reponse->getTypeReponse(),
                ':contenu' => $reponse->getContenu(),
                ':decision' => $reponse->getDecision()
            ]);

            $statut = 'traitee';
            if ($reponse->getTypeReponse() == 'rejet') $statut = 'rejetee';
            if ($reponse->getTypeReponse() == 'cloture') $statut = 'cloturee';

            $sqlUpd = "UPDATE reclamation SET statut = :statut, date_modification = NOW() WHERE id_reclamation = :id_reclamation";
            $reqUpd = $this->db->prepare($sqlUpd);
            $reqUpd->execute([
                ':statut' => $statut,
                ':id_reclamation' => $reponse->getIdReclamation()
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur ajout reponse: " . $e->getMessage());
            return false;
        }
    }

    public function ajouterReponseCitoyen($id_reclamation, $contenu) {
        $sql = "INSERT INTO reponse (id_reclamation, nom_agent, service_agent, type_reponse, contenu, decision, date_reponse, envoyeur)
                VALUES (:id_reclamation, 'Citoyen', NULL, 'information', :contenu, NULL, NOW(), 'citoyen')";

        try {
            $req = $this->db->prepare($sql);
            $req->execute([
                ':id_reclamation' => $id_reclamation,
                ':contenu' => $contenu
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur ajout reponse citoyen: " . $e->getMessage());
            return false;
        }
    }

    public function modifierReponse($id_reponse, $contenu, $type_reponse, $decision = null) {
        $sql = "UPDATE reponse SET contenu = :contenu, type_reponse = :type_reponse, decision = :decision, date_reponse = NOW()
                WHERE id_reponse = :id_reponse";

        try {
            $req = $this->db->prepare($sql);
            return $req->execute([
                ':contenu' => $contenu,
                ':type_reponse' => $type_reponse,
                ':decision' => $decision,
                ':id_reponse' => $id_reponse
            ]);
        } catch (Exception $e) {
            error_log("Erreur modification reponse: " . $e->getMessage());
            return false;
        }
    }

    public function getReponseReclamationById($id_reponse) {
        $sql = "SELECT r.*, rec.reference as ref_reclamation, rec.objet as objet_reclamation
                FROM reponse r
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation
                WHERE r.id_reponse = :id_reponse";

        try {
            $req = $this->db->prepare($sql);
            $req->execute([':id_reponse' => $id_reponse]);
            return $req->fetch();
        } catch (Exception $e) {
            error_log("Erreur getReponseReclamationById: " . $e->getMessage());
            return null;
        }
    }

    public function getAllReponses() {
        $sql = "SELECT r.*, rec.reference as ref_reclamation, rec.objet as objet_reclamation
                FROM reponse r
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation
                ORDER BY r.date_reponse DESC";

        try {
            $req = $this->db->query($sql);
            return $req->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getAllReponses: " . $e->getMessage());
            return [];
        }
    }

    public function getReponsesByReclamation($id_reclamation) {
        $sql = "SELECT * FROM reponse WHERE id_reclamation = :id_reclamation ORDER BY date_reponse ASC";

        try {
            $req = $this->db->prepare($sql);
            $req->execute([':id_reclamation' => $id_reclamation]);
            return $req->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getReponsesByReclamation: " . $e->getMessage());
            return [];
        }
    }

    public function supprimerReponse($id_reponse) {
        $sql = "DELETE FROM reponse WHERE id_reponse = :id_reponse";

        try {
            $req = $this->db->prepare($sql);
            $req->execute([':id_reponse' => $id_reponse]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur supprimerReponse: " . $e->getMessage());
            return false;
        }
    }
}
?>
