<?php
/**
 * Connexion à la base de données via PDO
 * 
 * @author tamalou25
 * @version 1.0
 */

try {
    // Configuration PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];
    
    // Création de la connexion PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        $options
    );
    
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    if (DEBUG_MODE) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    } else {
        die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
    }
}