# Groupe Aube Propreté — Site Web Professionnel

Site vitrine avec système de devis en ligne, espace client et back-office
administrateur pour Groupe Aube Propreté, entreprise de nettoyage professionnel
basée à Montrouge (92).

Projet réalisé dans le cadre d'un stage de fin d'année —
Bachelor Informatique, Cybersécurité et IA — École IPSSI Paris 2024.

---

## Fonctionnalités complètes

### Site vitrine
- Page d'accueil avec hero animé, bandeau de confiance, 6 services,
  témoignages clients et FAQ interactive accordion
- Page À propos : histoire, valeurs, équipe, chiffres clés
- Page Services : détail des 6 prestations avec listes inclusions
- Page Contact : formulaire multi-étapes 3 panneaux + validation RGPD
- Page Mentions légales + Politique de confidentialité complète RGPD
- Design responsive Mobile-First testé iPhone SE / tablette / desktop
- Animations IntersectionObserver au scroll
- Menu hamburger mobile JavaScript vanilla
- Bouton retour en haut animé
- Bannière cookies RGPD fonctionnelle (localStorage)

### Page Devis Pro (PHP)
- Thème immersif bleu nuit avec étoiles animées CSS
- Calculateur de prix interactif en temps réel (JS vanilla)
  - 6 types de prestations avec prix de base par m²
  - Slider superficie 20 → 1000 m²
  - 4 niveaux de fréquence avec réductions (jusqu'à -30%)
  - Options supplémentaires cumulables (week-end, nuit, urgent)
  - Affichage fourchette min/max en temps réel
- Galerie avant/après avec effet hover CSS
- Système de RDV avec créneaux horaires sélectionnables
- Soumission via PHP vers MySQL (PDO sécurisé)

### Espace client
- Inscription avec validation complète + connexion automatique
- Connexion sécurisée par session PHP
- Dashboard client : stats, historique des devis, statuts en temps réel
- Page profil : modification infos + changement mot de passe sécurisé
- Liaison automatique devis → compte client (avant et après connexion)
- Protection CSRF sur tous les formulaires

### Back-office administrateur
- Login sécurisé par session PHP
- Dashboard : 4 cartes de stats, tableau complet des devis
- Filtres par statut + recherche par nom/email/prestation
- Changement de statut en temps réel (AJAX sans rechargement)
- Modal de détail complet d'un devis
- Suppression avec animation
- Notifications toast
- Déconnexion sécurisée

### Sécurité
- Protection XSS : htmlspecialchars() sur toutes les sorties
- Protection SQL : requêtes préparées PDO exclusivement
- Mots de passe : password_hash() bcrypt (admin et clients)
- Sessions : vérification à chaque requête protégée
- CSRF : tokens hash_equals() sur tous les formulaires sensibles
- Validation double : client JS + serveur PHP

---

## Technologies

| Technologie | Version | Usage |
|-------------|---------|-------|
| HTML5 | — | Structure sémantique |
| CSS3 | — | Design, animations, responsive |
| JavaScript | ES6+ | Interactions, calculateur, FAQ |
| PHP | 8.3 | Backend, sessions, PDO |
| MySQL | 8.4 | Base de données relationnelle |
| Laragon | 2026 | Environnement dev local |

---

## Installation locale

### Prérequis
- Laragon (Apache + MySQL + PHP 8)

### Étapes
1. Cloner le dépôt dans `C:\laragon\www\groupe-aube`
2. Ouvrir phpMyAdmin : `http://localhost/phpmyadmin`
3. Créer une base de données `groupe_aube` en `utf8mb4_unicode_ci`
4. Importer le fichier `sql/groupe_aube.sql`
5. Ouvrir `http://localhost/groupe-aube`

### Identifiants par défaut
| Rôle | URL | Identifiant | Mot de passe |
|------|-----|-------------|--------------|
| Admin | `/admin/` | admin | password |
| Client | `/inscription.php` | (créer un compte) | — |

⚠️ Changer le mot de passe admin avant tout déploiement en production.

---

## Structure du projet


groupe-aube/
├── index.html # Accueil
├── about.html # À propos
├── services.html # Services
├── contact.html # Contact
├── devis-pro.php # Devis Pro + Calculateur + RDV
├── inscription.php # Inscription client
├── connexion.php # Connexion client
├── deconnexion.php # Déconnexion
├── merci.html # Confirmation envoi
├── mentions-legales.html # Mentions légales
├── politique-confidentialite.html # RGPD
│
├── espace-client/
│ ├── dashboard.php # Tableau de bord client
│ └── profil.php # Profil + mot de passe
│
├── admin/
│ ├── index.php # Login admin
│ ├── dashboard.php # Tableau de bord admin
│ ├── update_statut.php # API CRUD devis
│ └── logout.php # Déconnexion admin
│
├── actions/
│ └── save_devis.php # Sauvegarde devis en BDD
│
├── config/
│ └── database.php # Connexion PDO sécurisée
│
├── includes/
│ └── csrf.php # Protection CSRF réutilisable
│
├── sql/
│ └── groupe_aube.sql # Export BDD pour déploiement
│
└── assets/
├── css/style.css # CSS centralisé (2200+ lignes)
└── js/main.js # JS global


---

## Base de données

```sql
groupe_aube
├── admins      (id, username, password, nom, date_creation)
├── clients     (id, prenom, nom, email, telephone, password,
│                date_creation, derniere_connexion)
├── contacts    (id, prenom, nom, email, message, statut,
│                date_creation)
└── devis       (id, client_id*, prenom, nom, email, telephone,
                 societe, type_prestation, superficie, frequence,
                 message, date_rdv, heure_rdv, statut,
                 date_creation)
                 * clé étrangère nullable vers clients(id)
```

---

## Auteur

**Djeuny** — Stagiaire développeur web
Bachelor Informatique, Cybersécurité et Intelligence Artificielle
École IPSSI Paris — Promotion 2024
Stage : Groupe Aube Propreté, Montrouge (92)
Dépôt : https://github.com/djeuny/lumiere-studio