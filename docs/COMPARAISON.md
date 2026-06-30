# Comparaison : projet prof vs SHAE

**Date :** Juin 2026

## Chemins

| Projet | Chemin correct |
|--------|----------------|
| Exemple professeur | `C:\Users\HP\Projects\examen-php-laravel-l3` |
| SHAE (code métier) | `C:\Users\HP\Projects\shae` |

> Attention : le chemin utilise des **tirets** (`-l3`), pas un underscore (`_l3`).

---

## Verdict global

| | Projet prof | SHAE |
|---|-------------|------|
| **Rôle** | Squelette Laravel vide à compléter | Application marketplace complète |
| **Laravel** | **13.8** | 11.31 |
| **PHP** | **8.3** | 8.2 |
| **Code métier** | Aucun | Tout SHAE (marketplace, LabPay, 2FA…) |
| **Installation** | Complète (tests, configs, bootstrap/cache) | Partielle (scaffold manuel) |
| **vendor/** | Non installé à la racine* | Installé |

\* Un dossier `Examen/vendor/` existe par erreur dans le dépôt prof — à supprimer.

**Conclusion :** la **structure cible** est la même (celle du README du prof), mais le **contenu** est très différent. Il faut **migrer le code SHAE dans le projet du prof**.

---

## Structure demandée par le professeur

```
nom-du-projet/
├── app/Http/Controllers, Middleware, Requests
├── app/Models, Mail
├── database/migrations, seeders
├── resources/views, lang
├── routes/web.php, api.php
├── .env.example, README.md
```

### Projet prof (état actuel)

| Dossier | Présent | Contenu |
|---------|---------|---------|
| `app/Http/Controllers/` | Oui | 1 fichier (`Controller.php` seulement) |
| `app/Http/Middleware/` | **Non** | — |
| `app/Http/Requests/` | **Non** | — |
| `app/Models/` | Oui | `User.php` seul |
| `app/Mail/` | **Non** | — |
| `database/migrations/` | Oui | 3 migrations Laravel de base |
| `routes/web.php` | Oui | Route `welcome` seulement |
| `routes/api.php` | **Non** | — |
| `resources/views/` | Oui | `welcome.blade.php` seul |
| `resources/lang/` | **Non** | — |
| `docs/` | **Non** | — |
| `tests/` | Oui | Tests Laravel par défaut |

### SHAE (état actuel)

| Dossier | Présent | Contenu |
|---------|---------|---------|
| `app/Http/Controllers/` | Oui | 18 controllers (Admin, Vendor, Client, Api…) |
| `app/Http/Middleware/` | Oui | 3 middleware custom |
| `app/Http/Requests/` | Oui | 4 Form Requests |
| `app/Models/` | Oui | 7 modèles |
| `app/Mail/` | Oui | 5 Mailables |
| `app/Services/` | Oui | LabPay, OTP, Paiements |
| `app/Events/` + `Listeners/` | Oui | PaymentCompleted |
| `database/migrations/` | Oui | 10 migrations |
| `routes/web.php` + `api.php` | Oui | Routes complètes |
| `resources/views/` | Oui | 30+ vues Blade |
| `resources/lang/fr/` | Oui | Validation FR |
| `docs/` | Oui | schema-bdd, RAPPORT, INSTALLATION |

---

## Différences techniques importantes

| Élément | Prof | SHAE |
|---------|------|------|
| Laravel | 13 | 11 |
| User model | Attributs PHP 8 `#[Fillable]` | `$fillable` classique |
| Auth | Non implémentée | Auth manuelle + 2FA |
| Sanctum | Non installé | Installé |
| DomPDF | Non installé | Installé |
| Front | Vite + Tailwind (défaut L13) | Bootstrap CDN |
| BDD par défaut | SQLite | MySQL |

---

## Plan de réorganisation recommandé

1. **Base** = `examen-php-laravel-l3` (squelette prof, Laravel 13)
2. **Copier** tout le code métier depuis `shae/`
3. **Supprimer** le dossier parasite `Examen/` (vendor dupliqué)
4. **Ajouter** Sanctum + DomPDF au `composer.json` du prof
5. **Mettre à jour** `bootstrap/app.php` (routes API + middleware SHAE)
6. Lancer `composer install` puis `migrate --seed`

Script fourni : `migrate-shae.ps1` à la racine du projet prof.

---

## Après migration

Travailler uniquement dans :
`C:\Users\HP\Projects\examen-php-laravel-l3`

Le dossier `shae/` peut servir de backup puis être archivé.
