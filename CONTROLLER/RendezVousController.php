<?php

require_once __DIR__."/../model/config.php";
require_once __DIR__."/../model/RendezVous.php";

class RendezVousController{

// ========== MÉTHODES POUR CITOYEN (FRONTOFFICE) ==========

function ajouterRendezVous($rendezvous){
    $sql="INSERT INTO rendez_vous (id_citoyen, id_service, date_heure, statut, motif) 
          VALUES (:id_citoyen, :id_service, :date_heure, :statut, :motif)";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute([
            'id_citoyen'=>$rendezvous->getIdCitoyen(),
            'id_service'=>$rendezvous->getIdService(),
            'date_heure'=>$rendezvous->getDateHeure(),
            'statut'=>$rendezvous->getStatut(),
            'motif'=>$rendezvous->getMotif()
        ]);
        return $db->lastInsertId();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

function getRendezVousByCitoyen($id_citoyen){
    $sql="SELECT r.*, s.nom_service 
          FROM rendez_vous r
          JOIN services s ON r.id_service = s.id_service
          WHERE r.id_citoyen = :id_citoyen 
          ORDER BY r.date_heure DESC";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute(['id_citoyen'=>$id_citoyen]);
        return $req->fetchAll();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

function getAllRendezVous(){
    $sql="SELECT r.*, 
                 CONCAT(c.nom, ' ', c.prenom) as citoyen,
                 s.nom_service
          FROM rendez_vous r
          JOIN users c ON r.id_citoyen = c.id
          JOIN services s ON r.id_service = s.id_service
          ORDER BY r.date_heure DESC";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

function modifierRendezVous($id_rdv, $date_heure, $motif){
    $sql="UPDATE rendez_vous SET date_heure=:date_heure, motif=:motif WHERE id_rdv=:id_rdv";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute([
            'id_rdv'=>$id_rdv,
            'date_heure'=>$date_heure,
            'motif'=>$motif
        ]);
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

function annulerRendezVous($id_rdv){
    $sql="UPDATE rendez_vous SET statut='annule' WHERE id_rdv=:id_rdv";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute(['id_rdv'=>$id_rdv]);
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

function affecterAgent($id_rdv, $id_agent){
    $sql="UPDATE rendez_vous SET id_agent=:id_agent WHERE id_rdv=:id_rdv";
    $db=Config::getConnexion();

    try{
        $req=$db->prepare($sql);
        $req->execute([
            'id_rdv'=>$id_rdv,
            'id_agent'=>$id_agent
        ]);
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
    }
}

// ========== MÉTHODES POUR ADMIN (BACKOFFICE) ==========

function getAllCitoyens(){
    $sql = "SELECT id, nom, prenom, email FROM users WHERE role = 'citoyen' ORDER BY nom";
    $db = Config::getConnexion();

    try{
        $req = $db->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
        return [];
    }
}

function getRendezVousById($id_rdv){
    $sql = "SELECT r.*, 
                   CONCAT(c.nom, ' ', c.prenom) as citoyen_nom,
                   c.id as citoyen_id
            FROM rendez_vous r
            JOIN users c ON r.id_citoyen = c.id
            WHERE r.id_rdv = :id_rdv";
    $db = Config::getConnexion();

    try{
        $req = $db->prepare($sql);
        $req->execute(['id_rdv' => $id_rdv]);
        return $req->fetch();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
        return null;
    }
}

function adminAjouterRendezVous($id_citoyen, $id_service, $date_heure, $motif){
    $sql = "INSERT INTO rendez_vous (id_citoyen, id_service, date_heure, statut, motif) 
            VALUES (:id_citoyen, :id_service, :date_heure, 'en_attente', :motif)";
    $db = Config::getConnexion();

    try{
        $req = $db->prepare($sql);
        $req->execute([
            'id_citoyen' => $id_citoyen,
            'id_service' => $id_service,
            'date_heure' => $date_heure,
            'motif' => $motif
        ]);
        return $db->lastInsertId();
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
        return false;
    }
}

function adminModifierRendezVous($id_rdv, $id_citoyen, $id_service, $date_heure, $statut, $motif){
    $sql = "UPDATE rendez_vous SET 
            id_citoyen = :id_citoyen,
            id_service = :id_service,
            date_heure = :date_heure,
            statut = :statut,
            motif = :motif
            WHERE id_rdv = :id_rdv";
    $db = Config::getConnexion();

    try{
        $req = $db->prepare($sql);
        $req->execute([
            'id_rdv' => $id_rdv,
            'id_citoyen' => $id_citoyen,
            'id_service' => $id_service,
            'date_heure' => $date_heure,
            'statut' => $statut,
            'motif' => $motif
        ]);
        return true;
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
        return false;
    }
}

function adminSupprimerRendezVous($id_rdv){
    $sql = "DELETE FROM rendez_vous WHERE id_rdv = :id_rdv";
    $db = Config::getConnexion();

    try{
        $req = $db->prepare($sql);
        $req->execute(['id_rdv' => $id_rdv]);
        return true;
    }
    catch(Exception $e){
        echo "erreur".$e->getMessage();
        return false;
    }
}

}
?>