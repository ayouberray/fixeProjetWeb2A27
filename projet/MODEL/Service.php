<?php
class Service {
    private ?int $id_service;
    private string $nom_service;
    private ?string $description;
    private int $duree_moyenne;
    private string $statut;
    
    // Setters
    public function setIdService($id) { $this->id_service = $id; }
    public function setNomService($nom) { $this->nom_service = $nom; }
    public function setDescription($desc) { $this->description = $desc; }
    public function setDureeMoyenne($duree) { $this->duree_moyenne = $duree; }
    public function setStatut($statut) { $this->statut = $statut; }
    
    // Getters
    public function getIdService() { return $this->id_service; }
    public function getNomService() { return $this->nom_service; }
    public function getDescription() { return $this->description; }
    public function getDureeMoyenne() { return $this->duree_moyenne; }
    public function getStatut() { return $this->statut; }
    
    // Constructeur
    public function __construct($nom_service, $description = null, $duree_moyenne = 30) {
        $this->nom_service = $nom_service;
        $this->description = $description;
        $this->duree_moyenne = $duree_moyenne;
        $this->statut = 'actif';
    }
}
?>