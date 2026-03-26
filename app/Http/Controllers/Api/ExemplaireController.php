<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exemplaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ExemplaireController extends Controller
{
    // GET /api/v1/exemplaires → liste tous les exemplaires
    public function index(): JsonResponse
    {
        $exemplaires = Exemplaire::with('livre')->paginate(15);
        return response()->json($exemplaires);
    }

    // POST /api/v1/exemplaires → créer un exemplaire
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'livre_id'   => 'required|exists:livres,id',
            'code_barre' => 'nullable|string|max:100|unique:exemplaires,code_barre',
            'etat'       => ['nullable', Rule::in(['neuf','bon','acceptable','mauvais'])],
            'statut'     => ['nullable', Rule::in(['disponible','emprunte','reserve','perdu','retire'])],
        ]);

        $exemplaire = Exemplaire::create($validated);
        $exemplaire->load('livre');

        return response()->json([
            'message' => 'Exemplaire créé avec succès.',
            'data'    => $exemplaire,
        ], 201);
    }

    // GET /api/v1/exemplaires/{id} → voir un exemplaire
    public function show(Exemplaire $exemplaire): JsonResponse
    {
        $exemplaire->load('livre');
        return response()->json(['data' => $exemplaire]);
    }

    // PUT /api/v1/exemplaires/{id} → modifier un exemplaire
    public function update(Request $request, Exemplaire $exemplaire): JsonResponse
    {
        $validated = $request->validate([
            'livre_id' => 'sometimes|required|exists:livres,id',
            'etat'     => ['nullable', Rule::in(['neuf','bon','acceptable','mauvais'])],
            'statut'   => ['nullable', Rule::in(['disponible','emprunte','reserve','perdu','retire'])],
        ]);

        $exemplaire->update($validated);
        $exemplaire->load('livre');

        return response()->json([
            'message' => 'Exemplaire mis à jour avec succès.',
            'data'    => $exemplaire,
        ]);
    }

    // DELETE /api/v1/exemplaires/{id} → supprimer un exemplaire
    public function destroy(Exemplaire $exemplaire): JsonResponse
    {
        $exemplaire->delete();
        return response()->json(['message' => 'Exemplaire supprimé avec succès.']);
    }

    // PATCH /api/v1/exemplaires/{id}/statut → changer le statut
    public function changerStatut(Request $request, Exemplaire $exemplaire): JsonResponse
    {
        $validated = $request->validate([
            'statut' => ['required', Rule::in(['disponible','emprunte','reserve','perdu','retire'])],
        ]);

        $exemplaire->update(['statut' => $validated['statut']]);

        return response()->json([
            'message' => 'Statut mis à jour.',
            'data'    => $exemplaire,
        ]);
    }
}
