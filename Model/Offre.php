<?php
require_once __DIR__ . "/config.php";

class Offre {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }
    public function getAll($search = '', $sort = 'id_offre DESC') {
        $allowed_sorts = ['titre ASC', 'titre DESC', 'date_limite ASC', 'date_limite DESC', 'id_offre DESC'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'id_offre DESC';
        }

        if (!empty($search)) {
            $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM condidature c WHERE c.id_offre = o.id_offre) as nb_candidats
                FROM offre o WHERE o.titre LIKE :search OR o.entite LIKE :search ORDER BY $sort";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['search' => "%$search%"]);
            return $stmt->fetchAll();
        } else {
            $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM condidature c WHERE c.id_offre = o.id_offre) as nb_candidats
                FROM offre o ORDER BY $sort";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        }
    }
    public function getById($id) {
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM condidature c WHERE c.id_offre = o.id_offre) as nb_candidats
                FROM offre o WHERE o.id_offre = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function create($titre, $description, $entite, $date_limite, $nombre_postes, $statut = 'Ouvert') {
        $sql = "INSERT INTO offre (titre, description, entite, date_limite, nombre_postes, statut)
                VALUES (:titre, :description, :entite, :date_limite, :nombre_postes, :statut)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'entite' => $entite,
            'date_limite' => $date_limite,
            'nombre_postes' => $nombre_postes,
            'statut' => $statut
        ]);
    }

    public function update($id, $titre, $description, $entite, $date_limite, $nombre_postes, $statut) {
        $sql = "UPDATE offre SET titre = :titre, description = :description, entite = :entite,
                date_limite = :date_limite, nombre_postes = :nombre_postes, statut = :statut
                WHERE id_offre = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'titre' => $titre,
            'description' => $description,
            'entite' => $entite,
            'date_limite' => $date_limite,
            'nombre_postes' => $nombre_postes,
            'statut' => $statut
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM offre WHERE id_offre = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}