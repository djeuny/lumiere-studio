<?php
/* ============================================================
   GROUPE AUBE — Dashboard administrateur
   ============================================================ */
session_start();

// Vérifier que l'admin est connecté — sinon rediriger vers login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/database.php';

$pdo = getDB();

// Récupérer les statistiques
$stats = [];
$stats['total']    = $pdo->query("SELECT COUNT(*) FROM devis")->fetchColumn();
$stats['nouveau']  = $pdo->query("SELECT COUNT(*) FROM devis WHERE statut = 'nouveau'")->fetchColumn();
$stats['en_cours'] = $pdo->query("SELECT COUNT(*) FROM devis WHERE statut = 'en_cours'")->fetchColumn();
$stats['valide']   = $pdo->query("SELECT COUNT(*) FROM devis WHERE statut = 'valide'")->fetchColumn();

// Récupérer tous les devis avec filtrage optionnel
$filtre_statut = $_GET['statut'] ?? '';
$filtre_search = $_GET['search'] ?? '';

$sql = "SELECT * FROM devis WHERE 1=1";
$params = [];

if ($filtre_statut) {
    $sql .= " AND statut = ?";
    $params[] = $filtre_statut;
}

if ($filtre_search) {
    $sql .= " AND (prenom LIKE ? OR nom LIKE ? OR email LIKE ? OR type_prestation LIKE ?)";
    $terme = '%' . $filtre_search . '%';
    $params = array_merge($params, [$terme, $terme, $terme, $terme]);
}

