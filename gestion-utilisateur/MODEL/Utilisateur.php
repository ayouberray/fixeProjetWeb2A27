<?php
// Fichier: MODEL/Utilisateur.php
// Modèle Utilisateur - Version complète avec tous les setters

require_once __DIR__ . '/config.php';

class Utilisateur {
    private $db;
    private $pdo;
    
    // Propriétés
    private $id;
    private $nom;
    private $prenom;
    private $sexe;
    private $dateNaissance;
    private $typeCompte;
    private $role;
    private $pays;
    private $ville;
    private $email;
    private $telephone;
    private $password;
    private $nomOrganisation;
    private $profession;
    private $cin;
    private $statut;
    private $dateCreation;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }
    
    // ==================== SETTERS (CHAINABLES) ====================
    public function setId($id) { $this->id = $id; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setPrenom($prenom) { $this->prenom = $prenom; return $this; }
    public function setSexe($sexe) { $this->sexe = $sexe; return $this; }
    public function setDateNaissance($date) { $this->dateNaissance = $date; return $this; }
    public function setTypeCompte($type) { $this->typeCompte = $type; return $this; }
    public function setRole($role) { $this->role = $role; return $this; }
    public function setPays($pays) { $this->pays = $pays; return $this; }
    public function setVille($ville) { $this->ville = $ville; return $this; }
    public function setEmail($email) { $this->email = strtolower(trim($email)); return $this; }
    public function setTelephone($telephone) { $this->telephone = $telephone; return $this; }
    public function setPlainPassword($password) { 
        $this->password = password_hash($password, PASSWORD_DEFAULT); 
        return $this;
    }
    public function setNomOrganisation($org) { $this->nomOrganisation = $org; return $this; }
    public function setProfession($profession) { $this->profession = $profession; return $this; }
    public function setCin($cin) { $this->cin = $cin; return $this; }
    public function setStatut($statut) { $this->statut = $statut; return $this; }
    
    // ==================== GETTERS ====================
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getSexe() { return $this->sexe; }
    public function getDateNaissance() { return $this->dateNaissance; }
    public function getTypeCompte() { return $this->typeCompte; }
    public function getRole() { return $this->role; }
    public function getPays() { return $this->pays; }
    public function getVille() { return $this->ville; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getPassword() { return $this->password; }
    public function getNomOrganisation() { return $this->nomOrganisation; }
    public function getProfession() { return $this->profession; }
    public function getCin() { return $this->cin; }
    public function getStatut() { return $this->statut; }
    
    // ==================== MÉTHODES PRINCIPALES ====================
    
    // Sauvegarder un utilisateur (inscription)
    public function save() {
        $sql = "INSERT INTO utilisateurs (
                    nom, prenom, sexe, date_naissance, type_compte,
                    pays, ville, email, telephone, password, 
                    cin, role, statut, nom_organisation, profession
                ) VALUES (
                    :nom, :prenom, :sexe, :date_naissance, :type_compte,
                    :pays, :ville, :email, :telephone, :password,
                    :cin, :role, :statut, :nom_organisation, :profession
                )";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':sexe' => $this->sexe,
            ':date_naissance' => $this->dateNaissance,
            ':type_compte' => $this->typeCompte,
            ':pays' => $this->pays,
            ':ville' => $this->ville,
            ':email' => $this->email,
            ':telephone' => $this->telephone,
            ':password' => $this->password,
            ':cin' => $this->cin,
            ':role' => 'user',
            ':statut' => 'actif',
            ':nom_organisation' => $this->nomOrganisation,
            ':profession' => $this->profession
        ]);
    }
    
    // Créer un utilisateur (admin)
    public function create($data) {
        $sql = "INSERT INTO utilisateurs (
                    nom, prenom, sexe, date_naissance, type_compte,
                    pays, ville, email, telephone, password, 
                    cin, role, statut
                ) VALUES (
                    :nom, :prenom, 'Homme', '1990-01-01', 'citoyen',
                    'Tunisie', :ville, :email, :telephone, :password,
                    :cin, :role, 'actif'
                )";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? '',
            ':ville' => $data['ville'] ?? 'Tunis',
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':cin' => $data['cin'],
            ':role' => $data['role']
        ]);
    }
    
    // Vérifier si l'email existe
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Vérifier si le CIN existe
    public function cinExists($cin) {
        if (empty($cin)) return false;
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE cin = :cin";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cin' => $cin]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Récupérer par email
    public function getByEmail($email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    // Récupérer par ID
    public function getById($id) {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Récupérer tous les utilisateurs
    public function getAll() {
        $sql = "SELECT * FROM utilisateurs ORDER BY date_creation DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    // Connexion
    public function login($email, $password) {
        $user = $this->getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'] . ' ' . $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_type'] = $user['type_compte'];
            return $user;
        }
        return false;
    }
    
    // Déconnexion
    public function logout() {
        session_destroy();
        return true;
    }
    
    // Mettre à jour
    public function update($id, $data) {
        $sql = "UPDATE utilisateurs SET 
                    nom = :nom, 
                    prenom = :prenom, 
                    email = :email, 
                    telephone = :telephone,
                    cin = :cin,
                    role = :role
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':cin' => $data['cin'],
            ':role' => $data['role']
        ]);
    }
    
    // Supprimer
    public function delete($id) {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // ==================== STATISTIQUES ====================
    
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM utilisateurs";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE role = :role";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function countByType($type) {
        $sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE type_compte = :type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':type' => $type]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function countByStatut($statut) {
        $sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE statut = :statut";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':statut' => $statut]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
?>