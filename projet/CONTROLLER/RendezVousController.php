<?php

require_once __DIR__."/../model/config.php";
require_once __DIR__."/../model/RendezVous.php";

class RendezVousController{

    // ========== AJOUTER RENDEZ-VOUS (citoyen) ==========
    function ajouterRendezVous($rendezvous){
        $sql = "INSERT INTO rendez_vous (citoyen_nom, service_nom, date_heure, statut, motif) 
                VALUES (:citoyen_nom, :service_nom, :date_heure, :statut, :motif)";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'citoyen_nom' => $rendezvous->getCitoyenNom(),
                'service_nom' => $rendezvous->getServiceNom(),
                'date_heure' => $rendezvous->getDateHeure(),
                'statut' => $rendezvous->getStatut(),
                'motif' => $rendezvous->getMotif()
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== RÉCUPÉRER RDV PAR CITOYEN ==========
    function getRendezVousByCitoyen($citoyen_nom){
        $sql = "SELECT * FROM rendez_vous 
                WHERE citoyen_nom = :citoyen_nom 
                ORDER BY date_heure DESC";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['citoyen_nom' => $citoyen_nom]);
            return $req->fetchAll();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return [];
        }
    }

    // ========== RÉCUPÉRER TOUS LES RDV ==========
    function getAllRendezVous(){
        $sql = "SELECT * FROM rendez_vous ORDER BY date_heure DESC";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return [];
        }
    }

    // ========== MODIFIER RENDEZ-VOUS (date et motif seulement - version simple) ==========
    function modifierRendezVous($id_rdv, $date_heure, $motif){
        $sql = "UPDATE rendez_vous SET date_heure = :date_heure, motif = :motif, date_modification = NOW() 
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv' => $id_rdv,
                'date_heure' => $date_heure,
                'motif' => $motif
            ]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== MODIFIER TOUS LES CHAMPS D'UN RENDEZ-VOUS (version complète) ==========
    function modifierRendezVousComplet($id_rdv, $service_nom, $date_heure, $motif){
        $sql = "UPDATE rendez_vous SET 
                service_nom = :service_nom,
                date_heure = :date_heure, 
                motif = :motif,
                date_modification = NOW()
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv' => $id_rdv,
                'service_nom' => $service_nom,
                'date_heure' => $date_heure,
                'motif' => $motif
            ]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== ANNULER RENDEZ-VOUS (changer statut) ==========
    function annulerRendezVous($id_rdv){
        $sql = "UPDATE rendez_vous SET statut = 'annule' WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_rdv' => $id_rdv]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== SUPPRIMER RENDEZ-VOUS (pour citoyen - suppression définitive) ==========
    function supprimerRendezVous($id_rdv){
        $sql = "DELETE FROM rendez_vous WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_rdv' => $id_rdv]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== AFFECTER AGENT ==========
    function affecterAgent($id_rdv, $agent_nom){
        $sql = "UPDATE rendez_vous SET agent_nom = :agent_nom WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv' => $id_rdv,
                'agent_nom' => $agent_nom
            ]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== ADMIN AJOUTER RENDEZ-VOUS ==========
    function adminAjouterRendezVous($citoyen_nom, $service_nom, $date_heure, $motif){
        $sql = "INSERT INTO rendez_vous (citoyen_nom, service_nom, date_heure, statut, motif) 
                VALUES (:citoyen_nom, :service_nom, :date_heure, 'en_attente', :motif)";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'citoyen_nom' => $citoyen_nom,
                'service_nom' => $service_nom,
                'date_heure' => $date_heure,
                'motif' => $motif
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== ADMIN MODIFIER RENDEZ-VOUS ==========
    function adminModifierRendezVous($id_rdv, $citoyen_nom, $service_nom, $date_heure, $statut, $motif){
        $sql = "UPDATE rendez_vous SET 
                citoyen_nom = :citoyen_nom,
                service_nom = :service_nom,
                date_heure = :date_heure,
                statut = :statut,
                motif = :motif,
                date_modification = NOW()
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv' => $id_rdv,
                'citoyen_nom' => $citoyen_nom,
                'service_nom' => $service_nom,
                'date_heure' => $date_heure,
                'statut' => $statut,
                'motif' => $motif
            ]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== ADMIN SUPPRIMER RENDEZ-VOUS ==========
    function adminSupprimerRendezVous($id_rdv){
        $sql = "DELETE FROM rendez_vous WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_rdv' => $id_rdv]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== RÉCUPÉRER UN RDV PAR ID ==========
    function getRendezVousById($id_rdv){
        $sql = "SELECT * FROM rendez_vous WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute(['id_rdv' => $id_rdv]);
            return $req->fetch();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return null;
        }
    }
}
?>