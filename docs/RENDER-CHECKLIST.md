# Checklist Render — après le push GitHub

## A. Créer le repo GitHub (une seule fois)

1. Allez sur **https://github.com/new**
2. Nom du dépôt : `shae-marketplace` (ou autre)
3. **Public**
4. **Ne cochez pas** « Add README » (le projet existe déjà)
5. **Create repository**
6. Copiez l’URL HTTPS, ex. :  
   `https://github.com/VOTRE-NOM/shae-marketplace.git`

## B. Pousser le code

Double-cliquez **`pousser-github.bat`** dans le dossier du projet, ou :

```powershell
cd C:\Users\HP\Projects\examen-php-laravel-l3
git add -A
git commit -m "SHAE marketplace: LabPay, deploiement Render Docker MySQL"
git remote rename origin upstream
git remote add origin https://github.com/VOTRE-NOM/shae-marketplace.git
git push -u origin master
```

Si GitHub demande connexion : utilisez un **Personal Access Token** comme mot de passe  
(Settings → Developer settings → Tokens → Generate classic token → scope `repo`)

---

## C. Render — créer le service

1. **https://dashboard.render.com** → Sign up with **GitHub**
2. Autorisez Render à lire vos dépôts
3. **New +** → **Blueprint**
4. Sélectionnez le repo **`shae-marketplace`**
5. Render détecte **`render.yaml`** → cliquez **Apply**
6. Attendez **Deploy live** (10–20 min la 1ère fois)

Votre URL apparaît en haut, ex. :  
`https://shae.onrender.com`

---

## D. Variables d’environnement (obligatoire)

Render → service **shae** → **Environment** → ajoutez ou modifiez :

| Clé | Valeur |
|-----|--------|
| `APP_URL` | `https://shae.onrender.com` (votre URL exacte, sans `/` à la fin) |
| `LABPAY_CALLBACK_URL` | `https://shae.onrender.com/payments/callback` |
| `LABPAY_API_KEY` | Collez tout le token Labyrinthe (depuis `storage/app/labpay-api-token.txt`) |

Cliquez **Save, rebuild, and deploy**.

Déjà configuré par le Blueprint : `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, etc.

---

## E. Vérifications

1. Ouvrez `https://VOTRE-URL.onrender.com/up` → doit afficher OK
2. Page d’accueil SHAE
3. Login : `client@exemple.com` / `password`
4. Test paiement LabPay (petit montant)

---

## F. Si le premier chargement est lent

Plan gratuit : le site **dort** après 15 min. Ouvrez l’URL **1 minute avant** la démo.

---

## G. Logs en cas d’erreur

Render → **shae** → **Logs**  
Cherchez `[render] Starting MariaDB` puis `Starting Laravel`.
