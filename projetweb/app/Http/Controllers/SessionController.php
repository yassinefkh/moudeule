<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planning;
use App\Models\Cours;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les séances de cours et le planning
 *
 * Ce contrôleur permet de créer, afficher, modifier et supprimer des séances de cours,
 * ainsi que d'afficher les plannings spécifiques aux étudiants et aux enseignants.
 * 
 * @package App\Http\Controllers
 */
class SessionController extends Controller
{
    /**
     * Affiche la liste des séances de cours avec options de filtrage
     *
     * Cette méthode récupère les séances de cours en fonction du type d'utilisateur
     * (administrateur ou enseignant), avec possibilité de filtrer par semaine et
     * de trier par cours.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les paramètres de filtre
     * @return \Illuminate\View\View               Vue avec la liste des séances de cours paginées
     */
    public function index(Request $request): View
    {
        // Récupère la valeur de la semaine sélectionnée
        $week = $request->input('week');
        // Récupère la valeur pour le tri par cours
        $sortByCourse = $request->input('sort_by_course');

        // Initialise la requête pour récupérer les séances de cours
        if (Auth::user()->type === 'admin') {
            // Si l'utilisateur est un administrateur, récupère toutes les séances de cours avec les informations du cours et de l'utilisateur associés
            $planning = Planning::join('cours', 'plannings.cours_id', '=', 'cours.id')
                ->join('users', 'cours.user_id', '=', 'users.id')
                ->select('plannings.*', 'cours.intitule', 'users.nom', 'users.prenom');
        } else {
            // Sinon, récupère les séances de cours pour les cours enseignés par l'utilisateur connecté avec les informations du cours et de l'utilisateur associés
            $planning = Planning::join('cours', 'plannings.cours_id', '=', 'cours.id')
                ->join('users', 'cours.user_id', '=', 'users.id')
                ->where('cours.user_id', '=', Auth::user()->id)
                ->select('plannings.*', 'cours.intitule', 'users.nom', 'users.prenom');
        }

        // Filtre les séances de cours par semaine si une semaine a été sélectionnée
        if ($week == 'current') {
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
            $planning = $planning->whereBetween('date_debut', [$startOfWeek, $endOfWeek]);
        }
        // Trie les séances de cours par cours si l'option est sélectionnée
        if ($sortByCourse) {
            $planning = $planning->orderBy('cours.intitule');
        }

        $planning = $planning->paginate(5);

        // Renvoie la vue avec les séances de cours paginées
        return view('planning.index', compact('planning'));
    }

    /**
     * Affiche le formulaire de création d'une séance de cours
     *
     * Cette méthode affiche le formulaire pour créer une nouvelle séance de cours
     * en récupérant les cours disponibles selon le type d'utilisateur.
     *
     * @return \Illuminate\View\View  Vue du formulaire de création
     */
    public function create(): View
    {
        if (Auth::user()->type === 'enseignant') {
            $courses = Cours::where('user_id', Auth::id())->get();
        } else {
            $courses = Cours::all();
        }

        return view('planning.create', compact('courses'));
    }

    /**
     * Enregistre une nouvelle séance de cours
     *
     * Cette méthode valide les données du formulaire, vérifie la durée minimale
     * requise pour une séance, puis crée et enregistre la nouvelle séance.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données de la séance
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des séances ou message d'erreur
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:cours,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $dateDebut = strtotime($validated['date_debut']);
        $dateFin = strtotime($validated['date_fin']);
        $duree = ($dateFin - $dateDebut) / 60;

        if ($duree < 15) {
            return redirect()->back()->withErrors([
                'La durée de la séance de cours doit durer au moins 15 minutes !'
            ]);
        }

        $course = Cours::find($validated['course_id']);

        $session = new Planning([
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
        ]);

        $course->plannings()->save($session);

        return redirect()->route('planning.index')->with('success', 'La séance de cours a été créée avec succès.');
    }


    /**
     * Affiche le formulaire d'édition d'une séance de cours
     *
     * @param  int  $id  L'identifiant de la séance à modifier
     * @return \Illuminate\View\View  Vue du formulaire d'édition
     */
    public function edit($id): View
    {
        $session = Planning::findOrFail($id);
        return view('planning.edit', compact('session'));
    }

