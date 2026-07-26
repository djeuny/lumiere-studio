<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}
require_once '../config/database.php';
$pdo = getDB();
$client_id = $_SESSION['client_id'];

$stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?');
$stmt->execute([$client_id]);
$client = $stmt->fetch();

$stmt2 = $pdo->prepare('SELECT * FROM devis WHERE client_id = ? ORDER BY date_creation DESC');
$stmt2->execute([$client_id]);
$devis = $stmt2->fetchAll();

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
  <style>
    /* ── Reset et base dashboard client ── */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #070f1f;
      min-height: 100vh;
      font-family: 'DM Sans', sans-serif;
      color: #eef3fa;
    }

    /* ── Navbar ── */
    .client-nav {
      background: #050d1a;
      border-bottom: 1px solid rgba(91,163,217,0.1);
      padding: 1rem 2.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .client-nav-logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      font-weight: 700;
      color: #eef3fa;
      text-decoration: none;
    }

    .client-nav-logo span { color: #5ba3d9; }

    .client-nav-subtitle {
      font-size: 0.75rem;
      color: rgba(238,243,250,0.3);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-left: 1.5rem;
      padding-left: 1.5rem;
      border-left: 1px solid rgba(91,163,217,0.2);
    }

    .client-nav-right {
      display: flex;
      align-items: center;
      gap: 1.2rem;
    }

    .client-avatar {
      width: 36px; height: 36px;
      background: #1a4a8a;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.9rem;
      color: #eef3fa;
      flex-shrink: 0;
    }

    .client-name {
      font-size: 0.88rem;
      color: rgba(238,243,250,0.6);
    }

    .client-nav-btn {
      color: rgba(238,243,250,0.4);
      text-decoration: none;
      font-size: 0.8rem;
      border: 1px solid rgba(91,163,217,0.2);
      padding: 0.4rem 1rem;
      border-radius: 4px;
      transition: all 0.2s;
      letter-spacing: 0.03em;
    }

    .client-nav-btn:hover {
      border-color: rgba(91,163,217,0.5);
      color: #eef3fa;
    }

    .client-nav-btn.deconnexion:hover {
      border-color: rgba(224,90,90,0.4);
      color: #e05a5a;
    }

    /* ── Contenu ── */
    .client-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2.5rem 2rem;
    }

    /* ── En-tête ── */
    .client-header {
      margin-bottom: 2.5rem;
    }

    .client-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      color: #eef3fa;
      margin-bottom: 0.4rem;
    }

    .client-header p {
      color: rgba(238,243,250,0.4);
      font-size: 0.9rem;
    }

    /* ── Grille de stats ── */
    .client-stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.2rem;
      margin-bottom: 2rem;
    }

    .client-stat-card {
      background: #0a1628;
      border: 1px solid rgba(91,163,217,0.12);
      border-radius: 8px;
      padding: 1.5rem;
      transition: border-color 0.3s, transform 0.2s;
    }

    .client-stat-card:hover {
      border-color: #5ba3d9;
      transform: translateY(-2px);
    }

    .client-stat-label {
      font-size: 0.7rem;
      color: rgba(238,243,250,0.3);
      letter-spacing: 0.12em;
      text-transform: uppercase;
      margin-bottom: 0.8rem;
    }

    .client-stat-value {
      font-family: 'Playfair Display', serif;
      font-size: 2.8rem;
      font-weight: 700;
      line-height: 1;
      color: #eef3fa;
    }

    .client-stat-value.bleu   { color: #5ba3d9; }
    .client-stat-value.orange { color: #f39c12; }
    .client-stat-value.vert   { color: #2ecc71; }

    /* ── Bouton CTA ── */
    .client-cta-btn {
      display: inline-block;
      background: #0d2d5e;
      color: #eef3fa;
      text-decoration: none;
      padding: 0.85rem 2rem;
      border-radius: 4px;
      font-size: 0.88rem;
      font-weight: 500;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-bottom: 2rem;
      transition: background 0.3s, transform 0.2s;
    }

    .client-cta-btn:hover {
      background: #5ba3d9;
      color: #0a1628;
      transform: translateY(-2px);
    }

    /* ── Tableau ── */
    .client-table-wrapper {
      background: #0a1628;
      border: 1px solid rgba(91,163,217,0.1);
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .client-table-header {
      padding: 1.2rem 1.5rem;
      border-bottom: 1px solid rgba(91,163,217,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .client-table-header h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      color: #eef3fa;
    }

    .client-table-count {
      background: rgba(91,163,217,0.08);
      color: rgba(238,243,250,0.4);
      font-size: 0.78rem;
      padding: 0.3rem 0.9rem;
      border-radius: 20px;
    }

    table { width: 100%; border-collapse: collapse; }

    thead th {
      padding: 0.9rem 1.2rem;
      text-align: left;
      font-size: 0.68rem;
      font-weight: 500;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: rgba(238,243,250,0.25);
      border-bottom: 1px solid rgba(91,163,217,0.08);
    }

    tbody tr {
      border-bottom: 1px solid rgba(91,163,217,0.05);
      transition: background 0.2s;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(91,163,217,0.04); }

    tbody td {
      padding: 1rem 1.2rem;
      font-size: 0.88rem;
      color: rgba(238,243,250,0.65);
      vertical-align: middle;
    }

    /* Badges statut */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.3rem 0.85rem;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 500;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .badge::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: currentColor;
    }

    .badge-nouveau  { background: rgba(91,163,217,0.12);  color: #5ba3d9; }
    .badge-en_cours { background: rgba(243,156,18,0.12);  color: #f39c12; }
    .badge-valide   { background: rgba(46,204,113,0.12);  color: #2ecc71; }
    .badge-refuse   { background: rgba(224,90,90,0.12);   color: #e05a5a; }

    .prestation-tag {
      background: rgba(91,163,217,0.08);
      color: #5ba3d9;
      padding: 0.25rem 0.7rem;
      border-radius: 3px;
      font-size: 0.75rem;
      letter-spacing: 0.04em;
    }

    /* État vide */
    .client-empty {
      text-align: center;
      padding: 4rem 2rem;
      color: rgba(238,243,250,0.2);
    }

    .client-empty-icon { font-size: 3rem; margin-bottom: 1rem; }
    .client-empty p { font-size: 0.9rem; margin-bottom: 1.5rem; }

    /* Bannière devis enregistré */
    .devis-registered-banner {
      background: rgba(46,204,113,0.06);
      border: 1px solid rgba(46,204,113,0.2);
      border-radius: 8px;
      padding: 1.2rem 1.8rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .devis-registered-banner-icon { font-size: 1.5rem; }

    .devis-registered-banner h4 {
      font-family: 'Playfair Display', serif;
      color: #2ecc71;
      font-size: 1rem;
      margin-bottom: 0.2rem;
    }

    .devis-registered-banner p {
      color: rgba(238,243,250,0.4);
      font-size: 0.82rem;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .client-nav { padding: 1rem 1.2rem; }
      .client-nav-subtitle { display: none; }
      .client-content { padding: 1.5rem 1rem; }
      .client-stats { grid-template-columns: repeat(2, 1fr); }
      .client-name { display: none; }
    }

    @media (max-width: 480px) {
      .client-stats { grid-template-columns: 1fr 1fr; gap: 0.8rem; }
      .client-stat-value { font-size: 2rem; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="client-nav">
    <div style="display:flex; align-items:center;">
      <a href="../index.html" class="client-nav-logo">Groupe<span>.</span>Aube</a>
      <span class="client-nav-subtitle">Mon espace</span>
    </div>
    <div class="client-nav-right">
      <div class="client-avatar">
        <?= strtoupper(substr($client['prenom'], 0, 1)) ?>
      </div>
      <span class="client-name">
        <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
      </span>
      <a href="profil.php" class="client-nav-btn">Mon profil</a>
      <a href="../deconnexion.php" class="client-nav-btn deconnexion">Déconnexion</a>
    </div>
  </nav>

  <!-- CONTENU -->
  <div class="client-content">

    <!-- En-tête -->
    <div class="client-header">
      <h1>Bonjour, <?= htmlspecialchars($client['prenom']) ?> 👋</h1>
      <p>Suivez l'avancement de vos demandes de devis et rendez-vous.</p>
    </div>

    <!-- Bannière devis enregistré -->
    <?php if (!empty($_SESSION['dernier_devis_id'])): ?>
    <div class="devis-registered-banner">
      <div class="devis-registered-banner-icon">✅</div>
      <div>
        <h4>Votre devis a bien été enregistré !</h4>
        <p>Il est maintenant lié à votre espace client. Vous pouvez suivre son avancement ici.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="client-stats">
      <div class="client-stat-card">
        <div class="client-stat-label">Total demandes</div>
        <div class="client-stat-value"><?= $total_devis ?></div>
      </div>
      <div class="client-stat-card">
        <div class="client-stat-label">En attente</div>
        <div class="client-stat-value bleu"><?= $devis_nouveau ?></div>
      </div>
      <div class="client-stat-card">
        <div class="client-stat-label">En cours</div>
        <div class="client-stat-value orange"><?= $devis_en_cours ?></div>
      </div>
      <div class="client-stat-card">
        <div class="client-stat-label">Validés</div>
        <div class="client-stat-value vert"><?= $devis_valide ?></div>
      </div>
    </div>

    <!-- Bouton nouveau devis -->
    <a href="../devis-pro.php" class="client-cta-btn">+ Nouvelle demande de devis</a>

    <!-- Tableau -->
    <div class="client-table-wrapper">
      <div class="client-table-header">
        <h3>Mes demandes</h3>
        <span class="client-table-count">
          <?= $total_devis ?> demande<?= $total_devis > 1 ? 's' : '' ?>
        </span>
      </div>

      <?php if (empty($devis)): ?>
        <div class="client-empty">
          <div class="client-empty-icon">📋</div>
          <p>Vous n'avez pas encore de demande de devis.</p>
          <a href="../devis-pro.php" class="client-cta-btn">Faire une première demande</a>
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
              <td style="color:rgba(238,243,250,0.2); font-size:0.75rem;">#<?= $d['id'] ?></td>
              <td>
                <span class="prestation-tag"><?= htmlspecialchars($d['type_prestation']) ?></span>
                <?php if ($d['superficie']): ?>
                  <div style="font-size:0.75rem; color:rgba(238,243,250,0.25); margin-top:0.4rem;">
                    <?= htmlspecialchars($d['superficie']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($d['date_rdv']): ?>
                  <div style="font-size:0.85rem; color:#eef3fa;">
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
              <td style="font-size:0.82rem; color:rgba(238,243,250,0.4); max-width:180px;">
                <?php if ($d['message']): ?>
                  <?= htmlspecialchars(substr($d['message'], 0, 80)) ?>
                  <?= strlen($d['message']) > 80 ? '...' : '' ?>
                <?php else: ?>
                  <span style="color:rgba(238,243,250,0.15);">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>

  <script src="../assets/js/main.js"></script>
</body>
</html>