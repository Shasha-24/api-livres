# 📚 API Livres - Laravel

Une API REST pour gérer des livres et leurs exemplaires.
Parce que oui, même les livres ont besoin d'une API en 2026.

Développée en **Laravel 11** avec **SQLite**.

---

## Sommaire

- [Pourquoi ces technologies](#pourquoi-ces-technologies)
- [Comment ça marche](#comment-ça-marche)
- [Installation](#installation)
- [Lancer le serveur](#lancer-le-serveur)
- [Base de données](#base-de-données)
- [Les routes](#les-routes)
- [Endpoints](#endpoints)
- [Exemples de requêtes](#exemples-de-requêtes)
- [Choix techniques](#choix-techniques)
- [Application Android](#application-android)
- [Déploiement ISPConfig](#déploiement-ispconfig)

---

## Pourquoi ces technologies

### Laravel

Laravel c'est un framework PHP qui fait beaucoup de choses automatiquement.
Au lieu d'écrire des requêtes SQL à la main, on utilise **Eloquent** (l'ORM de Laravel) qui traduit le PHP en SQL tout seul.

Par exemple pour récupérer tous les livres :
```php
// Sans Laravel (SQL brut)
$livres = mysqli_query($conn, "SELECT * FROM livres");

// Avec Laravel (Eloquent)
$livres = Livre::all();
```

C'est plus lisible, plus rapide à écrire, et moins d'erreurs.

### SQLite

SQLite c'est une base de données qui tient dans un seul fichier (`database.sqlite`).
Pas besoin d'installer MySQL ou PostgreSQL, pas besoin de configurer un serveur de base de données.
Pour un projet comme celui-ci, c'est largement suffisant.

---

## Comment ça marche

Quand une requête arrive sur l'API, voici ce qui se passe dans l'ordre :

```
Requête HTTP
     ↓
routes/api.php        → "Cette URL va vers quel Controller ?"
     ↓
LivreController.php   → "Qu'est-ce qu'on fait avec cette requête ?"
     ↓
Livre.php (Model)     → "Je vais chercher les données en base"
     ↓
database.sqlite       → Les données
     ↓
Réponse JSON          → Renvoyée au client (appli Android, Postman...)
```

### Le Model

Le Model représente une table en base de données.
`Livre.php` correspond à la table `livres`, `Exemplaire.php` à la table `exemplaires`.

```php
class Livre extends Model
{
    protected $fillable = ['titre', 'auteur', 'isbn', 'editeur', 'description'];

    // Un livre a plusieurs exemplaires
    public function exemplaires()
    {
        return $this->hasMany(Exemplaire::class);
    }
}
```

### Le Controller

Le Controller contient la logique. Il reçoit la requête, fait ce qu'il faut, et renvoie une réponse JSON.

```php
// Exemple : récupérer tous les livres
public function index(): JsonResponse
{
    $livres = Livre::with('exemplaires')->paginate(15);
    return response()->json($livres);
}
```

---

## Installation

### Prérequis

- PHP >= 8.2
- Composer
- Git

```bash
php -v
composer -v
git --version
```

Si une de ces commandes ne marche pas... installe-la.

### Étapes

```bash
git clone https://github.com/Shasha-24/api-livres.git
cd api-livres
composer install
cp .env.example .env
php artisan key:generate
```

Créer le fichier SQLite :

```bash
# Windows
echo "" > database/database.sqlite

# Mac/Linux
touch database/database.sqlite
```

Créer les tables :

```bash
php artisan migrate
```

Si tu vois des `DONE` verts, c'est bon.

---

## Lancer le serveur

```bash
php artisan serve
```

L'API tourne sur : `http://127.0.0.1:8000`

Pour y accéder depuis une tablette ou un téléphone (même réseau WiFi obligatoire) :

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

---

## Base de données

### Relations entre les tables

Un livre peut avoir plusieurs exemplaires. C'est une relation **one-to-many**.

```
livres                    exemplaires
------                    -----------
id  ←─────────────────── livre_id
titre                     code_barre
auteur                    etat
isbn                      statut
editeur
description
```

Dans le code, cette relation se déclare comme ça :

```php
// Dans Livre.php → un livre a plusieurs exemplaires
public function exemplaires()
{
    return $this->hasMany(Exemplaire::class);
}

// Dans Exemplaire.php → un exemplaire appartient à un livre
public function livre()
{
    return $this->belongsTo(Livre::class);
}
```

Grâce à ça on peut écrire :
```php
$livre->exemplaires;      // Tous les exemplaires d'un livre
$exemplaire->livre;       // Le livre d'un exemplaire
```

Si on supprime un livre, tous ses exemplaires sont supprimés automatiquement (CASCADE).

### Table `livres`

| Colonne | Type | Description |
|---------|------|-------------|
| id | integer | Identifiant unique |
| titre | string | Titre du livre (obligatoire) |
| auteur | string | Auteur (obligatoire) |
| isbn | string | ISBN unique (optionnel) |
| editeur | string | Éditeur (optionnel) |
| description | text | Résumé (optionnel) |

### Table `exemplaires`

| Colonne | Type | Description |
|---------|------|-------------|
| id | integer | Identifiant unique |
| livre_id | integer | Référence vers un livre |
| code_barre | string | Code barre physique (optionnel) |
| etat | enum | `neuf`, `bon`, `acceptable`, `mauvais` |
| statut | enum | `disponible`, `emprunte`, `reserve`, `perdu`, `retire` |

---

## Les routes

Toutes les routes sont définies dans `routes/api.php`.

```php
Route::prefix('v1')->group(function () {

    Route::apiResource('livres', LivreController::class);
    Route::get('livres/{livre}/exemplaires', [LivreController::class, 'exemplaires']);

    Route::apiResource('exemplaires', ExemplaireController::class);
    Route::patch('exemplaires/{exemplaire}/statut', [ExemplaireController::class, 'changerStatut']);
});
```

`Route::apiResource` c'est un raccourci Laravel qui génère automatiquement 5 routes en une seule ligne :

```
GET    /api/v1/livres          → index()    (liste)
POST   /api/v1/livres          → store()    (créer)
GET    /api/v1/livres/{id}     → show()     (voir un)
PUT    /api/v1/livres/{id}     → update()   (modifier)
DELETE /api/v1/livres/{id}     → destroy()  (supprimer)
```

Pour voir toutes les routes disponibles :
```bash
php artisan route:list
```

---

## Endpoints

URL de base : `http://127.0.0.1:8000/api/v1`

### Livres

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/livres` | Liste tous les livres (15 par page) |
| POST | `/livres` | Créer un livre |
| GET | `/livres/{id}` | Voir un livre |
| PUT | `/livres/{id}` | Modifier un livre |
| DELETE | `/livres/{id}` | Supprimer un livre |
| GET | `/livres/{id}/exemplaires` | Voir les exemplaires d'un livre |

### Exemplaires

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/exemplaires` | Liste tous les exemplaires (15 par page) |
| POST | `/exemplaires` | Créer un exemplaire |
| GET | `/exemplaires/{id}` | Voir un exemplaire |
| PUT | `/exemplaires/{id}` | Modifier un exemplaire |
| DELETE | `/exemplaires/{id}` | Supprimer un exemplaire |
| PATCH | `/exemplaires/{id}/statut` | Changer uniquement le statut |

---

## Exemples de requêtes

Tu peux tester avec **Postman** ou **curl** (si t'aimes souffrir).

### Lister les livres

```http
GET /api/v1/livres
```

Réponse :
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
      "exemplaires": []
    }
  ],
  "total": 1,
  "per_page": 15
}
```

### Créer un livre

```http
POST /api/v1/livres
Content-Type: application/json
```

```json
{
  "titre": "Petit Pays",
  "auteur": "Gaël Faye",
  "isbn": "9782246859459",
  "editeur": "Grasset",
  "description": "Un enfant franco-rwandais voit son monde basculer."
}
```

Réponse `201` :
```json
{
  "message": "Livre créé avec succès.",
  "data": {
    "id": 2,
    "titre": "Petit Pays",
    "auteur": "Gaël Faye"
  }
}
```

**Champs obligatoires :** `titre`, `auteur`
**Champs optionnels :** `isbn`, `editeur`, `description`

### Modifier un livre

```http
PUT /api/v1/livres/1
Content-Type: application/json
```

```json
{
  "titre": "Harry Potter à l'école des sorciers"
}
```

Tu peux envoyer seulement les champs à modifier.

### Créer un exemplaire

```http
POST /api/v1/exemplaires
Content-Type: application/json
```

```json
{
  "livre_id": 1,
  "code_barre": "EX001",
  "etat": "neuf",
  "statut": "disponible"
}
```

### Changer le statut d'un exemplaire

```http
PATCH /api/v1/exemplaires/1/statut
Content-Type: application/json
```

```json
{
  "statut": "emprunte"
}
```

### Erreurs courantes

| Code | Signification | Solution |
|------|---------------|----------|
| 422 | Données invalides | Vérifie les champs obligatoires |
| 404 | Ressource introuvable | L'ID n'existe pas |
| 500 | Erreur serveur | Regarde les logs dans `storage/logs/` |

---

## Choix techniques

### CORS

Le CORS c'est un mécanisme de sécurité des navigateurs qui bloque les requêtes venant d'un domaine différent. Par défaut, une appli Android ne pourrait pas appeler notre API.

On a configuré le CORS dans `config/cors.php` pour autoriser tout le monde :

```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],   // Toutes les origines autorisées
'allowed_headers' => ['*'],
```

### Pagination

Laravel pagine automatiquement les résultats avec `paginate(15)`.
Ça veut dire que l'API renvoie 15 livres par page maximum, pas toute la base d'un coup.

Pour naviguer entre les pages :
```
GET /api/v1/livres?page=2
```

La réponse contient toujours `current_page`, `total`, `per_page` pour savoir où on en est.

C'est pour ça que dans l'appli Android on a besoin d'un wrapper `LivreResponse` :
```kotlin
data class LivreResponse(
    val data: List<Livre>  // Les livres sont dans "data", pas à la racine
)
```

### Validation

Chaque Controller valide les données reçues avant de les enregistrer.
Si un champ obligatoire manque, Laravel renvoie automatiquement une erreur `422` avec le détail :

```php
$request->validate([
    'titre'  => 'required|string|max:255',
    'auteur' => 'required|string|max:255',
    'isbn'   => 'nullable|string|max:20|unique:livres,isbn',
]);
```

---

## Application Android

L'appli Android (`ApiLivres`) se connecte à cette API via **Retrofit**.

### Comment fonctionne Retrofit

Retrofit transforme des appels Kotlin simples en vraies requêtes HTTP.
On déclare juste les endpoints dans une interface, et Retrofit fait le reste :

```kotlin
interface LivresApi {
    @GET("api/v1/livres")
    suspend fun getLivres(): LivreResponse

    @GET("api/v1/livres/{id}")
    suspend fun getLivreById(@Path("id") id: String): Livre

    @PUT("api/v1/livres/{id}")
    suspend fun editLivre(@Path("id") id: String, @Body livre: Livre): Livre
}
```

Quand on appelle `getLivres()`, Retrofit :
1. Construit la requête `GET http://BASE_URL/api/v1/livres`
2. L'envoie au serveur
3. Reçoit le JSON
4. Le convertit automatiquement en `LivreResponse` grâce à **Gson**

### Changer l'URL selon l'environnement

Dans `RetrofitClient.kt` :

```kotlin
object RetrofitClient {

    // Sur émulateur Android (10.0.2.2 = localhost du PC)
    private const val BASE_URL = "http://10.0.2.2:8000/"

    // Sur téléphone/tablette réel (même réseau WiFi)
    // private const val BASE_URL = "http://192.168.1.136:8000/"

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

> `10.0.2.2` est l'adresse spéciale de l'émulateur Android pour accéder au `localhost` du PC.

### Modèles Kotlin

Les modèles doivent correspondre exactement aux champs renvoyés par l'API :

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

// Wrapper pour la pagination Laravel
// L'API renvoie { "data": [...] } et pas directement une liste
data class LivreResponse(
    val data: List<Livre>
)
```

---

## Déploiement ISPConfig

### 1. Envoyer les fichiers via FTP (FileZilla)

- Hôte : `172.31.1.20` — Port : `21`
- Envoyer tout dans `/web` **sauf** `vendor/` et `.git/`

### 2. Se connecter en SSH

```bash
ssh merzouguibibli@172.31.1.20
cd ~/web
```

### 3. Installer les dépendances

```bash
composer install --no-dev --ignore-platform-reqs
```

### 4. Configurer et migrer

```bash
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
chmod -R 775 storage bootstrap/cache
```

### 5. Tester

```
http://bibliothequeapi.merzougui.net.local/api/v1/livres
```

Si t'es pas à l'école, ça marchera pas. C'est une adresse locale au réseau du lycée.

---

## Auteur

**Shayma MERZOUGUI** — BTS SIO 2ème année 2025-2026

GitHub : [@Shasha-24](https://github.com/Shasha-24)
