<?php

class RendezVous{
    private ?int $id_rdv;
    private int $id_citoyen;
    private int $id_service;
    private ?int $id_agent;
    private $date_heure;
    private string $statut;
    private ?string $motif;

    // Setters
    public function setIdRdv($id_rdv){
        $this->id_rdv = $id_rdv;
    }
    public function setIdCitoyen($id_citoyen){
        $this->id_citoyen = $id_citoyen;
    }
    public function setIdService($id_service){
        $this->id_service = $id_service;
    }
    public function setIdAgent($id_agent){
        $this->id_agent = $id_agent;
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

    // Getters
    public function getIdRdv(){
        return $this->id_rdv;
    }
    public function getIdCitoyen(){
        return $this->id_citoyen;
    }
    public function getIdService(){
        return $this->id_service;
    }
    public function getIdAgent(){
        return $this->id_agent;
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

    // Constructeur
    public function __construct($id_citoyen, $id_service, $date_heure, $motif){
        $this->id_citoyen = $id_citoyen;
        $this->id_service = $id_service;
        $this->date_heure = $date_heure;
        $this->motif = $motif;
        $this->statut = "en_attente";
        $this->id_agent = null;
    }
}

?>