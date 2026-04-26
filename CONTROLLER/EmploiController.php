<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../MODEL/config.php";

class EmploiController {
    
    function getAllEmplois() {
        $sql = "SELECT *
                FROM vue_emplois_shifts
                ORDER BY date_travail DESC";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return [];
        }
    }
    
    function getEmploiById($id_emploi) {
        $sql = "SELECT *
                FROM vue_emplois_shifts
                WHERE id_emploi = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_emploi]);
            return $req->fetch();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return null;
        }
    }
    
    function ajouterEmploi($id_agent, $id_service, $id_shift, $date_travail) {
        $id_agent = filter_var($id_agent, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_service = filter_var($id_service, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_shift = filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $date_travail = trim((string) $date_travail);

        $dateValide = DateTime::createFromFormat('Y-m-d', $date_travail);
        $today = new DateTime('today');
        $isDateFormatOk = $dateValide && $dateValide->format('Y-m-d') === $date_travail;
        $isDateNotPast = $isDateFormatOk && $dateValide >= $today;

        if ($id_agent === false || $id_service === false || $id_shift === false || !$isDateNotPast) {
            return false;
        }

        $sql = "INSERT INTO emplois (id_agent, id_service, id_shift, date_travail, statut) 
                VALUES (:id_agent, :id_service, :id_shift, :date_travail, 'planifie')";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function modifierEmploi($id_emploi, $id_agent, $id_service, $id_shift, $date_travail, $statut) {
        $id_emploi = filter_var($id_emploi, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_agent = filter_var($id_agent, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_service = filter_var($id_service, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_shift = filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $date_travail = trim((string) $date_travail);
        $statut = trim((string) $statut);

        $statutsValides = ['planifie', 'termine', 'annule'];
        $dateValide = DateTime::createFromFormat('Y-m-d', $date_travail);
        $today = new DateTime('today');
        $isDateFormatOk = $dateValide && $dateValide->format('Y-m-d') === $date_travail;
        $isDateNotPast = $isDateFormatOk && $dateValide >= $today;

        if (
            $id_emploi === false ||
            $id_agent === false ||
            $id_service === false ||
            $id_shift === false ||
            !$isDateNotPast ||
            !in_array($statut, $statutsValides, true)
        ) {
            return false;
        }

        $sql = "UPDATE emplois SET 
                id_agent = :id_agent, 
                id_service = :id_service, 
                id_shift = :id_shift, 
                date_travail = :date_travail,
                statut = :statut
                WHERE id_emploi = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id_emploi,
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail,
                'statut' => $statut
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function supprimerEmploi($id_emploi) {
        $sql = "DELETE FROM emplois WHERE id_emploi = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_emploi]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function getUserByName($nom_complet) {
        $nom_complet = trim(preg_replace('/\s+/', ' ', $nom_complet));
        if(empty($nom_complet)) {
            return null;
        }

        $full = mb_strtolower($nom_complet, 'UTF-8');
        $db = Config::getConnexion();
        try {
            $sql = "SELECT * FROM users WHERE role = 'agent' AND (
                    LOWER(CONCAT(nom, ' ', prenom)) = :full OR
                    LOWER(CONCAT(prenom, ' ', nom)) = :full
                ) LIMIT 1";
            $req = $db->prepare($sql);
            $req->execute(['full' => $full]);
            $result = $req->fetch();
            if($result) {
                return $result;
            }

            if(strpos($nom_complet, ' ') === false) {
                $sql = "SELECT * FROM users WHERE role = 'agent' AND (LOWER(nom) = :value OR LOWER(prenom) = :value) LIMIT 1";
                $req = $db->prepare($sql);
                $req->execute(['value' => $full]);
                return $req->fetch();
            }

            return null;
        } catch(Exception $e) {
            return null;
        }
    }
    
    function ajouterEmploiByName($nom_agent, $id_service, $id_shift, $date_travail) {
        $agent = $this->getUserByName($nom_agent);
        if(!$agent) {
            return false; // Agent not found
        }
        
        return $this->ajouterEmploi($agent['id'], $id_service, $id_shift, $date_travail);
    }
    
    function getAgents() {
        $sql = "SELECT * FROM users WHERE role = 'agent' ORDER BY nom, prenom";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
    
    function getServices() {
        $sql = "SELECT * FROM services WHERE statut = 'actif' ORDER BY nom_service";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
    
    function getShifts() {
        $sql = "SELECT * FROM shifts ORDER BY heure_debut";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
}
?>
