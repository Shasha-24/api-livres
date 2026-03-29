# API Livres - Laravel

API REST pour la gestion de livres et d'exemplaires.

## Prérequis
- PHP 8.x
- Laravel 11
- PostgreSQL

## Installation
```bash
git clone https://github.com/TON_PSEUDO/api-livres.git
cd api-livres
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Endpoints

### Livres
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | /api/v1/livres | Liste tous les livres |
| POST | /api/v1/livres | Créer un livre |
| GET | /api/v1/livres/{id} | Voir un livre |
| PUT | /api/v1/livres/{id} | Modifier un livre |
| DELETE | /api/v1/livres/{id} | Supprimer un livre |
| GET | /api/v1/livres/{id}/exemplaires | Exemplaires d'un livre |

### Exemplaires
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | /api/v1/exemplaires | Liste tous les exemplaires |
| POST | /api/v1/exemplaires | Créer un exemplaire |
| GET | /api/v1/exemplaires/{id} | Voir un exemplaire |
| PUT | /api/v1/exemplaires/{id} | Modifier un exemplaire |
| DELETE | /api/v1/exemplaires/{id} | Supprimer un exemplaire |
| PATCH | /api/v1/exemplaires/{id}/statut | Changer le statut |

## Exemple de requête

### Créer un livre
POST /api/v1/livres
```json
{
    "titre": "Harry Potter",
    "auteur": "J.K. Rowling",
    "isbn": "9782070584628",
    "editeur": "Gallimard",
    "description": "Un jeune sorcier découvre ses pouvoirs"
}
```

### Réponse
```json
{
    "message": "Livre créé avec succès.",
    "data": {
        "id": 1,
        "titre": "Harry Potter",
        "auteur": "J.K. Rowling",
        "isbn": "9782070584628",
        "editeur": "Gallimard",
        "description": "Un jeune sorcier découvre ses pouvoirs",
        "created_at": "2026-03-27T10:00:00Z",
        "updated_at": "2026-03-27T10:00:00Z"
    }
}
```
