<?php
require_once __DIR__ . "/../model/config.php";
require_once __DIR__ . "/../model/Reponse.php";

class ReponseController {

    public function ajouterReponse($reponse) {
        $sql = "INSERT INTO reponse (id_reclamation, nom_agent, service_agent, type_reponse, contenu, decision, date_reponse) 
                VALUES (:id_rec, :nom_agent, :service_agent, :type, :contenu, :decision, NOW())";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id_rec' => $reponse->getIdReclamation(),
                'nom_agent' => $reponse->getNomAgent(),
                'service_agent' => $reponse->getServiceAgent(),
                'type' => $reponse->getTypeReponse(),
                'contenu' => $reponse->getContenu(),
                'decision' => $reponse->getDecision()
            ]);
            return $db->lastInsertId();
        } catch(Exception $e) {
            return false;
        }
    }

    public function getAllReponses() {
        $sql = "SELECT r.*, rec.reference, rec.objet, c.nom, c.prenom 
                FROM reponse r 
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation
                JOIN citoyens c ON rec.id_citoyen = c.id_citoyen
                ORDER BY r.date_reponse DESC";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    public function getReponseById($id) {
        $sql = "SELECT r.*, rec.reference, rec.objet 
                FROM reponse r 
                JOIN reclamation rec ON r.id_reclamation = rec.id_reclamation
                WHERE r.id_reponse = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }

    public function getReponsesByReclamation($id_reclamation) {
        $sql = "SELECT * FROM reponse WHERE id_reclamation = :id_rec ORDER BY date_reponse DESC";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id_rec' => $id_reclamation]);
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    public function modifierReponse($id, $contenu, $type_reponse, $decision = null) {
        $sql = "UPDATE reponse SET contenu = :contenu, type_reponse = :type, decision = :decision WHERE id_reponse = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['contenu' => $contenu, 'type' => $type_reponse, 'decision' => $decision, 'id' => $id]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function supprimerReponse($id) {
        $sql = "DELETE FROM reponse WHERE id_reponse = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
?>