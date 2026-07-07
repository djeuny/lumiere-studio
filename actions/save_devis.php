<?php
/* ============================================================
   GROUPE AUBE PROPRETÉ — Sauvegarde d'un devis en base de données
   Ce fichier reçoit les données du formulaire via POST
   et les insère dans la table 'devis' de manière sécurisée
   ============================================================ */

// On autorise les requêtes depuis notre site (CORS simple)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// On inclut notre fichier de connexion
require_once '../config/database.php';
session_start();

/* ── 1. Vérifier que c'est bien une requête POST ── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

/* ── 2. Récupérer et nettoyer les données reçues ── */
// htmlspecialchars() protège contre les attaques XSS
// trim() supprime les espaces inutiles
function nettoyer($valeur) {
    return htmlspecialchars(strip_tags(trim($valeur)), ENT_QUOTES, 'UTF-8');
}

$prenom          = nettoyer($_POST['prenom'] ?? '');
$nom             = nettoyer($_POST['nom'] ?? '');
$email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telephone       = nettoyer($_POST['telephone'] ?? '');
$societe         = nettoyer($_POST['societe'] ?? '');
$type_prestation = nettoyer($_POST['sujet'] ?? '');
$superficie      = nettoyer($_POST['superficie'] ?? '');
$frequence       = nettoyer($_POST['frequence'] ?? '');
$message         = nettoyer($_POST['message'] ?? '');
$date_rdv        = nettoyer($_POST['date_rdv'] ?? '');
$heure_rdv       = nettoyer($_POST['heure_rdv'] ?? '');

/* ── 3. Valider les champs obligatoires ── */
$erreurs = [];

if (empty($prenom))          $erreurs[] = 'Le prénom est obligatoire.';
if (empty($nom))             $erreurs[] = 'Le nom est obligatoire.';
if (empty($email))           $erreurs[] = "L'email est obligatoire.";
if (empty($type_prestation)) $erreurs[] = 'Le type de prestation est obligatoire.';

// Validation format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

// Si des erreurs existent, on les retourne et on arrête
if (!empty($erreurs)) {
    echo json_encode(['success' => false, 'errors' => $erreurs]);
    exit;
}

/* ── 4. Insérer en base de données avec PDO ── */
try {
    $pdo = getDB();
    
    // Requête préparée — les ? sont des paramètres sécurisés
    // Jamais de variables directement dans la requête SQL !
    $sql = "INSERT INTO devis 
            (prenom, nom, email, telephone, societe, type_prestation, 
             superficie, frequence, message, date_rdv, heure_rdv) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
// Si le visiteur est déjà connecté, on récupère son ID
    $client_id = $_SESSION['client_id'] ?? null;

    $sql = "INSERT INTO devis 
            (client_id, prenom, nom, email, telephone, societe, type_prestation, 
             superficie, frequence, message, date_rdv, heure_rdv) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $client_id, $prenom, $nom, $email, $telephone, $societe,
        $type_prestation, $superficie, $frequence, $message,
        $date_rdv ?: null,
        $heure_rdv ?: null
    ]);
    
    // Récupérer l'ID du devis créé
    $devis_id = $pdo->lastInsertId();

    // On mémorise l'ID du devis en session pour pouvoir le relier
    // si le visiteur crée un compte juste après
    $_SESSION['dernier_devis_id'] = $devis_id;
    
    // Succès !
    echo json_encode([
        'success' => true,
        'message' => 'Votre demande a bien été enregistrée.',
        'devis_id' => $devis_id
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur insertion devis : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue. Veuillez réessayer.'
    ]);
}
?>
