<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annonce;
use App\Models\Cours;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Contrôleur gérant les fonctionnalités liées aux annonces de cours
 * 
 * Ce contrôleur permet de créer, afficher et supprimer des annonces
 * associées à un cours spécifique dans l'application.
 * 
 * @package App\Http\Controllers
 */
class AnnonceController extends Controller
{
    /**
     * Crée une nouvelle annonce pour un cours
     * 
     * Cette méthode valide les données soumises via le formulaire,
     * puis crée une nouvelle annonce associée au cours spécifié.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de l'annonce
     * @param  int  $coursId                       L'identifiant du cours auquel l'annonce est associée
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function store(Request $request, $coursId): RedirectResponse
    {
        // Valide les données du formulaire
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);

        // Crée une nouvelle annonce avec les données validées
        Annonce::create([
            'cours_id' => $coursId,
            'titre' => $validated['titre'],
            'contenu' => $validated['contenu'],
        ]);

        // Redirige vers la page précédente avec un message de succès
        return back()->with('success', 'Annonce ajoutée avec succès.');
    }

    /**
     * Affiche la liste des annonces pour un cours spécifique
     * 
     * Cette méthode récupère le cours demandé avec ses annonces associées,
     * puis les affiche dans une vue dédiée.
     *
     * @param  int  $coursId               L'identifiant du cours dont on souhaite voir les annonces
     * @return \Illuminate\View\View       Vue affichant les annonces du cours
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  Si le cours n'existe pas
     */
    public function index($coursId): View
    {
        // Récupère le cours avec ses annonces via le chargement anticipé (eager loading)
        $cours = Cours::with('annonces')->findOrFail($coursId);
        
        // Récupère les annonces du cours
        $annonces = $cours->annonces;

        // Retourne la vue avec le cours et ses annonces
        return view('annonces.index', compact('cours', 'annonces'));
    }

    /**
     * Supprime une annonce spécifique d'un cours
     * 
     * Cette méthode vérifie que l'annonce appartient bien au cours spécifié,
     * puis procède à sa suppression.
     *
     * @param  int  $coursId                       L'identifiant du cours
     * @param  int  $annonceId                     L'identifiant de l'annonce à supprimer
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  Si l'annonce n'existe pas ou n'appartient pas au cours
     */
    public function destroy($coursId, $annonceId): RedirectResponse
    {
        // Trouve l'annonce qui appartient au cours spécifié
        $annonce = Annonce::where('cours_id', $coursId)->findOrFail($annonceId);
        
        // Supprime l'annonce
        $annonce->delete();

        // Redirige vers la page précédente avec un message de succès
        return back()->with('success', 'Annonce supprimée avec succès.');
    }
}