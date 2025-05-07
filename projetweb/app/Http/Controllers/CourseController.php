<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cours;
use App\Models\Formation;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les cours de la plateforme
 *
 * Ce contrôleur permet la gestion complète des cours (création, affichage,
 * édition, mise à jour et suppression) ainsi que les inscriptions des étudiants.
 *
 * @package App\Http\Controllers
 */
class CourseController extends Controller
{
    /**
     * Affiche la liste des cours avec options de filtrage
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les filtres
     * @return \Illuminate\View\View               Vue de la liste des cours
     */
    public function index(Request $request): View
    {
        // Initialise la requête pour récupérer les cours avec leurs relations formation et utilisateur
        $query = Cours::with(['formation', 'user']);

        // Si une chaîne de recherche est fournie, filtre les cours en fonction de leur intitulé
        if ($request->input('q')) {
            $query->where('intitule', 'like', '%' . $request->input('q') . '%');
        }
        // Si un enseignant est spécifié, filtre les cours en fonction de l'ID de l'enseignant
        if ($request->input('enseignant')) {
            $query->where('user_id', $request->input('enseignant'));
        }
        // Pagination des résultats de la requête
        $cours = $query->paginate(5);
        // Récupère tous les enseignants
        $enseignants = User::where('type', 'enseignant')->get();
        // Retourne la vue avec les cours et les enseignants
        return view('cours.index', compact('cours', 'enseignants'));
    }

    /**
     * Affiche le formulaire de création d'un cours
     *
     * @return \Illuminate\View\View  Vue du formulaire de création
     */
    public function create(): View
    {
        // Récupère toutes les formations
        $formations = Formation::all();
        // Récupère tous les enseignants
        $enseignants = User::where('type', 'enseignant')->get();
        // Retourne la vue pour créer un nouveau cours avec les formations et les enseignants
        return view('cours.create', compact('formations', 'enseignants'));
    }

