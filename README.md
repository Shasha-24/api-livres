# 📚 API Livres — Laravel 11

API REST complète pour la gestion d'une bibliothèque (livres et exemplaires).  
Développée avec **Laravel 11**, **PHP 8.5** et **SQLite**.

---

## Sommaire

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Lancer le serveur](#lancer-le-serveur)
- [Structure du projet](#structure-du-projet)
- [Base de données](#base-de-données)
- [Endpoints](#endpoints)
- [Exemples de requêtes](#exemples-de-requêtes)
- [Application Android](#application-android)
- [Déploiement ISPConfig](#déploiement-ispconfig)
- [Auteur](#auteur)

---

## Prérequis

Avant de commencer, assure-toi d'avoir installé :

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Git**

Vérifie les versions installées :

```bash
php -v
composer -v
git --version
```

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/Shasha-24/api-livres.git
cd api-livres
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Créer le fichier SQLite

```bash
# Sur Linux/Mac
touch database/database.sqlite

# Sur Windows
echo "" > database/database.sqlite
```

### 6. Lancer les migrations

```bash
php artisan migrate
```

---

## Configuration

Le fichier `.env` contient toute la configuration. Voici les variables importantes :

```env
APP_NAME=ApiLivres
APP_ENV=local
APP_KEY=             # Généré automatiquement avec key:generate
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite # Pas besoin de DB_HOST, DB_PORT, etc.

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> ⚠️ **Important** : Ne jamais committer le fichier `.env` sur GitHub. Il est déjà dans le `.gitignore`.

---

## Lancer le serveur

### En local (développement)

```bash
php artisan serve
```

L'API est accessible sur : `http://127.0.0.1:8000`

### Accessible depuis un autre appareil (tablette, téléphone)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

L'API est alors accessible depuis n'importe quel appareil sur le même réseau via l'IP de ta machine, par exemple : `http://192.168.1.136:8000`

---

## Structure du projet

```
api-livres/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── LivreController.php        # CRUD livres
│   │           └── ExemplaireController.php   # CRUD exemplaires
│   └── Models/
│       ├── Livre.php                          # Modèle Livre
│       └── Exemplaire.php                     # Modèle Exemplaire
├── database/
│   ├── migrations/
│   │   ├── ..._create_livres_table.php
│   │   └── ..._create_exemplaires_table.php
│   └── database.sqlite                        # Base de données SQLite
├── routes/
│   └── api.php                                # Définition des routes
├── config/
│   └── cors.php                               # Configuration CORS
└── .env                                       # Configuration locale
```

---

## Base de données

### Table `livres`

| Colonne | Type | Contrainte | Description |
|---------|------|-----------|-------------|
| id | integer | PK, auto-increment | Identifiant unique |
| titre | string(255) | NOT NULL | Titre du livre |
| auteur | string(255) | NOT NULL | Auteur du livre |
| isbn | string(20) | UNIQUE, nullable | Code ISBN |
| editeur | string(255) | nullable | Nom de l'éditeur |
| description | text | nullable | Résumé du livre |
| created_at | timestamp | auto | Date de création |
| updated_at | timestamp | auto | Date de modification |

### Table `exemplaires`

| Colonne | Type | Contrainte | Description |
|---------|------|-----------|-------------|
| id | integer | PK, auto-increment | Identifiant unique |
| livre_id | integer | FK → livres.id | Référence au livre |
| code_barre | string(100) | UNIQUE, nullable | Code barre physique |
| etat | enum | NOT NULL | `neuf`, `bon`, `acceptable`, `mauvais` |
| statut | enum | NOT NULL | `disponible`, `emprunte`, `reserve`, `perdu`, `retire` |
| created_at | timestamp | auto | Date de création |
| updated_at | timestamp | auto | Date de modification |

> La clé étrangère `livre_id` est en `CASCADE DELETE` : supprimer un livre supprime automatiquement tous ses exemplaires.

### Migrations

Pour créer ou recréer toutes les tables :

```bash
php artisan migrate         # Crée les tables
php artisan migrate:fresh   # Supprime et recrée tout
php artisan migrate:rollback # Annule la dernière migration
```

---

## Endpoints

### Base URL

```
http://127.0.0.1:8000/api/v1
```

### Livres

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `GET` | `/livres` | Liste tous les livres (paginé, 15 par page) |
| `POST` | `/livres` | Créer un nouveau livre |
| `GET` | `/livres/{id}` | Récupérer un livre par son ID |
| `PUT` | `/livres/{id}` | Modifier un livre |
| `DELETE` | `/livres/{id}` | Supprimer un livre |
| `GET` | `/livres/{id}/exemplaires` | Lister les exemplaires d'un livre |

### Exemplaires

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `GET` | `/exemplaires` | Liste tous les exemplaires (paginé, 15 par page) |
| `POST` | `/exemplaires` | Créer un nouvel exemplaire |
| `GET` | `/exemplaires/{id}` | Récupérer un exemplaire par son ID |
| `PUT` | `/exemplaires/{id}` | Modifier un exemplaire |
| `DELETE` | `/exemplaires/{id}` | Supprimer un exemplaire |
| `PATCH` | `/exemplaires/{id}/statut` | Changer uniquement le statut |

---

## Exemples de requêtes

> Tu peux tester ces requêtes avec **Postman** ou **curl**.

### Lister tous les livres

```http
GET /api/v1/livres
```

**Réponse 200 :**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "titre": "Harry Potter",
      "auteur": "J.K. Rowling",
      "isbn": "9782070584628",
      "editeur": "Gallimard",
      "description": "Un jeune sorcier découvre ses pouvoirs",
      "created_at": "2026-03-29T10:00:00.000000Z",
      "updated_at": "2026-03-29T10:00:00.000000Z",
      "exemplaires": []
    }
  ],
  "per_page": 15,
  "total": 1
}
```

---

### Créer un livre

```http
POST /api/v1/livres
Content-Type: application/json
```

**Body :**
```json
{
  "titre": "Le Petit Prince",
  "auteur": "Antoine de Saint-Exupéry",
  "isbn": "9782070408504",
  "editeur": "Gallimard",
  "description": "Un aviateur rencontre un mystérieux petit prince dans le désert."
}
```

**Réponse 201 :**
```json
{
  "message": "Livre créé avec succès.",
  "data": {
    "id": 2,
    "titre": "Le Petit Prince",
    "auteur": "Antoine de Saint-Exupéry",
    "isbn": "9782070408504",
    "editeur": "Gallimard",
    "description": "Un aviateur rencontre un mystérieux petit prince dans le désert.",
    "created_at": "2026-03-29T10:05:00.000000Z",
    "updated_at": "2026-03-29T10:05:00.000000Z"
  }
}
```

**Champs obligatoires :** `titre`, `auteur`  
**Champs optionnels :** `isbn`, `editeur`, `description`

---

### Récupérer un livre avec ses exemplaires

```http
GET /api/v1/livres/1
```

**Réponse 200 :**
```json
{
  "data": {
    "id": 1,
    "titre": "Harry Potter",
    "auteur": "J.K. Rowling",
    "isbn": "9782070584628",
    "editeur": "Gallimard",
    "description": "Un jeune sorcier découvre ses pouvoirs",
    "exemplaires": [
      {
        "id": 1,
        "livre_id": 1,
        "code_barre": "EX001",
        "etat": "neuf",
        "statut": "disponible"
      }
    ]
  }
}
```

---

### Modifier un livre

```http
PUT /api/v1/livres/1
Content-Type: application/json
```

**Body (seuls les champs à modifier) :**
```json
{
  "titre": "Harry Potter à l'école des sorciers",
  "editeur": "Folio Junior"
}
```

**Réponse 200 :**
```json
{
  "message": "Livre mis à jour avec succès.",
  "data": { }
}
```

---

### Supprimer un livre

```http
DELETE /api/v1/livres/1
```

**Réponse 200 :**
```json
{
  "message": "Livre supprimé avec succès."
}
```

> ⚠️ La suppression d'un livre supprime automatiquement tous ses exemplaires (CASCADE).

---

### Créer un exemplaire

```http
POST /api/v1/exemplaires
Content-Type: application/json
```

**Body :**
```json
{
  "livre_id": 1,
  "code_barre": "EX001",
  "etat": "neuf",
  "statut": "disponible"
}
```

**Valeurs acceptées pour `etat` :** `neuf`, `bon`, `acceptable`, `mauvais`  
**Valeurs acceptées pour `statut` :** `disponible`, `emprunte`, `reserve`, `perdu`, `retire`

**Réponse 201 :**
```json
{
  "message": "Exemplaire créé avec succès.",
  "data": {
    "id": 1,
    "livre_id": 1,
    "code_barre": "EX001",
    "etat": "neuf",
    "statut": "disponible",
    "livre": {
      "id": 1,
      "titre": "Harry Potter",
      "auteur": "J.K. Rowling"
    }
  }
}
```

---

### Changer le statut d'un exemplaire

```http
PATCH /api/v1/exemplaires/1/statut
Content-Type: application/json
```

**Body :**
```json
{
  "statut": "emprunte"
}
```

**Réponse 200 :**
```json
{
  "message": "Statut mis à jour.",
  "data": { }
}
```

---

### Lister les exemplaires d'un livre

```http
GET /api/v1/livres/1/exemplaires
```

**Réponse 200 :**
```json
{
  "data": [
    {
      "id": 1,
      "livre_id": 1,
      "code_barre": "EX001",
      "etat": "neuf",
      "statut": "disponible"
    }
  ]
}
```

---

## Gestion des erreurs

### Erreur de validation (422)

Si des champs obligatoires sont manquants ou invalides :

```json
{
  "message": "The titre field is required.",
  "errors": {
    "titre": ["The titre field is required."]
  }
}
```

### Ressource introuvable (404)

```json
{
  "message": "No query results for model [App\\Models\\Livre] 99"
}
```

---

## CORS

L'API accepte les requêtes depuis n'importe quelle origine (`*`).  
Configuration dans `config/cors.php` :

```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

---

## Application Android

Le projet Android (`ApiLivres`) se connecte à cette API via **Retrofit**.

### Configuration de l'URL dans `RetrofitClient.kt`

```kotlin
object RetrofitClient {
    // En local avec émulateur Android
    private const val BASE_URL = "http://10.0.2.2:8000/"

    // Sur le serveur de l'école
    // private const val BASE_URL = "http://bibliothequeapi.merzougui.net.local/"

    val livresApi: LivresApi by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(LivresApi::class.java)
    }
}
```

> `10.0.2.2` est l'adresse spéciale de l'émulateur Android pour accéder à `localhost` du PC.

### Modèle de données Android

```kotlin
data class Livre(
    val id: String,
    val titre: String,
    val auteur: String,
    val isbn: String,
    val exemplaires: List<Exemplaire>? = null
)

data class Exemplaire(
    val id: Int,
    val livre_id: Int,
    val etat: String,
    val statut: String
)

data class LivreResponse(
    val data: List<Livre>  // Wrapper pour la pagination Laravel
)
```

---

## Déploiement ISPConfig

### Prérequis serveur
- PHP >= 8.2
- Composer
- SSH activé

### Étapes

**1. Envoyer les fichiers via FTP (FileZilla)**

Se connecter avec :
- Hôte : `172.31.1.20`
- Port : `21`
- Envoyer tout le projet dans `/web` sauf `vendor/` et `.git/`

**2. Se connecter en SSH**

```bash
ssh merzouguibibli@172.31.1.20
cd ~/web
```

**3. Installer les dépendances**

```bash
composer install --no-dev --ignore-platform-reqs
```

**4. Configurer l'environnement**

Modifier le `.env` sur le serveur :
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://bibliothequeapi.merzougui.net.local
DB_CONNECTION=sqlite
```

**5. Lancer les migrations**

```bash
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
chmod -R 775 storage bootstrap/cache
```

**6. Tester**

```
http://bibliothequeapi.merzougui.net.local/api/v1/livres
```

---

## Auteur

**Shayma MERZOUGUI**  
BTS SIO — 2ème année — 2025-2026  
GitHub : [@Shasha-24](https://github.com/Shasha-24)
