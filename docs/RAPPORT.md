# Rapport projet SHAE

**Étudiant(e) :** MULEMBWE NGUBA SHARONE  
**Date :** Juin 2026

## 1. Présentation

**SHAE** est une marketplace web développée avec Laravel. Les gestionnaires publient et modèrent les produits ; les clients achètent en ligne et paient via mobile money (API LabPay).

## 2. Objectifs atteints

- Marketplace fonctionnelle avec catalogue géré par les gestionnaires
- 3 rôles utilisateurs : administrateur, gestionnaire, client
- Paiement mobile intégré (simulation locale + API LabPay)
- Dashboard administrateur avec statistiques
- Authentification 2FA par OTP email
- API REST avec Sanctum

## 3. Architecture technique

- **Backend :** Laravel 13, PHP 8.3+
- **Base de données :** MySQL
- **Frontend :** Blade + Bootstrap 5
- **Graphiques :** Chart.js
- **PDF :** DomPDF
- **API :** Laravel Sanctum
- **Paiement :** LabPay

## 4. Schéma de base de données

Voir `docs/schema-bdd.md`

Tables principales : users, categories, products, orders, order_items, payments, otp_codes.

## 5. Fonctionnalités par niveau

### Niveau 1 — Fondamentaux
MVC, migrations, seeders, CRUD produits, pagination, recherche, Form Requests, Blade layouts.

### Niveau 2 — Intermédiaires
3 rôles, 3 middleware custom, dashboard admin, 5 emails, 2FA OTP.

### Niveau 3 — Avancés
API REST Sanctum, upload images, LabPay, Event/Listener PaymentCompleted, reçu PDF.

## 6. Middleware personnalisés

| Middleware | Description |
|------------|-------------|
| CheckRole | Contrôle l'accès par rôle |
| CheckAccountActive | Bloque les comptes désactivés |
| RateLimited | Limite les requêtes par IP |

## 7. Difficultés rencontrées

1. **Synchronisation Composer** — Packages ajoutés dans `composer.json` sans mise à jour du lock file ; résolu avec `composer update`.
2. **LabPay en local** — Mode simulation pour tester sans clés API.
3. **Emails** — `QUEUE_CONNECTION=sync` en développement.

## 8. Tests effectués

- [ ] Installation `migrate --seed`
- [ ] Connexion 3 rôles
- [ ] CRUD produits (gestionnaire)
- [ ] Parcours achat client
- [ ] Simulation paiement
- [ ] API Postman

## 9. Déploiement

À compléter : hébergeur, domaine, base production, clés LabPay.

## 10. Conclusion

SHAE répond aux exigences du cahier des charges Laravel avec une architecture claire, trois rôles distincts et un flux de paiement mobile complet.

---

*Document à exporter en PDF pour le rendu final.*
