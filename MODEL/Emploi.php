<?php
class Emploi {
    private ?int $id_emploi;
    private string $nom_emploi;
    private string $description;
    private string $salaire;
    
    public function __construct($nom_emploi, $description, $salaire) {
        $this->nom_emploi = $nom_emploi;
        $this->description = $description;
        $this->salaire = $salaire;
    }
    
    public function getIdEmploi() { return $this->id_emploi; }
    public function getNomEmploi() { return $this->nom_emploi; }
    public function getDescription() { return $this->description; }
    public function getSalaire() { return $this->salaire; }
}
?>
