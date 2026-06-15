<?php
/* ============================================================
   GROUPE AUBE PROPRETÉ — Connexion base de données
   Utilise PDO pour sécuriser les requêtes (protection injection SQL)
   ============================================================ */

// Paramètres de connexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'groupe_aube');
define('DB_USER', 'root');
define('DB_PASS', '');         // Laragon : mot de passe vide par défaut
define('DB_CHARSET', 'utf8mb4');

/**
 * Fonction qui retourne une connexion PDO sécurisée
 * On l'appelle avec : $pdo = getDB();
 */
function getDB() {
    // DSN = Data Source Name : chaîne qui décrit la connexion
    $dsn = "mysql:host=" . DB_HOST . 
           ";dbname=" . DB_NAME . 
           ";charset=" . DB_CHARSET;
    
    // Options PDO — important pour la sécurité
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Affiche les erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retourne des tableaux associatifs
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Vraies requêtes préparées
    ];
    
    try {
        // Tentative de connexion
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // En production, ne jamais afficher l'erreur au visiteur
        // On log l'erreur et on affiche un message générique
        error_log("Erreur BDD : " . $e->getMessage());
        die(json_encode([
            'success' => false, 
            'message' => 'Erreur de connexion à la base de données.'
        ]));
    }
}
?>