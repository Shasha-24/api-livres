<?php

use App\Http\Controllers\Api\ExemplaireController;
use App\Http\Controllers\Api\LivreController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Livres ──────────────────────────────────────────
    Route::apiResource('livres', LivreController::class);

    // GET /api/v1/livres/{id}/exemplaires
    Route::get('livres/{livre}/exemplaires', [LivreController::class, 'exemplaires'])
        ->name('livres.exemplaires');

    // ── Exemplaires ─────────────────────────────────────
    Route::apiResource('exemplaires', ExemplaireController::class);

    // PATCH /api/v1/exemplaires/{id}/statut
    Route::patch('exemplaires/{exemplaire}/statut', [ExemplaireController::class, 'changerStatut'])
        ->name('exemplaires.statut');
});
