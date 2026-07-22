<?php
/* ============================================================
   GROUPE AUBE — Dashboard espace client
   ============================================================ */
session_start();

// Vérifier que le client est connecté
if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}

require_once '../config/database.php';
$pdo = getDB();

$client_id = $_SESSION['client_id'];

// Récupérer les infos du client
$stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
$stmt->execute([$client_id]);
$client = $stmt->fetch();

// Récupérer tous les devis du client
$stmt2 = $pdo->prepare('SELECT * FROM devis WHERE client_id = ? ORDER BY date_creation DESC');
$stmt2->execute([$client_id]);
$devis = $stmt2->fetchAll();

// Statistiques rapides
$total_devis    = count($devis);
$devis_nouveau  = count(array_filter($devis, fn($d) => $d['statut'] === 'nouveau'));
$devis_en_cours = count(array_filter($devis, fn($d) => $d['statut'] === 'en_cours'));
$devis_valide   = count(array_filter($devis, fn($d) => $d['statut'] === 'valide'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mon espace — Groupe Aube Propreté</title>
  <meta name="robots" content="noindex, nofollow"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/style.css"/>
</head>
<body style="background:#070f1f; min-height:100vh;">

  <!-- NAVBAR ESPACE CLIENT -->
  <nav class="dash-nav">
    <div class="dash-nav-left">
      <a href="../index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
      <span class="dash-nav-title">Mon espace</span>
    </div>
    <div class="dash-nav-right">
      <div class="admin-badge">
        <div class="admin-avatar">
          <?= strtoupper(substr($client['prenom'], 0, 1)) ?>
        </div>
        <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
      </div>
      <a href="profil.php" class="btn-logout" style="border-color:rgba(91,163,217,0.2); color:rgba(238,243,250,0.4);">
        Mon profil
      </a>
      <a href="../deconnexion.php" class="btn-logout">Déconnexion</a>
    </div>
  </nav>

  <div class="dash-content">

    <!-- En-tête -->
    <div class="dash-header">
      <h1>Bonjour, <?= htmlspecialchars($client['prenom']) ?> 👋</h1>
      <p>Suivez l'avancement de vos demandes de devis et rendez-vous.</p>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-label">Total demandes</div>
        <div class="stat-card-value"><?= $total_devis ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">En attente</div>
        <div class="stat-card-value nouveau"><?= $devis_nouveau ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">En cours</div>
        <div class="stat-card-value en-cours"><?= $devis_en_cours ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Validés</div>
        <div class="stat-card-value valide"><?= $devis_valide ?></div>
      </div>
    </div>

    <!-- Bouton nouveau devis -->
    <div style="margin-bottom: 1.5rem;">
      <a href="../devis-pro.php" class="btn-primary">+ Nouvelle demande de devis</a>
    </div>

    <!-- Tableau des devis -->
    <div class="devis-table-wrapper">
      <div class="devis-table-header">
        <h3>Mes demandes</h3>
        <span class="devis-count">
          <?= $total_devis ?> demande<?= $total_devis > 1 ? 's' : '' ?>
        </span>
      </div>

      <?php if (empty($devis)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">📋</div>
          <p>Vous n'avez pas encore de demande de devis.</p>
          <div style="margin-top:1.5rem;">
            <a href="../devis-pro.php" class="btn-primary">Faire une demande</a>
          </div>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Prestation</th>
              <th>RDV souhaité</th>
              <th>Date demande</th>
              <th>Statut</th>
              <th>Message</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($devis as $d): ?>
            <tr>
              <td style="color:rgba(238,243,250,0.2); font-size:0.75rem;">
                #<?= $d['id'] ?>
              </td>
              <td>
                <span class="td-prestation">
                  <?= htmlspecialchars($d['type_prestation']) ?>
                </span>
                <?php if ($d['superficie']): ?>
                  <div style="font-size:0.75rem; color:rgba(238,243,250,0.3); margin-top:0.3rem;">
                    <?= htmlspecialchars($d['superficie']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($d['date_rdv']): ?>
                  <div style="font-size:0.85rem; color:var(--creme);">
                    <?= date('d/m/Y', strtotime($d['date_rdv'])) ?>
                  </div>
                  <?php if ($d['heure_rdv']): ?>
                    <div style="font-size:0.75rem; color:rgba(238,243,250,0.3);">
                      à <?= substr($d['heure_rdv'], 0, 5) ?>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:rgba(238,243,250,0.2); font-size:0.75rem;">—</span>
                <?php endif; ?>
              </td>
              <td style="font-size:0.8rem; color:rgba(238,243,250,0.3);">
                <?= date('d/m/Y', strtotime($d['date_creation'])) ?>
              </td>
              <td>
                <span class="badge badge-<?= $d['statut'] ?>">
                  <?= ucfirst(str_replace('_', ' ', $d['statut'])) ?>
                </span>
              </td>
              <td style="font-size:0.82rem; color:rgba(238,243,250,0.4); max-width:200px;">
                <?php if ($d['message']): ?>
                  <?= htmlspecialchars(substr($d['message'], 0, 80)) ?>
                  <?= strlen($d['message']) > 80 ? '...' : '' ?>
                <?php else: ?>
                  <span style="color:rgba(238,243,250,0.2);">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- Bloc "Créer un compte après un devis" -->
    <?php if (!empty($_SESSION['dernier_devis_id'])): ?>
    <div class="devis-cta-banner" style="margin-top:2rem;">
      <div class="devis-cta-banner-text">
        <h4>Votre devis a bien été enregistré !</h4>
        <p>Il est maintenant lié à votre espace client. Vous pouvez suivre son avancement ici.</p>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <script src="../assets/js/main.js"></script>
</body>
</html>