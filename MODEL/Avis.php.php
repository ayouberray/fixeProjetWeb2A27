<?php
class Avis {
    private ?int $id_avis;
    private int $id_reclamation;
    private int $note;
    private string $satisfaction;
    private ?string $commentaire;
    private string $date_avis;

    public function __construct($id_reclamation, $note, $satisfaction, $commentaire = null) {
        $this->id_reclamation = $id_reclamation;
        $this->note = $note;
        $this->satisfaction = $satisfaction;
        $this->commentaire = $commentaire;
        $this->date_avis = date('Y-m-d H:i:s');
    }

    public function getIdAvis() { return $this->id_avis; }
    public function getIdReclamation() { return $this->id_reclamation; }
    public function getNote() { return $this->note; }
    public function getSatisfaction() { return $this->satisfaction; }
    public function getCommentaire() { return $this->commentaire; }
    public function getDateAvis() { return $this->date_avis; }
    
    public function setIdAvis($id) { $this->id_avis = $id; }
    public function setNote($note) { $this->note = $note; }
    public function setSatisfaction($satisfaction) { $this->satisfaction = $satisfaction; }
    public function setCommentaire($commentaire) { $this->commentaire = $commentaire; }
}
?>