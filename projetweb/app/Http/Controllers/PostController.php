<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les publications du forum
 *
 * Ce contrôleur permet la création, l'affichage, et la suppression
 * des publications du forum de la plateforme.
 *
 * @package App\Http\Controllers
 */
class PostController extends Controller
{
    /**
     * Affiche la liste des publications du forum
     *
     * @return \Illuminate\View\View  Vue de la liste des publications
     */
    public function index(): View
    {
        $posts = Post::with('user')->latest()->paginate(4);
        return view('forum.index', compact('posts'));
    }

    /**
     * Affiche le formulaire de création d'une publication
     *
     * @return \Illuminate\View\View  Vue du formulaire de création
     */
    public function create(): View
    {
        return view('forum.create');
    }

    /**
     * Enregistre une nouvelle publication dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de la publication
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des publications
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|max:255',
            'contenu' => 'required',
        ]);

        Post::create([
            'titre' => $validated['titre'],
            'contenu' => $validated['contenu'],
            'user_id' => Auth::id(),
            'reactions_count' => 0,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post créé avec succès.');
    }

    /**
     * Supprime une publication et toutes ses données associées
     *
     * @param  \App\Models\Post  $post              La publication à supprimer
     * @return \Illuminate\Http\RedirectResponse    Redirection vers la liste des publications
     * @throws \Illuminate\Auth\Access\AuthorizationException  Si l'utilisateur n'est pas administrateur
     */
    public function destroy(Post $post): RedirectResponse
    {
        if (auth()->user()->type !== 'admin') {
            abort(403);
        }

        $post->comments()->delete();
        $post->reactions()->delete();
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post supprimé avec succès.');
    }

    /**
     * Affiche les détails d'une publication spécifique
     *
     * @param  int  $id  L'identifiant de la publication
     * @return \Illuminate\View\View  Vue des détails de la publication
     */
    public function show($id): View
    {
        $post = Post::with(['user', 'comments.user', 'reactions'])->findOrFail($id);
        return view('forum.show', compact('post'));
    }
}