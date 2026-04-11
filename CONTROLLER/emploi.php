<?php

class Emploi{
    private ?int $id;
    private string $titre;
    private string $description;
    private float $salaire;

    public function __construct($id = null, $titre = '', $description = '', $salaire = 0.0){
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->salaire = $salaire;
    }

    // Getters
    public function getId(){
        return $this->id;
    }

    public function getTitre(){
        return $this->titre;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getSalaire(){
        return $this->salaire;
    }

    // Setters
    public function setId($id){
        $this->id = $id;
    }

    public function setTitre($titre){
        $this->titre = $titre;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function setSalaire($salaire){
        $this->salaire = $salaire;
    }
}