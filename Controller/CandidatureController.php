<?php
// CONTROLLER/CandidatureController.php
require_once __DIR__ . "/../MODEL/Candidature.php";
require_once __DIR__ . "/../MODEL/Offre.php";

class CandidatureController {
    private $candidatureModel;
    private $offreModel;

    public function __construct() {
        $this->candidatureModel = new Candidature();
        $this->offreModel = new Offre();
    }

    // Traitement du formulaire de candidature (Front)
    public function postuler() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $offre_id = $_POST['offre_id'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $num_tel = $_POST['num_tel'];

            // Gestion du CV
            $cv_path = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/../assets/uploads/cv/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $extension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('cv_') . '.' . $extension;
                $destination = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
                    $cv_path = 'assets/uploads/cv/' . $fileName;
                }
            }

            if ($cv_path) {
                $result = $this->candidatureModel->create($offre_id, $nom, $prenom, $email, $num_tel, $cv_path);
                if ($result) {
                    header("Location: confirmation.php?success=1");
                    exit;
                }
            }
            // En cas d'erreur
            header("Location: detail.php?id=$offre_id&error=1");
            exit;
        }
    }

    // Backoffice : lister toutes les candidatures
    public function adminListerCandidatures() {
        $candidatures = $this->candidatureModel->getAll();
        include __DIR__ . "/../VIEW/backoffice/condidatures/lister.php";
    }

    // Backoffice : accepter/refuser une candidature (AJAX)
    public function traiterCandidature() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $action = $_POST['action']; // 'accepter' ou 'refuser'
            $nouveauStatut = ($action === 'accepter') ? 'validee' : 'rejetee';
            $result = $this->candidatureModel->updateStatut($id, $nouveauStatut);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }
    }

    // Télécharger le CV
    public function telechargerCV($id) {
        $candidature = $this->candidatureModel->getById($id);
        if ($candidature && !empty($candidature['cv_path'])) {
            $filePath = __DIR__ . "/../" . $candidature['cv_path'];
            if (file_exists($filePath)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                readfile($filePath);
                exit;
            }
        }
        http_response_code(404);
        echo "Fichier non trouvé";
    }
}