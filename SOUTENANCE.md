# Fiche de soutenance — Groupe Aube Propreté

**Étudiant·e :** Djeuny
**École :** IPSSI Paris — Bachelor Informatique, Cybersécurité et IA
**Entreprise de stage :** Groupe Aube Propreté, Montrouge (92)
**Date de soutenance :** Septembre 2024

---

## Pitch d'introduction (2 minutes)

> "Mon stage consistait à réaliser la refonte complète du site web de Groupe Aube Propreté,
> une entreprise de nettoyage professionnel basée à Montrouge.
> J'ai développé un site vitrine en HTML5/CSS3/JavaScript vanilla côté frontend,
> et un backend PHP avec base de données MySQL côté serveur.
> Le projet comprend six pages publiques, un espace client sécurisé,
> un back-office administrateur, et un système de devis en ligne
> avec calculateur de prix interactif.
> J'ai travaillé seule, avec VSCode, Laragon, et Git pour le versioning."

---

## Démonstration recommandée (5 minutes)

1. Ouvrir `http://localhost/groupe-aube/` — montrer le hero, la FAQ
2. Cliquer sur "Devis gratuit" → montrer le calculateur de prix en direct
3. Soumettre un RDV → montrer la confirmation verte
4. Ouvrir `http://localhost/groupe-aube/admin/` → se connecter
5. Montrer le dashboard admin : stats, changer un statut en direct
6. Ouvrir `http://localhost/groupe-aube/espace-client/dashboard.php`
7. Montrer le devis apparu automatiquement lié au compte
8. Ouvrir phpMyAdmin → montrer les tables et le mot de passe haché

---

## Questions probables du jury et tes réponses

### Architecture & Choix techniques

**Q : Pourquoi avoir choisi PHP plutôt qu'un framework comme Laravel ?**
R : "En première année de Bachelor, maîtriser les fondamentaux du PHP procédural
est prioritaire. J'ai appliqué une architecture MVC simplifiée avec
séparation config/actions/includes pour structurer le code proprement,
sans la complexité d'un framework complet."

**Q : Qu'est-ce que PDO et pourquoi l'utiliser plutôt que mysqli ?**
R : "PDO est une interface d'abstraction qui fonctionne avec plusieurs
systèmes de bases de données. Sa principale force est de supporter
nativement les requêtes préparées, qui séparent le code SQL des données
utilisateur et empêchent les injections SQL. C'est la méthode recommandée
par OWASP."

**Q : Expliquez la différence entre validation côté client et côté serveur.**
R : "La validation côté client (JavaScript) améliore l'expérience utilisateur
en donnant un retour immédiat sans rechargement. Mais elle peut être
contournée en désactivant JavaScript. La validation côté serveur (PHP)
est donc indispensable pour la sécurité réelle — on ne peut pas la
désactiver côté serveur."

---

### Sécurité

**Q : Qu'est-ce qu'une injection SQL ? Comment vous en protégez-vous ?**
R : "Une injection SQL consiste à insérer du code SQL malveillant dans
un formulaire pour manipuler la base de données — par exemple supprimer
toutes les tables. Je me protège avec des requêtes préparées PDO :
les paramètres sont envoyés séparément du code SQL, MySQL les traite
comme de simples données et jamais comme des instructions."

**Q : Qu'est-ce que XSS et comment l'avez-vous géré ?**
R : "XSS (Cross-Site Scripting) : un attaquant injecte du JavaScript
malveillant dans les données affichées sur le site, qui s'exécute dans
le navigateur des autres visiteurs. Je me protège avec htmlspecialchars()
sur toutes les sorties PHP : les caractères < > & sont convertis en
entités HTML inoffensives."

**Q : Qu'est-ce qu'un token CSRF ?**
R : "CSRF (Cross-Site Request Forgery) : un site malveillant peut forcer
votre navigateur à envoyer une requête à mon site à votre insu.
Le token CSRF est un code secret unique par session, généré avec
random_bytes() et vérifié à chaque soumission. Si le token ne correspond
pas, la requête est rejetée."

