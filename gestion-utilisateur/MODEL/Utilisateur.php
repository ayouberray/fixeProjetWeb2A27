<?php
// Fichier: MODEL/Utilisateur.php
// Classe complète de gestion des utilisateurs - Adaptée à innogov_db

class Utilisateur {
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
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère tous les utilisateurs
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT id, nom, prenom, sexe, date_naissance, type_compte, role, 
                   pays, ville, email, telephone, cin, statut, date_creation,
                   nom_organisation, profession
            FROM utilisateurs 
            ORDER BY date_creation DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère tous les utilisateurs avec tri et filtres
     */
    public function getAllOrdered($sort = 'date_creation', $order = 'DESC', $search = '', $roleFilter = '', $typeFilter = '') {
        $allowedSorts = [
            'id', 'nom', 'prenom', 'email', 'cin', 'telephone', 
            'type_compte', 'role', 'date_creation', 'ville', 'statut'
        ];
        
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'date_creation';
        }
        
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql = "SELECT id, nom, prenom, sexe, date_naissance, type_compte, role, 
                       pays, ville, email, telephone, cin, statut, date_creation,
                       nom_organisation, profession
                FROM utilisateurs WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (nom LIKE :search OR prenom LIKE :search2 OR email LIKE :search3 OR cin LIKE :search4 OR telephone LIKE :search5 OR ville LIKE :search6)";
            $searchTerm = "%$search%";
            $params[':search'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
            $params[':search4'] = $searchTerm;
            $params[':search5'] = $searchTerm;
            $params[':search6'] = $searchTerm;
        }
        
        if (!empty($roleFilter)) {
            $sql .= " AND role = :role";
            $params[':role'] = $roleFilter;
        }
        
        if (!empty($typeFilter)) {
            $sql .= " AND type_compte = :type";
            $params[':type'] = $typeFilter;
        }
        
