# Guide d'installation SHAE

**Étudiant(e) :** MULEMBWE NGUBA SHARONE

## Prérequis

- PHP 8.3+
- Composer
- MySQL (XAMPP, WAMP, Laragon…)

## Étapes

### 1. Créer la base de données

```sql
CREATE DATABASE shae CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Installer les dépendances

```powershell
cd C:\Users\HP\Projects\examen-php-laravel-l3
```

Si `composer install` affiche que **sanctum** ou **dompdf** ne sont pas dans le lock file :

```powershell
composer update laravel/sanctum barryvdh/laravel-dompdf --no-interaction
composer install
```

Sinon :

```powershell
composer install
```

### 3. Configurer l'environnement

```powershell
copy .env.example .env
php artisan key:generate
```

Vérifier dans `.env` : `DB_DATABASE=shae`, `DB_USERNAME=root`, `DB_PASSWORD=`.

### Mailtrap (emails + 2FA OTP)

Utilisez **Email Testing** (pas Email Sending / live) :

1. [mailtrap.io](https://mailtrap.io) → **Email Testing** → **Inboxes** → votre inbox → **SMTP**
2. Copiez **Username** et **Password** dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
```

3. Test :

```powershell
php test-mail.php
php artisan config:clear
```

Les emails apparaissent dans l’inbox Mailtrap (pas dans Gmail).

### 4. Migrer et lancer

```powershell
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Ouvrir [http://localhost:8000](http://localhost:8000)

## Comptes de test (3 rôles)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | sharonemulembweng@gmail.com | password |
| Gestionnaire | gestionnaire@exemple.com | password |
| Client | client@exemple.com | password |

Le **gestionnaire** gère le catalogue (`/gestionnaire/products`) et la modération.

## Tester le paiement mobile (local — simulation)

1. Connexion **client**
2. Panier → checkout → commande
3. Paiement LabPay avec un numéro mobile (ex: `0891234567`)
4. Page statut → **Simuler paiement réussi (dev)**

## Paiement mobile réel (LabPay / Labyrinthe)

Flux réel (asynchrone) :

1. SHAE appelle `POST /api/V1/payment/mobile` avec votre token
2. Le client reçoit un **push USSD** sur son téléphone (M-Pesa, Airtel, Orange)
3. Le client saisit son **code PIN** mobile money
4. LabPay envoie un **callback HTTPS** à votre site → commande marquée payée

### Ce que vous devez faire vous-même

1. Créer un compte marchand : [pay.labyrinthe-rdc.com](https://pay.labyrinthe-rdc.com)
2. Compléter le profil (téléphone lié au compte)
3. Générer un **token API** dans le tableau de bord
4. Dans `.env` :
   ```env
   LABPAY_API_KEY=votre_token_ici
   LABPAY_CURRENCY=USD
   LABPAY_COUNTRY=CD
   LABPAY_CALLBACK_URL=https://VOTRE-DOMAINE/payments/callback
   ```
5. **Callback public HTTPS** : LabPay ne peut pas appeler `http://127.0.0.1:8000`. Options :
   - Déployer le site (hébergeur avec HTTPS)
   - Ou utiliser **ngrok** : `ngrok http 8000` puis mettre l’URL ngrok dans `APP_URL` et `LABPAY_CALLBACK_URL`
6. Tester avec **votre propre numéro** mobile money et un **petit montant**

Le code PIN n’est **jamais** saisi sur le site SHAE — uniquement sur le téléphone via l’opérateur.

## Dépannage

| Erreur | Solution |
|--------|----------|
| Packages absents du lock file | `composer update laravel/sanctum barryvdh/laravel-dompdf` |
| `Access denied for user` | Vérifier `DB_*` dans `.env` |
| `Unknown database shae` | Créer la base MySQL |
| Images non visibles | `php artisan storage:link` |
