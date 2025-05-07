<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les réactions aux publications
 *
 * Ce contrôleur permet aux utilisateurs de réagir aux publications
 * avec différents types d'émojis.
 *
 * @package App\Http\Controllers
 */
class ReactionController extends Controller
{
    /**
     * Ajoute ou met à jour une réaction à une publication
     *
     * Cette méthode permet à un utilisateur de réagir à une publication.
     * Si l'utilisateur a déjà réagi à cette publication, sa réaction est mise à jour.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de la réaction
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la page précédente
     */
    public function react(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'emoji' => 'required|string'
        ]);

        Reaction::updateOrCreate(
            ['user_id' => Auth::id(), 'post_id' => $validated['post_id']],
            ['emoji' => $validated['emoji']]
        );

        return back();
    }
}