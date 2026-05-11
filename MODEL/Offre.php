<?php
// MODEL/Offre.php
require_once __DIR__ . "/config.php";

class Offre {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    // Récupérer toutes les offres
    public function getAll() {
        $sql = "SELECT * FROM offre ORDER BY id_offre DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Récupérer une offre par son ID
    public function getById($id) {
        $sql = "SELECT * FROM offre WHERE id_offre = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Ajouter une offre
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

    // Modifier une offre
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

    // Supprimer une offre
    public function delete($id) {
        $sql = "DELETE FROM offre WHERE id_offre = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}