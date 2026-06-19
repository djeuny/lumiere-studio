<?php
/* ============================================================
   GROUPE AUBE — Page de connexion administrateur
   ============================================================ */
session_start();

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../config/database.php';

$erreur = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $pdo = getDB();
            
            // Chercher l'admin par son nom d'utilisateur
            $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            // Vérifier le mot de passe avec password_verify()
            if ($admin && password_verify($password, $admin['password'])) {
                // Connexion réussie — créer la session
                $_SESSION['admin_id']  = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['admin_user']= $admin['username'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $erreur = 'Identifiant ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $erreur = 'Erreur de connexion à la base de données.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administration — Groupe Aube Propreté</title>
  <meta name="robots" content="noindex, nofollow"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <style>
    body { background: var(--nuit); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-wrapper { width: 100%; max-width: 420px; padding: 2rem; }
    .login-logo { text-align: center; margin-bottom: 2.5rem; }
    .login-logo .nav-logo { font-size: 1.8rem; }
    .login-card {
      background: var(--nuit-accent);
      border: 1px solid rgba(91,163,217,0.15);
      border-radius: 8px;
      padding: 2.5rem;
    }
    .login-card h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: var(--creme);
      margin-bottom: 0.5rem;
    }
    .login-card p {
      color: rgba(238,243,250,0.4);
      font-size: 0.85rem;
      margin-bottom: 2rem;
    }
    .erreur-msg {
      background: rgba(224,90,90,0.1);
      border: 1px solid rgba(224,90,90,0.3);
      color: #e05a5a;
      padding: 0.8rem 1rem;
      border-radius: 4px;
      font-size: 0.85rem;
      margin-bottom: 1.5rem;
    }
    .form-group label { color: rgba(238,243,250,0.4); }
    .form-group input {
      background: var(--nuit);
      border: 1px solid rgba(91,163,217,0.15);
      color: var(--creme);
      width: 100%;
    }
    .form-group input:focus { border-color: var(--or); }
    .btn-login {
      width: 100%;
      background: var(--or);
      color: var(--noir);
      border: none;
      padding: 1rem;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 1.5rem;
      transition: background 0.3s, transform 0.2s;
    }
    .btn-login:hover { background: var(--or-clair); transform: translateY(-2px); }
    .login-back {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.8rem;
    }
    .login-back a { color: rgba(238,243,250,0.3); text-decoration: none; transition: color 0.3s; }
    .login-back a:hover { color: var(--or); }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-logo">
      <a href="../index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
    </div>
    <div class="login-card">
      <h1>Espace Administration</h1>
      <p>Connectez-vous pour accéder au tableau de bord.</p>

      <?php if ($erreur): ?>
        <div class="erreur-msg">⚠️ <?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="username">Identifiant</label>
          <input type="text" id="username" name="username"
                 placeholder="admin"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 required autofocus/>
        </div>
        <div class="form-group" style="margin-top:1rem;">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password"
                 placeholder="••••••••" required/>
        </div>
        <button type="submit" class="btn-login">Se connecter →</button>
      </form>
    </div>
    <div class="login-back">
      <a href="../index.html">← Retour au site</a>
    </div>
  </div>
</body>
</html>