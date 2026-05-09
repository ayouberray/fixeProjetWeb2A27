<?php
// CONTROLLER/OffreController.php
require_once __DIR__ . "/../MODEL/Offre.php";

class OffreController {
    private $offreModel;

    public function __construct() {
        $this->offreModel = new Offre();
    }

    // Afficher la liste des offres (Front)
    public function listerOffres() {
        $offres = $this->offreModel->getAll();
        include __DIR__ . "/../VIEW/frontoffice/offres/lister.php";
    }

    // Afficher le détail d'une offre (Front)
    public function detailOffre($id) {
        $offre = $this->offreModel->getById($id);
        if (!$offre) {
            header("HTTP/1.0 404 Not Found");
            echo "Offre non trouvée";
            exit;
        }
        include __DIR__ . "/../VIEW/frontoffice/offres/detail.php";
    }

    // --- Backoffice ---
    public function adminListerOffres() {
        $offres = $this->offreModel->getAll();
        include __DIR__ . "/../VIEW/backoffice/offres/lister.php";
    }

    public function ajouterOffre() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $entite = $_POST['entite'];
            $date_limite = $_POST['date_limite'];
            $nombre_postes = $_POST['nombre_postes'];
            $statut = $_POST['statut'] ?? 'Ouvert';
            $result = $this->offreModel->create($titre, $description, $entite, $date_limite, $nombre_postes, $statut);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }
        include __DIR__ . "/../VIEW/backoffice/offres/ajouter.php";
    }

    public function modifierOffre($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $entite = $_POST['entite'];
            $date_limite = $_POST['date_limite'];
            $nombre_postes = $_POST['nombre_postes'];
            $statut = $_POST['statut'];
            $result = $this->offreModel->update($id, $titre, $description, $entite, $date_limite, $nombre_postes, $statut);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }
        $offre = $this->offreModel->getById($id);
        include __DIR__ . "/../VIEW/backoffice/offres/modifier.php";
    }

    public function supprimerOffre($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->offreModel->delete($id);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }
        // GET : afficher une confirmation (facultatif)
        $offre = $this->offreModel->getById($id);
        include __DIR__ . "/../VIEW/backoffice/offres/supprimer.php";
    }
}