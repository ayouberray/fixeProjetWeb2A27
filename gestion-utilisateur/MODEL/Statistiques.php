<?php
// Fichier: MODEL/Statistiques.php
// Classe de statistiques avancées et KPIs professionnels
// Adaptée à la base de données innogov_db

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
    
    // ==========================================
    // MÉTRIQUES UTILISATEURS
    // ==========================================
    
    /**
     * Statistiques récentes (7 derniers jours)
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
     * Statistiques d'engagement global
     */
    public function getEngagementStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_utilisateurs,
                    (SELECT COUNT(*) FROM utilisateurs WHERE statut = 'actif') as utilisateurs_actifs,
                    (SELECT COUNT(*) FROM demandes) as total_demandes,
                    (SELECT COUNT(*) FROM demandes WHERE statut IN ('en_cours', 'traite')) as demandes_traitees,
                    (SELECT COUNT(*) FROM reclamations) as total_reclamations,
                    (SELECT COUNT(*) FROM reclamations WHERE statut IN ('traitee', 'cloturee')) as reclamations_resolues,
                    (SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme') as rdv_confirme
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Taux de croissance quotidien
     */
    public function getTauxCroissanceQuotidien() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs WHERE DATE(date_creation) = CURDATE()) as aujourdhui,
                    (SELECT COUNT(*) FROM utilisateurs WHERE DATE(date_creation) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)) as hier
            ");
            $result = $stmt->fetch();
            
            if ($result['hier'] > 0) {
                return round((($result['aujourdhui'] - $result['hier']) / $result['hier']) * 100, 1);
            }
            return $result['aujourdhui'] > 0 ? 100 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Progression mensuelle (6 derniers mois)
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
     * Taux de conversion (citoyens → demandes)
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
     * Taux de rétention
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
    
    // ==========================================
    // RÉPARTITION DÉMOGRAPHIQUE
    // ==========================================
    
    /**
     * Répartition par sexe
     */
    public function getRepartitionSexe() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    sexe,
                    COUNT(*) as total,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM utilisateurs), 1) as pourcentage
                FROM utilisateurs
                GROUP BY sexe
                ORDER BY total DESC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Répartition par tranche d'âge
     */
    public function getRepartitionAge() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN 'Moins de 18 ans'
                        WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 18 AND 25 THEN '18-25 ans'
                        WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 26 AND 35 THEN '26-35 ans'
                        WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 36 AND 50 THEN '36-50 ans'
                        WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 51 AND 65 THEN '51-65 ans'
                        ELSE 'Plus de 65 ans'
                    END as tranche_age,
                    COUNT(*) as total
                FROM utilisateurs
                WHERE date_naissance IS NOT NULL AND date_naissance != '0000-00-00'
                GROUP BY tranche_age
                ORDER BY FIELD(tranche_age, 'Moins de 18 ans', '18-25 ans', '26-35 ans', '36-50 ans', '51-65 ans', 'Plus de 65 ans')
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Statistiques par ville
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
                    ['region' => 'Aucune donnée', 'total' => 0]
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Top 5 des villes les plus actives
     */
    public function getTopVillesActives() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    ville,
                    COUNT(*) as total_utilisateurs,
                    SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as actifs,
                    ROUND(SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as taux_activite
                FROM utilisateurs
                WHERE ville IS NOT NULL AND ville != ''
                GROUP BY ville
                HAVING COUNT(*) >= 1
                ORDER BY total_utilisateurs DESC
                LIMIT 5
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==========================================
    // MÉTRIQUES DE SERVICES
    // ==========================================
    
    /**
     * Métriques des demandes
     */
    public function getMetriquesDemandes() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_demandes,
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) as traitees,
                    SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refusees,
                    ROUND(AVG(CASE WHEN statut = 'traite' THEN DATEDIFF(date_modification, date_creation) END), 1) as delai_moyen
                FROM demandes
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Métriques des réclamations
     */
    public function getMetriquesReclamations() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_reclamations,
                    SUM(CASE WHEN priorite IN ('haute', 'urgente') THEN 1 ELSE 0 END) as prioritaires,
                    SUM(CASE WHEN statut IN ('traitee', 'cloturee') THEN 1 ELSE 0 END) as resolues,
                    SUM(CASE WHEN statut IN ('soumise', 'en_cours') THEN 1 ELSE 0 END) as en_cours
                FROM reclamations
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Répartition des demandes par type
     */
    public function getDemandesParType() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    type_demande as type,
                    COUNT(*) as total
                FROM demandes
                GROUP BY type_demande
                ORDER BY total DESC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Répartition des réclamations par catégorie
     */
    public function getReclamationsParCategorie() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    categorie,
                    COUNT(*) as total
                FROM reclamations
                GROUP BY categorie
                ORDER BY total DESC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // ==========================================
    // TENDANCES ET PRÉDICTIONS
    // ==========================================
    
    /**
     * Tendance des 7 derniers jours
     */
    public function getTendanceHebdomadaire() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    DATE(date_creation) as date,
                    DAYNAME(date_creation) as jour,
                    COUNT(*) as total
                FROM utilisateurs
                WHERE date_creation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY DATE(date_creation), DAYNAME(date_creation)
                ORDER BY date ASC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Taux d'acquisition par mois (12 derniers mois)
     */
    public function getTauxAcquisitionMensuel() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    DATE_FORMAT(date_creation, '%Y-%m') as mois,
                    type_compte,
                    COUNT(*) as total
                FROM utilisateurs
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(date_creation, '%Y-%m'), type_compte
                ORDER BY mois ASC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Activité par heure (simulée pour la heatmap)
     */
    public function getActiviteParHeure() {
        // Données d'activité simulées par jour de la semaine (Lun-Dim)
        return [45, 67, 89, 78, 92, 34, 12];
    }
    
    // ==========================================
    // KPIs DE PERFORMANCE
    // ==========================================
    
    /**
     * KPIs de performance globaux
     */
    public function getKPIPerformance() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_utilisateurs,
                    (SELECT COUNT(*) FROM utilisateurs WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as nouveaux_30j,
                    (SELECT COUNT(*) FROM demandes) as total_demandes,
                    (SELECT COUNT(*) FROM demandes WHERE statut = 'traite') as demandes_resolues,
                    (SELECT COUNT(*) FROM reclamations) as total_reclamations,
                    (SELECT COUNT(*) FROM reclamations WHERE statut IN ('traitee', 'cloturee')) as reclamations_resolues,
                    (SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme') as rdv_confirme,
                    (SELECT COUNT(*) FROM rendez_vous WHERE date_heure >= NOW()) as rdv_a_venir
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Score de satisfaction (basé sur les réclamations résolues)
     */
    public function getScoreSatisfaction() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    (SELECT COUNT(*) FROM reclamations) as total,
                    (SELECT COUNT(*) FROM reclamations WHERE statut IN ('traitee', 'cloturee')) as resolues,
                    (SELECT COUNT(*) FROM avis WHERE satisfaction IN ('satisfait', 'tres_satisfait')) as avis_positifs,
                    (SELECT COUNT(*) FROM avis) as total_avis
            ");
            $result = $stmt->fetch();
            
            if ($result['total_avis'] > 0) {
                return round(($result['avis_positifs'] / $result['total_avis']) * 100);
            } elseif ($result['total'] > 0) {
                return round(($result['resolues'] / $result['total']) * 100);
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Temps moyen de résolution des demandes
     */
    public function getTempsResolution() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    ROUND(AVG(DATEDIFF(date_modification, date_creation)), 1) as jours_moyen,
                    MIN(DATEDIFF(date_modification, date_creation)) as min_jours,
                    MAX(DATEDIFF(date_modification, date_creation)) as max_jours
                FROM demandes
                WHERE statut = 'traite'
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return ['jours_moyen' => 0, 'min_jours' => 0, 'max_jours' => 0];
        }
    }
    
    // ==========================================
    // RÉSUMÉ GLOBAL POUR LE DASHBOARD
    // ==========================================
    
    /**
     * Résumé complet pour le dashboard
     */
    public function getResumeComplet() {
        return [
            'utilisateurs' => [
                'total' => $this->db->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch()['total'],
                'actifs' => $this->db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE statut = 'actif'")->fetch()['total'],
                'nouveaux_30j' => $this->db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch()['total'],
                'taux_retention' => $this->getRetentionRate()
            ],
            'demandes' => [
                'total' => $this->db->query("SELECT COUNT(*) as total FROM demandes")->fetch()['total'],
                'en_attente' => $this->db->query("SELECT COUNT(*) as total FROM demandes WHERE statut = 'en_attente'")->fetch()['total'],
                'traitees' => $this->db->query("SELECT COUNT(*) as total FROM demandes WHERE statut = 'traite'")->fetch()['total']
            ],
            'reclamations' => [
                'total' => $this->db->query("SELECT COUNT(*) as total FROM reclamations")->fetch()['total'],
                'prioritaires' => $this->db->query("SELECT COUNT(*) as total FROM reclamations WHERE priorite IN ('haute', 'urgente')")->fetch()['total'],
                'resolues' => $this->db->query("SELECT COUNT(*) as total FROM reclamations WHERE statut IN ('traitee', 'cloturee')")->fetch()['total']
            ],
            'rendez_vous' => [
                'confirmes' => $this->db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut = 'confirme'")->fetch()['total'],
                'a_venir' => $this->db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE date_heure >= NOW()")->fetch()['total']
            ]
        ];
    }
}