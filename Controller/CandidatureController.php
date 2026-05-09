<?php
require_once __DIR__ . "/../MODEL/Candidature.php";
require_once __DIR__ . "/../MODEL/Offre.php";

class CandidatureController {
    private $candidatureModel;
    private $offreModel;

    public function __construct() {
        $this->candidatureModel = new Candidature();
        $this->offreModel = new Offre();
    }
    public function postuler() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $offre_id = $_POST['offre_id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $num_tel = $_POST['num_tel'];

        $errors = [];
        if (empty($nom) || empty($prenom) || empty($email) || empty($num_tel))
            $errors[] = "Tous les champs sont requis";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = "Email invalide";
        if (!preg_match('/^[0-9+\- ]{8,15}$/', $num_tel))
            $errors[] = "Numéro de téléphone invalide";
        if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK)
            $errors[] = "CV manquant";
        else {
            $allowed = ['pdf', 'docx'];
            $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed))
                $errors[] = "Format de CV non autorisé (PDF/DOCX uniquement)";
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                exit;
            } else {
                header("Location: detail.php?id=$offre_id&error=1");
                exit;
            }
        }

        require_once __DIR__ . "/../MODEL/Offre.php";
        $offreModel = new Offre();
        $offre = $offreModel->getById($offre_id);
        if (($offre['nb_candidats'] ?? 0) >= ($offre['nombre_postes'] ?? 0)) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Désolé, cette offre est désormais complète.']);
                exit;
            }
        }
        $cv_path = '';
        $uploadDir = __DIR__ . "/../assets/uploads/cv/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $extension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('cv_') . '.' . $extension;
        $destination = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
            $cv_path = 'assets/uploads/cv/' . $fileName;
        }

        if ($cv_path) {
            $candidatureId = $this->candidatureModel->create($offre_id, $nom, $prenom, $email, $num_tel, $cv_path);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success'       => (bool)$candidatureId,
                    'candidature_id'=> $candidatureId,
                    'nom'           => $nom,
                    'prenom'        => $prenom,
                    'offre_id'      => $offre_id,
                    'message'       => $candidatureId ? 'Candidature envoyée' : 'Erreur enregistrement'
                ]);
                exit;
            } else {
                if ($candidatureId) header("Location: index.php?controller=candidature&action=badge&id=$candidatureId");
                else header("Location: index.php?controller=offre&action=detail&id=$offre_id&error=1");
                exit;
            }
        } else {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload du CV']);
                exit;
            } else {
                header("Location: detail.php?id=$offre_id&error=1");
                exit;
            }
        }
    }
}
    
private function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
    public function adminListerCandidatures() {
        $candidatures = $this->candidatureModel->getAll();
        include __DIR__ . "/../VIEW/backoffice/condidatures/lister.php";
    }
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