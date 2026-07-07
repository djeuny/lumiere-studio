<?php
/* ============================================================
   GROUPE AUBE — Connexion client
   ============================================================ */
session_start();
require_once 'config/database.php';
require_once 'includes/csrf.php';

if (isset($_SESSION['client_id'])) {
    header('Location: espace-client/dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $erreur = 'Veuillez remplir tous les champs.';
        } else {
            try {
                $pdo = getDB();
                $stmt = $pdo->prepare('SELECT * FROM clients WHERE email = ?');
                $stmt->execute([$email]);
                $client = $stmt->fetch();

                if ($client && password_verify($password, $client['password'])) {
                    $_SESSION['client_id']     = $client['id'];
                    $_SESSION['client_prenom'] = $client['prenom'];
                    $_SESSION['client_email']  = $client['email'];

                    // Mettre à jour la dernière connexion
                    $update = $pdo->prepare('UPDATE clients SET derniere_connexion = NOW() WHERE id = ?');
                    $update->execute([$client['id']]);

                    // Relier un éventuel devis fait juste avant
                    if (!empty($_SESSION['dernier_devis_id'])) {
                        $link = $pdo->prepare('UPDATE devis SET client_id = ? WHERE id = ?');
                        $link->execute([$client['id'], $_SESSION['dernier_devis_id']]);
                        unset($_SESSION['dernier_devis_id']);
                    }

                    header('Location: espace-client/dashboard.php');
                    exit;
                } else {
                    $erreur = 'Email ou mot de passe incorrect.';
                }
            } catch (PDOException $e) {
                error_log('Erreur connexion : ' . $e->getMessage());
                $erreur = 'Une erreur est survenue.';
            }
        }
    }
}

$csrf_token = genererTokenCSRF();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion — Groupe Aube Propreté</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body class="auth-body">
  <div class="auth-wrapper">
    <div class="auth-logo">
      <a href="index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
    </div>
    <div class="auth-card">
      <h1>Mon espace client</h1>
      <p>Connectez-vous pour suivre vos demandes.</p>

      <?php if ($erreur): ?>
        <div class="erreur-msg">⚠️ <?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="form-nuit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="marie@exemple.fr" required autofocus/>
        </div>
        <div class="form-group" style="margin-top:1rem;">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required/>
        </div>

        <button type="submit" class="btn-login">Se connecter →</button>
      </form>

      <p class="auth-switch">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
    </div>
    <div class="login-back">
      <a href="index.html">← Retour au site</a>
    </div>
  </div>
</body>
</html>