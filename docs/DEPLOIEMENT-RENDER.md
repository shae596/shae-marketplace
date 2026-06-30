# Déploiement SHAE sur Render

Objectif : **URL HTTPS fixe** pour LabPay, avec **Docker + MySQL** (comme XAMPP en local). Pas de PostgreSQL.

---

## Architecture

| Composant | Détail |
|-----------|--------|
| **Render Web Service** | Conteneur Docker (PHP 8.3 + Laravel) |
| **MySQL** | **MariaDB dans le même conteneur** (`127.0.0.1:3306`) |
| **Base** | `shae` — migrations + seed automatiques au démarrage |

Render ne propose pas de MySQL managé gratuit ; la base tourne **dans le Docker**, comme un mini XAMPP intégré.

> **Note :** sur le plan gratuit, les données MySQL sont sur le disque du conteneur. Un **redéploiement complet** peut réinitialiser la base (le seed recrée les comptes de test). Pour un MySQL externe (hébergeur MySQL), changez `DB_HOST` dans Render.

---

## Étape 1 — Pousser le code sur GitHub

```powershell
cd C:\Users\HP\Projects\examen-php-laravel-l3
git add .
git status
git commit -m "Déploiement Render Docker + MySQL"
git push origin master
```

(Créez votre repo GitHub si ce n’est pas encore fait — voir guide précédent.)

---

## Étape 2 — Render

1. [https://render.com](https://render.com) → inscription GitHub
2. **New → Blueprint** → votre dépôt
3. Render lit `render.yaml` → **Apply**
4. Attendre le build (5–15 min)

URL obtenue : `https://shae.onrender.com` (ou nom choisi)

---

## Étape 3 — Variables obligatoires (Environment)

| Variable | Valeur |
|----------|--------|
| `APP_URL` | `https://VOTRE-URL.onrender.com` |
| `LABPAY_CALLBACK_URL` | `https://VOTRE-URL.onrender.com/payments/callback` |
| `LABPAY_API_KEY` | Token Labyrinthe (complet, avec `$`) |

Déjà configuré par le Blueprint :

- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_DATABASE=shae`
- `DB_USERNAME=shae`
- `DB_PASSWORD` (généré automatiquement)

**Save** → redéploiement automatique.

---

## Étape 4 — Vérifier

1. `https://VOTRE-URL.onrender.com/up` → OK
2. Login client : `client@exemple.com` / `password`
3. Paiement LabPay → callback HTTPS fixe

---

## MySQL externe (optionnel)

Si vous avez un MySQL hébergé ailleurs (InfinityFree, etc.) :

```
DB_HOST=sql123.infinityfree.com
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Le script ne démarre pas MariaDB embarqué si `DB_HOST` n’est pas `127.0.0.1` / `localhost`.

---

## Dépannage

| Problème | Solution |
|----------|----------|
| Build échoue | Logs Render → Build |
| 500 au démarrage | Logs → attendre MariaDB (30–60 s au 1er lancement) |
| Paiement pending | `APP_URL` + `LABPAY_CALLBACK_URL` en HTTPS |
| Base vide après redeploy | Normal sur plan gratuit ; reconnectez-vous après seed auto |

---

## Fichiers Render

- `Dockerfile` — PHP 8.3 + MariaDB + `pdo_mysql`
- `docker/render-start.sh` — démarre MySQL, migrate, seed, Laravel
- `render.yaml` — Blueprint (web Docker uniquement, pas de PostgreSQL)
