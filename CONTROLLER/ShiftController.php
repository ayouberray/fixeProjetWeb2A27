<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../MODEL/config.php";
class ShiftController {
    private function validerShiftInput($nom_shift, $heure_debut, $heure_fin) {
        $nom_shift = trim((string) $nom_shift);
        $heure_debut = trim((string) $heure_debut);
        $heure_fin = trim((string) $heure_fin);

        if ($nom_shift === '' || $heure_debut === '' || $heure_fin === '') {
            return false;
        }

        $timePattern = '/^([01]\d|2[0-3]):[0-5]\d$/';
        if (!preg_match($timePattern, $heure_debut) || !preg_match($timePattern, $heure_fin)) {
            return false;
        }

        return $heure_debut < $heure_fin;
    }
    
    function getAllShifts($filters = []) {
        $where = [];
        $params = [];

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $where[] = "LOWER(s.nom_shift) LIKE :search";
            $params['search'] = '%' . mb_strtolower($search, 'UTF-8') . '%';
        }

        $periode = trim((string) ($filters['periode'] ?? ''));
        if ($periode === 'matin') {
            $where[] = "s.heure_debut < '12:00:00'";
        } elseif ($periode === 'apres_midi') {
            $where[] = "s.heure_debut >= '12:00:00' AND s.heure_debut < '18:00:00'";
        } elseif ($periode === 'soir') {
            $where[] = "s.heure_debut >= '18:00:00'";
        }

        $sorts = [
            'heure_asc' => 's.heure_debut ASC',
            'heure_desc' => 's.heure_debut DESC',
            'nom' => 's.nom_shift ASC',
            'duree_desc' => 'duree_minutes DESC',
            'usage_desc' => 'emplois_count DESC',
        ];
        $sort = $filters['sort'] ?? 'heure_asc';
        $orderBy = $sorts[$sort] ?? $sorts['heure_asc'];

        $sql = "SELECT
                    s.id_shift,
                    s.nom_shift,
                    s.heure_debut,
                    s.heure_fin,
                    ROUND(TIME_TO_SEC(TIMEDIFF(s.heure_fin, s.heure_debut)) / 60) AS duree_minutes,
                    COUNT(e.id_emploi) AS emplois_count,
                    SUM(CASE WHEN e.statut = 'planifie' THEN 1 ELSE 0 END) AS emplois_planifies
                FROM shifts s
                LEFT JOIN emplois e ON e.id_shift = s.id_shift";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " GROUP BY s.id_shift, s.nom_shift, s.heure_debut, s.heure_fin
                  ORDER BY " . $orderBy;
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
    