$sql .= " ORDER BY date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$devis = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — Groupe Aube Propreté</title>
  <meta name="robots" content="noindex, nofollow"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <style>
    /* ── Layout dashboard ── */
    body { background: #070f1f; min-height: 100vh; }

    .dash-nav {
      background: var(--nuit);
      border-bottom: 1px solid rgba(91,163,217,0.1);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .dash-nav-left { display: flex; align-items: center; gap: 2rem; }

    .dash-nav .nav-logo { font-size: 1.2rem; }

    .dash-nav-title {
      font-size: 0.8rem;
      color: rgba(238,243,250,0.3);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      border-left: 1px solid rgba(91,163,217,0.2);
      padding-left: 2rem;
    }

    .dash-nav-right { display: flex; align-items: center; gap: 1.5rem; }

    .admin-badge {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.85rem;
      color: rgba(238,243,250,0.5);
    }

    .admin-avatar {
      width: 32px; height: 32px;
      background: var(--bleu-clair);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      color: var(--creme);
      font-weight: 600;
    }

    .btn-logout {
      color: rgba(238,243,250,0.3);
      text-decoration: none;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      transition: color 0.3s;
      border: 1px solid rgba(91,163,217,0.15);
      padding: 0.4rem 0.9rem;
      border-radius: 4px;
    }

    .btn-logout:hover { color: #e05a5a; border-color: rgba(224,90,90,0.3); }

    /* ── Contenu principal ── */
    .dash-content { padding: 2.5rem; }

    .dash-header { margin-bottom: 2.5rem; }

    .dash-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: var(--creme);
      margin-bottom: 0.3rem;
    }

    .dash-header p {
      color: rgba(238,243,250,0.3);
      font-size: 0.85rem;
    }

    /* ── Cartes de statistiques ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.2rem;
      margin-bottom: 2.5rem;
    }

    .stat-card {
      background: var(--nuit);
      border: 1px solid rgba(91,163,217,0.1);
      border-radius: 8px;
      padding: 1.5rem;
      transition: border-color 0.3s, transform 0.3s;
    }

    .stat-card:hover { border-color: var(--or); transform: translateY(-2px); }

    .stat-card-label {
      font-size: 0.72rem;
      color: rgba(238,243,250,0.3);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-bottom: 0.8rem;
    }

    .stat-card-value {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--creme);
      line-height: 1;
    }

    .stat-card-value.nouveau { color: var(--or); }
    .stat-card-value.en-cours { color: #f39c12; }
    .stat-card-value.valide { color: var(--vert-ok); }

    /* ── Filtres et recherche ── */
    .dash-filters {
      display: flex;
      gap: 1rem;
      align-items: center;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .filter-search {
      flex: 1;
      min-width: 200px;
      background: var(--nuit);
      border: 1px solid rgba(91,163,217,0.15);
      color: var(--creme);
      padding: 0.7rem 1rem;
      border-radius: 4px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.3s;
    }

    .filter-search:focus { border-color: var(--or); }
    .filter-search::placeholder { color: rgba(238,243,250,0.2); }

    .filter-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .filter-btn {
      padding: 0.6rem 1.2rem;
      background: var(--nuit);
      border: 1px solid rgba(91,163,217,0.15);
      border-radius: 4px;
      color: rgba(238,243,250,0.5);
      font-size: 0.8rem;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
      font-family: 'DM Sans', sans-serif;
    }

    .filter-btn:hover { border-color: var(--or); color: var(--or); }
    .filter-btn.active { background: rgba(91,163,217,0.1); border-color: var(--or); color: var(--or); }

    /* ── Tableau des devis ── */
    .devis-table-wrapper {
      background: var(--nuit);
      border: 1px solid rgba(91,163,217,0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    .devis-table-header {
      padding: 1.2rem 1.5rem;
      border-bottom: 1px solid rgba(91,163,217,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .devis-table-header h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.1rem;
      color: var(--creme);
    }

    .devis-count {
      font-size: 0.8rem;
      color: rgba(238,243,250,0.3);
      background: rgba(91,163,217,0.08);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead th {
      padding: 0.9rem 1.2rem;
      text-align: left;
      font-size: 0.7rem;
      font-weight: 500;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: rgba(238,243,250,0.3);
      border-bottom: 1px solid rgba(91,163,217,0.08);
      white-space: nowrap;
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
      color: rgba(238,243,250,0.7);
      vertical-align: middle;
    }

    .td-nom { color: var(--creme); font-weight: 500; }

    .td-email { font-size: 0.8rem; color: rgba(238,243,250,0.4); }

    .td-prestation {
      background: rgba(91,163,217,0.08);
      color: var(--or);
      padding: 0.25rem 0.7rem;
      border-radius: 3px;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      white-space: nowrap;
    }

    /* Badges de statut */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.3rem 0.8rem;
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

    .badge-nouveau  { background: rgba(91,163,217,0.1); color: var(--or); }
    .badge-en_cours { background: rgba(243,156,18,0.1); color: #f39c12; }
    .badge-valide   { background: rgba(46,204,113,0.1); color: var(--vert-ok); }
    .badge-refuse   { background: rgba(224,90,90,0.1);  color: #e05a5a; }

    /* Actions */
    .td-actions { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

    .action-select {
      background: rgba(91,163,217,0.08);
      border: 1px solid rgba(91,163,217,0.15);
      color: var(--creme);
      padding: 0.35rem 0.6rem;
      border-radius: 4px;
      font-size: 0.75rem;
      cursor: pointer;
      font-family: 'DM Sans', sans-serif;
      outline: none;
      transition: border-color 0.2s;
    }

    .action-select:focus { border-color: var(--or); }
    .action-select option { background: var(--nuit-accent); }

    .btn-delete {
      background: transparent;
      border: 1px solid rgba(224,90,90,0.2);
      color: rgba(224,90,90,0.5);
      padding: 0.35rem 0.6rem;
      border-radius: 4px;
      font-size: 0.75rem;
      cursor: pointer;
      font-family: 'DM Sans', sans-serif;
      transition: all 0.2s;
    }

    .btn-delete:hover { background: rgba(224,90,90,0.1); color: #e05a5a; border-color: #e05a5a; }

    .btn-voir {
      background: transparent;
      border: 1px solid rgba(91,163,217,0.2);
      color: rgba(238,243,250,0.4);
      padding: 0.35rem 0.6rem;
      border-radius: 4px;
      font-size: 0.75rem;
      cursor: pointer;
      font-family: 'DM Sans', sans-serif;
      transition: all 0.2s;
    }

    .btn-voir:hover { border-color: var(--or); color: var(--or); }

    /* Ligne vide */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: rgba(238,243,250,0.2);
    }

    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-state p { font-size: 0.9rem; }

    /* Modal détail devis */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(5,13,26,0.9);
      z-index: 200;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .modal-overlay.open { display: flex; }

    .modal {
      background: var(--nuit-accent);
      border: 1px solid rgba(91,163,217,0.15);
      border-radius: 8px;
      padding: 2.5rem;
      max-width: 600px;
      width: 100%;
      max-height: 80vh;
      overflow-y: auto;
      position: relative;
    }

    .modal-close {
      position: absolute;
      top: 1.2rem; right: 1.2rem;
      background: none;
      border: none;
      color: rgba(238,243,250,0.3);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.2s;
      line-height: 1;
    }

    .modal-close:hover { color: var(--creme); }

    .modal h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem;
      color: var(--creme);
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(91,163,217,0.1);
    }

    .modal-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .modal-field { }

    .modal-field-label {
      font-size: 0.7rem;
      color: rgba(238,243,250,0.3);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-bottom: 0.3rem;
    }

    .modal-field-value {
      font-size: 0.9rem;
      color: var(--creme);
    }

    .modal-message {
      grid-column: 1 / -1;
      margin-top: 0.5rem;
    }

    .modal-message .modal-field-value {
      background: var(--nuit);
      padding: 1rem;
      border-radius: 4px;
      border: 1px solid rgba(91,163,217,0.1);
      line-height: 1.6;
      color: rgba(238,243,250,0.6);
      font-size: 0.85rem;
    }

    /* Notification toast */
    .toast {
      position: fixed;
      bottom: 2rem; right: 2rem;
      background: var(--nuit-accent);
      border: 1px solid rgba(46,204,113,0.3);
      color: var(--vert-ok);
      padding: 1rem 1.5rem;
      border-radius: 4px;
      font-size: 0.85rem;
      z-index: 300;
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.3s;
    }

    .toast.show { transform: translateY(0); opacity: 1; }

    /* Responsive dashboard */
    @media (max-width: 900px) {
      .dash-content { padding: 1rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .dash-nav-title { display: none; }
      table { font-size: 0.78rem; }
      thead th:nth-child(4),
      tbody td:nth-child(4) { display: none; }
      .modal-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR DASHBOARD -->
  <nav class="dash-nav">
    <div class="dash-nav-left">
      <a href="../index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
      <span class="dash-nav-title">Tableau de bord</span>
    </div>
    <div class="dash-nav-right">
      <div class="admin-badge">
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_nom'], 0, 1)) ?></div>
        <?= htmlspecialchars($_SESSION['admin_nom']) ?>
      </div>
      <a href="logout.php" class="btn-logout">Déconnexion</a>
    </div>
  </nav>

  <!-- CONTENU -->
  <div class="dash-content">

    <!-- En-tête -->
    <div class="dash-header">
      <h1>Tableau de bord</h1>
      <p>Gérez les demandes de devis et de rendez-vous reçues.</p>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-label">Total reçus</div>
        <div class="stat-card-value"><?= $stats['total'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Nouveaux</div>
        <div class="stat-card-value nouveau"><?= $stats['nouveau'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">En cours</div>
        <div class="stat-card-value en-cours"><?= $stats['en_cours'] ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Validés</div>
        <div class="stat-card-value valide"><?= $stats['valide'] ?></div>
      </div>
    </div>

    <!-- Filtres -->
    <form method="GET" class="dash-filters">
      <input type="text" name="search" class="filter-search"
             placeholder="Rechercher par nom, email, prestation..."
             value="<?= htmlspecialchars($filtre_search) ?>"/>
      <div class="filter-btns">
        <a href="dashboard.php" class="filter-btn <?= !$filtre_statut ? 'active' : '' ?>">Tous</a>
        <a href="?statut=nouveau<?= $filtre_search ? '&search='.urlencode($filtre_search) : '' ?>"
           class="filter-btn <?= $filtre_statut === 'nouveau' ? 'active' : '' ?>">Nouveaux</a>
        <a href="?statut=en_cours<?= $filtre_search ? '&search='.urlencode($filtre_search) : '' ?>"
           class="filter-btn <?= $filtre_statut === 'en_cours' ? 'active' : '' ?>">En cours</a>
        <a href="?statut=valide<?= $filtre_search ? '&search='.urlencode($filtre_search) : '' ?>"
           class="filter-btn <?= $filtre_statut === 'valide' ? 'active' : '' ?>">Validés</a>
        <a href="?statut=refuse<?= $filtre_search ? '&search='.urlencode($filtre_search) : '' ?>"
           class="filter-btn <?= $filtre_statut === 'refuse' ? 'active' : '' ?>">Refusés</a>
        <?php if ($filtre_search): ?>
          <button type="submit" class="filter-btn active">🔍 Rechercher</button>
        <?php endif; ?>
      </div>
    </form>

    <!-- Tableau des devis -->
    <div class="devis-table-wrapper">
      <div class="devis-table-header">
        <h3>Demandes de devis</h3>
        <span class="devis-count"><?= count($devis) ?> résultat<?= count($devis) > 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($devis)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">📭</div>
          <p>Aucune demande trouvée pour ces critères.</p>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Client</th>
              <th>Prestation</th>
              <th>RDV</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($devis as $d): ?>
            <tr id="row-<?= $d['id'] ?>">
              <td style="color:rgba(238,243,250,0.2); font-size:0.75rem;">#<?= $d['id'] ?></td>
              <td>
                <div class="td-nom"><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></div>
                <div class="td-email"><?= htmlspecialchars($d['email']) ?></div>
                <?php if ($d['telephone']): ?>
                  <div class="td-email"><?= htmlspecialchars($d['telephone']) ?></div>
                <?php endif; ?>
              </td>
              <td><span class="td-prestation"><?= htmlspecialchars($d['type_prestation']) ?></span></td>
              <td>
                <?php if ($d['date_rdv']): ?>
                  <div style="font-size:0.8rem; color:var(--creme);">
                    <?= date('d/m/Y', strtotime($d['date_rdv'])) ?>
                  </div>
                  <div style="font-size:0.75rem; color:rgba(238,243,250,0.3);">
                    <?= $d['heure_rdv'] ? substr($d['heure_rdv'], 0, 5) : '' ?>
                  </div>
                <?php else: ?>
                  <span style="color:rgba(238,243,250,0.2); font-size:0.75rem;">—</span>
                <?php endif; ?>
              </td>
              <td style="font-size:0.78rem; color:rgba(238,243,250,0.3); white-space:nowrap;">
                <?= date('d/m/Y', strtotime($d['date_creation'])) ?><br>
                <?= date('H:i', strtotime($d['date_creation'])) ?>
              </td>
              <td>
                <span class="badge badge-<?= $d['statut'] ?>" id="badge-<?= $d['id'] ?>">
                  <?= ucfirst(str_replace('_', ' ', $d['statut'])) ?>
                </span>
              </td>
              <td>
                <div class="td-actions">
                  <!-- Bouton voir détail -->
                  <button class="btn-voir"
                    onclick="voirDetail(<?= htmlspecialchars(json_encode($d)) ?>)">
                    Voir
                  </button>
                  <!-- Changer statut -->
                  <select class="action-select"
                          onchange="changerStatut(<?= $d['id'] ?>, this.value, this)">
                    <option value="nouveau"  <?= $d['statut']==='nouveau'  ? 'selected':'' ?>>Nouveau</option>
                    <option value="en_cours" <?= $d['statut']==='en_cours' ? 'selected':'' ?>>En cours</option>
                    <option value="valide"   <?= $d['statut']==='valide'   ? 'selected':'' ?>>Validé</option>
                    <option value="refuse"   <?= $d['statut']==='refuse'   ? 'selected':'' ?>>Refusé</option>
                  </select>
                  <!-- Supprimer -->
                  <button class="btn-delete"
                          onclick="supprimerDevis(<?= $d['id'] ?>)">
                    ✕
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- MODAL DÉTAIL DEVIS -->
  <div class="modal-overlay" id="modal-overlay" onclick="fermerModal(event)">
    <div class="modal">
      <button class="modal-close" onclick="fermerModal()">×</button>
      <h3 id="modal-titre">Détail du devis</h3>
      <div class="modal-grid" id="modal-content"></div>
    </div>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div class="toast" id="toast"></div>

  <script>
  /* ── Changer le statut d'un devis ── */
  async function changerStatut(id, statut, selectEl) {
    try {
      const formData = new FormData();
      formData.append('id', id);
      formData.append('statut', statut);
      formData.append('action', 'update_statut');

      const res  = await fetch('update_statut.php', { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success) {
        /* Mettre à jour le badge de statut */
        const badge = document.getElementById('badge-' + id);
        badge.className = 'badge badge-' + statut;
        badge.textContent = statut.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        afficherToast('Statut mis à jour avec succès ✓');
      } else {
        afficherToast('Erreur lors de la mise à jour.', true);
      }
    } catch (err) {
      afficherToast('Erreur de connexion.', true);
    }
  }

  /* ── Supprimer un devis ── */
  async function supprimerDevis(id) {
    if (!confirm('Supprimer définitivement cette demande ?')) return;

    try {
      const formData = new FormData();
      formData.append('id', id);
      formData.append('action', 'delete');

      const res  = await fetch('update_statut.php', { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success) {
        /* Retirer la ligne du tableau avec animation */
        const row = document.getElementById('row-' + id);
        row.style.transition = 'opacity 0.3s, transform 0.3s';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 300);
        afficherToast('Demande supprimée ✓');
      } else {
        afficherToast('Erreur lors de la suppression.', true);
      }
    } catch (err) {
      afficherToast('Erreur de connexion.', true);
    }
  }

  /* ── Voir le détail d'un devis ── */
  function voirDetail(devis) {
    document.getElementById('modal-titre').textContent =
      'Devis #' + devis.id + ' — ' + devis.prenom + ' ' + devis.nom;

    document.getElementById('modal-content').innerHTML = `
      <div class="modal-field">
        <div class="modal-field-label">Prénom</div>
        <div class="modal-field-value">${devis.prenom}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Nom</div>
        <div class="modal-field-value">${devis.nom}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Email</div>
        <div class="modal-field-value">${devis.email}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Téléphone</div>
        <div class="modal-field-value">${devis.telephone || '—'}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Société</div>
        <div class="modal-field-value">${devis.societe || '—'}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Prestation</div>
        <div class="modal-field-value">${devis.type_prestation}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Superficie</div>
        <div class="modal-field-value">${devis.superficie || '—'}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Fréquence</div>
        <div class="modal-field-value">${devis.frequence || '—'}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Date RDV</div>
        <div class="modal-field-value">${devis.date_rdv || '—'} ${devis.heure_rdv ? 'à ' + devis.heure_rdv.substring(0,5) : ''}</div>
      </div>
      <div class="modal-field">
        <div class="modal-field-label">Statut</div>
        <div class="modal-field-value">
          <span class="badge badge-${devis.statut}">${devis.statut.replace('_',' ')}</span>
        </div>
      </div>
      <div class="modal-field modal-message">
        <div class="modal-field-label">Message</div>
        <div class="modal-field-value">${devis.message}</div>
      </div>
    `;

    document.getElementById('modal-overlay').classList.add('open');
  }

  /* ── Fermer la modal ── */
  function fermerModal(e) {
    if (!e || e.target === document.getElementById('modal-overlay')) {
      document.getElementById('modal-overlay').classList.remove('open');
    }
  }

  /* Fermer avec Echap */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fermerModal();
  });

  /* ── Afficher un toast de notification ── */
  function afficherToast(msg, erreur = false) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.style.borderColor = erreur ? 'rgba(224,90,90,0.3)' : 'rgba(46,204,113,0.3)';
    toast.style.color = erreur ? '#e05a5a' : 'var(--vert-ok)';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
  }
  </script>

</body>
</html>