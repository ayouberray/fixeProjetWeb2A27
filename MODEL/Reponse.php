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

    public function __construct($id_reclamation, $nom_agent, $type_reponse, $contenu, $service_agent = null, $decision = null) {
        $this->id_reclamation = $id_reclamation;
        $this->nom_agent = $nom_agent;
        $this->type_reponse = $type_reponse;
        $this->contenu = $contenu;
        $this->service_agent = $service_agent;
        $this->decision = $decision;
        $this->date_reponse = date('Y-m-d H:i:s');
    }

    // Getters
    public function getIdReponse() { return $this->id_reponse; }
    public function getIdReclamation() { return $this->id_reclamation; }
    public function getNomAgent() { return $this->nom_agent; }
    public function getServiceAgent() { return $this->service_agent; }
    public function getTypeReponse() { return $this->type_reponse; }
    public function getContenu() { return $this->contenu; }
    public function getDecision() { return $this->decision; }
    public function getDateReponse() { return $this->date_reponse; }

    // Setters
    public function setIdReponse($id_reponse) { $this->id_reponse = $id_reponse; }
    public function setIdReclamation($id_reclamation) { $this->id_reclamation = $id_reclamation; }
    public function setNomAgent($nom_agent) { $this->nom_agent = $nom_agent; }
    public function setServiceAgent($service_agent) { $this->service_agent = $service_agent; }
    public function setTypeReponse($type_reponse) { $this->type_reponse = $type_reponse; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setDecision($decision) { $this->decision = $decision; }
    public function setDateReponse($date_reponse) { $this->date_reponse = $date_reponse; }
}
?>
