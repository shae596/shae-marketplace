# Schéma de base de données SHAE

## Diagramme des relations

```
users (1) ──────< products (N)
users (1) ──────< orders (N)
users (1) ──────< payments (N)
users (1) ──────< otp_codes (N)

categories (1) ──< products (N)

orders (1) ──────< order_items (N)
products (1) ────< order_items (N)

orders (1) ──────< payments (1)
```

## Tables

### users
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | Identifiant |
| name | string | Nom complet |
| email | string unique | Email |
| password | string | Mot de passe hashé |
| phone | string nullable | Téléphone |
| role | string | admin, gestionnaire, client |
| is_active | boolean | Compte actif |
| two_factor_enabled | boolean | 2FA activé |
| email_verified_at | timestamp | Vérification email |

### categories
| Colonne | Type |
|---------|------|
| id | bigint PK |
| name | string |
| slug | string unique |
| description | text nullable |

5 catégories principales : Électronique, Mode, Maison, Alimentation, Beauté.

### products
| Colonne | Type |
|---------|------|
| id | bigint PK |
| user_id | FK → users (gestionnaire responsable) |
| category_id | FK → categories |
| name, slug, description | string/text |
| price | decimal(12,2) |
| stock | unsigned int |
| image | string nullable |
| status | enum: draft, pending, approved, rejected |

### orders
| Colonne | Type |
|---------|------|
| id | bigint PK |
| reference | string unique |
| user_id | FK → users |
| total | decimal(12,2) |
| status | enum: pending, paid, processing, shipped, delivered, cancelled |
| shipping_address | string |
| shipping_phone | string |

### order_items
| Colonne | Type |
|---------|------|
| id | bigint PK |
| order_id | FK → orders |
| product_id | FK → products |
| quantity | unsigned int |
| unit_price | decimal(12,2) |
| subtotal | decimal(12,2) |

### payments
| Colonne | Type |
|---------|------|
| id | bigint PK |
| order_id | FK → orders |
| user_id | FK → users |
| amount | decimal(12,2) |
| provider | string (labpay) |
| reference | string unique |
| external_id | string nullable |
| phone | string |
| status | enum: pending, success, failed, cancelled |
| provider_response | json nullable |
| paid_at | timestamp nullable |

### otp_codes
| Colonne | Type |
|---------|------|
| id | bigint PK |
| user_id | FK → users |
| code | string(6) |
| used | boolean |
| expires_at | timestamp |
