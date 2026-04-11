<?php
require_once __DIR__ . "/../model/config.php";
require_once __DIR__ . "/../model/Reclamation.php";

class ReclamationController {

    public function genererReference() {
        return 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    // CREATE
    public function ajouterReclamation($reclamation) {
        $sql = "INSERT INTO reclamation (reference, id_citoyen, id_service, categorie, objet, description, lieu, priorite, statut, piece_jointe, date_soumission) 
                VALUES (:ref, :id_citoyen, :id_service, :categorie, :objet, :desc, :lieu, :priorite, 'soumise', :piece, NOW())";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'ref' => $reclamation->getReference(),
                'id_citoyen' => $reclamation->getIdCitoyen(),
                'id_service' => $reclamation->getIdService(),
                'categorie' => $reclamation->getCategorie(),
                'objet' => $reclamation->getObjet(),
                'desc' => $reclamation->getDescription(),
                'lieu' => $reclamation->getLieu(),
                'priorite' => $reclamation->getPriorite(),
                'piece' => $reclamation->getPieceJointe()
            ]);
            return $db->lastInsertId();
        } catch(Exception $e) {
            error_log("Erreur ajout réclamation: " . $e->getMessage());
            return false;
        }
    }

    // READ - All
    public function getAllReclamations($filtre_statut = null, $filtre_categorie = null) {
        $sql = "SELECT r.*, c.nom, c.prenom, c.email, s.nom_service 
                FROM reclamation r 
                LEFT JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE 1=1";
        $params = [];
        
        if ($filtre_statut) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $filtre_statut;
        }
        if ($filtre_categorie) {
            $sql .= " AND r.categorie = :categorie";
            $params['categorie'] = $filtre_categorie;
        }
        
        $sql .= " ORDER BY r.date_soumission DESC";
        
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    // READ - By ID
    public function getReclamationById($id) {
        $sql = "SELECT r.*, c.nom, c.prenom, c.email, c.telephone, s.nom_service 
                FROM reclamation r 
                LEFT JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE r.id_reclamation = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch(Exception $e) {
            return null;
        }
    }

    // READ - By citizen
    public function getReclamationsByCitoyen($id_citoyen) {
        $sql = "SELECT r.*, s.nom_service 
                FROM reclamation r 
                LEFT JOIN services s ON r.id_service = s.id_service 
                WHERE r.id_citoyen = :id_citoyen 
                ORDER BY r.date_soumission DESC";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id_citoyen' => $id_citoyen]);
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    // UPDATE
    public function modifierReclamation($id, $objet, $description, $priorite, $categorie, $lieu = null, $id_service = null) {
        $sql = "UPDATE reclamation SET 
                objet = :objet, 
                description = :description, 
                priorite = :priorite, 
                categorie = :categorie,
                lieu = :lieu,
                id_service = :id_service,
                date_modification = NOW() 
                WHERE id_reclamation = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'objet' => $objet,
                'description' => $description,
                'priorite' => $priorite,
                'categorie' => $categorie,
                'lieu' => $lieu,
                'id_service' => $id_service,
                'id' => $id
            ]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    // UPDATE status
    public function modifierStatut($id, $nouveau_statut) {
        $sql = "UPDATE reclamation SET statut = :statut, date_modification = NOW() WHERE id_reclamation = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['statut' => $nouveau_statut, 'id' => $id]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    // DELETE
    public function supprimerReclamation($id) {
        $db = Config::getConnexion();
        try {
            $db->beginTransaction();
            $sql_reponse = "DELETE FROM reponse WHERE id_reclamation = :id";
            $req_reponse = $db->prepare($sql_reponse);
            $req_reponse->execute(['id' => $id]);
            
            $sql_avis = "DELETE FROM avis WHERE id_reclamation = :id";
            $req_avis = $db->prepare($sql_avis);
            $req_avis->execute(['id' => $id]);
            
            $sql_rec = "DELETE FROM reclamation WHERE id_reclamation = :id";
            $req_rec = $db->prepare($sql_rec);
            $req_rec->execute(['id' => $id]);
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Statistics
    public function getStatistiques() {
        $db = Config::getConnexion();
        $stats = [];
        
        $sqls = [
            'total' => "SELECT COUNT(*) as total FROM reclamation",
            'soumise' => "SELECT COUNT(*) as soumise FROM reclamation WHERE statut = 'soumise'",
            'en_cours' => "SELECT COUNT(*) as en_cours FROM reclamation WHERE statut = 'en_cours'",
            'traitee' => "SELECT COUNT(*) as traitee FROM reclamation WHERE statut = 'traitee'",
            'rejetee' => "SELECT COUNT(*) as rejetee FROM reclamation WHERE statut = 'rejetee'",
            'cloturee' => "SELECT COUNT(*) as cloturee FROM reclamation WHERE statut = 'cloturee'",
            'haute_priorite' => "SELECT COUNT(*) as haute FROM reclamation WHERE priorite IN ('haute', 'urgente')"
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