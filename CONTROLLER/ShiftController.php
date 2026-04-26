<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../MODEL/config.php";
class ShiftController {
    private function validerShiftInput($nom_shift, $heure_debut, $heure_fin) {
        $nom_shift = trim((string) $nom_shift);
        $heure_debut = trim((string) $heure_debut);
        $heure_fin = trim((string) $heure_fin);

        if ($nom_shift === '' || $heure_debut === '' || $heure_fin === '') {
            return false;
        }

        $timePattern = '/^([01]\d|2[0-3]):[0-5]\d$/';
        if (!preg_match($timePattern, $heure_debut) || !preg_match($timePattern, $heure_fin)) {
            return false;
        }

        return $heure_debut < $heure_fin;
    }
    
    function getAllShifts() {
        $sql = "SELECT * FROM shifts ORDER BY heure_debut";
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
    
    function getShiftById($id_shift) {
        $sql = "SELECT * FROM shifts WHERE id_shift = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_shift]);
            return $req->fetch();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return null;
        }
    }
    
    function ajouterShift($nom_shift, $heure_debut, $heure_fin) {
        if (!$this->validerShiftInput($nom_shift, $heure_debut, $heure_fin)) {
            return false;
        }

        $sql = "INSERT INTO shifts (nom_shift, heure_debut, heure_fin) VALUES (:nom, :debut, :fin)";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $nom_shift,
                'debut' => $heure_debut,
                'fin' => $heure_fin
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function modifierShift($id_shift, $nom_shift, $heure_debut, $heure_fin) {
        if (!filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            return false;
        }

        if (!$this->validerShiftInput($nom_shift, $heure_debut, $heure_fin)) {
            return false;
        }

        $sql = "UPDATE shifts SET nom_shift = :nom, heure_debut = :debut, heure_fin = :fin WHERE id_shift = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id_shift,
                'nom' => $nom_shift,
                'debut' => $heure_debut,
                'fin' => $heure_fin
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function supprimerShift($id_shift) {
        $sql = "DELETE FROM shifts WHERE id_shift = :id";
        $db = Config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_shift]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
}
?>
