<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Devis Professionnel — Groupe Aube Propreté</title>
  <meta name="description" content="Obtenez une estimation de prix instantanée et réservez votre créneau de consultation avec Groupe Aube Propreté."/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    .fade-in {
      opacity: 0; transform: translateY(20px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .fade-in.visible { opacity: 1; transform: translateY(0); }
  </style>
</head>
<body>

  <!-- NAVIGATION THÈME NUIT -->
  <nav class="nav-nuit">
    <a href="index.html" class="nav-logo">Groupe<span>.</span>Aube</a>
    <button class="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <ul class="nav-links">
      <li><a href="services.html">Services</a></li>
      <li><a href="about.html">À propos</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="devis-pro.php" class="active nav-cta">Devis Pro</a></li>
    </ul>
  </nav>

  <!-- HERO IMMERSIF -->
  <section class="hero-nuit">
    <div class="hero-nuit-content fade-in">
      <div class="hero-nuit-badge">
        ✦ Estimation instantanée & Réservation en ligne
      </div>
      <h1>
        Votre espace,
        <em>notre expertise.</em>
      </h1>
      <p>Calculez le coût estimatif de votre prestation en quelques clics, puis réservez directement votre créneau de consultation gratuite avec notre équipe.</p>
      <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="#calculateur" class="btn-primary">Calculer mon prix →</a>
        <a href="#rdv" class="btn-secondary" style="color:rgba(238,243,250,0.6);">Prendre RDV directement</a>
      </div>
      <div class="hero-nuit-stats">
        <div class="hero-stat">
          <div class="hero-stat-value">300+</div>
          <div class="hero-stat-label">Sites nettoyés</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-value">15 ans</div>
          <div class="hero-stat-label">D'expérience</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-value">98%</div>
          <div class="hero-stat-label">Clients satisfaits</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-value">24h</div>
          <div class="hero-stat-label">Réponse garantie</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CALCULATEUR DE PRIX -->
  <section class="calculateur" id="calculateur">
    <div style="text-align:center; margin-bottom:4rem;">
      <span class="section-label">Estimation instantanée</span>
      <h2 class="section-title" style="color:var(--creme);">Calculez votre prix</h2>
      <p style="color:rgba(238,243,250,0.4); max-width:500px; margin:1rem auto 0; font-size:0.95rem; font-weight:300;">
        Obtenez une fourchette de prix en fonction de vos besoins. Devis définitif après consultation gratuite.
      </p>
    </div>
    <div class="calculateur-wrapper">

      <!-- Colonne gauche : options -->
      <div class="calc-options">

        <!-- Type de prestation -->
        <div class="calc-group">
          <span class="calc-group-title">1. Type de prestation</span>
          <div class="option-btns" id="opt-prestation">
            <button class="option-btn selected" data-val="nettoyage" data-prix="15">Nettoyage standard</button>
            <button class="option-btn" data-val="profondeur" data-prix="25">En profondeur</button>
            <button class="option-btn" data-val="desinfection" data-prix="30">Désinfection</button>
            <button class="option-btn" data-val="ecologique" data-prix="20">Écologique</button>
            <button class="option-btn" data-val="sols" data-prix="18">Sols & Tapis</button>
            <button class="option-btn" data-val="copropriete" data-prix="12">Copropriété</button>
          </div>
        </div>

        <!-- Superficie -->
        <div class="calc-group">
          <span class="calc-group-title">2. Superficie</span>
          <label>Surface approximative : <span class="superficie-value" id="superficie-display">100</span> m²</label>
          <input type="range" class="superficie-slider" id="superficie-slider"
                 min="20" max="1000" value="100" step="10"/>
        </div>

        <!-- Fréquence -->
        <div class="calc-group">
          <span class="calc-group-title">3. Fréquence</span>
          <div class="option-btns" id="opt-frequence">
            <button class="option-btn selected" data-val="ponctuel" data-mult="1">Ponctuel</button>
            <button class="option-btn" data-val="mensuel" data-mult="0.9">Mensuel <small>−10%</small></button>
            <button class="option-btn" data-val="hebdo" data-mult="0.8">Hebdo <small>−20%</small></button>
            <button class="option-btn" data-val="annuel" data-mult="0.7">Contrat annuel <small>−30%</small></button>
          </div>
        </div>

        <!-- Options supplémentaires -->
        <div class="calc-group">
          <span class="calc-group-title">4. Options supplémentaires</span>
          <div class="option-btns" id="opt-extras">
            <button class="option-btn" data-val="weekend" data-extra="50">Week-end +50€</button>
            <button class="option-btn" data-val="nuit" data-extra="80">Nuit +80€</button>
            <button class="option-btn" data-val="urgent" data-extra="100">Urgent +100€</button>
            <button class="option-btn" data-val="produits" data-extra="30">Nos produits +30€</button>
          </div>
        </div>
      </div>

      <!-- Colonne droite : résultat -->
      <div class="calculateur-result-side">
        <div class="calc-result-card">
          <h3>Estimation de votre prestation</h3>
          <div class="prix-estime" id="prix-estime">450 €</div>
          <div class="prix-periode" id="prix-periode">intervention ponctuelle</div>

          <div class="prix-detail">
            <div class="prix-detail-item">
              <span>Type de prestation</span>
              <span id="detail-prestation">Nettoyage standard</span>
            </div>
            <div class="prix-detail-item">
              <span>Superficie</span>
              <span id="detail-superficie">100 m²</span>
            </div>
            <div class="prix-detail-item">
              <span>Fréquence</span>
              <span id="detail-frequence">Ponctuelle</span>
            </div>
            <div class="prix-detail-item">
              <span>Options</span>
              <span id="detail-extras">Aucune</span>
            </div>
            <div class="prix-detail-item" style="border-top:1px solid rgba(91,163,217,0.15); padding-top:0.8rem; margin-top:0.4rem;">
              <span style="color:var(--creme); font-weight:500;">Total estimé</span>
              <span style="color:var(--or); font-weight:600;" id="detail-total">450 €</span>
            </div>
          </div>

          <a href="#rdv" class="btn-primary" style="width:100%; text-align:center; display:block;">
            Valider et prendre RDV →
          </a>
          <p class="calc-disclaimer">
            * Cette estimation est fournie à titre indicatif. Le prix définitif sera établi lors de votre consultation gratuite, selon les spécificités de vos locaux.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- GALERIE AVANT/APRÈS -->
  <section class="galerie fade-in">
    <div style="text-align:center; margin-bottom:1rem;">
      <span class="section-label">Nos réalisations</span>
      <h2 class="section-title" style="color:var(--creme);">Avant & Après</h2>
      <p style="color:rgba(238,243,250,0.4); max-width:500px; margin:1rem auto 0; font-size:0.95rem; font-weight:300;">
        Passez la souris sur chaque image pour voir la transformation.
      </p>
    </div>
    <div class="galerie-grid">

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Bureau</div>
          <div class="galerie-img apres">APRÈS — Bureau</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Open-space 200 m²</h4>
          <p>Nettoyage en profondeur — Paris 8e</p>
        </div>
      </div>

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Couloir</div>
          <div class="galerie-img apres">APRÈS — Couloir</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Parties communes immeuble</h4>
          <p>Contrat syndic — Montrouge</p>
        </div>
      </div>

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Sol</div>
          <div class="galerie-img apres">APRÈS — Sol</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Nettoyage de sols industriels</h4>
          <p>Traitement spécialisé — Bagneux</p>
        </div>
      </div>

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Cuisine</div>
          <div class="galerie-img apres">APRÈS — Cuisine</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Cuisine professionnelle</h4>
          <p>Désinfection complète — Issy-les-Moulineaux</p>
        </div>
      </div>

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Salle</div>
          <div class="galerie-img apres">APRÈS — Salle</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Salle de réunion</h4>
          <p>Entretien hebdomadaire — Paris 15e</p>
        </div>
      </div>

      <div class="galerie-item">
        <div class="galerie-before-after">
          <div class="galerie-img avant">AVANT — Parking</div>
          <div class="galerie-img apres">APRÈS — Parking</div>
          <span class="galerie-label">Avant</span>
          <span class="galerie-label-apres">Après</span>
        </div>
        <div class="galerie-info">
          <h4>Parking souterrain</h4>
          <p>Nettoyage haute pression — Malakoff</p>
        </div>
      </div>

    </div>
  </section>

  <!-- SYSTÈME DE RDV -->
  <section class="rdv-section" id="rdv">
    <div style="text-align:center; margin-bottom:4rem;">
      <span class="section-label">Consultation gratuite</span>
      <h2 class="section-title" style="color:var(--creme);">Réservez votre créneau</h2>
    </div>
    <div class="rdv-wrapper">

      <!-- Infos -->
      <div class="rdv-info fade-in">
        <h3>Une consultation sans engagement</h3>
        <p>Notre équipe se déplace gratuitement pour évaluer vos besoins, estimer la superficie et vous proposer un devis définitif adapté à votre budget.</p>
        <div class="rdv-avantages">
          <div class="rdv-avantage">
            <div class="rdv-avantage-icon">🚗</div>
            Déplacement gratuit sur Montrouge et Paris
          </div>
          <div class="rdv-avantage">
            <div class="rdv-avantage-icon">⏱️</div>
            Consultation de 30 minutes maximum
          </div>
          <div class="rdv-avantage">
            <div class="rdv-avantage-icon">📋</div>
            Devis détaillé remis sous 24h
          </div>
          <div class="rdv-avantage">
            <div class="rdv-avantage-icon">🤝</div>
            Aucun engagement après la consultation
          </div>
        </div>
      </div>

      <!-- Formulaire RDV -->
      <div class="rdv-form fade-in">
        <h4>Choisissez votre créneau</h4>

        <!-- Message de succès (caché par défaut) -->
        <div id="rdv-success" style="display:none; background:rgba(46,204,113,0.1); border:1px solid var(--vert-ok); border-radius:4px; padding:1.5rem; text-align:center; margin-bottom:1.5rem;">
          <div style="font-size:2rem; margin-bottom:0.5rem;">✅</div>
          <p style="color:var(--vert-ok); font-weight:500;">Demande envoyée avec succès !</p>
          <p style="color:rgba(238,243,250,0.5); font-size:0.85rem; margin-top:0.5rem;">Notre équipe vous confirme le créneau sous 2 heures.</p>
        </div>

        <form class="contact-form form-nuit" id="rdv-form">
          <div class="form-row">
            <div class="form-group">
              <label for="rdv-prenom">Prénom *</label>
              <input type="text" id="rdv-prenom" name="prenom" placeholder="Marie" required/>
            </div>
            <div class="form-group">
              <label for="rdv-nom">Nom *</label>
              <input type="text" id="rdv-nom" name="nom" placeholder="Dupont" required/>
            </div>
          </div>

          <div class="form-group">
            <label for="rdv-email">Email *</label>
            <input type="email" id="rdv-email" name="email" placeholder="marie@exemple.fr" required/>
          </div>

          <div class="form-group">
            <label for="rdv-tel">Téléphone *</label>
            <input type="tel" id="rdv-tel" name="telephone" placeholder="06 00 00 00 00" required/>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="rdv-date">Date souhaitée *</label>
              <input type="date" id="rdv-date" name="date_rdv" required/>
            </div>
            <div class="form-group">
              <label for="rdv-sujet">Prestation *</label>
              <select id="rdv-sujet" name="sujet" required>
                <option value="">Sélectionner...</option>
                <option value="nettoyage-profondeur">Nettoyage en profondeur</option>
                <option value="desinfection">Désinfection</option>
                <option value="ecologique">Écologique</option>
                <option value="sols-tapis">Sols & Tapis</option>
                <option value="copropriete">Copropriété</option>
                <option value="bureaux">Bureaux</option>
              </select>
            </div>
          </div>

          <!-- Créneaux horaires -->
          <div class="form-group">
            <label>Créneau horaire *</label>
            <div class="creneaux-grid">
              <button type="button" class="creneau-btn" data-heure="08:00">08h00</button>
              <button type="button" class="creneau-btn" data-heure="09:00">09h00</button>
              <button type="button" class="creneau-btn" data-heure="10:00">10h00</button>
              <button type="button" class="creneau-btn indisponible" data-heure="11:00">11h00</button>
              <button type="button" class="creneau-btn" data-heure="14:00">14h00</button>
              <button type="button" class="creneau-btn" data-heure="15:00">15h00</button>
              <button type="button" class="creneau-btn" data-heure="16:00">16h00</button>
              <button type="button" class="creneau-btn indisponible" data-heure="17:00">17h00</button>
              <button type="button" class="creneau-btn" data-heure="18:00">18h00</button>
            </div>
            <input type="hidden" id="heure-selected" name="heure_rdv"/>
          </div>

          <div class="form-group">
            <label for="rdv-message">Informations complémentaires</label>
            <textarea id="rdv-message" name="message"
              placeholder="Superficie approximative, type de locaux, accès particulier..."
              style="min-height:80px;"></textarea>
          </div>

          <div class="rgpd-group">
            <input type="checkbox" id="rdv-rgpd" required/>
            <label for="rdv-rgpd" style="color:rgba(238,243,250,0.3);">
              J'accepte que mes données soient utilisées pour traiter ma demande de RDV.
            </label>
          </div>

          <button type="submit" class="btn-submit" id="rdv-submit" style="width:100%;">
            Confirmer ma réservation →
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer style="background:var(--nuit);">
    <span class="footer-logo">Groupe<span>.</span>Aube</span>
    <ul class="footer-links">
      <li><a href="index.html">Accueil</a></li>
      <li><a href="about.html">À propos</a></li>
      <li><a href="services.html">Services</a></li>
      <li><a href="mentions-legales.html">Mentions légales</a></li>
    </ul>
    <span class="footer-copy">© 2024 Groupe Aube Propreté — Tous droits réservés</span>
  </footer>

  <script src="assets/js/main.js"></script>
  <script>
  /* ============================================================
     CALCULATEUR DE PRIX
  ============================================================ */
  const state = {
    prixBase: 15,
    superficie: 100,
    multiplicateur: 1,
    extras: [],
    prestationNom: 'Nettoyage standard',
    frequenceNom: 'Ponctuelle'
  };

  /* Boutons de prestation */
  document.querySelectorAll('#opt-prestation .option-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('#opt-prestation .option-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      state.prixBase = parseInt(this.dataset.prix);
      state.prestationNom = this.textContent.trim();
      calculerPrix();
    });
  });

  /* Slider superficie */
  const slider = document.getElementById('superficie-slider');
  slider.addEventListener('input', function() {
    state.superficie = parseInt(this.value);
    document.getElementById('superficie-display').textContent = this.value;
    calculerPrix();
  });

  /* Boutons fréquence */
  document.querySelectorAll('#opt-frequence .option-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('#opt-frequence .option-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      state.multiplicateur = parseFloat(this.dataset.mult);
      state.frequenceNom = this.textContent.replace(/−\d+%/, '').trim();
      calculerPrix();
    });
  });

  /* Boutons extras (multi-sélection) */
  document.querySelectorAll('#opt-extras .option-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      this.classList.toggle('selected');
      const val = this.dataset.val;
      const extra = parseInt(this.dataset.extra);
      if (this.classList.contains('selected')) {
        state.extras.push({ val, extra, nom: this.textContent.split('+')[0].trim() });
      } else {
        state.extras = state.extras.filter(e => e.val !== val);
      }
      calculerPrix();
    });
  });

  /* Fonction de calcul */
  function calculerPrix() {
    let base = state.prixBase * state.superficie * state.multiplicateur;
    let totalExtras = state.extras.reduce((acc, e) => acc + e.extra, 0);
    let total = Math.round(base + totalExtras);
    let min = Math.round(total * 0.9);
    let max = Math.round(total * 1.15);

    document.getElementById('prix-estime').textContent = min + ' – ' + max + ' €';
    document.getElementById('detail-prestation').textContent = state.prestationNom;
    document.getElementById('detail-superficie').textContent = state.superficie + ' m²';
    document.getElementById('detail-frequence').textContent = state.frequenceNom;
    document.getElementById('detail-extras').textContent =
      state.extras.length ? state.extras.map(e => e.nom).join(', ') : 'Aucune';
    document.getElementById('detail-total').textContent = min + ' – ' + max + ' €';
  }

  /* ============================================================
     CRÉNEAUX HORAIRES
  ============================================================ */
  document.querySelectorAll('.creneau-btn:not(.indisponible)').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.creneau-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('heure-selected').value = this.dataset.heure;
    });
  });

  /* Date minimum = aujourd'hui */
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('rdv-date').setAttribute('min', today);

  /* ============================================================
     ENVOI FORMULAIRE RDV VIA PHP
  ============================================================ */
  document.getElementById('rdv-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    /* Vérifier qu'un créneau est sélectionné */
    if (!document.getElementById('heure-selected').value) {
      alert('Veuillez sélectionner un créneau horaire.');
      return;
    }

    const btn = document.getElementById('rdv-submit');
    btn.textContent = 'Envoi en cours...';
    btn.disabled = true;

    /* Récupérer les données du formulaire */
    const formData = new FormData(this);

    try {
      /* Envoi vers notre script PHP */
      const response = await fetch('actions/save_devis.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        /* Succès : afficher le message de confirmation */
        document.getElementById('rdv-success').style.display = 'block';
        this.style.display = 'none';
        window.scrollTo({ top: document.getElementById('rdv').offsetTop, behavior: 'smooth' });
      } else {
        alert('Erreur : ' + (data.message || 'Veuillez réessayer.'));
        btn.textContent = 'Confirmer ma réservation →';
        btn.disabled = false;
      }
    } catch (err) {
      alert('Erreur de connexion. Vérifiez votre connexion internet.');
      btn.textContent = 'Confirmer ma réservation →';
      btn.disabled = false;
    }
  });
  </script>

</body>
</html>