    /**
     * Met à jour une séance de cours existante
     *
     * Cette méthode valide les données du formulaire, vérifie la durée minimale
     * requise pour une séance, puis met à jour la séance existante.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les nouvelles données
     * @param  int  $id                            L'identifiant de la séance à modifier
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la liste des séances ou message d'erreur
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $dateDebut = strtotime($request->input('date_debut'));
        $dateFin = strtotime($request->input('date_fin'));
        $duree = ($dateFin - $dateDebut) / 60;

        if ($duree < 15) {
            return redirect()->back()->withErrors(['La durée de la séance de cours doit durer au moins 15 minutes ! Quand même... ce ne serait pas un cours sinon mais un podcast Konbini.']);
        }

        // Récupération de la séance de cours à modifier
        $session = Planning::findOrFail($id);
        $session->date_debut = $request->input('date_debut');
        $session->date_fin = $request->input('date_fin');
        $session->save();

        // Redirection vers la page des séances de cours avec un message de succès en fonction du type d'utilisateur
        if (Auth::user()->type == 'admin') {
            return redirect()->route('planning.index')->with('success', 'La séance de cours a été modifiée avec succès.');
        } else {
            return redirect()->route('planning.index', $session->cours->id)->with('success', 'La séance de cours a été modifiée avec succès.');
        }
    }

    /**
     * Supprime une séance de cours
     *
     * @param  int  $id  L'identifiant de la séance à supprimer
     * @return \Illuminate\Http\RedirectResponse  Redirection avec message de succès
     */
    public function destroy($id): RedirectResponse
    {
        // Récupère la séance de cours correspondant à l'id
        $session = Planning::findOrFail($id);
        // Récupère l'ID du cours associé à la séance de cours
        $courseId = $session->cours->id;
        // Supprime la séance de cours de la base de données
        $session->delete();

        return redirect()->route('planning.index', $courseId)->with('success', 'La séance de cours a été supprimée avec succès.');
    }

    /**
     * Affiche le planning hebdomadaire d'un étudiant
     *
     * Cette méthode génère une vue calendaire du planning d'un étudiant
     * pour une semaine spécifique ou la semaine courante par défaut.
     *
     * @param  int|null  $week  Le numéro de la semaine à afficher (null = semaine courante)
     * @return \Illuminate\View\View  Vue du planning étudiant
     */
    public function studentPlanning($week = null): View
    {
        // Récupère l'id de l'utilisateur actuel
        $user_id = auth()->id();
        // Récupère les séances de cours associées aux cours suivis par l'utilisateur actuel
        $planning = Planning::join('cours', 'plannings.cours_id', '=', 'cours.id')
            ->join('cours_users', 'cours.id', '=', 'cours_users.cours_id')
            ->join('users', 'cours.user_id', '=', 'users.id')
            ->select('plannings.*', 'cours.intitule', 'users.nom', 'users.prenom')
            ->where('cours_users.user_id', $user_id)
            ->get();
        // Si la semaine n'est pas spécifiée, on prend la semaine courante
        if ($week == null) {
            $week = date('W');
        }

        // Retourne la vue qui affiche les séances de cours et la semaine courante 
        return view('planning.etudiant', compact('week', 'planning'));
    }

    /**
     * Affiche le planning d'un étudiant en format tableau
     *
     * Cette méthode génère une vue tabulaire du planning d'un étudiant
     * avec options de filtrage et de tri.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les paramètres de filtre
     * @return \Illuminate\View\View               Vue tabulaire du planning étudiant
     */
    public function studentPlanningTable(Request $request): View
    {
        $user_id = auth()->id();
        // Récupération des paramètres de tri et de la semaine courante
        $sortByCourse = $request->input('sort_by_course');
        $week = $request->input('week');
        // Jointure des tables Planning, Cours, Cours_Users et Users pour récupérer les informations des planning de cours
        $planning = Planning::join('cours', 'plannings.cours_id', '=', 'cours.id')
            ->join('cours_users', 'cours.id', '=', 'cours_users.cours_id')
            ->join('users', 'cours.user_id', '=', 'users.id')
            ->select('plannings.*', 'cours.intitule', 'users.nom', 'users.prenom')
            ->where('cours_users.user_id', $user_id);
        // Tri des planning de cours par cours si l'option de tri est activée
        if ($sortByCourse) {
            $planning = $planning->orderBy('cours.intitule');
        }
        // Filtrage des planning de cours pour la semaine courante si l'option est activée
        if ($week == 'current') {
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
            $planning = $planning->whereBetween('date_debut', [$startOfWeek, $endOfWeek]);
        } else if ($week != null) { // Filtrage des planning de cours pour une semaine spécifique si une semaine est spécifiée
            $planning = $planning->whereRaw('WEEK(plannings.date_debut) = ?', [$week])->orderBy('plannings.date_debut');
        }

        $planning = $planning->paginate(10);

        return view('planning.etudiant_table', compact('week', 'planning'));
    }
}