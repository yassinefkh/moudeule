<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formation;
use App\Models\User;
use App\Models\Cours;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur gérant les formations de la plateforme
 *
 * Ce contrôleur permet la gestion complète des formations (création, affichage,
 * édition, mise à jour et suppression) dans le système.
 *
 * @package App\Http\Controllers
 */
class FormationController extends Controller
{
    /**
     * Affiche la liste des formations avec leurs statistiques
     *
     * @return \Illuminate\View\View  Vue de la liste des formations
     */
    public function index(): View
    {
        $formations = Formation::withCount(['students as etudiants_count', 'cours'])
            ->orderBy('id', 'desc')
            ->paginate(3);

        return view('admin.formations.index', compact('formations'));
    }

    /**
     * Affiche le formulaire de création d'une formation
     *
     * @return \Illuminate\View\View  Vue du formulaire de création
     */
    public function create(): View
    {
        return view('admin.formations.create');
    }

    /**
     * Enregistre une nouvelle formation dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de la formation
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des formations
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs
        $validated = $request->validate([
            'intitule' => 'required|string|max:255|unique:formations',
        ]);

        // Création de la formation en base de données
        Formation::create([
            'intitule' => $validated['intitule'],
        ]);
        
        // Redirection vers la page d'index des formations avec un message de succès
        return redirect()->route('admin.formations.index')->with('success', 'Formation créée avec succès');
    }

    /**
     * Affiche le formulaire d'édition d'une formation
     *
     * @param  \App\Models\Formation  $formation  La formation à modifier
     * @return \Illuminate\View\View              Vue du formulaire d'édition
     */
    public function edit(Formation $formation): View
    {
        return view('admin.formations.edit', compact('formation'));
    }

    /**
     * Met à jour une formation existante
     *
     * @param  \Illuminate\Http\Request  $request     La requête HTTP avec les nouvelles données
     * @param  \App\Models\Formation  $formation      La formation à mettre à jour
     * @return \Illuminate\Http\RedirectResponse      Redirection vers la liste des formations
     */
    public function update(Request $request, Formation $formation): RedirectResponse
    {
        $validated = $request->validate([
            'intitule' => 'required|string|max:255|unique:formations,intitule,' . $formation->id,
        ]);
        
        // Mise à jour de l'intitulé de la formation dans la base de données
        $formation->update([
            'intitule' => $validated['intitule'],
        ]);

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour avec succès');
    }

    /**
     * Affiche la page de confirmation de suppression d'une formation
     *
     * @param  int  $id  L'identifiant de la formation à supprimer
     * @return \Illuminate\View\View  Vue de confirmation de suppression
     */
    public function confirmDestroy($id): View
    {
        $formation = Formation::withCount('students')->with('students')->findOrFail($id);
        return view('admin.formations.confirm-delete', compact('formation'));
    }

    /**
     * Supprime une formation et toutes ses données associées
     *
     * Cette méthode utilise une transaction de base de données pour assurer l'intégrité
     * des données lors de la suppression d'une formation et de ses relations.
     *
     * @param  int  $id                            L'identifiant de la formation à supprimer
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant la confirmation
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des formations
     */
    public function destroy($id, Request $request): RedirectResponse
    {
        $formation = Formation::withCount(['students', 'cours'])->findOrFail($id);
        $etudiantsCount = $formation->students_count;
        $coursCount = $formation->cours_count;

        if (!$request->has('confirm') || $request->confirm !== 'yes') {
            return redirect()->route('admin.formations.confirm-destroy', $formation->id);
        }

        try {
            DB::beginTransaction();

            $coursIds = $formation->cours()->pluck('id')->toArray();

            if (!empty($coursIds)) {
                // 1. Supprimer les entrées dans la table pivot cours_users
                DB::table('cours_users')->whereIn('cours_id', $coursIds)->delete();

                // 2. Recherche et suppression de toutes les entités qui référencent un cours
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table';");

                foreach ($tables as $table) {
                    $tableName = $table->name;

                    // Ignorer certaines tables système
                    if (
                        $tableName == 'migrations' || $tableName == 'sqlite_sequence' ||
                        $tableName == 'password_reset_tokens' || $tableName == 'personal_access_tokens'
                    ) {
                        continue;
                    }

                    // Vérifier si cette table a une colonne cours_id
                    $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

                    if (in_array('cours_id', $columns)) {
                        // Cette table a une référence à cours, supprimons les entrées correspondantes
                        DB::table($tableName)->whereIn('cours_id', $coursIds)->delete();
                    }
                }
            }

            // Supprimer les cours de la formation
            DB::table('cours')->where('formation_id', $formation->id)->delete();

            // Supprimer les étudiants de la formation
            User::where('formation_id', $formation->id)
                ->where('type', 'etudiant')
                ->delete();

            // Mettre à jour les utilisateurs associés à cette formation
            User::where('formation_id', $formation->id)
                ->update(['formation_id' => null]);

            // Supprimer la formation
            $formation->delete();

            DB::commit();

            $message = 'La formation a été supprimée avec succès.';
            if ($etudiantsCount > 0) {
                $message .= ' ' . $etudiantsCount . ' compte(s) étudiant(s) ont été supprimés.';
            }
            if ($coursCount > 0) {
                $message .= ' ' . $coursCount . ' cours ont été supprimés.';
            }

            return redirect()->route('admin.formations.index')
                ->with('warning', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.formations.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage() .
                    (config('app.debug') ? ' - Trace: ' . $e->getTraceAsString() : ''));
        }
    }

    /**
     * Affiche les détails d'une formation avec ses relations
     *
     * @param  int  $id  L'identifiant de la formation
     * @return \Illuminate\View\View  Vue des détails de la formation
     */
    public function show($id): View
    {
        $formation = Formation::with([
            'students' => function ($q) {
                $q->orderBy('nom');
            },
            'courses.user', // matière + enseignant
        ])->findOrFail($id);

        $etudiants = $formation->students; // utilisateurs avec type 'etudiant'
        $cours = $formation->courses; // avec .user déjà chargé (enseignant)
        $enseignants = $cours->pluck('user')->unique('id'); // liste unique

        return view('formations.show', compact('formation', 'etudiants', 'cours', 'enseignants'));
    }
}