<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les commentaires sur les publications
 *
 * Ce contrôleur permet la création de commentaires liés aux publications,
 * y compris les réponses à d'autres commentaires (commentaires imbriqués).
 *
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{
    /**
     * Enregistre un nouveau commentaire dans la base de données
     *
     * Cette méthode valide les données soumises via le formulaire,
     * crée un nouveau commentaire associé à une publication et à l'utilisateur connecté,
     * puis redirige vers la page précédente.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données du commentaire
     * @return \Illuminate\Http\RedirectResponse    Redirection avec message de succès
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'contenu' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Création du commentaire
        Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => Auth::id(),
            'contenu' => $validated['contenu'],
            'parent_id' => $validated['parent_id'] ?? null,

        ]);

        return back()->with('success', 'Commentaire ajouté avec succès.');
    }
}