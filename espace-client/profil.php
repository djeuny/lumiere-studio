<?php
/* ============================================================
   GROUPE AUBE — Profil client
   ============================================================ */
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/csrf.php';

$pdo = getDB();
$client_id = $_SESSION['client_id'];

$stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
$stmt->execute([$client_id]);
$client = $stmt->fetch();

$succes = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = 'Session expirée.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profil') {
            $prenom    = trim($_POST['prenom'] ?? '');
            $nom       = trim($_POST['nom'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');

            if (empty($prenom) || empty($nom)) {
                $erreur = 'Prénom et nom sont obligatoires.';
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE clients SET prenom = ?, nom = ?, telephone = ? WHERE id = ?'
                );
                $stmt->execute([$prenom, $nom, $telephone, $client_id]);

                $_SESSION['client_prenom'] = $prenom;
                $succes = 'Profil mis à jour avec succès.';

                // Recharger les infos
                $stmt2 = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
                $stmt2->execute([$client_id]);
                $client = $stmt2->fetch();
            }

        } elseif ($action === 'update_password') {
            $ancien    = $_POST['ancien_password'] ?? '';
            $nouveau   = $_POST['nouveau_password'] ?? '';
            $confirm   = $_POST['confirm_password'] ?? '';

            if (!password_verify($ancien, $client['password'])) {
                $erreur = 'Ancien mot de passe incorrect.';
            } elseif (strlen($nouveau) < 8) {
                $erreur = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            } elseif ($nouveau !== $confirm) {
                $erreur = 'Les mots de passe ne correspondent pas.';
            } else {
                $hash = password_hash($nouveau, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('UPDATE clients SET password = ? WHERE id = ?');
                $stmt->execute([$hash, $client_id]);
                $succes = 'Mot de passe modifié avec succès.';
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
  <title>Mon profil — Groupe Aube Propreté</title>
  <meta name="robots" content="noindex, nofollow"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/style.css"/>
</head>
<body style="background:#070f1f; min-height:100vh;">

  <nav class="dash-nav">
    <div class="dash-nav-left">
      <a href="../index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
      <span class="dash-nav-title">Mon profil</span>
    </div>
    <div class="dash-nav-right">
      <a href="dashboard.php" class="btn-logout" style="border-color:rgba(91,163,217,0.2); color:rgba(238,243,250,0.4);">
        ← Mon tableau de bord
      </a>
      <a href="../deconnexion.php" class="btn-logout">Déconnexion</a>
    </div>
  </nav>

  <div class="dash-content">
    <div class="dash-header">
      <h1>Mon profil</h1>
      <p>Gérez vos informations personnelles et votre mot de passe.</p>
    </div>

    <?php if ($succes): ?>
      <div style="background:rgba(46,204,113,0.1); border:1px solid rgba(46,204,113,0.3); color:var(--vert-ok); padding:1rem 1.5rem; border-radius:4px; margin-bottom:2rem; font-size:0.9rem;">
        ✓ <?= htmlspecialchars($succes) ?>
      </div>
    <?php endif; ?>

    <?php if ($erreur): ?>
      <div class="erreur-msg" style="margin-bottom:2rem;">
        ⚠️ <?= htmlspecialchars($erreur) ?>
      </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

      <!-- Modifier les infos -->
      <div class="devis-table-wrapper" style="padding:2rem;">
        <h3 style="font-family:'Playfair Display',serif; color:var(--creme); margin-bottom:1.5rem; font-size:1.2rem;">
          Informations personnelles
        </h3>
        <form method="POST" class="form-nuit">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>
          <input type="hidden" name="action" value="update_profil"/>
          <div class="form-group">
            <label>Prénom *</label>
            <input type="text" name="prenom"
                   value="<?= htmlspecialchars($client['prenom']) ?>" required/>
          </div>
          <div class="form-group" style="margin-top:1rem;">
            <label>Nom *</label>
            <input type="text" name="nom"
                   value="<?= htmlspecialchars($client['nom']) ?>" required/>
          </div>
          <div class="form-group" style="margin-top:1rem;">
            <label>Téléphone</label>
            <input type="tel" name="telephone"
                   value="<?= htmlspecialchars($client['telephone'] ?? '') ?>"/>
          </div>
          <div class="form-group" style="margin-top:1rem;">
            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($client['email']) ?>" disabled
                   style="opacity:0.4; cursor:not-allowed;"/>
            <span style="font-size:0.72rem; color:rgba(238,243,250,0.2); margin-top:0.3rem; display:block;">
              L'email ne peut pas être modifié.
            </span>
          </div>
          <button type="submit" class="btn-submit" style="margin-top:1.5rem;">
            Enregistrer les modifications
          </button>
        </form>
      </div>

      <!-- Modifier le mot de passe -->
      <div class="devis-table-wrapper" style="padding:2rem;">
        <h3 style="font-family:'Playfair Display',serif; color:var(--creme); margin-bottom:1.5rem; font-size:1.2rem;">
          Modifier le mot de passe
        </h3>
        <form method="POST" class="form-nuit">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>
          <input type="hidden" name="action" value="update_password"/>
          <div class="form-group">
            <label>Ancien mot de passe *</label>
            <input type="password" name="ancien_password" placeholder="••••••••" required/>
          </div>
          <div class="form-group" style="margin-top:1rem;">
            <label>Nouveau mot de passe *</label>
            <input type="password" name="nouveau_password" placeholder="8 caractères min." required/>
          </div>
          <div class="form-group" style="margin-top:1rem;">
            <label>Confirmer *</label>
            <input type="password" name="confirm_password" placeholder="••••••••" required/>
          </div>
          <button type="submit" class="btn-submit" style="margin-top:1.5rem;">
            Changer le mot de passe
          </button>
        </form>
      </div>
    </div>

    <!-- Membre depuis -->
    <div style="margin-top:2rem; padding:1.2rem 1.5rem; background:var(--nuit); border:1px solid rgba(91,163,217,0.08); border-radius:4px; display:flex; gap:2rem; flex-wrap:wrap;">
      <div>
        <div style="font-size:0.7rem; color:rgba(238,243,250,0.3); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.3rem;">Membre depuis</div>
        <div style="font-size:0.9rem; color:var(--creme);">
          <?= date('d/m/Y', strtotime($client['date_creation'])) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.7rem; color:rgba(238,243,250,0.3); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.3rem;">Dernière connexion</div>
        <div style="font-size:0.9rem; color:var(--creme);">
          <?= $client['derniere_connexion'] ? date('d/m/Y à H:i', strtotime($client['derniere_connexion'])) : 'Première visite' ?>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/main.js"></script>
</body>
</html>