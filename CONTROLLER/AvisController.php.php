<?php
require_once __DIR__ . "/../model/config.php";
require_once __DIR__ . "/../model/Avis.php";

class AvisController {

    public function ajouterAvis($avis) {
        $sql_check = "SELECT COUNT(*) FROM avis WHERE id_reclamation = :id_rec";
        $db = Config::getConnexion();
        $req_check = $db->prepare($sql_check);
        $req_check->execute(['id_rec' => $avis->getIdReclamation()]);
        
        if($req_check->fetchColumn() > 0) {
            return false;
        }
        
        $sql = "INSERT INTO avis (id_reclamation, note, satisfaction, commentaire, date_avis) 
                VALUES (:id_rec, :note, :satisfaction, :commentaire, NOW())";
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id_rec' => $avis->getIdReclamation(),
                'note' => $avis->getNote(),
                'satisfaction' => $avis->getSatisfaction(),
                'commentaire' => $avis->getCommentaire()
            ]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function getAllAvis() {
        $sql = "SELECT a.*, r.reference, r.objet, c.nom, c.prenom 
                FROM avis a 
                JOIN reclamation r ON a.id_reclamation = r.id_reclamation
                JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                ORDER BY a.date_avis DESC";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    public function getAvisById($id) {
        $sql = "SELECT a.*, r.reference, r.objet 
                FROM avis a 
                JOIN reclamation r ON a.id_reclamation = r.id_reclamation
                WHERE a.id_avis = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }

    public function getAvisByReclamation($id_reclamation) {
        $sql = "SELECT * FROM avis WHERE id_reclamation = :id_rec";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id_rec' => $id_reclamation]);
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }

    public function modifierAvis($id, $note, $satisfaction, $commentaire = null) {
        $sql = "UPDATE avis SET note = :note, satisfaction = :satisfaction, commentaire = :commentaire WHERE id_avis = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['note' => $note, 'satisfaction' => $satisfaction, 'commentaire' => $commentaire, 'id' => $id]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function supprimerAvis($id) {
        $sql = "DELETE FROM avis WHERE id_avis = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function getStatistiques() {
        $db = Config::getConnexion();
        $stats = [];
        
        $sqls = [
            'total' => "SELECT COUNT(*) as total FROM avis",
            'moyenne' => "SELECT AVG(note) as moyenne FROM avis"
        ];
        
        foreach ($sqls as $key => $sql) {
            $req = $db->prepare($sql);
            $req->execute();
            $result = $req->fetch();
            $stats[$key] = $result[$key] ?? 0;
        }
        
        return $stats;
    }
}
?>