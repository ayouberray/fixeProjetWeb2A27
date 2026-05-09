<?php

class Config{
private static $pdo;

public static function getConnexion(){
if(!isset(self::$pdo)){
$servername="localhost";
$username="root";
$password="";
$dbname="municipalite";
try{
self::$pdo=new PDO("mysql:host=$servername;dbname=$dbname",
$username,
$password,
[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
);
}
catch(PDOException $e){
    echo "erreur".$e->getMessage();
}

}
return self::$pdo;


}
}

?>
