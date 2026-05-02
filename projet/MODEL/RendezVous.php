<?php

class RendezVous{
    private ?int $id_rdv;
    private string $citoyen_nom;
    private int $id_service;
    private ?string $agent_nom;
    private $date_heure;
    private string $statut;
    private ?string $motif;

    // ========== SETTERS ==========
    public function setIdRdv($id_rdv){
        $this->id_rdv = $id_rdv;
    }
    public function setCitoyenNom($citoyen_nom){
        $this->citoyen_nom = $citoyen_nom;
    }
    public function setIdService($id_service){
        $this->id_service = $id_service;
    }
    public function setAgentNom($agent_nom){
        $this->agent_nom = $agent_nom;
    }
    public function setDateHeure($date_heure){
        $this->date_heure = $date_heure;
    }
    public function setStatut($statut){
        $this->statut = $statut;
    }
    public function setMotif($motif){
        $this->motif = $motif;
    }

    // ========== GETTERS ==========
    public function getIdRdv(){
        return $this->id_rdv;
    }
    public function getCitoyenNom(){
        return $this->citoyen_nom;
    }
    public function getIdService(){
        return $this->id_service;
    }
    public function getAgentNom(){
        return $this->agent_nom;
    }
    public function getDateHeure(){
        return $this->date_heure;
    }
    public function getStatut(){
        return $this->statut;
    }
    public function getMotif(){
        return $this->motif;
    }

    // ========== CONSTRUCTEUR ==========
    public function __construct($citoyen_nom, $id_service, $date_heure, $motif){
        $this->citoyen_nom = $citoyen_nom;
        $this->id_service = $id_service;
        $this->date_heure = $date_heure;
        $this->motif = $motif;
        $this->statut = "en_attente";
        $this->agent_nom = null;
    }
}
?>