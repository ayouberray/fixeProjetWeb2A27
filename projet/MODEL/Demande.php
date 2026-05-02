<?php

class Employe{
  private ?int $id;
    private string $nom;
   private  string $prenom;
    private float $salaire;   
    private  $anneEmbauche;

    public function setNom($nom){
$this->nom;
    }
    
    public function setid($id){
$this->id;
    }
     public function setPrenom($prenom){
$this->prenom;
    }
     public function setSalaire($salaire){
$this->salaire;
    } public function setAnneEmbauche($anneEmbauche){
$this->anneEmbauche;
    }
  public function getid(){
        return $this->id;
    }

    public function getNom(){
        return $this->nom;
    }
     public function getPrenom(){
        return $this->prenom;
    }
     public function getSalaire(){
        return $this->salaire;
    }
     public function getAnneEmbauche(){
        return $this->anneEmbauche;
    }


    public function __construct($id,$nom,$prenom,$salaire,$anneEmbauche){
       $this->id=$id;

 $this->nom=$nom;
    $this->prenom=$prenom;
    $this->salaire=$salaire;
    $this->anneEmbauche=$anneEmbauche;
    }
//    public function saisirInfo($nom,$prenom,$salaire,$anneEmbauche){
//     $this->nom=$nom;
//     $this->prenom=$prenom;
//     $this->salaire=$salaire;
//     $this->anneEmbauche=$anneEmbauche;
//     }

    public function afficherInfo(){
        echo "le nom est :".$this->nom."<br>". "le prénom est : ".$this->prenom."<br>".
    "le salaire est".$this->salaire."<br>"."l'année d'embauche est :".$this->anneEmbauche;

    }

    // public function __destruct(){
    //     echo "l'objet a été bien détruit";
    // }

}

class Manager extends Employe{
public float $bonus;

public function afficherInfoManager(){
    parent::afficherInfo();
    echo "le bonus est ".$this->bonus;

}
public function __construct($nom,$prenom,$salaire,$anneEmbauche,$bonus){
parent::__construct($nom,$prenom,$salaire,$anneEmbauche);
$this->bonus=$bonus;

}
public function calculSalaireFinale(){
    echo "<br>"."le salaire finale est:".($this->getSalaire()+$this->bonus);
}

}

// $employe1=new Employe("ben flen","flen",1000,2015);
// // $employe1->saisirInfo("ben flen","flen",1000,2015);
//  $employe1->afficherInfo();


// $manager1=new Manager("ben mohamed","mohamed",2000,2024,500);
// $manager1->afficherInfoManager();

//  $manager1->calculSalaireFinale();
// //  unset($employe1);


?>