**Q : Comment stockez-vous les mots de passe ?**
R : "Jamais en clair. J'utilise password_hash() avec l'algorithme bcrypt.
Bcrypt génère automatiquement un sel aléatoire et applique un hachage
à sens unique — il est impossible de retrouver le mot de passe original
même en cas de fuite de base de données."

---

### Base de données

**Q : Expliquez la relation entre les tables clients et devis.**
R : "C'est une relation un-à-plusieurs : un client peut avoir plusieurs
devis. J'ai ajouté une colonne client_id dans la table devis, qui est
une clé étrangère nullable vers la table clients. Nullable signifie
qu'un devis peut exister sans être rattaché à un compte client —
ça permet aux visiteurs de soumettre un devis sans créer de compte,
puis de relier ce devis à leur compte plus tard."

**Q : Pourquoi ON DELETE SET NULL ?**
R : "Si on supprime un compte client, ses anciens devis restent en base
avec client_id mis à NULL — ils ne sont plus rattachés à personne
mais restent disponibles pour les statistiques et la comptabilité.
C'est préférable à ON DELETE CASCADE qui supprimerait tous ses devis."

---

### Frontend

**Q : Qu'est-ce que le Mobile-First ?**
R : "C'est une approche CSS qui consiste à écrire les styles de base
pour les petits écrans, puis à ajouter des media queries pour les
écrans plus grands. C'est l'inverse du responsive traditionnel.
Aujourd'hui plus de 60% du trafic web vient du mobile — partir du
mobile garantit une expérience optimale sur tous les appareils."

**Q : Qu'est-ce que l'IntersectionObserver ?**
R : "C'est une API JavaScript native qui détecte quand un élément
entre dans le viewport (la zone visible du navigateur). Je l'utilise
pour déclencher les animations d'apparition au scroll : quand
un élément .fade-in devient visible, on lui ajoute la classe .visible
qui déclenche sa transition CSS. C'est plus performant que de calculer
la position au scroll avec window.scrollY."

**Q : Pourquoi pas de jQuery ou de framework JavaScript ?**
R : "J'ai choisi JavaScript vanilla pour deux raisons. D'abord,
les fondamentaux : maîtriser le DOM natif avant les abstractions.
Ensuite, les performances : pas de dépendance externe à charger,
le site reste rapide. Les features que j'utilise (querySelector,
fetch, IntersectionObserver) sont supportées par tous les navigateurs modernes."

---

### RGPD

**Q : Quelles obligations RGPD avez-vous respectées ?**
R : "J'ai implémenté cinq éléments obligatoires :
1. Le consentement explicite avec case à cocher sur chaque formulaire
2. Une bannière cookies fonctionnelle qui mémorise le choix
3. Une politique de confidentialité détaillant chaque donnée collectée,
   sa finalité et sa durée de conservation
4. Des mentions légales conformes
5. Des mesures de sécurité documentées (bcrypt, PDO, CSRF, XSS)"

---

## Points forts à mettre en avant

✅ Projet complet et fonctionnel de bout en bout
✅ Séparation claire frontend/backend
✅ Sécurité implémentée à tous les niveaux
✅ Espace client + admin = deux interfaces distinctes
✅ Calculateur de prix interactif sans rechargement
✅ Base de données relationnelle avec clé étrangère
✅ Git avec historique de commits propre
✅ README professionnel documentant tout le projet
✅ Conformité RGPD complète

---

## Chiffres à retenir pour impressionner le jury

- **9 pages** publiques et protégées
- **4 tables** MySQL avec relations
- **2200+ lignes** de CSS centralisé
- **5 niveaux** de sécurité implémentés
- **3 espaces** distincts : visiteur / client / admin
- **10 jours** de développement structuré