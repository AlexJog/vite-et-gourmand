<?php
// Configuration Heroku
session_start();

// Définir l'URL de base
define('BASE_URL', '/');

// Récupérer les variables d'environnement Heroku (ClearDB)
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

define('DB_HOST', $url["host"]);
define('DB_NAME', substr($url["path"], 1));
define('DB_USER', $url["user"]);
define('DB_PASS', $url["pass"]);

// Connexion PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>