    /**
     * Enregistre un nouveau cours dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données du cours
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des cours
     */
    public function store(Request $request): RedirectResponse
    {
        // Valide les données de la requête
        $validated = $request->validate([
            'intitule' => 'required|string|max:50|min:5',
            'formation_id' => 'required|integer|exists:formations,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        
        // Crée une nouvelle instance de Cours avec les données validées
        $course = new Cours([
            'intitule' => $validated['intitule'],
            'formation_id' => $validated['formation_id'],
            'user_id' => $validated['user_id'],
        ]);
        
        // Sauvegarde l'instance de Cours dans la base de données
        $course->save();
        
        // Redirige vers la liste des cours avec un message de succès
        return redirect()->route('cours.index')->with('success', 'Le cours a été créé avec succès et l\'enseignant a été associé.');
    }

    /**
     * Affiche les détails d'un cours spécifique
     * 
     * Vérifie également que l'utilisateur a les droits d'accès à ce cours.
     *
     * @param  int  $id  L'identifiant du cours
     * @return \Illuminate\View\View  Vue des détails du cours
     * @throws \Illuminate\Auth\Access\AuthorizationException  Si l'étudiant n'est pas inscrit au cours
     */
    public function show($id): View
    {
        $course = Cours::with(['formation', 'user', 'plannings'])->findOrFail($id);
        $user = auth()->user();

        // Si l'utilisateur est étudiant, vérifier s'il est inscrit
        if ($user->type === 'etudiant' && !$user->courses->contains($course->id)) {
            abort(403, 'Vous devez être inscrit à ce cours pour y accéder.');
        }

        return view('cours.show', compact('course'));
    }

    /**
     * Affiche le formulaire d'édition d'un cours
     *
     * @param  int  $id  L'identifiant du cours
     * @return \Illuminate\View\View  Vue du formulaire d'édition
     */
    public function edit($id): View
    {
        // Récupère le cours à partir de l'ID fourni, ou génère une erreur si non trouvé
        $course = Cours::findOrFail($id);
        // Récupère toutes les formations
        $formations = Formation::all();
        // Récupère tous les utilisateurs de type 'enseignant'
        $enseignants = User::where('type', 'enseignant')->get();
        // Retourne la vue 'cours.edit' avec le cours récupéré, les formations et les enseignants
        return view('cours.edit', compact('course', 'formations', 'enseignants'));
    }

    /**
     * Met à jour un cours existant
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP avec les nouvelles données
     * @param  int  $id                            L'identifiant du cours à modifier
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des cours
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Valide les données reçues dans la requête
        $validated = $request->validate([
            'intitule' => 'required',
            'user_id' => 'required',
        ]);

        // Récupère le cours à partir de l'ID fourni, ou génère une erreur si non trouvé
        $course = Cours::findOrFail($id);
        
        // Met à jour l'intitulé et l'ID de l'utilisateur du cours avec les données validées
        $course->intitule = $validated['intitule'];
        $course->user_id = $validated['user_id'];
        
        // Enregistre les modifications apportées au cours
        $course->save();
        
        // Redirige vers la liste des cours avec un message de succès
        return redirect()->route('cours.index', $course->id)->with('success', 'Le cours a été modifié avec succès.');
    }

    /**
     * Supprime un cours et toutes ses données associées
     *
     * @param  int  $id  L'identifiant du cours à supprimer
     * @return \Illuminate\Http\RedirectResponse  Redirection vers la liste des cours
     */
    public function destroy($id): RedirectResponse
    {
        // Récupère le cours à partir de l'ID fourni, ou génère une erreur si non trouvé
        $course = Cours::findOrFail($id);

        // Supprime les séances de cours associées
        foreach ($course->plannings as $session) {
            $session->delete();
        }

        // Désinscrire tous les étudiants inscrits à ce cours
        $course->students()->detach();
        
        // Supprime le cours
        $course->delete();
        
        // Redirige vers la liste des cours avec un message de succès
        return redirect()->route('cours.index')->with('success', 'Le cours a été supprimé avec succès.');
    }

    /**
     * Affiche les cours disponibles pour un étudiant
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les filtres
     * @return \Illuminate\View\View               Vue des cours disponibles pour l'étudiant
     */
    public function studentCourses(Request $request): View
    {
        $user = auth()->user()->loadMissing('courses');
        $formation_id = $user->formation_id;
        $search = $request->input('search');

        $courses = Cours::with(['formation', 'user', 'users'])
            ->where('formation_id', $formation_id)
            ->when($search, function ($query, $search) {
                return $query->where('intitule', 'like', '%' . $search . '%');
            })
            ->paginate(3);

        return view('cours.student', compact('courses', 'user'));
    }

    /**
     * Inscrit l'utilisateur connecté à un cours
     *
     * @param  int  $id  L'identifiant du cours
     * @return \Illuminate\Http\RedirectResponse  Redirection vers la liste des cours
     */
    public function enroll(int $id): RedirectResponse
    {
        // On récupère l'utilisateur connecté
        $user = auth()->user();
        // On trouve le cours spécifique à l'aide de l'ID passé en paramètre
        $course = Cours::findOrFail($id);
        
        // Si l'utilisateur est déjà inscrit au cours, on redirige avec un message d'avertissement
        if ($user->courses->contains($course)) {
            return redirect()->route('student.courses')->with('warning', 'Vous êtes déjà inscrit à ce cours.');
        }
        
        // On inscrit l'utilisateur au cours en utilisant la méthode attach() 
        $course->students()->attach($user->id);
        
        // On redirige vers la liste des cours avec un message de succès
        return redirect()->route('student.courses')->with('success', 'Vous êtes maintenant inscrit au cours.');
    }

    /**
     * Désinscrit l'utilisateur connecté d'un cours
     *
     * @param  int  $id  L'identifiant du cours
     * @return \Illuminate\Http\RedirectResponse  Redirection vers la liste des cours
     */
    public function unenroll(int $id): RedirectResponse
    {
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
        // Récupérer le cours correspondant à l'ID fourni
        $course = Cours::findOrFail($id);
        
        // Si l'utilisateur n'est pas inscrit à ce cours, rediriger avec un message d'avertissement
        if (!$user->courses->contains($id)) {
            return redirect()->route('student.courses')->with('warning', 'Vous n\'êtes pas inscrit à ce cours.');
        }
        
        // Désinscrire l'utilisateur du cours
        $course->students()->detach($user->id);
        
        // Rediriger avec un message de succès
        return redirect()->route('student.courses')->with('danger', 'Vous êtes désinscrit du cours.');
    }
}