# SHAE — Marketplace web

**Examen final Laravel — FASI/UPC — Promotion L3 — Année académique 2025–2026**

**Étudiant(e) :** MULEMBWE NGUBA SHARONE

---

## Description du projet

**SHAE** est une marketplace en ligne inspirée des plateformes e-commerce modernes. Les **gestionnaires** publient et modèrent le catalogue produits ; les **clients** parcourent le catalogue, passent commande et paient via **mobile money** (API LabPay) ; l’**administrateur** supervise la plateforme (utilisateurs, statistiques).

---

## Technologies utilisées

| Composant | Version / outil |
|-----------|-----------------|
| Framework | Laravel 13 |
| Langage | PHP 8.3+ |
| Base de données | MySQL |
| Frontend | Blade, Bootstrap 5 |
| Graphiques | Chart.js (dashboard admin) |
| API | Laravel Sanctum |
| PDF | DomPDF (`barryvdh/laravel-dompdf`) |
| Paiement mobile | LabPay — [documentation](https://doc.api.labyrinthe-rdc.com/) |
| Emails (dev) | Mailtrap (Email Testing) |

**Dépendances ajoutées :** `laravel/sanctum`, `barryvdh/laravel-dompdf`

---

## Installation

### Prérequis

- PHP 8.3+, Composer, MySQL (XAMPP / Laragon / WAMP)
- Compte [Mailtrap](https://mailtrap.io) (Email Testing) pour les emails et la 2FA en local

### Étapes rapides

```powershell
cd C:\Users\HP\Projects\examen-php-laravel-l3

# Si besoin (sanctum / dompdf absents du lock file) :
composer update laravel/sanctum barryvdh/laravel-dompdf --no-interaction

composer install
copy .env.example .env
php artisan key:generate
```

1. Créer la base MySQL : `CREATE DATABASE shae;`
2. Configurer `.env` : `DB_DATABASE=shae`, identifiants MySQL, **Mailtrap SMTP** (voir ci-dessous)
3. Migrer et peupler :

```powershell
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Ouvrir [http://127.0.0.1:8000](http://127.0.0.1:8000)

**Scripts utiles :** `install-shae.bat`, `lancer-shae.bat`, `test-mail.bat`

**Guide détaillé :** [docs/INSTALLATION.md](docs/INSTALLATION.md)

### Configuration Mailtrap (emails + 2FA)

Dans Mailtrap → **Email Testing** → **Inboxes** → **SMTP**, copier Username et Password :

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
MAIL_SCHEME=smtp
```

Tester : `php artisan config:clear` puis `.\test-mail.bat`

---

## Comptes de test

Créés automatiquement par `DatabaseSeeder` :

| Rôle | Email | Mot de passe | Accès principal |
|------|-------|--------------|-----------------|
| Administrateur | sharonemulembweng@gmail.com | password | `/admin/dashboard` |
| Gestionnaire | gestionnaire@exemple.com | password | `/gestionnaire/products` |
| Client | client@exemple.com | password | Catalogue, panier, commandes |

> L’inscription publique crée toujours un compte **client**. La 2FA est **optionnelle** (activable dans Mon profil).

---

## Fonctionnalités implémentées

### Niveau 1 — Fondamentaux

- [x] Architecture MVC (Models, Controllers, Views Blade)
- [x] Migrations + seeders (aucune table créée manuellement)
- [x] Relations Eloquent (`User`, `Product`, `Category`, `Order`, `Payment`, etc.)
- [x] CRUD produits (gestionnaire) avec pagination
- [x] Recherche et filtrage par catégorie sur le catalogue
- [x] Resource routes (`gestionnaire/products`, API `products`)
- [x] Layouts Blade, partials, affichage conditionnel par rôle
- [x] Form Requests + validation + protection CSRF

### Niveau 2 — Intermédiaires

- [x] **3 rôles** : administrateur, gestionnaire, client
- [x] Redirection après connexion selon le rôle
- [x] **3 middleware personnalisés** (voir tableau ci-dessous)
- [x] Dashboard administrateur (statistiques, graphiques Chart.js, utilisateurs)
- [x] **5 emails** (Mailables + templates Blade) :
  - Bienvenue à l’inscription (`WelcomeMail`)
  - Confirmation de commande (`OrderConfirmedMail`)
  - Vérification email (Laravel `Registered` + notice)
  - Reçu de paiement (`PaymentReceiptMail`)
  - Notification de statut (`StatusNotificationMail`)
  - Code OTP 2FA (`OtpMail`)
- [x] **2FA** : OTP par email, expiration 10 min, activation/désactivation dans le profil

### Niveau 3 — Avancés *(4 fonctionnalités)*

- [x] **API REST** Sanctum (`/api/login`, `/api/products` CRUD, `/api/me`)
- [x] **Upload d’images** produits (`storage/app/public`, validation)
- [x] **Paiement mobile LabPay** : initiation → statut → callback → historique
- [x] **Event/Listener** `PaymentCompleted` → emails + mise à jour stock
- [x] **Reçu PDF** téléchargeable (DomPDF)

---

## Middleware personnalisés

| Middleware | Fichier | Rôle |
|------------|---------|------|
| `CheckRole` | `app/Http/Middleware/CheckRole.php` | Restreint l’accès aux routes selon le rôle (`admin`, `gestionnaire`, `client`) |
| `CheckAccountActive` | `app/Http/Middleware/CheckAccountActive.php` | Bloque les comptes désactivés par l’administrateur |
| `RateLimited` | `app/Http/Middleware/RateLimited.php` | Limite les tentatives (ex. login) par adresse IP |

---

## Paiement mobile (LabPay)

**Workflow :** panier → commande → initiation paiement (numéro mobile) → confirmation → statut mis à jour → reçu PDF + email.

| Mode | Configuration | Comportement |
|------|---------------|--------------|
| **Développement** | `LABPAY_API_KEY` vide | Simulation : bouton « Simuler paiement réussi (dev) » — aucun débit réel |
| **Production / démo réelle** | `LABPAY_API_KEY` (token Labyrinthe) + callback HTTPS public | Push USSD sur le téléphone → saisie du PIN → callback LabPay → commande payée |

Variables `.env` : `LABPAY_API_URL`, `LABPAY_API_KEY`, `LABPAY_CURRENCY` (USD/CDF), `LABPAY_COUNTRY` (CD), `LABPAY_CALLBACK_URL`

Documentation API : [doc.api.labyrinthe-rdc.com](https://doc.api.labyrinthe-rdc.com/)

**Test local (simulation) :** panier → checkout → initier paiement → **Simuler paiement réussi**.

**Test réel :** compte sur [pay.labyrinthe-rdc.com](https://pay.labyrinthe-rdc.com) → token API → `.env` → site accessible en HTTPS (ngrok ou hébergement) pour le callback.

---

## Schéma de la base de données

Documentation complète : [docs/schema-bdd.md](docs/schema-bdd.md)

**Tables principales :** `users`, `categories`, `products`, `orders`, `order_items`, `payments`, `otp_codes`, `personal_access_tokens`

**Catégories :** 5 catégories plates (Électronique, Mode, Maison, Alimentation, Beauté).

---

## Difficultés rencontrées et solutions

| Difficulté | Solution |
|------------|----------|
| Packages `sanctum` / `dompdf` absents du lock Composer | `composer update laravel/sanctum barryvdh/laravel-dompdf` |
| Erreur 500 au démarrage (MySQL, migrations) | Démarrer MySQL (XAMPP), `php artisan migrate:fresh --seed` |
| Conflit de routes `products.show` (web vs API) | Préfixe des noms de routes API : `api.products.*` |
| Emails SMTP / 2FA (erreur 530, limite Mailtrap) | Mailtrap **Email Testing** (sandbox), pas Email Sending ; identifiants SMTP de l’inbox ; gestion des erreurs mail sans bloquer le paiement |
| Paiement LabPay en local sans clés API | Mode simulation documenté + bouton dev sur `/payments/status/{id}` |
| Fichier `.env` invalide (espaces dans les valeurs) | Mettre les valeurs avec espaces entre guillemets ou utiliser le username SMTP sans espace |

---

## Structure du dépôt

```
examen-php-laravel-l3/
├── app/Http/Controllers/    # Web + API + Admin + Gestionnaire + Client
├── app/Http/Middleware/     # CheckRole, CheckAccountActive, RateLimited
├── app/Mail/                # 5+ Mailables
├── app/Services/            # LabPay, OTP, PaymentCompletion
├── database/migrations/     # Schéma complet
├── database/seeders/        # Comptes + catalogue de démo
├── resources/views/         # Blade (marketplace magenta)
├── routes/web.php, api.php
├── docs/                    # INSTALLATION, RAPPORT, schema-bdd
└── .env.example
```

---

## Déploiement

| Élément | Statut |
|---------|--------|
| Hébergeur | **Render** (Docker, `render.yaml`) |
| URL publique | `https://shae.onrender.com` (après déploiement) |
| Base production | **MySQL** (MariaDB dans le conteneur Docker) |
| Clés LabPay production | Variable `LABPAY_API_KEY` dans le dashboard Render |

Guide pas à pas : **[docs/DEPLOIEMENT-RENDER.md](docs/DEPLOIEMENT-RENDER.md)**

---

## Documentation complémentaire

| Fichier | Contenu |
|---------|---------|
| [docs/DEPLOIEMENT-RENDER.md](docs/DEPLOIEMENT-RENDER.md) | Déployer SHAE sur Render (URL fixe LabPay) |
| [docs/INSTALLATION.md](docs/INSTALLATION.md) | Guide d’installation détaillé |
| [docs/RAPPORT.md](docs/RAPPORT.md) | Rapport technique (à exporter en PDF pour le rendu) |
| [docs/schema-bdd.md](docs/schema-bdd.md) | Schéma relationnel |

---

## Dépôt GitHub

*À compléter :* `https://github.com/votre-username/shae`

> Historique de commits exigé pour le rendu.

---

*Projet réalisé dans le cadre du cours Laravel L3 — FASI/UPC — 2025–2026*
