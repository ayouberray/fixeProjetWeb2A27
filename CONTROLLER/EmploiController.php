<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../MODEL/config.php";

class EmploiController {
    
    function getAllEmplois($filters = []) {
        $where = [];
        $params = [];

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $where[] = "(LOWER(v.agent_nom) LIKE :search
                         OR LOWER(v.agent_prenom) LIKE :search
                         OR LOWER(v.nom_service) LIKE :search
                         OR LOWER(v.nom_shift) LIKE :search
                         OR LOWER(v.statut) LIKE :search
                         OR v.date_travail LIKE :search
                         OR DATE_FORMAT(v.date_travail, '%d/%m/%Y') LIKE :search)";
            $params['search'] = '%' . mb_strtolower($search, 'UTF-8') . '%';
        }

        $statut = trim((string) ($filters['statut'] ?? ''));
        if (in_array($statut, ['planifie', 'termine', 'annule'], true)) {
            $where[] = "v.statut = :statut";
            $params['statut'] = $statut;
        }

        $idService = filter_var($filters['id_service'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($idService !== false && $idService !== null) {
            $where[] = "v.id_service = :id_service";
            $params['id_service'] = $idService;
        }

        $dateFrom = $this->normaliserDate($filters['date_from'] ?? '');
        if ($dateFrom !== null) {
            $where[] = "v.date_travail >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        $dateTo = $this->normaliserDate($filters['date_to'] ?? '');
        if ($dateTo !== null) {
            $where[] = "v.date_travail <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $sorts = [
            'date_desc' => 'v.date_travail DESC, v.id_emploi DESC',
            'date_asc' => 'v.date_travail ASC, v.id_emploi ASC',
            'agent' => 'v.agent_nom ASC, v.agent_prenom ASC, v.date_travail DESC',
            'service' => 'v.nom_service ASC, v.date_travail DESC',
            'shift' => 'v.heure_debut ASC, v.date_travail DESC',
            'statut' => 'v.statut ASC, v.date_travail DESC',
        ];
        $sort = $filters['sort'] ?? 'date_desc';
        $orderBy = $sorts[$sort] ?? $sorts['date_desc'];

        $phoneColumn = $this->getUserPhoneColumn();
        $phoneSelect = $phoneColumn ? ", u.`" . $phoneColumn . "` AS agent_telephone" : ", NULL AS agent_telephone";
        $phoneJoin = $phoneColumn ? " LEFT JOIN users u ON v.id_agent = u.id" : "";

        $sql = "SELECT v.*" . $phoneSelect . "
                FROM vue_emplois_shifts v" . $phoneJoin;
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY " . $orderBy;

        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return [];
        }
    }
    
    function getEmploiById($id_emploi) {
        $phoneColumn = $this->getUserPhoneColumn();
        $phoneSelect = $phoneColumn ? ", u.`" . $phoneColumn . "` AS agent_telephone" : ", NULL AS agent_telephone";
        $phoneJoin = $phoneColumn ? " LEFT JOIN users u ON v.id_agent = u.id" : "";

        $sql = "SELECT v.*" . $phoneSelect . "
                FROM vue_emplois_shifts v" . $phoneJoin . "
                WHERE v.id_emploi = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return null;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_emploi]);
            return $req->fetch();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return null;
        }
    }
    
    function ajouterEmploi($id_agent, $id_service, $id_shift, $date_travail) {
        $id_agent = filter_var($id_agent, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_service = filter_var($id_service, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_shift = filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $date_travail = trim((string) $date_travail);

        $dateValide = DateTime::createFromFormat('Y-m-d', $date_travail);
        $today = new DateTime('today');
        $isDateFormatOk = $dateValide && $dateValide->format('Y-m-d') === $date_travail;
        $isDateNotPast = $isDateFormatOk && $dateValide >= $today;

        if ($id_agent === false || $id_service === false || $id_shift === false || !$isDateNotPast) {
            return false;
        }

        $sql = "INSERT INTO emplois (id_agent, id_service, id_shift, date_travail, statut) 
                VALUES (:id_agent, :id_service, :id_shift, :date_travail, 'planifie')";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function modifierEmploi($id_emploi, $id_agent, $id_service, $id_shift, $date_travail, $statut) {
        $id_emploi = filter_var($id_emploi, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_agent = filter_var($id_agent, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_service = filter_var($id_service, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $id_shift = filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        $date_travail = trim((string) $date_travail);
        $statut = trim((string) $statut);

        $statutsValides = ['planifie', 'termine', 'annule'];
        $dateValide = DateTime::createFromFormat('Y-m-d', $date_travail);
        $today = new DateTime('today');
        $isDateFormatOk = $dateValide && $dateValide->format('Y-m-d') === $date_travail;
        $isDateNotPast = $isDateFormatOk && $dateValide >= $today;

        if (
            $id_emploi === false ||
            $id_agent === false ||
            $id_service === false ||
            $id_shift === false ||
            !$isDateNotPast ||
            !in_array($statut, $statutsValides, true)
        ) {
            return false;
        }

        $sql = "UPDATE emplois SET 
                id_agent = :id_agent, 
                id_service = :id_service, 
                id_shift = :id_shift, 
                date_travail = :date_travail,
                statut = :statut
                WHERE id_emploi = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id_emploi,
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail,
                'statut' => $statut
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function supprimerEmploi($id_emploi) {
        $sql = "DELETE FROM emplois WHERE id_emploi = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_emploi]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function getUserByName($nom_complet) {
        $nom_complet = trim(preg_replace('/\s+/', ' ', $nom_complet));
        if(empty($nom_complet)) {
            return null;
        }

        $full = mb_strtolower($nom_complet, 'UTF-8');
        $db = Config::getConnexion();
        if (!$db) {
            return null;
        }

        try {
            $sql = "SELECT * FROM users WHERE role = 'agent' AND (
                    LOWER(CONCAT(nom, ' ', prenom)) = :full OR
                    LOWER(CONCAT(prenom, ' ', nom)) = :full
                ) LIMIT 1";
            $req = $db->prepare($sql);
            $req->execute(['full' => $full]);
            $result = $req->fetch();
            if($result) {
                return $result;
            }

            if(strpos($nom_complet, ' ') === false) {
                $sql = "SELECT * FROM users WHERE role = 'agent' AND (LOWER(nom) = :value OR LOWER(prenom) = :value) LIMIT 1";
                $req = $db->prepare($sql);
                $req->execute(['value' => $full]);
                return $req->fetch();
            }

            return null;
        } catch(Exception $e) {
            return null;
        }
    }
    
    function ajouterEmploiByName($nom_agent, $id_service, $id_shift, $date_travail) {
        $agent = $this->getUserByName($nom_agent);
        if(!$agent) {
            return false; // Agent not found
        }
        
        return $this->ajouterEmploi($agent['id'], $id_service, $id_shift, $date_travail);
    }
    
    function getAgents() {
        $sql = "SELECT * FROM users WHERE role = 'agent' ORDER BY nom, prenom";
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
    
    function getServices() {
        $sql = "SELECT * FROM services WHERE statut = 'actif' ORDER BY nom_service";
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }
    
    function getShifts() {
        $sql = "SELECT * FROM shifts ORDER BY heure_debut";
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute();
            return $req->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    function getStatistiquesEmplois() {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) AS planifies,
                    SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) AS termines,
                    SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) AS annules,
                    SUM(CASE WHEN date_travail = CURDATE() THEN 1 ELSE 0 END) AS aujourdhui,
                    SUM(CASE WHEN date_travail BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS semaine,
                    COUNT(DISTINCT id_agent) AS agents
                FROM vue_emplois_shifts";
        $db = Config::getConnexion();
        if (!$db) {
            return [
                'total' => 0,
                'planifies' => 0,
                'termines' => 0,
                'annules' => 0,
                'aujourdhui' => 0,
                'semaine' => 0,
                'agents' => 0,
            ];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute();
            $stats = $req->fetch();
            return $stats ?: [
                'total' => 0,
                'planifies' => 0,
                'termines' => 0,
                'annules' => 0,
                'aujourdhui' => 0,
                'semaine' => 0,
                'agents' => 0,
            ];
        } catch(Exception $e) {
            return [];
        }
    }

    function getNotificationsEmplois() {
        $notifications = [];
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare("SELECT COUNT(*) AS total
                                 FROM vue_emplois_shifts
                                 WHERE statut = 'planifie'
                                   AND date_travail BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)");
            $req->execute();
            $soon = (int) ($req->fetch()['total'] ?? 0);
            if ($soon > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'title' => 'Planning proche',
                    'message' => $soon . ' emploi(s) planifie(s) dans les prochaines 48 heures.',
                ];
            }

            $req = $db->prepare("SELECT COUNT(*) AS total
                                 FROM vue_emplois_shifts
                                 WHERE statut = 'planifie'
                                   AND date_travail < CURDATE()");
            $req->execute();
            $late = (int) ($req->fetch()['total'] ?? 0);
            if ($late > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'fa-triangle-exclamation',
                    'title' => 'Statut a verifier',
                    'message' => $late . ' emploi(s) ancien(s) sont encore planifies.',
                ];
            }

            $req = $db->prepare("SELECT agent_nom, agent_prenom, date_travail, COUNT(*) AS total
                                 FROM vue_emplois_shifts
                                 WHERE statut = 'planifie'
                                 GROUP BY id_agent, agent_nom, agent_prenom, date_travail
                                 HAVING COUNT(*) > 1
                                 ORDER BY date_travail ASC
                                 LIMIT 3");
            $req->execute();
            foreach ($req->fetchAll() as $row) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'fa-user-clock',
                    'title' => 'Double affectation',
                    'message' => trim(($row['agent_nom'] ?? '') . ' ' . ($row['agent_prenom'] ?? '')) . ' a ' . (int) $row['total'] . ' emplois le ' . date('d/m/Y', strtotime($row['date_travail'])) . '.',
                ];
            }
        } catch(Exception $e) {
            return [];
        }

        return $notifications;
    }

    function getCalendrierEmplois($month = null) {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $month) ? $month : date('Y-m');
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $phoneColumn = $this->getUserPhoneColumn();
        $phoneSelect = $phoneColumn ? ", u.`" . $phoneColumn . "` AS agent_telephone" : ", NULL AS agent_telephone";
        $phoneJoin = $phoneColumn ? " LEFT JOIN users u ON v.id_agent = u.id" : "";

        $sql = "SELECT v.*" . $phoneSelect . "
                FROM vue_emplois_shifts v" . $phoneJoin . "
                WHERE v.date_travail BETWEEN :start AND :end
                ORDER BY v.date_travail ASC, v.heure_debut ASC";
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute([
                'start' => $start,
                'end' => $end,
            ]);
            $items = [];
            foreach ($req->fetchAll() as $emploi) {
                $items[$emploi['date_travail']][] = $emploi;
            }
            return $items;
        } catch(Exception $e) {
            return [];
        }
    }

    private function normaliserDate($date) {
        $date = trim((string) $date);
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if ($dateObj && $dateObj->format('Y-m-d') === $date) {
            return $date;
        }

        return null;
    }

    private function getUserPhoneColumn() {
        static $phoneColumn = null;
        static $checked = false;

        if ($checked) {
            return $phoneColumn;
        }

        $checked = true;
        $candidates = ['telephone', 'phone', 'tel', 'numero_telephone', 'num_tel', 'mobile', 'gsm'];
        $db = Config::getConnexion();
        if (!$db) {
            return null;
        }

        try {
            $req = $db->prepare("SELECT COLUMN_NAME
                                 FROM INFORMATION_SCHEMA.COLUMNS
                                 WHERE TABLE_SCHEMA = DATABASE()
                                   AND TABLE_NAME = 'users'");
            $req->execute();
            $columns = array_map('strtolower', array_column($req->fetchAll(), 'COLUMN_NAME'));

            foreach ($candidates as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $phoneColumn = $candidate;
                    break;
                }
            }
        } catch(Exception $e) {
            $phoneColumn = null;
        }

        return $phoneColumn;
    }
}
?>
