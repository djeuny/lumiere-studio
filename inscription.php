<?php
/* ============================================================
   GROUPE AUBE — Inscription client
   ============================================================ */
session_start();
require_once 'config/database.php';
require_once 'includes/csrf.php';

// Si déjà connecté, rediriger vers l'espace client
if (isset($_SESSION['client_id'])) {
    header('Location: espace-client/dashboard.php');
    exit;
}

$erreur = '';
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérification CSRF en premier
    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $prenom    = trim($_POST['prenom'] ?? '');
        $nom       = trim($_POST['nom'] ?? '');
        $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $telephone = trim($_POST['telephone'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Validations
        if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
            $erreur = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "L'adresse email n'est pas valide.";
        } elseif (strlen($password) < 8) {
            $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($password !== $password2) {
            $erreur = 'Les mots de passe ne correspondent pas.';
        } else {
            try {
                $pdo = getDB();

                // Vérifier que l'email n'existe pas déjà
                $check = $pdo->prepare('SELECT id FROM clients WHERE email = ?');
                $check->execute([$email]);

                if ($check->fetch()) {
                    $erreur = 'Un compte existe déjà avec cet email. <a href="connexion.php">Se connecter</a>';
                } else {
                    // Hash sécurisé du mot de passe
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);

                    $stmt = $pdo->prepare(
                        'INSERT INTO clients (prenom, nom, email, telephone, password) VALUES (?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([$prenom, $nom, $email, $telephone, $password_hash]);

                    $client_id = $pdo->lastInsertId();

                    // Connexion automatique après inscription
                    $_SESSION['client_id']     = $client_id;
                    $_SESSION['client_prenom'] = $prenom;
                    $_SESSION['client_email']  = $email;

                    // Si l'inscription vient juste après un devis, on relie le devis au compte
                    if (!empty($_SESSION['dernier_devis_id'])) {
                        $link = $pdo->prepare('UPDATE devis SET client_id = ? WHERE id = ?');
                        $link->execute([$client_id, $_SESSION['dernier_devis_id']]);
                        unset($_SESSION['dernier_devis_id']);
                    }

                    header('Location: espace-client/dashboard.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('Erreur inscription : ' . $e->getMessage());
                $erreur = 'Une erreur est survenue. Veuillez réessayer.';
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
  <title>Créer mon compte — Groupe Aube Propreté</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body class="auth-body">
  <div class="auth-wrapper">
    <div class="auth-logo">
      <a href="index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
    </div>
    <div class="auth-card">
      <h1>Créer mon espace client</h1>
      <p>Suivez vos demandes de devis et rendez-vous en un coup d'œil.</p>

      <?php if ($erreur): ?>
        <div class="erreur-msg">⚠️ <?= $erreur ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="form-nuit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>

        <div class="form-row">
          <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" placeholder="Marie"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required/>
          </div>
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" placeholder="Dupont"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required/>
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" placeholder="marie@exemple.fr"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
        </div>

        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="tel" id="telephone" name="telephone" placeholder="06 00 00 00 00"
                 value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"/>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Mot de passe *</label>
            <input type="password" id="password" name="password" placeholder="8 caractères min." required/>
          </div>
          <div class="form-group">
            <label for="password2">Confirmer *</label>
            <input type="password" id="password2" name="password2" placeholder="••••••••" required/>
          </div>
        </div>

        <button type="submit" class="btn-login">Créer mon compte →</button>
      </form>

      <p class="auth-switch">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </div>
    <div class="login-back">
      <a href="index.html">← Retour au site</a>
    </div>
  </div>
</body>
</html>