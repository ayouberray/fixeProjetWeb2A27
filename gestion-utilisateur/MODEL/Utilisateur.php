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
                   nom_organisation, profession, email_verifie
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
                       nom_organisation, profession, email_verifie
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
                   nom_organisation, profession, email_verifie
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
                   pays, ville, email, telephone, password, cin, statut, date_creation, email_verifie
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
                    nom_organisation, profession, statut, date_creation, email_verifie
                ) VALUES (
                    :nom, :prenom, :sexe, :date_naissance, :type_compte, :role,
                    :pays, :ville, :email, :telephone, :password, :cin,
                    :nom_organisation, :profession, :statut, NOW(), 0
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
            ':statut' => $data['statut'] ?? 'en_attente'
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
     * Récupère les inscriptions mensuelles
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
    
    // ==================== MÉTHODES POUR MOT DE PASSE OUBLIÉ ====================
    
    /**
     * Récupérer un utilisateur par son email (pour forgot password)
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email AND statut IN ('actif', 'en_attente')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Sauvegarder le token de réinitialisation
     */
    public function saveResetToken($email, $token, $expires) {
        $sql = "UPDATE utilisateurs SET reset_token = :token, reset_expires = :expires WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);
    }
    
    /**
     * Récupérer un utilisateur par son token (et vérifier qu'il n'est pas expiré)
     */
    public function getUserByToken($token) {
        $sql = "SELECT * FROM utilisateurs 
                WHERE reset_token = :token 
                AND reset_expires > NOW() 
                AND statut IN ('actif', 'en_attente')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET password = :password WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'email' => $email
        ]);
    }
    
    /**
     * Supprimer le token après réinitialisation
     */
    public function clearResetToken($email) {
        $sql = "UPDATE utilisateurs SET reset_token = NULL, reset_expires = NULL WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['email' => $email]);
    }
    
    // ==================== MÉTHODES POUR CONFIRMATION D'EMAIL ====================
    
    /**
     * Sauvegarder le token de vérification d'email
     */
    public function saveVerificationToken($email, $token, $expires) {
        $sql = "UPDATE utilisateurs SET verification_token = :token, verification_expires = :expires WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);
    }
    
    /**
     * Vérifier si l'email est déjà vérifié
     */
    public function isEmailVerified($email) {
        $sql = "SELECT email_verifie FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result && $result['email_verifie'] == 1;
    }
    
    /**
     * Vérifier un token de confirmation et activer le compte
     */
    public function verifyEmailByToken($token) {
        $sql = "UPDATE utilisateurs 
                SET email_verifie = 1, 
                    statut = 'actif',
                    verification_token = NULL, 
                    verification_expires = NULL 
                WHERE verification_token = :token AND verification_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['token' => $token]);
    }
    
    /**
     * Récupérer un utilisateur par son token de vérification
     */
    public function getUserByVerificationToken($token) {
        $sql = "SELECT * FROM utilisateurs 
                WHERE verification_token = :token AND verification_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Activer directement un compte admin sans vérification email
     */
    public function verifyAdminEmail($email) {
        $sql = "UPDATE utilisateurs 
                SET email_verifie = 1, 
                    statut = 'actif',
                    verification_token = NULL, 
                    verification_expires = NULL 
                WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['email' => $email]);
    }
    
    /**
     * Marquer un email comme vérifié (pour pré-inscription)
     */
    public function markEmailAsVerified($email) {
        $sql = "UPDATE utilisateurs 
                SET email_verifie = 1, 
                    statut = 'actif',
                    verification_token = NULL, 
                    verification_expires = NULL 
                WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['email' => $email]);
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
?>