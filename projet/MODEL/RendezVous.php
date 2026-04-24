<?php

class RendezVous{
    private ?int $id_rdv;
    private string $citoyen_nom;
    private string $service_nom;
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
    public function setServiceNom($service_nom){
        $this->service_nom = $service_nom;
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
    public function getServiceNom(){
        return $this->service_nom;
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
    public function __construct($citoyen_nom, $service_nom, $date_heure, $motif){
        $this->citoyen_nom = $citoyen_nom;
        $this->service_nom = $service_nom;
        $this->date_heure = $date_heure;
        $this->motif = $motif;
        $this->statut = "en_attente";
        $this->agent_nom = null;
    }
}
?>