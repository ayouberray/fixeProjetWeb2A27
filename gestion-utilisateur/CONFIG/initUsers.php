<?php
/**
 * Script d'initialisation des utilisateurs de test
 * À exécuter une seule fois pour initialiser la base de données
 * 
 * Usage: Accédez à /gestion-utilisateur/CONFIG/initUsers.php dans votre navigateur
 */

// Connexion à la base de données
$host = 'localhost';
$dbname = 'innogov_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Erreur de connexion: ' . $e->getMessage());
}

// Vérifier si le script a été exécuté (protection)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm_init'])) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Initialisation des Utilisateurs de Test</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffc107;
                color: #856404;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .success { color: #28a745; }
            .error { color: #dc3545; }
            .info { color: #17a2b8; }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            th {
                background: #f8f9fa;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background: #f9f9f9;
            }
            .button {
                background: #dc3545;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                margin: 10px 0;
            }
            .button:hover {
                background: #c82333;
            }
            .button.confirm {
                background: #28a745;
            }
            .button.confirm:hover {
                background: #218838;
            }
            .code {
                background: #f4f4f4;
                padding: 10px;
                border-left: 3px solid #0066cc;
                font-family: monospace;
                margin: 10px 0;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📋 Initialisation des Utilisateurs de Test</h1>
            
            <div class="warning">
                <strong>⚠️ Attention:</strong> Ce script va <strong>supprimer tous les utilisateurs existants</strong> et les remplacer par des utilisateurs de test.
                <br>À utiliser uniquement en développement!
            </div>

            <h2>Utilisateurs à créer:</h2>
            
            <h3 class="success">✓ 1 Administrateur</h3>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Mot de passe</th>
                    <th>Rôle</th>
                    <th>Accès</th>
                </tr>
                <tr>
                    <td>admin@innogov.tn</td>
                    <td>admin123456</td>
                    <td>admin</td>
                    <td>Backoffice Complet</td>
                </tr>
            </table>

            <h3 class="info">ℹ️ 2 Agents Publics</h3>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Mot de passe</th>
                    <th>Rôle</th>
                    <th>Organisation</th>
                    <th>Accès</th>
                </tr>
                <tr>
                    <td>agent@innogov.tn</td>
                    <td>agent123456</td>
                    <td>agent</td>
                    <td>Ministère de l'Intérieur</td>
                    <td>Backoffice</td>
                </tr>
                <tr>
                    <td>fatima@innogov.tn</td>
                    <td>agent123456</td>
                    <td>agent</td>
                    <td>Ministère des Affaires Sociales</td>
                    <td>Backoffice</td>
                </tr>
            </table>

            <h3 class="success">✓ 2 Citoyens</h3>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Mot de passe</th>
                    <th>Région</th>
                    <th>Accès</th>
                </tr>
                <tr>
                    <td>ahmed@mail.tn</td>
                    <td>user123456</td>
                    <td>Sfax</td>
                    <td>Frontoffice</td>
                </tr>
                <tr>
                    <td>leila@mail.tn</td>
                    <td>user123456</td>
                    <td>Sousse</td>
                    <td>Frontoffice</td>
                </tr>
            </table>

            <h3 class="success">✓ 2 Professionnels</h3>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Mot de passe</th>
                    <th>Profession</th>
                    <th>Accès</th>
                </tr>
                <tr>
                    <td>contact@entreprise.tn</td>
                    <td>user123456</td>
                    <td>PDG - TechStartup Tunisia</td>
                    <td>Frontoffice</td>
                </tr>
                <tr>
                    <td>sophia@consulting.tn</td>
                    <td>user123456</td>
                    <td>Consultante - Digital Consulting</td>
                    <td>Frontoffice</td>
                </tr>
            </table>

            <h2>Instructions:</h2>
            <ol>
                <li>Cliquez sur "Initialiser la BDD" ci-dessous</li>
                <li>Tous les utilisateurs de test seront créés</li>
                <li>Accédez à <a href="../../VIEW/frontoffice/login.php" target="_blank">la page login</a> et essayez les comptes</li>
                <li><strong>Supprimez ce fichier après initialisation</strong> pour des raisons de sécurité</li>
            </ol>

            <h2>Test des accès:</h2>
            <div class="code">
                Admin: admin@innogov.tn / admin123456 → Backoffice<br>
                Agent: agent@innogov.tn / agent123456 → Backoffice<br>
                Client: ahmed@mail.tn / user123456 → Frontoffice<br>
            </div>

            <form method="POST" onsubmit="return confirm('Êtes-vous sûr? Cette action est irréversible!')">
                <input type="hidden" name="confirm_init" value="1">
                <button type="submit" class="button confirm">✓ Initialiser la BDD avec les utilisateurs de test</button>
            </form>

            <p style="margin-top: 30px; font-size: 12px; color: #666;">
                <strong>Note:</strong> Ce script ne peut être exécuté qu'une fois par session POST.
                Pour réinitialiser, actualisez la page.
            </p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ============================================================
// EXÉCUTION DE L'INITIALISATION
// ============================================================

// Désactiver l'affichage des erreurs PDO
error_reporting(E_ALL);
ini_set('display_errors', 1);

$users = [
    // Admin
    [
        'nom' => 'Admin',
        'prenom' => 'InnoGov',
        'email' => 'admin@innogov.tn',
        'password' => 'admin123456',
        'role' => 'admin',
        'type_compte' => 'N/A',
        'cin' => '00000000',
        'telephone' => '29000000',
        'ville' => 'Tunis',
        'sexe' => 'Homme',
        'date_naissance' => '1990-01-01',
        'statut' => 'actif'
    ],
    // Agents
    [
        'nom' => 'Jean',
        'prenom' => 'Agent',
        'email' => 'agent@innogov.tn',
        'password' => 'agent123456',
        'role' => 'agent',
        'type_compte' => 'agent_public',
        'cin' => '12345678',
        'telephone' => '22000000',
        'ville' => 'Tunis',
        'sexe' => 'Homme',
        'date_naissance' => '1985-05-10',
        'nom_organisation' => 'Ministère de l\'Intérieur',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Fatima',
        'prenom' => 'Agent',
        'email' => 'fatima@innogov.tn',
        'password' => 'agent123456',
        'role' => 'agent',
        'type_compte' => 'agent_public',
        'cin' => '87654321',
        'telephone' => '21000000',
        'ville' => 'Ariana',
        'sexe' => 'Femme',
        'date_naissance' => '1988-03-22',
        'nom_organisation' => 'Ministère des Affaires Sociales',
        'statut' => 'actif'
    ],
    // Citoyens
    [
        'nom' => 'Ahmed',
        'prenom' => 'Citoyen',
        'email' => 'ahmed@mail.tn',
        'password' => 'user123456',
        'role' => 'user',
        'type_compte' => 'citoyen',
        'cin' => '12345670',
        'telephone' => '99000000',
        'ville' => 'Sfax',
        'sexe' => 'Homme',
        'date_naissance' => '1995-03-15',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Leila',
        'prenom' => 'Citoyenne',
        'email' => 'leila@mail.tn',
        'password' => 'user123456',
        'role' => 'user',
        'type_compte' => 'citoyen',
        'cin' => '98765432',
        'telephone' => '98999999',
        'ville' => 'Sousse',
        'sexe' => 'Femme',
        'date_naissance' => '1992-07-20',
        'statut' => 'actif'
    ],
    // Professionnels
    [
        'nom' => 'Mohamed',
        'prenom' => 'Entreprise',
        'email' => 'contact@entreprise.tn',
        'password' => 'user123456',
        'role' => 'user',
        'type_compte' => 'professionnel',
        'cin' => '11223344',
        'telephone' => '71555555',
        'ville' => 'Tunis',
        'sexe' => 'Homme',
        'date_naissance' => '1980-06-01',
        'nom_organisation' => 'TechStartup Tunisia',
        'profession' => 'PDG',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Sophia',
        'prenom' => 'Consultant',
        'email' => 'sophia@consulting.tn',
        'password' => 'user123456',
        'role' => 'user',
        'type_compte' => 'professionnel',
        'cin' => '55667788',
        'telephone' => '72999999',
        'ville' => 'Monastir',
        'sexe' => 'Femme',
        'date_naissance' => '1988-09-12',
        'nom_organisation' => 'Digital Consulting Group',
        'profession' => 'Consultante',
        'statut' => 'actif'
    ]
];

try {
    // Supprimer tous les utilisateurs existants
    $pdo->exec("TRUNCATE TABLE utilisateurs");
    
    // Préparer la requête d'insertion
    $sql = "INSERT INTO utilisateurs (
                nom, prenom, email, password, role, type_compte,
                cin, telephone, ville, sexe, date_naissance,
                nom_organisation, profession, statut, date_creation
            ) VALUES (
                :nom, :prenom, :email, :password, :role, :type_compte,
                :cin, :telephone, :ville, :sexe, :date_naissance,
                :nom_organisation, :profession, :statut, NOW()
            )";
    
    $stmt = $pdo->prepare($sql);
    
    // Insérer chaque utilisateur
    foreach ($users as $user) {
        $stmt->execute([
            ':nom' => $user['nom'],
            ':prenom' => $user['prenom'],
            ':email' => $user['email'],
            ':password' => password_hash($user['password'], PASSWORD_DEFAULT),
            ':role' => $user['role'],
            ':type_compte' => $user['type_compte'],
            ':cin' => $user['cin'],
            ':telephone' => $user['telephone'],
            ':ville' => $user['ville'],
            ':sexe' => $user['sexe'],
            ':date_naissance' => $user['date_naissance'],
            ':nom_organisation' => $user['nom_organisation'] ?? null,
            ':profession' => $user['profession'] ?? null,
            ':statut' => $user['statut']
        ]);
    }
    
    $count = count($users);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Initialisation Réussie</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .success-box {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
                padding: 20px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .links {
                margin: 30px 0;
            }
            .links a {
                display: inline-block;
                background: #007bff;
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                text-decoration: none;
                margin: 5px;
            }
            .links a:hover {
                background: #0056b3;
            }
            .code {
                background: #f4f4f4;
                padding: 10px;
                border-left: 3px solid #0066cc;
                font-family: monospace;
                margin: 10px 0;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-box">
                <h1>✓ Initialisation Réussie!</h1>
                <p><strong><?php echo $count; ?> utilisateurs de test</strong> ont été créés avec succès.</p>
            </div>

            <h2>Prochaines étapes:</h2>
            <ol>
                <li>Allez à la <a href="../../VIEW/frontoffice/login.php" target="_blank">page de connexion</a></li>
                <li>Testez les différents accès avec les comptes créés</li>
                <li><strong>Supprimez ce fichier (initUsers.php) immédiatement</strong> pour des raisons de sécurité</li>
            </ol>

            <h2>Comptes de test créés:</h2>
            <div class="code">
                <strong>Admin (Accès Backoffice Complet):</strong><br>
                Email: admin@innogov.tn<br>
                Mot de passe: admin123456<br>
                <br>
                <strong>Agent (Accès Backoffice):</strong><br>
                Email: agent@innogov.tn<br>
                Mot de passe: agent123456<br>
                <br>
                <strong>Client - Citoyen (Accès Frontoffice):</strong><br>
                Email: ahmed@mail.tn<br>
                Mot de passe: user123456<br>
                <br>
                <strong>Client - Professionnel (Accès Frontoffice):</strong><br>
                Email: contact@entreprise.tn<br>
                Mot de passe: user123456<br>
            </div>

            <div class="links">
                <a href="../../VIEW/frontoffice/login.php" target="_blank">→ Aller à la page de connexion</a>
                <a href="../../VIEW/frontoffice/register.php" target="_blank">→ Aller à l'inscription</a>
            </div>

            <h2>Sécurité:</h2>
            <p style="color: #d32f2f; font-weight: bold;">
                ⚠️ IMPORTANT: Supprimez le fichier <code>CONFIG/initUsers.php</code> après initialisation!
            </p>
            <p>
                Ce fichier permet à n'importe qui d'initialiser ou de réinitialiser votre base de données.
                Il ne doit être accessible que pendant la phase de développement.
            </p>
        </div>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur lors de l'initialisation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .error-box {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 20px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .code {
                background: #f4f4f4;
                padding: 10px;
                border-left: 3px solid #d32f2f;
                font-family: monospace;
                margin: 10px 0;
                overflow-x: auto;
                word-break: break-all;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-box">
                <h1>✗ Erreur lors de l'initialisation</h1>
                <p>Une erreur est survenue lors de la création des utilisateurs.</p>
            </div>

            <h2>Détails de l'erreur:</h2>
            <div class="code">
                <?php echo htmlspecialchars($e->getMessage()); ?>
            </div>

            <h2>Solutions possibles:</h2>
            <ul>
                <li>Vérifiez que la base de données <code>innogov_db</code> existe</li>
                <li>Vérifiez que la table <code>utilisateurs</code> existe</li>
                <li>Vérifiez les droits d'accès à la base de données (user: root)</li>
                <li>Vérifiez que le serveur MySQL est lancé</li>
            </ul>

            <div style="margin-top: 30px;">
                <a href="initUsers.php" style="background: #007bff; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">← Retour</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
