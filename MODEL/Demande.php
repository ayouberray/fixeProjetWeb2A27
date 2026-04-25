<?php


class Demande {
    
    private ?int $id_demande;
    private ?int $id_citoyen;
    private ?int $id_service;
    private string $titre;
    private string $description;
    private string $type_demande;
    private string $statut;
    private ?string $date_creation;
    private ?string $date_modification;
    
    
    public function __construct(
        $id_demande = null,
        $titre = "",
        $description = "",
        $type_demande = "",
        $statut = "en_attente",
        $date_creation = null,
        $id_citoyen = null,
        $id_service = null
    ) {
        $this->id_demande = $id_demande;
        $this->titre = $titre;
        $this->description = $description;
        $this->type_demande = $type_demande;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
        $this->id_citoyen = $id_citoyen;
        $this->id_service = $id_service;
        $this->date_modification = null;
    }
    
    
    public function setIdDemande($id) {
        $this->id_demande = $id;
    }
    
    public function setIdCitoyen($id) {
        $this->id_citoyen = $id;
    }
    
    public function setIdService($id) {
        $this->id_service = $id;
    }
    
    public function setTitre($titre) {
        $this->titre = $titre;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function setTypeDemande($type) {
        $this->type_demande = $type;
    }
    
    public function setStatut($statut) {
        $this->statut = $statut;
    }
    
    public function setDateCreation($date) {
        $this->date_creation = $date;
    }
    
    public function setDateModification($date) {
        $this->date_modification = $date;
    }
    
    
    public function getIdDemande() {
        return $this->id_demande;
    }
    
    public function getIdCitoyen() {
        return $this->id_citoyen;
    }
    
    public function getIdService() {
        return $this->id_service;
    }
    
    public function getTitre() {
        return $this->titre;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getTypeDemande() {
        return $this->type_demande;
    }
    
    public function getStatut() {
        return $this->statut;
    }
    
    public function getDateCreation() {
        return $this->date_creation;
    }
    
    public function getDateModification() {
        return $this->date_modification;
    }
    
 
    public function afficherInfo() {
        echo "ID Demande : " . $this->id_demande . "<br>";
        echo "Titre : " . $this->titre . "<br>";
        echo "Description : " . $this->description . "<br>";
        echo "Type : " . $this->type_demande . "<br>";
        echo "Statut : " . $this->statut . "<br>";
        echo "ID Citoyen : " . $this->id_citoyen . "<br>";
        echo "ID Service : " . $this->id_service . "<br>";
        echo "Date création : " . $this->date_creation . "<br>";
    }
    
    
    public function toArray() {
        return [
            'id_demande' => $this->id_demande,
            'id_citoyen' => $this->id_citoyen,
            'id_service' => $this->id_service,
            'titre' => $this->titre,
            'description' => $this->description,
            'type_demande' => $this->type_demande,
            'statut' => $this->statut,
            'date_creation' => $this->date_creation,
            'date_modification' => $this->date_modification
        ];
    }
    
    public function getStatutAffiche() {
        $statuts = [
            'en_attente' => '⏳ En attente',
            'en_cours' => '🔄 En cours',
            'traite' => '✅ Traité',
            'refuse' => '❌ Refusé'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }
    
    public function getTypeAffiche() {
        $types = [
            'urbanisme' => '🏗️ Urbanisme',
            'voirie' => '🛣️ Voirie',
            'etat_civil' => '📜 État Civil',
            'culture' => '🎭 Culture',
            'social' => '🤝 Social',
            'autre' => '📌 Autre'
        ];
        return $types[$this->type_demande] ?? $this->type_demande;
    }
    
    public function peutEtreModifiee() {
        return in_array($this->statut, ['en_attente', 'en_cours']);
    }
    
    public function peutEtreSupprimee() {
        return true;
    }
}
?>