<?php

use App\Http\Controllers\Api\ExemplaireController;
use App\Http\Controllers\Api\LivreController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {


    Route::apiResource('livres', LivreController::class);


    Route::get('livres/{livre}/exemplaires', [LivreController::class, 'exemplaires'])
        ->name('livres.exemplaires');


    Route::apiResource('exemplaires', ExemplaireController::class);


    Route::patch('exemplaires/{exemplaire}/statut', [ExemplaireController::class, 'changerStatut'])
        ->name('exemplaires.statut');
});
