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
                         OR v.qr_token LIKE :search
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

        $sql = "SELECT v.*
                FROM vue_emplois_shifts v";
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
        $sql = "SELECT v.*
                FROM vue_emplois_shifts v
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

    function getEmploiByQrToken($token) {
        $token = trim((string) $token);
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            return null;
        }

        $sql = "SELECT v.*
                FROM vue_emplois_shifts v
                WHERE v.qr_token = :token";
        $db = Config::getConnexion();
        if (!$db) {
            return null;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute(['token' => $token]);
            return $req->fetch() ?: null;
        } catch(Exception $e) {
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

        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $qrToken = $this->generateQrToken($db);
            $sql = "INSERT INTO emplois (id_agent, id_service, id_shift, date_travail, statut, qr_token) 
                    VALUES (:id_agent, :id_service, :id_shift, :date_travail, 'planifie', :qr_token)";
            $req = $db->prepare($sql);
            $req->execute([
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail,
                'qr_token' => $qrToken
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
                'id_agent' => $id_agent,
                'id_service' => $id_service,
                'id_shift' => $id_shift,
                'date_travail' => $date_travail,
                'statut' => $statut,
                'id' => $id_emploi
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
        $db = Config::getConnexion();
        $default = [
            'total' => 0, 'planifies' => 0, 'termines' => 0, 'annules' => 0,
            'aujourdhui' => 0, 'semaine' => 0, 'agents' => 0,
            'duree_moyenne' => 0, 'duree_max' => 0,
            'par_service' => [], 'par_shift' => [], 'par_mois' => [],
            'par_periode' => [], 'par_horaire' => []
        ];

        if (!$db) return $default;

        try {
            // Stats globales
            $sql = "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) AS planifies,
                        SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) AS termines,
                        SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) AS annules,
                        SUM(CASE WHEN date_travail = CURDATE() THEN 1 ELSE 0 END) AS aujourdhui,
                        SUM(CASE WHEN date_travail BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS semaine,
                        COUNT(DISTINCT id_agent) AS agents,
                        ROUND(AVG(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 60)) AS duree_moyenne,
                        ROUND(MAX(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 60)) AS duree_max
                    FROM vue_emplois_shifts";
            $req = $db->query($sql);
            $stats = ($req && ($row = $req->fetch(PDO::FETCH_ASSOC))) ? $row : $default;

            // Distribution par Service
            $sqlService = "SELECT nom_service as label, COUNT(*) as value 
                           FROM vue_emplois_shifts 
                           GROUP BY id_service, nom_service 
                           ORDER BY value DESC LIMIT 5";
            $reqS = $db->query($sqlService);
            $stats['par_service'] = $reqS ? $reqS->fetchAll(PDO::FETCH_ASSOC) : [];

            // Distribution par Shift
            $sqlShift = "SELECT nom_shift as label, COUNT(*) as value 
                         FROM vue_emplois_shifts 
                         GROUP BY id_shift, nom_shift 
                         ORDER BY value DESC";
            $reqSh = $db->query($sqlShift);
            $stats['par_shift'] = $reqSh ? $reqSh->fetchAll(PDO::FETCH_ASSOC) : [];

            // Evolution par Mois (6 derniers mois)
            $sqlMois = "SELECT DATE_FORMAT(date_travail, '%m/%Y') as label, COUNT(*) as value 
                        FROM vue_emplois_shifts 
                        WHERE date_travail >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                        GROUP BY DATE_FORMAT(date_travail, '%Y-%m'), label
                        ORDER BY DATE_FORMAT(date_travail, '%Y-%m') ASC";
            $reqM = $db->query($sqlMois);
            $stats['par_mois'] = $reqM ? $reqM->fetchAll(PDO::FETCH_ASSOC) : [];

            // Distribution par Période
            $sqlPeriode = "SELECT 
                            CASE 
                                WHEN heure_debut < '12:00:00' THEN 'Matin'
                                WHEN heure_debut < '18:00:00' THEN 'Après-midi'
                                ELSE 'Soir'
                            END as label,
                            COUNT(*) as value
                           FROM vue_emplois_shifts
                           GROUP BY label";
            $reqP = $db->query($sqlPeriode);
            $stats['par_periode'] = $reqP ? $reqP->fetchAll(PDO::FETCH_ASSOC) : [];

            // Distribution par Horaire
            $sqlHoraire = "SELECT 
                            DATE_FORMAT(heure_debut, '%H:00') as label,
                            COUNT(*) as value
                           FROM vue_emplois_shifts
                           GROUP BY HOUR(heure_debut)
                           ORDER BY HOUR(heure_debut) ASC";
            $reqH = $db->query($sqlHoraire);
            $stats['par_horaire'] = $reqH ? $reqH->fetchAll(PDO::FETCH_ASSOC) : [];

            // Assurer que les clés par défaut existent si les requêtes ont échoué
            foreach(['par_service', 'par_shift', 'par_mois', 'par_periode', 'par_horaire'] as $k) {
                if (!isset($stats[$k])) $stats[$k] = [];
            }

            return $stats;
        } catch(Exception $e) {
            return $default;
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

        $sql = "SELECT v.*
                FROM vue_emplois_shifts v
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

    private function generateQrToken($db) {
        do {
            try {
                $token = bin2hex(random_bytes(16));
            } catch(Exception $e) {
                $token = md5(uniqid('', true));
            }

            $req = $db->prepare("SELECT COUNT(*) AS total FROM emplois WHERE qr_token = :token");
            $req->execute(['token' => $token]);
            $exists = (int) ($req->fetch()['total'] ?? 0) > 0;
        } while ($exists);

        return $token;
    }

}
?>
