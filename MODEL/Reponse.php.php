<?php
class Reponse {
    private ?int $id_reponse;
    private int $id_reclamation;
    private string $nom_agent;
    private ?string $service_agent;
    private string $type_reponse;
    private string $contenu;
    private ?string $decision;
    private string $date_reponse;

    public function __construct($id_reclamation, $nom_agent, $contenu, $type_reponse = 'information', $service_agent = null, $decision = null) {
        $this->id_reclamation = $id_reclamation;
        $this->nom_agent = $nom_agent;
        $this->service_agent = $service_agent;
        $this->type_reponse = $type_reponse;
        $this->contenu = $contenu;
        $this->decision = $decision;
        $this->date_reponse = date('Y-m-d H:i:s');
    }

    public function getIdReponse() { return $this->id_reponse; }
    public function getIdReclamation() { return $this->id_reclamation; }
    public function getNomAgent() { return $this->nom_agent; }
    public function getServiceAgent() { return $this->service_agent; }
    public function getTypeReponse() { return $this->type_reponse; }
    public function getContenu() { return $this->contenu; }
    public function getDecision() { return $this->decision; }
    public function getDateReponse() { return $this->date_reponse; }
    
    public function setIdReponse($id) { $this->id_reponse = $id; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setTypeReponse($type) { $this->type_reponse = $type; }
    public function setDecision($decision) { $this->decision = $decision; }
}
?>