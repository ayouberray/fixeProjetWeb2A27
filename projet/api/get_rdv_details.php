<?php
header('Content-Type: application/json');
require_once __DIR__."/../MODEL/config.php";

$type = $_GET['type'] ?? '';
$name = $_GET['name'] ?? '';

if (empty($name)) {
    echo json_encode([]);
    exit;
}

$db = Config::getConnexion();

if ($type === 'service') {
    $sql = "SELECT r.*, s.nom_service 
            FROM rendez_vous r 
            JOIN services s ON r.id_service = s.id_service 
            WHERE s.nom_service = :name 
            ORDER BY r.date_heure DESC";
} else {
    $sql = "SELECT r.*, s.nom_service 
            FROM rendez_vous r 
            LEFT JOIN services s ON r.id_service = s.id_service 
            WHERE r.agent_nom = :name 
            ORDER BY r.date_heure DESC";
}

try {
    $req = $db->prepare($sql);
    $req->execute(['name' => $name]);
    $results = $req->fetchAll(PDO::FETCH_ASSOC);
    
    // Format date for better readability
    foreach ($results as &$r) {
        $r['date_heure'] = date('d/m/Y H:i', strtotime($r['date_heure']));
    }
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
