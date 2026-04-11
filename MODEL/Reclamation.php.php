<?php
class Reclamation {
    private ?int $id_reclamation;
    private string $reference;
    private int $id_citoyen;
    private ?int $id_service;
    private string $categorie;
    private string $objet;
    private string $description;
    private ?string $lieu;
    private string $priorite;
    private string $statut;
    private ?string $piece_jointe;
    private string $date_soumission;
    private ?string $date_modification;

    public function __construct($reference, $id_citoyen, $categorie, $objet, $description, $priorite = 'normale', $id_service = null, $lieu = null, $piece_jointe = null) {
        $this->reference = $reference;
        $this->id_citoyen = $id_citoyen;
        $this->id_service = $id_service;
        $this->categorie = $categorie;
        $this->objet = $objet;
        $this->description = $description;
        $this->lieu = $lieu;
        $this->priorite = $priorite;
        $this->statut = 'soumise';
        $this->piece_jointe = $piece_jointe;
        $this->date_soumission = date('Y-m-d H:i:s');
    }

    // Getters
    public function getIdReclamation() { return $this->id_reclamation; }
    public function getReference() { return $this->reference; }
    public function getIdCitoyen() { return $this->id_citoyen; }
    public function getIdService() { return $this->id_service; }
    public function getCategorie() { return $this->categorie; }
    public function getObjet() { return $this->objet; }
    public function getDescription() { return $this->description; }
    public function getLieu() { return $this->lieu; }
    public function getPriorite() { return $this->priorite; }
    public function getStatut() { return $this->statut; }
    public function getPieceJointe() { return $this->piece_jointe; }
    public function getDateSoumission() { return $this->date_soumission; }
    public function getDateModification() { return $this->date_modification; }
    
    // Setters
    public function setIdReclamation($id) { $this->id_reclamation = $id; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateModification($date) { $this->date_modification = $date; }
    public function setObjet($objet) { $this->objet = $objet; }
    public function setDescription($description) { $this->description = $description; }
    public function setPriorite($priorite) { $this->priorite = $priorite; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setIdService($id_service) { $this->id_service = $id_service; }
}
?>