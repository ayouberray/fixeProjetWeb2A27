<?php
require_once __DIR__ . "/config.php";

class Candidature {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }
    public function getAll() {
        $sql = "SELECT c.*, o.titre as offre_titre 
                FROM condidature c 
                JOIN offre o ON c.id_offre = o.id_offre 
                ORDER BY c.date_cond DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    public function getByOffre($offre_id) {
        $sql = "SELECT * FROM condidature WHERE id_offre = :offre_id ORDER BY date_cond DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['offre_id' => $offre_id]);
        return $stmt->fetchAll();
    }
    public function create($offre_id, $nom, $prenom, $email, $num_tel, $cv_path) {
        $sql = "INSERT INTO condidature (id_offre, nom, prenom, email, num_tel, cv_path, statut)
                VALUES (:offre_id, :nom, :prenom, :email, :num_tel, :cv_path, 'en_attend')";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'offre_id' => $offre_id,
            'nom'      => $nom,
            'prenom'   => $prenom,
            'email'    => $email,
            'num_tel'  => $num_tel,
            'cv_path'  => $cv_path
        ]);
        return $ok ? (int)$this->db->lastInsertId() : false;
    }
    public function updateStatut($id, $statut) {
        $sql = "UPDATE condidature SET statut = :statut WHERE id_cond = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'statut' => $statut]);
    }
    public function getById($id) {
        $sql = "SELECT * FROM condidature WHERE id_cond = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function delete($id) {
        $sql = "DELETE FROM condidature WHERE id_cond = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}