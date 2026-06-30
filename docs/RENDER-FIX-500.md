# Corriger erreur 500 sur Render (checklist rapide)

## 1. Variables Render → shae → Environment

Supprimez ou corrigez ces valeurs **exactement** :

| Variable | Valeur correcte |
|----------|-----------------|
| `DB_HOST` | `localhost` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | *(laissez VIDE — supprimez la valeur générée)* |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `APP_URL` | `https://shae.onrender.com` |
| `APP_KEY` | *(doit exister — généré par Render)* |

## 2. Pousser le dernier code

```powershell
cd C:\Users\HP\Projects\examen-php-laravel-l3
git add -A
git commit -m "Fix Render 500: sessions fichier, pas de config cache"
git push origin main
```

## 3. Redéployer

Render → **Manual Deploy** ou attendez le déploiement auto.

## 4. Tester

- https://shae.onrender.com/up → OK (health check Render)
- https://shae.onrender.com/login → page de connexion (pas 500)

## Pourquoi /up marche mais pas le site ?

`/up` = test minimal sans session.  
Le reste du site utilise les **sessions** → si MySQL/session mal configuré = **500**.
