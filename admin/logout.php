<?php
/* ============================================================
   GROUPE AUBE — Déconnexion administrateur
   ============================================================ */
session_start();

// Détruire toutes les données de session
$_SESSION = [];
session_destroy();

// Rediriger vers le login
header('Location: index.php');
exit;
?>