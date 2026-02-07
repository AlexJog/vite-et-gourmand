<?php
// Détection de l'environnement
if (getenv("JAWSDB_URL")) {
    // Environnement Heroku
    require_once __DIR__ . '/config-heroku.php';
} else {
    // Environnement local
    session_start();
    
    define('BASE_URL', '/ViteEtGourmand/');
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'vite_gourmand');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    
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
}
?>