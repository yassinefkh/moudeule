<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;
use App\Models\Section;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les sections de cours
 *
 * Ce contrôleur permet la création, la modification et la suppression
 * de sections organisationnelles au sein d'un cours.
 *
 * @package App\Http\Controllers
 */
class SectionController extends Controller
{
    /**
     * Crée une nouvelle section pour un cours
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de la section
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la page du cours
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'cours_id' => 'required|integer|exists:cours,id',
            'ordre' => 'nullable|integer|min:1',
        ]);

        $coursId = $validated['cours_id'];

        $ordre = $request->filled('ordre')
            ? $validated['ordre']
            : (Section::where('cours_id', $coursId)->max('ordre') ?? 0) + 1;

        $section = new Section([
            'titre' => $validated['titre'],
            'cours_id' => $coursId,
            'ordre' => $ordre,
        ]);

        $section->save();

        return redirect()->route('cours.show', $coursId)->with('success', 'Section ajoutée avec succès.');
    }

    /**
     * Met à jour une section existante
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP avec les nouvelles données
     * @param  int  $sectionId                     L'identifiant de la section à mettre à jour
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la page du cours associé
     */
    public function update(Request $request, $sectionId): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'ordre' => 'required|integer|min:1',
        ]);

        $section = Section::findOrFail($sectionId);
        $section->titre = $validated['titre'];
        $section->ordre = $validated['ordre'];
        $section->save();

        return redirect()->route('cours.show', $section->cours_id)->with('success', 'Section mise à jour avec succès.');
    }

    /**
     * Supprime une section et tous ses documents associés
     *
     * @param  int  $sectionId                     L'identifiant de la section à supprimer
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la page du cours associé
     */
    public function destroy($sectionId): RedirectResponse
    {
        $section = Section::findOrFail($sectionId);
        $coursId = $section->cours_id;

        // Supprimer tous les documents associés à cette section
        foreach ($section->documents as $doc) {
            $doc->delete();
        }

        // Supprimer la section
        $section->delete();

        return redirect()->route('cours.show', $coursId)->with('success', 'Section supprimée avec succès.');
    }
}