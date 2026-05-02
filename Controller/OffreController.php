<?php
require_once __DIR__ . "/../MODEL/Offre.php";

class OffreController {
    private $offreModel;

    public function __construct() {
        $this->offreModel = new Offre();
    }

    public function listerOffres() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id_offre DESC';
        $offres = $this->offreModel->getAll($search, $sort);
        include __DIR__ . "/../VIEW/frontoffice/offres/lister.php";
    }

    public function detailOffre($id) {
        $offre = $this->offreModel->getById($id);
        if (!$offre) {
            header("HTTP/1.0 404 Not Found");
            echo "Offre non trouvée";
            exit;
        }
        include __DIR__ . "/../VIEW/frontoffice/offres/detail.php";
    }

    public function adminListerOffres() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id_offre DESC';
        $offres = $this->offreModel->getAll($search, $sort);
        $db = Config::getConnexion();
        $stats = [
            'total_offres'          => $db->query("SELECT COUNT(*) FROM offre")->fetchColumn(),
            'offres_urgentes'       => $db->query("SELECT COUNT(*) FROM offre WHERE statut = 'Ouvert' AND DATEDIFF(date_limite, CURDATE()) BETWEEN 0 AND 7")->fetchColumn(),
            'total_candidatures'    => $db->query("SELECT COUNT(*) FROM condidature")->fetchColumn(),
            'candidatures_attente'  => $db->query("SELECT COUNT(*) FROM condidature WHERE statut = 'en_attend'")->fetchColumn(),
            'candidatures_semaine'  => $db->query("SELECT COUNT(*) FROM condidature WHERE date_cond >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
            'taux_traitement'       => $db->query("SELECT ROUND((SELECT COUNT(*) FROM condidature WHERE statut != 'en_attend') / GREATEST(COUNT(*),1) * 100) FROM condidature")->fetchColumn()
        ];

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
        $result = $this->offreModel->delete($id);
        header("Location: index.php?controller=offre&action=admin-lister&msg=supprime");
        exit;
    }
}