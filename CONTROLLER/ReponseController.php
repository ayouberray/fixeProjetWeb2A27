<?php
require_once __DIR__ . "/../MODEL/config.php";
require_once __DIR__ . "/../MODEL/Reponse.php";

class ReponseController {

    public function ajouterReponse($reponse) {
        $sql = "INSERT INTO reponse (id_reclamation, nom_agent, service_agent, type_reponse, contenu, decision, date_reponse) 
                VALUES (:id_reclamation, :nom_agent, :service_agent, :type_reponse, :contenu, :decision, NOW())";
        $db = Config::getConnexion();

        try {
            $db->beginTransaction();
            
            $req = $db->prepare($sql);
            $req->execute([
                ':id_reclamation' => $reponse->getIdReclamation(),
                ':nom_agent' => $reponse->getNomAgent(),
                ':service_agent' => $reponse->getServiceAgent(),
                ':type_reponse' => $reponse->getTypeReponse(),
                ':contenu' => $reponse->getContenu(),
                ':decision' => $reponse->getDecision()
            ]);

            // Mettre à jour le statut de la réclamation
            $statut = 'traitee';
            if ($reponse->getTypeReponse() == 'rejet') $statut = 'rejetee';
            if ($reponse->getTypeReponse() == 'cloture') $statut = 'cloturee';
            
            $sqlUpd = "UPDATE reclamation SET statut = :statut, date_modification = NOW() WHERE id_reclamation = :id_reclamation";
            $reqUpd = $db->prepare($sqlUpd);
            $reqUpd->execute([
                ':statut' => $statut,
                ':id_reclamation' => $reponse->getIdReclamation()
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Erreur ajout réponse: " . $e->getMessage());
            return false;
        }
    }

    public function modifierReponse($id_reponse, $contenu, $type_reponse, $decision = null) {
        $sql = "UPDATE reponse SET contenu = :contenu, type_reponse = :type_reponse, decision = :decision, date_reponse = NOW() 
                WHERE id_reponse = :id_reponse";
        $db = Config::getConnexion();

        try {
            $req = $db->prepare($sql);
            return $req->execute([
                ':contenu' => $contenu,
                ':type_reponse' => $type_reponse,
                ':decision' => $decision,
                ':id_reponse' => $id_reponse
            ]);
        } catch (Exception $e) {
            error_log("Erreur modification réponse: " . $e->getMessage());
            return false;
        }
    }

    public function getReponseById($id_reponse) {
        $sql = "SELECT r.*, rec.reference as ref_reclamation, rec.objet as objet_reclamation 
                FROM reponse r 
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation 
                WHERE r.id_reponse = :id_reponse";
        $db = Config::getConnexion();

        try {
            $req = $db->prepare($sql);
            $req->execute([':id_reponse' => $id_reponse]);
            return $req->fetch();
        } catch (Exception $e) {
            error_log("Erreur getReponseById: " . $e->getMessage());
            return null;
        }
    }

    public function getAllReponses() {
        $sql = "SELECT r.*, rec.reference as ref_reclamation, rec.objet as objet_reclamation 
                FROM reponse r 
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation 
                ORDER BY r.date_reponse DESC";
        $db = Config::getConnexion();

        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getAllReponses: " . $e->getMessage());
            return [];
        }
    }

    public function getReponsesByReclamation($id_reclamation) {
        $sql = "SELECT * FROM reponse WHERE id_reclamation = :id_reclamation ORDER BY date_reponse ASC";
        $db = Config::getConnexion();

        try {
            $req = $db->prepare($sql);
            $req->execute([':id_reclamation' => $id_reclamation]);
            return $req->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getReponsesByReclamation: " . $e->getMessage());
            return [];
        }
    }

    public function supprimerReponse($id_reponse) {
        $sql = "DELETE FROM reponse WHERE id_reponse = :id_reponse";
        $db = Config::getConnexion();

        try {
            $req = $db->prepare($sql);
            $req->execute([':id_reponse' => $id_reponse]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur supprimerReponse: " . $e->getMessage());
            return false;
        }
    }
}
?>
