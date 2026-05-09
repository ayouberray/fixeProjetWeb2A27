<?php
require_once __DIR__ . '/../../MODEL/config.php';
require_once __DIR__ . '/../../MODEL/SuiviReponse.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_demande = (int)($_POST['id_demande'] ?? 0);
    $id_parent = $_POST['id_parent'] ? (int)$_POST['id_parent'] : null;
    $contenu = trim($_POST['contenu'] ?? '');
    $redirect = $_POST['redirect'] ?? 'client.php';
    
    if ($id_demande && strlen($contenu) >= 5) {
        $db = Config::getConnexion();
        
        // Récupérer le citoyen de la demande
        $stmt = $db->prepare("SELECT id_citoyen FROM demandes WHERE id_demande = ?");
        $stmt->execute([$id_demande]);
        $demande = $stmt->fetch();
        
        if ($demande) {
            $suiviReponse = new SuiviReponse();
            $suiviReponse->ajouter(
                $id_demande,
                $demande['id_citoyen'],
                null, // pas d'agent pour le citoyen
                $contenu,
                'citoyen',
                'citoyen',
                $id_parent
            );
            
            header('Location: ' . $redirect . '?success=Réponse envoyée avec succès');
            exit();
        }
    }
    
    header('Location: ' . $redirect . '?error=Erreur lors de l\'envoi');
    exit();
}