<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Config {
    private static $pdo;
    private static $checked = false;

    public static function getConnexion() {
        if (!self::$checked) {
            self::$checked = true;
            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = '2a27';

            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Erreur connexion DB: ' . $e->getMessage());
                self::$pdo = null;
            }
        }

        return self::$pdo;
    }
}
?>