    function getShiftById($id_shift) {
        $sql = "SELECT * FROM shifts WHERE id_shift = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return null;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_shift]);
            return $req->fetch();
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return null;
        }
    }
    
    function ajouterShift($nom_shift, $heure_debut, $heure_fin) {
        if (!$this->validerShiftInput($nom_shift, $heure_debut, $heure_fin)) {
            return false;
        }

        $sql = "INSERT INTO shifts (nom_shift, heure_debut, heure_fin) VALUES (:nom, :debut, :fin)";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $nom_shift,
                'debut' => $heure_debut,
                'fin' => $heure_fin
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function modifierShift($id_shift, $nom_shift, $heure_debut, $heure_fin) {
        if (!filter_var($id_shift, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            return false;
        }

        if (!$this->validerShiftInput($nom_shift, $heure_debut, $heure_fin)) {
            return false;
        }

        $sql = "UPDATE shifts SET nom_shift = :nom, heure_debut = :debut, heure_fin = :fin WHERE id_shift = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute([
                'id' => $id_shift,
                'nom' => $nom_shift,
                'debut' => $heure_debut,
                'fin' => $heure_fin
            ]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }
    
    function supprimerShift($id_shift) {
        $sql = "DELETE FROM shifts WHERE id_shift = :id";
        $db = Config::getConnexion();
        if (!$db) {
            return false;
        }

        try {
            $req = $db->prepare($sql);
            $req->execute(['id' => $id_shift]);
            return true;
        } catch(Exception $e) {
            echo "erreur: ".$e->getMessage();
            return false;
        }
    }

    function getContactsByShiftIds($shiftIds) {
        $shiftIds = array_values(array_unique(array_filter(array_map('intval', (array) $shiftIds), function ($id) {
            return $id > 0;
        })));

        if (empty($shiftIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($shiftIds), '?'));

        $sql = "SELECT
                    e.id_shift,
                    e.id_emploi,
                    e.date_travail,
                    e.statut,
                    u.nom AS agent_nom,
                    u.prenom AS agent_prenom,
                    s.nom_service,
                    sh.nom_shift,
                    sh.heure_debut,
                    sh.heure_fin
                FROM emplois e
                INNER JOIN users u ON e.id_agent = u.id
                INNER JOIN services s ON e.id_service = s.id_service
                INNER JOIN shifts sh ON e.id_shift = sh.id_shift
                WHERE e.id_shift IN (" . $placeholders . ")
                  AND e.statut = 'planifie'
                  AND e.date_travail >= CURDATE()
                ORDER BY e.date_travail ASC, sh.heure_debut ASC, u.nom ASC, u.prenom ASC";

        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare($sql);
            $req->execute($shiftIds);
            $contacts = [];
            foreach ($req->fetchAll() as $row) {
                $contacts[(int) $row['id_shift']][] = $row;
            }

            return $contacts;
        } catch(Exception $e) {
            return [];
        }
    }

    function getStatistiquesShifts() {
        $db = Config::getConnexion();
        $default = [
            'total' => 0, 'matin' => 0, 'apres_midi' => 0, 'soir' => 0,
            'duree_moyenne' => 0, 'duree_max' => 0, 'emplois_lies' => 0,
            'shifts_utilises' => 0, 'shifts_non_utilises' => 0,
            'par_periode' => [], 'par_duree' => [], 'par_utilisation' => [], 'par_horaire' => []
        ];

        if (!$db) return $default;

        try {
            $sql = "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN heure_debut < '12:00:00' THEN 1 ELSE 0 END) AS matin,
                        SUM(CASE WHEN heure_debut >= '12:00:00' AND heure_debut < '18:00:00' THEN 1 ELSE 0 END) AS apres_midi,
                        SUM(CASE WHEN heure_debut >= '18:00:00' THEN 1 ELSE 0 END) AS soir,
                        ROUND(AVG(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 60)) AS duree_moyenne,
                        ROUND(MAX(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 60)) AS duree_max
                    FROM shifts";
            $req = $db->query($sql);
            $stats = ($req && ($row = $req->fetch(PDO::FETCH_ASSOC))) ? array_merge($default, $row) : $default;

            $reqUsage = $db->query("SELECT
                                        COUNT(e.id_emploi) AS emplois_lies,
                                        COUNT(DISTINCT CASE WHEN e.id_emploi IS NOT NULL THEN s.id_shift END) AS shifts_utilises,
                                        SUM(CASE WHEN e.id_emploi IS NULL THEN 1 ELSE 0 END) AS shifts_non_utilises
                                    FROM shifts s
                                    LEFT JOIN emplois e ON e.id_shift = s.id_shift");
            if ($reqUsage && ($usage = $reqUsage->fetch(PDO::FETCH_ASSOC))) {
                $stats['emplois_lies'] = (int) ($usage['emplois_lies'] ?? 0);
                $stats['shifts_utilises'] = (int) ($usage['shifts_utilises'] ?? 0);
                $stats['shifts_non_utilises'] = (int) ($usage['shifts_non_utilises'] ?? 0);
            }

            // Distribution par période pour graphique
            $stats['par_periode'] = [
                ['label' => 'Matin', 'value' => (int) $stats['matin']],
                ['label' => 'Après-midi', 'value' => (int) $stats['apres_midi']],
                ['label' => 'Soir', 'value' => (int) $stats['soir']],
            ];

            // Distribution par durée (tranches de 2h)
            $sqlDuree = "SELECT 
                            CASE 
                                WHEN (TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut))/3600) <= 4 THEN 'Court (<4h)'
                                WHEN (TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut))/3600) <= 8 THEN 'Normal (4-8h)'
                                ELSE 'Long (>8h)'
                            END as label,
                            COUNT(*) as value
                         FROM shifts
                         GROUP BY label";
            $reqD = $db->query($sqlDuree);
            $dureeRows = $reqD ? $reqD->fetchAll(PDO::FETCH_ASSOC) : [];
            $dureeMap = ['Court (<4h)' => 0, 'Normal (4-8h)' => 0, 'Long (>8h)' => 0];
            foreach ($dureeRows as $row) {
                $dureeMap[$row['label']] = (int) $row['value'];
            }
            $stats['par_duree'] = [];
            foreach ($dureeMap as $label => $value) {
                $stats['par_duree'][] = ['label' => $label, 'value' => $value];
            }

            $reqTop = $db->query("SELECT
                                      s.nom_shift AS label,
                                      COUNT(e.id_emploi) AS value
                                  FROM shifts s
                                  LEFT JOIN emplois e ON e.id_shift = s.id_shift
                                  GROUP BY s.id_shift, s.nom_shift
                                  ORDER BY value DESC, s.nom_shift ASC
                                  LIMIT 6");
            $stats['par_utilisation'] = $reqTop ? $reqTop->fetchAll(PDO::FETCH_ASSOC) : [];

            $reqHoraire = $db->query("SELECT
                                          DATE_FORMAT(heure_debut, '%H:00') AS label,
                                          COUNT(*) AS value
                                      FROM shifts
                                      GROUP BY HOUR(heure_debut)
                                      ORDER BY HOUR(heure_debut) ASC");
            $stats['par_horaire'] = $reqHoraire ? $reqHoraire->fetchAll(PDO::FETCH_ASSOC) : [];

            return $stats;
        } catch(Exception $e) {
            return $default;
        }
    }

    function getNotificationsShifts() {
        $notifications = [];
        $db = Config::getConnexion();
        if (!$db) {
            return [];
        }

        try {
            $req = $db->prepare("SELECT COUNT(*) AS total
                                 FROM shifts
                                 WHERE TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 60 > 600");
            $req->execute();
            $longs = (int) ($req->fetch()['total'] ?? 0);
            if ($longs > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'fa-clock',
                    'title' => 'Shifts longs',
                    'message' => $longs . ' shift(s) depassent 10 heures.',
                ];
            }

            $req = $db->prepare("SELECT COUNT(*) AS total
                                 FROM shifts s
                                 LEFT JOIN emplois e ON e.id_shift = s.id_shift
                                 WHERE e.id_emploi IS NULL");
            $req->execute();
            $unused = (int) ($req->fetch()['total'] ?? 0);
            if ($unused > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fa-link-slash',
                    'title' => 'Shifts non utilises',
                    'message' => $unused . ' shift(s) ne sont pas encore affectes a un emploi.',
                ];
            }

            $req = $db->prepare("SELECT nom_shift, heure_debut, heure_fin, COUNT(*) AS total
                                 FROM shifts
                                 GROUP BY nom_shift, heure_debut, heure_fin
                                 HAVING COUNT(*) > 1
                                 LIMIT 3");
            $req->execute();
            foreach ($req->fetchAll() as $row) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'fa-copy',
                    'title' => 'Doublon possible',
                    'message' => (int) $row['total'] . ' shifts ont le meme nom et le meme horaire: ' . $row['nom_shift'] . '.',
                ];
            }
        } catch(Exception $e) {
            return [];
        }

        return $notifications;
    }
}
?>
