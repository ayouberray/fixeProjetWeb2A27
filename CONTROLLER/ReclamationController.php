<?php
require_once __DIR__."/../model/config.php";
require_once __DIR__."/../model/Reclamation.php";

class ReclamationController{

    // Générer référence unique
    public function genererReference() {
        return 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    // ========== MÉTHODES POUR CITOYEN (FRONTOFFICE) ==========

    function ajouterReclamation($reclamation){
        $sql = "INSERT INTO reclamation (reference, id_citoyen, id_service, categorie, objet, description, lieu, priorite, statut, piece_jointe, date_soumission) 
                VALUES (:reference, :id_citoyen, :id_service, :categorie, :objet, :description, :lieu, :priorite, 'soumise', :piece_jointe, NOW())";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                ':reference' => $reclamation->getReference(),
                ':id_citoyen' => $reclamation->getIdCitoyen(),
                ':id_service' => $reclamation->getIdService(),
                ':categorie' => $reclamation->getCategorie(),
                ':objet' => $reclamation->getObjet(),
                ':description' => $reclamation->getDescription(),
                ':lieu' => $reclamation->getLieu(),
                ':priorite' => $reclamation->getPriorite(),
                ':piece_jointe' => $reclamation->getPieceJointe()
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            error_log("Erreur ajout réclamation: " . $e->getMessage());
            return false;
        }
    }

    function getReclamationByCitoyen($id_citoyen){
        $sql = "SELECT r.*, s.nom_service 
                FROM reclamation r
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE r.id_citoyen = :id_citoyen 
                ORDER BY r.date_soumission DESC";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_citoyen' => $id_citoyen]);
            return $req->fetchAll();
        }
        catch(Exception $e){
            error_log("Erreur getReclamationByCitoyen: " . $e->getMessage());
            return [];
        }
    }

    // ========== MÉTHODES POUR ADMIN (BACKOFFICE) ==========

    function getAllReclamations(){
        $sql = "SELECT r.*, 
                        CONCAT(c.nom, ' ', c.prenom) as citoyen,
                        c.email as citoyen_email,
                        c.telephone as citoyen_telephone,
                        s.nom_service
                FROM reclamation r
                LEFT JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                LEFT JOIN services s ON r.id_service = s.id_service
                ORDER BY r.date_soumission DESC";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
        catch(Exception $e){
            error_log("Erreur getAllReclamations: " . $e->getMessage());
            return [];
        }
    }

    function getReclamationById($id_reclamation){
        $sql = "SELECT r.*, 
                        CONCAT(c.nom, ' ', c.prenom) as citoyen,
                        c.email as citoyen_email,
                        c.telephone as citoyen_telephone,
                        s.nom_service
                FROM reclamation r
                LEFT JOIN citoyens c ON r.id_citoyen = c.id_citoyen
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE r.id_reclamation = :id_reclamation";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_reclamation' => $id_reclamation]);
            return $req->fetch();
        }
        catch(Exception $e){
            error_log("Erreur getReclamationById: " . $e->getMessage());
            return null;
        }
    }

    function getAllCitoyens(){
        $sql = "SELECT id_citoyen, nom, prenom, email FROM citoyens ORDER BY nom";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
        catch(Exception $e){
            error_log("Erreur getAllCitoyens: " . $e->getMessage());
            return [];
        }
    }

    function adminAjouterReclamation($id_citoyen, $id_service, $categorie, $objet, $description, $priorite, $lieu){
        $reference = 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
        $sql = "INSERT INTO reclamation (reference, id_citoyen, id_service, categorie, objet, description, lieu, priorite, statut, date_soumission) 
                VALUES (:reference, :id_citoyen, :id_service, :categorie, :objet, :description, :lieu, :priorite, 'soumise', NOW())";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                ':reference' => $reference,
                ':id_citoyen' => $id_citoyen,
                ':id_service' => $id_service,
                ':categorie' => $categorie,
                ':objet' => $objet,
                ':description' => $description,
                ':lieu' => $lieu,
                ':priorite' => $priorite
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            error_log("Erreur adminAjouterReclamation: " . $e->getMessage());
            return false;
        }
    }

    function adminModifierReclamation($id_reclamation, $id_citoyen, $id_service, $categorie, $objet, $description, $priorite, $statut, $lieu){
        $sql = "UPDATE reclamation SET 
                id_citoyen = :id_citoyen,
                id_service = :id_service,
                categorie = :categorie,
                objet = :objet,
                description = :description,
                priorite = :priorite,
                statut = :statut,
                lieu = :lieu,
                date_modification = NOW()
                WHERE id_reclamation = :id_reclamation";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                ':id_reclamation' => $id_reclamation,
                ':id_citoyen' => $id_citoyen,
                ':id_service' => $id_service,
                ':categorie' => $categorie,
                ':objet' => $objet,
                ':description' => $description,
                ':priorite' => $priorite,
                ':statut' => $statut,
                ':lieu' => $lieu
            ]);
            return true;
        }
        catch(Exception $e){
            error_log("Erreur adminModifierReclamation: " . $e->getMessage());
            return false;
        }
    }

    function adminSupprimerReclamation($id_reclamation){
        $sql = "DELETE FROM reclamation WHERE id_reclamation = :id_reclamation";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([':id_reclamation' => $id_reclamation]);
            return true;
        }
        catch(Exception $e){
            error_log("Erreur adminSupprimerReclamation: " . $e->getMessage());
            return false;
        }
    }

    function getStatistiques(){
        $db = Config::getConnexion();
        
        $total = $db->query("SELECT COUNT(*) as total FROM reclamation")->fetch();
        $soumise = $db->query("SELECT COUNT(*) as total FROM reclamation WHERE statut='soumise'")->fetch();
        $en_cours = $db->query("SELECT COUNT(*) as total FROM reclamation WHERE statut='en_cours'")->fetch();
        $traitee = $db->query("SELECT COUNT(*) as total FROM reclamation WHERE statut='traitee'")->fetch();
        $rejetee = $db->query("SELECT COUNT(*) as total FROM reclamation WHERE statut='rejetee'")->fetch();
        
        return [
            'total' => $total['total'],
            'soumise' => $soumise['total'],
            'en_cours' => $en_cours['total'],
            'traitee' => $traitee['total'],
            'rejetee' => $rejetee['total']
        ];
    }
}
?>