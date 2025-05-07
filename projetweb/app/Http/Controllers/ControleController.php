<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Cours;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les contrôles dans les cours
 *
 * Ce contrôleur permet la gestion complète des contrôles (création, affichage,
 * édition, mise à jour et suppression) pour un cours spécifique.
 *
 * @package App\Http\Controllers
 */
class ControleController extends Controller
{
    /**
     * Affiche la liste des contrôles pour un cours spécifique
     *
     * @param  int  $coursId  L'identifiant du cours
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue de la liste des contrôles ou redirection
     */
    public function index($coursId)
    {
        $course = Cours::findOrFail($coursId);
        
        // Vérifier que l'utilisateur a accès à ce cours
        if (Auth::user()->type === 'etudiant' && !$course->students->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à ce cours.');
        }
        
        $controles = $course->controles()->orderBy('date_controle', 'desc')->get();
        
        return view('controles.index', compact('course', 'controles'));
    }
    
    /**
     * Affiche le formulaire de création d'un contrôle
     *
     * @param  int  $coursId  L'identifiant du cours
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue du formulaire ou redirection
     */
    public function create($coursId)
    {
        $course = Cours::findOrFail($coursId);
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer un contrôle.');
        }
        
        return view('controles.create', compact('course'));
    }
    
    /**
     * Enregistre un nouveau contrôle dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données du contrôle
     * @param  int  $coursId                       L'identifiant du cours
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des contrôles
     */
    public function store(Request $request, $coursId): RedirectResponse
    {
        $course = Cours::findOrFail($coursId);
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer un contrôle.');
        }
        
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_controle' => 'required|date',
            'coefficient' => 'required|numeric|min:0.1|max:10'
        ]);
        
        $controle = new Controle([
            'cours_id' => $course->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'date_controle' => $validated['date_controle'],
            'coefficient' => $validated['coefficient']
        ]);
        
        $controle->save();
        
        return redirect()->route('controles.index', $course->id)
            ->with('success', 'Contrôle créé avec succès.');
    }
    
    /**
     * Affiche les détails d'un contrôle
     *
     * @param  int  $coursId     L'identifiant du cours
     * @param  int  $controleId  L'identifiant du contrôle
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue des détails ou redirection
     */
    public function show($coursId, $controleId)
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur a accès à ce cours
        if (Auth::user()->type === 'etudiant' && !$course->students->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à ce cours.');
        }
        
        $students = $course->students;
        $notes = $controle->notes;
        
        return view('controles.show', compact('course', 'controle', 'students', 'notes'));
    }
    
    /**
     * Affiche le formulaire d'édition d'un contrôle
     *
     * @param  int  $coursId     L'identifiant du cours
     * @param  int  $controleId  L'identifiant du contrôle
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue du formulaire ou redirection
     */
    public function edit($coursId, $controleId)
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier un contrôle.');
        }
        
        return view('controles.edit', compact('course', 'controle'));
    }
    
    /**
     * Met à jour un contrôle existant
     *
     * @param  \Illuminate\Http\Request  $request     La requête HTTP avec les nouvelles données
     * @param  int  $coursId                          L'identifiant du cours
     * @param  int  $controleId                       L'identifiant du contrôle
     * @return \Illuminate\Http\RedirectResponse      Redirection vers les détails du contrôle
     */
    public function update(Request $request, $coursId, $controleId): RedirectResponse
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de modifier un contrôle.');
        }
        
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_controle' => 'required|date',
            'coefficient' => 'required|numeric|min:0.1|max:10'
        ]);
        
        $controle->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'date_controle' => $validated['date_controle'],
            'coefficient' => $validated['coefficient']
        ]);
        
        return redirect()->route('controles.show', [$course->id, $controle->id])
            ->with('success', 'Contrôle mis à jour avec succès.');
    }
    
    /**
     * Supprime un contrôle et ses données associées
     *
     * @param  int  $coursId                       L'identifiant du cours
     * @param  int  $controleId                    L'identifiant du contrôle
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des contrôles
     */
    public function destroy($coursId, $controleId): RedirectResponse
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de supprimer un contrôle.');
        }
        
        // Les notes associées sont supprimées automatiquement grâce à la contrainte ON DELETE CASCADE
        $controle->delete();
        
        return redirect()->route('controles.index', $course->id)
            ->with('success', 'Contrôle supprimé avec succès.');
    }
}