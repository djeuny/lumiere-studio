/* ============================================================
   GROUPE AUBE PROPRETÉ — Script JavaScript global
   Auteur : [Ton Prénom] — Stage 2024
   Description : Interactions UI pour toutes les pages du site
   ============================================================ */

/* ========================
   1. NAVIGATION — Comportement au scroll
   → La nav devient compacte et ajoute une ombre quand on défile
======================== */
window.addEventListener('scroll', function () {
  const nav = document.querySelector('nav');
  if (!nav) return; // Sécurité : si pas de nav sur la page, on sort

  if (window.scrollY > 60) {
    nav.classList.add('scrolled');
  } else {
    nav.classList.remove('scrolled');
  }
});

/* ========================
   2. MENU HAMBURGER — Ouverture/fermeture sur mobile
   → Le bouton ☰ affiche ou cache les liens de navigation
======================== */
const navToggle = document.querySelector('.nav-toggle');
const navLinks  = document.querySelector('.nav-links');

if (navToggle && navLinks) {
  navToggle.addEventListener('click', function () {
    navLinks.classList.toggle('open');

    // Accessibilité : on indique à l'écran si le menu est ouvert
    const isOpen = navLinks.classList.contains('open');
    navToggle.setAttribute('aria-expanded', isOpen);
  });

  // Fermer le menu si on clique en dehors
  document.addEventListener('click', function (e) {
    if (!nav.contains(e.target)) {
      navLinks.classList.remove('open');
      navToggle.setAttribute('aria-expanded', false);
    }
  });
}

/* ========================
   3. SCROLL FLUIDE — Liens d'ancrage internes (#section)
   → Tous les liens href="#quelquechose" scrollent doucement
======================== */
document.querySelectorAll('a[href^="#"]').forEach(function (lien) {
  lien.addEventListener('click', function (e) {
    const cible = document.querySelector(this.getAttribute('href'));
    if (!cible) return; // Si la section n'existe pas, on ne fait rien

    e.preventDefault();
    cible.scrollIntoView({ behavior: 'smooth' });

    // Fermer le menu mobile après clic
    if (navLinks) navLinks.classList.remove('open');

    // Si c'est le lien contact, on met le focus sur le premier champ
    if (this.getAttribute('href') === '#contact') {
      setTimeout(function () {
        const premierChamp = document.getElementById('prenom');
        if (premierChamp) premierChamp.focus();
      }, 600);
    }
  });
});

/* ========================
   4. VALIDATION FORMULAIRE — Côté client
   → Vérifie les champs avant envoi et affiche des messages clairs
======================== */
const form = document.querySelector('.contact-form');

if (form) {
  form.addEventListener('submit', function (e) {
    let valide = true;

    // Nettoyer les anciens messages d'erreur
    document.querySelectorAll('.form-error').forEach(function (el) {
      el.remove();
    });

    // Vérifier chaque champ requis
    form.querySelectorAll('[required]').forEach(function (champ) {
      if (!champ.value.trim()) {
        valide = false;
        afficherErreur(champ, 'Ce champ est obligatoire.');
      }
    });

    // Vérifier format email spécifiquement
    const champEmail = form.querySelector('#email');
    if (champEmail && champEmail.value.trim()) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(champEmail.value.trim())) {
        valide = false;
        afficherErreur(champEmail, 'Veuillez entrer une adresse email valide.');
      }
    }

    // Si le formulaire n'est pas valide, on bloque l'envoi
    if (!valide) {
      e.preventDefault();
    }
  });
}

/* Fonction utilitaire : affiche un message d'erreur sous un champ */
function afficherErreur(champ, message) {
  const erreur = document.createElement('span');
  erreur.className = 'form-error';
  erreur.textContent = message;
  erreur.style.cssText = 'color:#e05a5a; font-size:0.75rem; margin-top:0.3rem; display:block;';
  champ.parentNode.appendChild(erreur);
  champ.style.borderColor = '#e05a5a';

  // Remettre la couleur normale quand l'utilisateur tape
  champ.addEventListener('input', function () {
    champ.style.borderColor = '';
    if (erreur.parentNode) erreur.remove();
  }, { once: true });
}

/* ========================
   5. ANIMATION D'APPARITION au scroll
   → Les sections apparaissent en fondu quand on arrive dessus
   (Utilise l'API IntersectionObserver — moderne et performante)
======================== */
const observerOptions = {
  threshold: 0.1,      // Déclenche quand 10% de l'élément est visible
  rootMargin: '0px'
};

const observer = new IntersectionObserver(function (entries) {
  entries.forEach(function (entry) {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target); // On n'observe plus une fois apparu
    }
  });
}, observerOptions);

// On observe tous les éléments qui ont la classe .fade-in
document.querySelectorAll('.fade-in').forEach(function (el) {
  observer.observe(el);
});

/* ========================
   JOUR 7 — FAQ Interactive
   Chaque question s'ouvre/ferme au clic
======================== */
document.querySelectorAll('.faq-question').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const answer = this.nextElementSibling;
    const isOpen = answer.classList.contains('open');

    // Fermer toutes les réponses ouvertes
    document.querySelectorAll('.faq-answer').forEach(function(a) {
      a.classList.remove('open');
    });
    document.querySelectorAll('.faq-question').forEach(function(q) {
      q.classList.remove('active');
    });

    // Si la question cliquée était fermée, on l'ouvre
    if (!isOpen) {
      answer.classList.add('open');
      this.classList.add('active');
    }
  });
});

/* ========================
   JOUR 7 — Bouton retour en haut
   Apparaît après 400px de scroll
======================== */
const scrollTopBtn = document.querySelector('.scroll-top-btn');

if (scrollTopBtn) {
  window.addEventListener('scroll', function() {
    if (window.scrollY > 400) {
      scrollTopBtn.classList.add('visible');
    } else {
      scrollTopBtn.classList.remove('visible');
    }
  });

  scrollTopBtn.addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/* ========================
   JOUR 9 — Bannière cookies RGPD
======================== */
function initCookieBanner() {
  const banner = document.getElementById('cookie-banner');
  if (!banner) return;

  // Vérifier si le choix a déjà été fait
  const choix = localStorage.getItem('cookie_consent');
  if (!choix) {
    // Afficher la bannière après 1 seconde
    setTimeout(() => banner.classList.add('visible'), 1000);
  }
}

function accepterCookies() {
  localStorage.setItem('cookie_consent', 'accepted');
  document.getElementById('cookie-banner').classList.remove('visible');
}

function refuserCookies() {
  localStorage.setItem('cookie_consent', 'refused');
  document.getElementById('cookie-banner').classList.remove('visible');
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', initCookieBanner);