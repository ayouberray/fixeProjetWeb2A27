<?php
// Fichier: MODEL/Statistiques.php
// Classe pour les statistiques avancées - Adaptée à innogov_db

class Statistiques {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=innogov_db;charset=utf8',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw new Exception('Erreur connexion Statistiques: ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère les statistiques récentes (7 derniers jours)
     */
    public function getStatsRecentes() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    DATE(date_creation) as date,
                    COUNT(*) as total_inscriptions,
                    SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as citoyens,
                    SUM(CASE WHEN role = 'agent' THEN 1 ELSE 0 END) as agents,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins
                FROM utilisateurs 
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(date_creation)
                ORDER BY date DESC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupère les statistiques d'engagement
     */
    public function getEngagementStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_utilisateurs,
                    (SELECT COUNT(*) FROM utilisateurs WHERE statut = 'actif') as utilisateurs_actifs,
                    (SELECT COUNT(*) FROM demandes) as total_demandes,
                    (SELECT COUNT(*) FROM reclamations) as total_reclamations,
                    (SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme') as rdv_confirme
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupère la progression mensuelle
     */
    public function getProgressionMensuelle() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    DATE_FORMAT(date_creation, '%Y-%m') as mois,
                    COUNT(*) as total,
                    MONTH(date_creation) as mois_num,
                    YEAR(date_creation) as annee
                FROM utilisateurs 
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
                ORDER BY annee ASC, mois_num ASC
            ");
            
            $result = $stmt->fetchAll();
            
            $mois = [];
            $croissance = [];
            $nomsMois = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            
            foreach ($result as $row) {
                $mois[] = $nomsMois[(int)$row['mois_num']] . ' ' . $row['annee'];
                $croissance[] = (int)$row['total'];
            }
            
            return [
                'mois' => $mois,
                'croissance' => $croissance
            ];
        } catch (Exception $e) {
            return ['mois' => [], 'croissance' => []];
        }
    }
    
    /**
     * Calcule le taux de conversion
     */
    public function getTauxConversion() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs WHERE role = 'user') as total_citoyens,
                    (SELECT COUNT(DISTINCT id_citoyen) FROM demandes) as citoyens_actifs
            ");
            $result = $stmt->fetch();
            
            if ($result['total_citoyens'] > 0) {
                return round(($result['citoyens_actifs'] / $result['total_citoyens']) * 100);
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Calcule le taux de rétention
     */
    public function getRetentionRate() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as actifs
                FROM utilisateurs
            ");
            $result = $stmt->fetch();
            
            if ($result['total'] > 0) {
                return round(($result['actifs'] / $result['total']) * 100);
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Récupère les statistiques par ville
     */
    public function getStatsParRegion() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    ville as region,
                    COUNT(*) as total
                FROM utilisateurs
                WHERE ville IS NOT NULL AND ville != ''
                GROUP BY ville
                ORDER BY total DESC
                LIMIT 8
            ");
            
            $result = $stmt->fetchAll();
            
            if (empty($result)) {
                return [
                    ['region' => 'Tunis', 'total' => 0],
                    ['region' => 'Ariana', 'total' => 0],
                    ['region' => 'Ben Arous', 'total' => 0],
                    ['region' => 'Autres', 'total' => 0]
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupère l'activité par jour de la semaine (données simulées)
     */
    public function getActiviteParHeure() {
        return [45, 67, 89, 78, 92, 34, 12];
    }
    
    /**
     * Récupère les KPIs de performance
     */
    public function getKPIPerformance() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_utilisateurs,
                    (SELECT COUNT(*) FROM demandes) as total_demandes,
                    (SELECT COUNT(*) FROM reclamations) as total_reclamations,
                    (SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme') as rdv_confirme,
                    (SELECT COUNT(*) FROM demandes WHERE statut = 'traite') as demandes_resolues
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
}