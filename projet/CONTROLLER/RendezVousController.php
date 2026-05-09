<?php

require_once __DIR__."/../model/config.php";
require_once __DIR__."/../model/RendezVous.php";

class RendezVousController{

    // ========== AJOUTER RENDEZ-VOUS (citoyen) ==========
    function ajouterRendezVous($rendezvous){
        $sql = "INSERT INTO rendez_vous (citoyen_nom, id_service, date_heure, statut, motif) 
                VALUES (:citoyen_nom, :id_service, :date_heure, :statut, :motif)";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'citoyen_nom' => $rendezvous->getCitoyenNom(),
                'id_service'  => $rendezvous->getIdService(),
                'date_heure'  => $rendezvous->getDateHeure(),
                'statut'      => $rendezvous->getStatut(),
                'motif'       => $rendezvous->getMotif()
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== RÉCUPÉRER RDV PAR CITOYEN (avec JOIN, RECHERCHE, TRI, FILTRE) ==========
    function getRendezVousByCitoyen($citoyen_nom, $search = '', $sort = 'date_desc', $filter_statut = ''){
        $sql = "SELECT r.*, s.nom_service AS service_nom
                FROM rendez_vous r
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE r.citoyen_nom = :citoyen_nom";
        
        $params = ['citoyen_nom' => $citoyen_nom];

        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (r.id_rdv = :exact_id OR s.nom_service LIKE :search OR r.agent_nom LIKE :search)";
                $params['exact_id'] = $search;
                $params['search'] = "%$search%";
            } else {
                $sql .= " AND (s.nom_service LIKE :search OR r.agent_nom LIKE :search)";
                $params['search'] = "%$search%";
            }
        }

        if (!empty($filter_statut)) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $filter_statut;
        }

        if ($sort === 'date_asc') {
            $sql .= " ORDER BY r.date_heure ASC";
        } elseif ($sort === 'service_asc') {
            $sql .= " ORDER BY s.nom_service ASC";
        } elseif ($sort === 'service_desc') {
            $sql .= " ORDER BY s.nom_service DESC";
        } else {
            $sql .= " ORDER BY r.date_heure DESC";
        }

        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return [];
        }
    }

    // ========== RÉCUPÉRER TOUS LES RDV (avec JOIN, RECHERCHE, TRI, FILTRE) ==========
    function getAllRendezVous($search = '', $sort = 'date_desc', $filter_statut = ''){
        $sql = "SELECT r.*, s.nom_service AS service_nom
                FROM rendez_vous r
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE 1=1";
        
        $params = [];

        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (r.id_rdv = :exact_id OR s.nom_service LIKE :search OR r.agent_nom LIKE :search OR r.citoyen_nom LIKE :search)";
                $params['exact_id'] = $search;
                $params['search'] = "%$search%";
            } else {
                $sql .= " AND (s.nom_service LIKE :search OR r.agent_nom LIKE :search OR r.citoyen_nom LIKE :search)";
                $params['search'] = "%$search%";
            }
        }

        if (!empty($filter_statut)) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $filter_statut;
        }

        if ($sort === 'date_asc') {
            $sql .= " ORDER BY r.date_heure ASC";
        } elseif ($sort === 'service_asc') {
            $sql .= " ORDER BY s.nom_service ASC";
        } elseif ($sort === 'service_desc') {
            $sql .= " ORDER BY s.nom_service DESC";
        } else {
            $sql .= " ORDER BY r.date_heure DESC";
        }

        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return [];
        }
    }

    // ========== MODIFIER RENDEZ-VOUS (date et motif seulement) ==========
    function modifierRendezVous($id_rdv, $date_heure, $motif){
        $sql = "UPDATE rendez_vous SET date_heure = :date_heure, motif = :motif, date_modification = NOW() 
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv'     => $id_rdv,
                'date_heure' => $date_heure,
                'motif'      => $motif
            ]);
            return true;
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== MODIFIER TOUS LES CHAMPS D'UN RENDEZ-VOUS (version complète) ==========
    function modifierRendezVousComplet($id_rdv, $id_service, $date_heure, $motif){
        $sql = "UPDATE rendez_vous SET 
                id_service = :id_service,
                date_heure = :date_heure, 
                motif = :motif,
                date_modification = NOW()
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv'     => $id_rdv,
                'id_service' => $id_service,
                'date_heure' => $date_heure,
                'motif'      => $motif
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

    // ========== SUPPRIMER RENDEZ-VOUS ==========
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
                'id_rdv'    => $id_rdv,
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
    function adminAjouterRendezVous($citoyen_nom, $id_service, $date_heure, $motif){
        $sql = "INSERT INTO rendez_vous (citoyen_nom, id_service, date_heure, statut, motif) 
                VALUES (:citoyen_nom, :id_service, :date_heure, 'en_attente', :motif)";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'citoyen_nom' => $citoyen_nom,
                'id_service'  => $id_service,
                'date_heure'  => $date_heure,
                'motif'       => $motif
            ]);
            return $db->lastInsertId();
        }
        catch(Exception $e){
            echo "erreur: " . $e->getMessage();
            return false;
        }
    }

    // ========== ADMIN MODIFIER RENDEZ-VOUS ==========
    function adminModifierRendezVous($id_rdv, $citoyen_nom, $id_service, $date_heure, $statut, $motif){
        $sql = "UPDATE rendez_vous SET 
                citoyen_nom = :citoyen_nom,
                id_service  = :id_service,
                date_heure  = :date_heure,
                statut      = :statut,
                motif       = :motif,
                date_modification = NOW()
                WHERE id_rdv = :id_rdv";
        $db = Config::getConnexion();

        try{
            $req = $db->prepare($sql);
            $req->execute([
                'id_rdv'      => $id_rdv,
                'citoyen_nom' => $citoyen_nom,
                'id_service'  => $id_service,
                'date_heure'  => $date_heure,
                'statut'      => $statut,
                'motif'       => $motif
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
        return $this->supprimerRendezVous($id_rdv);
    }

    // ========== RÉCUPÉRER UN RDV PAR ID (avec JOIN) ==========
    function getRendezVousById($id_rdv){
        $sql = "SELECT r.*, s.nom_service AS service_nom
                FROM rendez_vous r
                LEFT JOIN services s ON r.id_service = s.id_service
                WHERE r.id_rdv = :id_rdv";
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
