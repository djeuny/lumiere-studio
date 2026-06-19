<?php
/* ============================================================
   GROUPE AUBE — Mise à jour statut ou suppression d'un devis
   ============================================================ */
session_start();
header('Content-Type: application/json');

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

require_once '../config/database.php';

$action = $_POST['action'] ?? '';
$id     = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID invalide.']);
    exit;
}

try {
    $pdo = getDB();

    if ($action === 'update_statut') {
        $statuts_valides = ['nouveau', 'en_cours', 'valide', 'refuse'];
        $statut = $_POST['statut'] ?? '';

        if (!in_array($statut, $statuts_valides)) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE devis SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);

        echo json_encode(['success' => true, 'message' => 'Statut mis à jour.']);

    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM devis WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Devis supprimé.']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
    }

} catch (PDOException $e) {
    error_log("Erreur update_statut : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur base de données.']);
}
?>