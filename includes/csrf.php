<?php
/* ============================================================
   GROUPE AUBE — Protection CSRF réutilisable
   À inclure sur CHAQUE formulaire sensible (inscription, 
   connexion, devis, RDV...)
   ============================================================ */

/**
 * Génère un token CSRF et le stocke en session
 * S'il en existe déjà un, on le réutilise (évite de casser
 * un formulaire ouvert dans plusieurs onglets)
 */
function genererTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie que le token reçu correspond à celui en session
 * Utilise hash_equals() pour éviter les attaques "timing"
 */
function verifierTokenCSRF($token_recu) {
    if (empty($_SESSION['csrf_token']) || empty($token_recu)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token_recu);
}
?>