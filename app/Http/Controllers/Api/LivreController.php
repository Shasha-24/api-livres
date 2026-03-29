<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class LivreController extends Controller
{

    public function index(): JsonResponse
    {
        $livres = Livre::with('exemplaires')->paginate(15);
        return response()->json($livres);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'auteur'      => 'required|string|max:255',
            'isbn'        => 'nullable|string|max:20|unique:livres,isbn',
            'editeur'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $livre = Livre::create($validated);

        return response()->json([
            'message' => 'Livre créé avec succès.',
            'data'    => $livre,
        ], 201);
    }


    public function show(Livre $livre): JsonResponse
    {
        $livre->load('exemplaires');
        return response()->json(['data' => $livre]);
    }


    public function update(Request $request, Livre $livre): JsonResponse
    {
        $validated = $request->validate([
            'titre'       => 'sometimes|required|string|max:255',
            'auteur'      => 'sometimes|required|string|max:255',
            'isbn'        => ['nullable', 'string', 'max:20',
                Rule::unique('livres','isbn')->ignore($livre->id)],
            'editeur'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $livre->update($validated);

        return response()->json([
            'message' => 'Livre mis à jour avec succès.',
            'data'    => $livre,
        ]);
    }


    public function destroy(Livre $livre): JsonResponse
    {
        $livre->delete();
        return response()->json(['message' => 'Livre supprimé avec succès.']);
    }


    public function exemplaires(Livre $livre): JsonResponse
    {
        return response()->json(['data' => $livre->exemplaires]);
    }
}
