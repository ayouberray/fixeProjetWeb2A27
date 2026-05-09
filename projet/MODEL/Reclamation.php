<?php
class Reclamation{
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

    // Setters
    public function setIdReclamation($id_reclamation){ $this->id_reclamation = $id_reclamation; }
    public function setReference($reference){ $this->reference = $reference; }
    public function setIdCitoyen($id_citoyen){ $this->id_citoyen = $id_citoyen; }
    public function setIdService($id_service){ $this->id_service = $id_service; }
    public function setCategorie($categorie){ $this->categorie = $categorie; }
    public function setObjet($objet){ $this->objet = $objet; }
    public function setDescription($description){ $this->description = $description; }
    public function setLieu($lieu){ $this->lieu = $lieu; }
    public function setPriorite($priorite){ $this->priorite = $priorite; }
    public function setStatut($statut){ $this->statut = $statut; }
    public function setPieceJointe($piece_jointe){ $this->piece_jointe = $piece_jointe; }
    public function setDateSoumission($date_soumission){ $this->date_soumission = $date_soumission; }
    public function setDateModification($date_modification){ $this->date_modification = $date_modification; }

    // Getters
    public function getIdReclamation(){ return $this->id_reclamation; }
    public function getReference(){ return $this->reference; }
    public function getIdCitoyen(){ return $this->id_citoyen; }
    public function getIdService(){ return $this->id_service; }
    public function getCategorie(){ return $this->categorie; }
    public function getObjet(){ return $this->objet; }
    public function getDescription(){ return $this->description; }
    public function getLieu(){ return $this->lieu; }
    public function getPriorite(){ return $this->priorite; }
    public function getStatut(){ return $this->statut; }
    public function getPieceJointe(){ return $this->piece_jointe; }
    public function getDateSoumission(){ return $this->date_soumission; }
    public function getDateModification(){ return $this->date_modification; }

    // Constructeur
    public function __construct($id_citoyen, $categorie, $objet, $description, $priorite = 'normale', $id_service = null, $lieu = null){
        $this->reference = 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
        $this->id_citoyen = $id_citoyen;
        $this->id_service = $id_service;
        $this->categorie = $categorie;
        $this->objet = $objet;
        $this->description = $description;
        $this->lieu = $lieu;
        $this->priorite = $priorite;
        $this->statut = 'soumise';
        $this->piece_jointe = null;
        $this->date_soumission = date('Y-m-d H:i:s');
        $this->date_modification = null;
    }
}
?>