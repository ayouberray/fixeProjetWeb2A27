<?php
require 'MODEL/config.php';
$db = Config::getConnexion();
$stmt = $db->query('SELECT * FROM services');
print_r($stmt->fetchAll());
?>
