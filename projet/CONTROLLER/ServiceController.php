<?php
require_once __DIR__."/../model/config.php";
require_once __DIR__."/../model/Service.php";

class ServiceController {
    
    // ========== LISTER TOUS LES SERVICES ==========
    public function getAllServices() {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM services ORDER BY nom_service";
        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return [];
        }
    }
    
    // ========== AJOUTER UN SERVICE ==========
    public function ajouterService($service) {
        $db = Config::getConnexion();
        $sql = "INSERT INTO services (nom_service, description, duree_moyenne, statut) 
                VALUES (:nom, :description, :duree, 'actif')";
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $service->getNomService(),
                'description' => $service->getDescription(),
                'duree' => $service->getDureeMoyenne()
            ]);
            return $db->lastInsertId();
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }
    
    // ========== MODIFIER UN SERVICE ==========
    public function modifierService($id_service, $nom_service, $description, $duree_moyenne, $statut) {
        $db = Config::getConnexion();
        $sql = "UPDATE services SET 
                nom_service = :nom, 
                description = :description, 
                duree_moyenne = :duree,
                statut = :statut
                WHERE id_service = :id";
        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id_service,
                'nom' => $nom_service,
                'description' => $description,
                'duree' => $duree_moyenne,
                'statut' => $statut
            ]);
            return true;
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }
    
    // ========== SUPPRIMER UN SERVICE ==========
    public function supprimerService($id_service) {
        $db = Config::getConnexion();
        // Vérifier si des rendez-vous utilisent ce service
        $check = $db->prepare("SELECT COUNT(*) as total FROM rendez_vous WHERE id_service = :id");
        $check->execute(['id' => $id_service]);
        $result = $check->fetch();
        
        if($result['total'] > 0) {
            return false; // Ne pas supprimer si des RDV existent
        }
        
        $sql = "DELETE FROM services WHERE id_service = :id";
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_service]);
            return true;
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }
    
    // ========== RÉCUPÉRER UN SERVICE PAR ID ==========
    public function getServiceById($id_service) {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM services WHERE id_service = :id";
        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_service]);
            return $req->fetch();
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage();
            return null;
        }
    }
}
?>