        $sql .= " ORDER BY $sort $order";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un utilisateur par son ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT id, nom, prenom, sexe, date_naissance, type_compte, role, 
                   pays, ville, email, telephone, cin, statut, date_creation,
                   nom_organisation, profession
            FROM utilisateurs WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère un utilisateur par son email
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT id, nom, prenom, sexe, date_naissance, type_compte, role, 
                   pays, ville, email, telephone, password, cin, statut, date_creation
            FROM utilisateurs WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        $sql = "INSERT INTO utilisateurs (
                    nom, prenom, sexe, date_naissance, type_compte, role, 
                    pays, ville, email, telephone, password, cin, 
                    nom_organisation, profession, statut, date_creation
                ) VALUES (
                    :nom, :prenom, :sexe, :date_naissance, :type_compte, :role,
                    :pays, :ville, :email, :telephone, :password, :cin,
                    :nom_organisation, :profession, :statut, NOW()
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? '',
            ':sexe' => $data['sexe'] ?? 'Homme',
            ':date_naissance' => $data['date_naissance'] ?? '2000-01-01',
            ':type_compte' => $data['type_compte'] ?? 'citoyen',
            ':role' => $data['role'] ?? 'user',
            ':pays' => $data['pays'] ?? 'Tunisie',
            ':ville' => $data['ville'] ?? 'Tunis',
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? '',
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':cin' => $data['cin'] ?? '',
            ':nom_organisation' => $data['nom_organisation'] ?? null,
            ':profession' => $data['profession'] ?? null,
            ':statut' => $data['statut'] ?? 'actif'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        $sql = "UPDATE utilisateurs SET 
                nom = :nom,
                prenom = :prenom,
                email = :email,
                telephone = :telephone,
                cin = :cin,
                ville = :ville,
                type_compte = :type_compte,
                role = :role,
                statut = :statut";
        
        $params = [
            ':id' => $id,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? '',
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? '',
            ':cin' => $data['cin'] ?? '',
            ':ville' => $data['ville'] ?? 'Tunis',
            ':type_compte' => $data['type_compte'] ?? 'citoyen',
            ':role' => $data['role'] ?? 'user',
            ':statut' => $data['statut'] ?? 'actif'
        ];
        
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Compte tous les utilisateurs
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM utilisateurs");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Compte les utilisateurs par rôle
     */
    public function countByRole($role) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM utilisateurs WHERE role = :role");
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Compte les utilisateurs par type de compte
     */
    public function countByTypeCompte($type) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM utilisateurs WHERE type_compte = :type");
        $stmt->execute([':type' => $type]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Compte les utilisateurs par période
     */
    public function countUsersByPeriod($period) {
        switch ($period) {
            case 'week':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'last_week':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND date_creation < DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'last_month':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND date_creation < DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'quarter':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            case 'last_quarter':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 180 DAY) AND date_creation < DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            case 'year':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
                break;
            case 'last_year':
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 730 DAY) AND date_creation < DATE_SUB(NOW(), INTERVAL 365 DAY)";
                break;
            default:
                $condition = "date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM utilisateurs WHERE $condition");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Compte les utilisateurs actifs
     */
    public function countActiveUsers() {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total 
            FROM utilisateurs 
            WHERE statut = 'actif'
        ");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Récupère le taux de complétion des profils
     */
    public function getProfileCompletionRate() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE 
                    WHEN nom IS NOT NULL AND nom != '' 
                    AND prenom IS NOT NULL AND prenom != ''
                    AND email IS NOT NULL AND email != ''
                    AND cin IS NOT NULL AND cin != ''
                    AND telephone IS NOT NULL AND telephone != ''
                    AND ville IS NOT NULL AND ville != ''
                    THEN 1 ELSE 0 
                END) as complete
            FROM utilisateurs
        ");
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            return round(($result['complete'] / $result['total']) * 100);
        }
        return 0;
    }
    
    /**
     * Récupère les inscriptions mensuelles (12 derniers mois)
     */
    public function getMonthlyRegistrations() {
        $stmt = $this->db->query("
            SELECT 
                MONTH(date_creation) as mois,
                YEAR(date_creation) as annee,
                COUNT(*) as total
            FROM utilisateurs 
            WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY YEAR(date_creation), MONTH(date_creation)
            ORDER BY annee ASC, mois ASC
        ");
        
        $result = $stmt->fetchAll();
        
        $data = [];
        $currentDate = new DateTime();
        $currentDate->modify('-11 months');
        
        for ($i = 0; $i < 12; $i++) {
            $monthKey = (int)$currentDate->format('m');
            $yearKey = (int)$currentDate->format('Y');
            
            $found = false;
            foreach ($result as $row) {
                if ((int)$row['mois'] === $monthKey && (int)$row['annee'] === $yearKey) {
                    $data[] = (int)$row['total'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $data[] = 0;
            }
            
            $currentDate->modify('+1 month');
        }
        
        return $data;
    }
    
    /**
     * Statistiques par ville
     */
    public function getStatsParVille() {
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
                ['region' => 'Ariana', 'total' => 0]
            ];
        }
        
        return $result;
    }
    
    /**
     * Vérifie si l'utilisateur connecté est admin
     */
    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Vérifie si l'utilisateur connecté est agent
     */
    public function isAgent() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'agent';
    }
    
    /**
     * Authentifie un utilisateur
     */
    public function login($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Déconnecte un utilisateur
     */
    public function logout() {
        session_destroy();
    }

    // ==================== PROPRIÉTÉS PRIVÉES POUR LES SETTERS ====================
    private $nom;
    private $prenom;
    private $sexe;
    private $dateNaissance;
    private $cin;
    private $telephone;
    private $typeCompte;
    private $pays;
    private $ville;
    private $email;
    private $plainPassword;
    private $nomOrganisation;
    private $profession;
    private $statut;
    private $role;

    // ==================== SETTERS (RETOURNENT $this POUR FLUENT INTERFACE) ====================
    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
        return $this;
    }

    public function setSexe($sexe) {
        $this->sexe = $sexe;
        return $this;
    }

    public function setDateNaissance($dateNaissance) {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function setCin($cin) {
        $this->cin = $cin;
        return $this;
    }

    public function setTelephone($telephone) {
        $this->telephone = $telephone;
        return $this;
    }

    public function setTypeCompte($typeCompte) {
        $this->typeCompte = $typeCompte;
        return $this;
    }

    public function setPays($pays) {
        $this->pays = $pays;
        return $this;
    }

    public function setVille($ville) {
        $this->ville = $ville;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setNomOrganisation($nomOrganisation) {
        $this->nomOrganisation = $nomOrganisation;
        return $this;
    }

    public function setProfession($profession) {
        $this->profession = $profession;
        return $this;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    // ==================== MÉTHODE POUR ENREGISTRER L'UTILISATEUR ====================
    /**
     * Enregistre un nouvel utilisateur avec les propriétés définies par les setters
     * Assigne automatiquement le rôle selon le type_compte
     */
    public function save() {
        // Assigner le rôle selon le type_compte
        $role = 'user'; // Rôle par défaut
        
        if ($this->typeCompte === 'agent_public') {
            $role = 'agent'; // Les agents publics deviennent agents
        }
        
        $sql = "INSERT INTO utilisateurs (
                    nom, prenom, sexe, date_naissance, type_compte, role, 
                    pays, ville, email, telephone, password, cin, 
                    nom_organisation, profession, statut, date_creation
                ) VALUES (
                    :nom, :prenom, :sexe, :date_naissance, :type_compte, :role,
                    :pays, :ville, :email, :telephone, :password, :cin,
                    :nom_organisation, :profession, :statut, NOW()
                )";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':nom' => $this->nom,
                ':prenom' => $this->prenom,
                ':sexe' => $this->sexe,
                ':date_naissance' => $this->dateNaissance,
                ':type_compte' => $this->typeCompte,
                ':role' => $role,
                ':pays' => $this->pays,
                ':ville' => $this->ville,
                ':email' => $this->email,
                ':telephone' => $this->telephone,
                ':password' => password_hash($this->plainPassword, PASSWORD_DEFAULT),
                ':cin' => $this->cin,
                ':nom_organisation' => empty($this->nomOrganisation) ? null : $this->nomOrganisation,
                ':profession' => empty($this->profession) ? null : $this->profession,
                ':statut' => $this->statut
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'insertion : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une email existe déjà
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();
        return (int)$result['total'] > 0;
    }

    /**
     * Vérifie si un CIN existe déjà
     */
    public function cinExists($cin) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM utilisateurs WHERE cin = :cin");
        $stmt->execute([':cin' => $cin]);
        $result = $stmt->fetch();
        return (int)$result['total'] > 0;
    }
}