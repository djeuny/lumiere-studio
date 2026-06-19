# Groupe Aube Propreté — Site Web Professionnel

Site vitrine avec système de devis en ligne et back-office administrateur
pour Groupe Aube Propreté, entreprise de nettoyage professionnel
basée à Montrouge (92).

---

## Fonctionnalités

### Frontend
- Page d'accueil avec hero animé, bandeau de confiance, services, témoignages et FAQ interactive
- Page À propos avec histoire, valeurs, équipe et chiffres clés
- Page Services avec détail des 6 prestations
- Page Contact avec formulaire multi-étapes (3 étapes) et validation RGPD
- Page Devis Pro avec calculateur de prix interactif, galerie avant/après et système de RDV
- Page Merci et Mentions légales
- Design responsive Mobile-First (iPhone SE → desktop)
- Animations d'apparition au scroll (IntersectionObserver)
- Menu hamburger mobile en JavaScript vanilla
- Bouton retour en haut

### Backend
- Formulaire de devis sauvegardé en base de données MySQL via PDO sécurisé
- Protection XSS et injection SQL
- Espace administrateur sécurisé par session PHP
- Dashboard avec statistiques, filtres, recherche et CRUD complet
- Changement de statut des devis en temps réel (AJAX)
- Modal de détail et notifications toast

---

## Technologies utilisées

| Technologie | Usage |
|-------------|-------|
| HTML5 sémantique | Structure des pages |
| CSS3 vanilla | Design, animations, responsive |
| JavaScript vanilla | Interactions, FAQ, calculateur |
| PHP 8 | Backend, sessions, PDO |
| MySQL | Base de données |
| Laragon | Environnement de développement local |

---

## Installation locale

### Prérequis
- Laragon (Apache + MySQL + PHP 8)

### Étapes
1. Cloner le dépôt dans `C:\laragon\www\groupe-aube`
2. Ouvrir phpMyAdmin sur `http://localhost/phpmyadmin`
3. Créer une base de données `groupe_aube` en `utf8mb4_unicode_ci`
4. Importer le fichier `database.sql` (voir dossier `/sql`)
5. Ouvrir `http://localhost/groupe-aube`

### Accès administration
- URL : `http://localhost/groupe-aube/admin/`
- Identifiant : `admin`
- Mot de passe : `password` (à changer en production)

---

## Structure du projet


groupe-aube/

├── index.html              # Page d'accueil

├── about.html              # Page À propos

├── services.html           # Page Services

├── contact.html            # Page Contact

├── devis-pro.php           # Page Devis Pro (PHP)

├── merci.html              # Page de confirmation

├── mentions-legales.html   # Mentions légales

├── admin/

│   ├── index.php           # Login administrateur

│   ├── dashboard.php       # Tableau de bord

│   ├── update_statut.php   # API mise à jour statut

│   └── logout.php          # Déconnexion

├── actions/

│   └── save_devis.php      # Sauvegarde formulaire en BDD

├── config/

│   └── database.php        # Connexion PDO sécurisée

└── assets/

├── css/style.css        # CSS centralisé

└── js/main.js           # JS global


---

## Sécurité implémentée

- Protection XSS via `htmlspecialchars()` sur toutes les sorties
- Protection injection SQL via requêtes préparées PDO
- Mots de passe hachés avec `password_hash()` (bcrypt)
- Sessions PHP sécurisées pour l'espace admin
- Validation double (client JS + serveur PHP)
- Case RGPD obligatoire sur tous les formulaires
- Page mentions légales conforme RGPD

---

## Auteur

Projet réalisé dans le cadre d'un stage de fin d'année
**Bachelor Informatique, Cybersécurité et Intelligence Artificielle**
École IPSSI — Promotion 2024

Développé par : **Djeuny**
Encadrement : Groupe Aube Propreté, Montrouge


