<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les fonctionnalités d'administration des utilisateurs
 * 
 * Ce contrôleur permet de gérer les utilisateurs de l'application,
 * notamment leur création, modification, approbation, et gestion des mots de passe.
 * 
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * Constructeur du contrôleur
     * 
     * Applique le middleware d'authentification à toutes les méthodes de ce contrôleur
     * pour garantir que seuls les utilisateurs authentifiés y ont accès.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des utilisateurs avec filtrage et pagination
     * 
     * Cette méthode récupère les utilisateurs en fonction des critères de recherche
     * et de type spécifiés dans la requête. Elle permet également de compter le nombre
     * d'utilisateurs en attente d'approbation.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les paramètres de filtrage
     * @return \Illuminate\View\View     Vue contenant la liste des utilisateurs et les statistiques
     */
    public function index(Request $request): View
    {
        // Récupère les valeurs de recherche et de type à partir de la requête
        $search = $request->input('search');
        $type = $request->input('type');

        // Initialise une nouvelle requête pour les utilisateurs
        $query = User::query();

        // Si une recherche est spécifiée, filtre les utilisateurs par nom, prénom ou login
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('login', 'LIKE', "%{$search}%");
            });
        }

        // Si un type est spécifié, filtre les utilisateurs par type
        if ($type) {
            if ($type === 'pending') {
                // Pour les utilisateurs en attente, chercher ceux qui ont type = NULL
                $query->whereNull('type');
            } else {
                // Pour les autres types, filtrer normalement
                $query->where('type', $type);
            }
        }

        // Exécute la requête et récupère les utilisateurs paginés
        $users = $query->paginate(5)->withQueryString();
        
        // Compte le nombre d'utilisateurs en attente
        $enAttente = User::whereNull('type')->count();

        // Retourne la vue avec les utilisateurs et le nombre d'utilisateurs en attente
        return view('admin.users.index', compact('users', 'enAttente'));
    }

    /**
     * Approuve un utilisateur en attente
     * 
     * Cette méthode définit le type d'un utilisateur sur 'etudiant',
     * ce qui lui donne accès aux fonctionnalités étudiantes de l'application.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP
     * @param  \App\Models\User  $user             L'utilisateur à approuver
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function approveUser(Request $request, User $user): RedirectResponse
    {
        // Met à jour le type de l'utilisateur en tant qu'étudiant
        $user->update([
            'type' => 'etudiant',
        ]);
        
        // Redirige vers la page de la liste des utilisateurs avec un message de succès
        return redirect()->route('admin.users.index')->with('success', "L'utilisateur a été approuvé avec succès.");
    }

    /**
     * Met à jour le type d'un utilisateur
     * 
     * Cette méthode permet de modifier le rôle d'un utilisateur dans le système
     * (étudiant, enseignant, admin, etc).
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant le nouveau type
     * @param  \App\Models\User  $user             L'utilisateur à modifier
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function updateType(Request $request, User $user): RedirectResponse
    {
        // Met à jour le type de l'utilisateur avec la valeur fournie par la requête
        $user->update(['type' => $request->input('type')]);
        
        // Redirige vers la page de la liste des utilisateurs avec un message de succès
        return redirect()->route('admin.users.index')->with('success', 'Type utilisateur modifié avec succès.');
    }

    /**
     * Refuse et supprime un utilisateur du système
     * 
     * Cette méthode marque un utilisateur comme refusé puis le supprime
     * complètement de la base de données.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP
     * @param  \App\Models\User  $user             L'utilisateur à refuser
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function refuseUser(Request $request, User $user): RedirectResponse
    {
        // Met à jour le type de l'utilisateur à NULL avant suppression (pour audit)
        $user->update([
            'type' => NULL,
        ]);
        
        // Supprime l'utilisateur de la base de données
        $user->delete();
        
        // Redirige vers la page de la liste des utilisateurs avec un message de succès
        return redirect()->route('admin.users.index')->with('success', "L'utilisateur a été refusé avec succès.");
    }

    /**
     * Affiche le formulaire de changement de mot de passe d'un utilisateur
     * 
     * Cette méthode présente un formulaire permettant à l'administrateur
     * de modifier le mot de passe d'un utilisateur spécifique.
     *
     * @param  \App\Models\User  $user  L'utilisateur dont le mot de passe sera modifié
     * @return \Illuminate\View\View    Vue contenant le formulaire de changement de mot de passe
     */
    public function showChangePasswordUserForm(User $user): View
    {
        return view('admin.users.changepassword', compact('user'));
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     * 
     * Cette méthode traite la soumission du formulaire de changement de mot de passe
     * et met à jour le mot de passe de l'utilisateur après validation.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant le nouveau mot de passe
     * @param  \App\Models\User  $user             L'utilisateur dont le mot de passe est modifié
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function changePasswordUser(Request $request, User $user): RedirectResponse
    {
        // Valide les données du formulaire de changement de mot de passe
        $validated = $request->validate([
            'new_password' => 'required|min:2',
            'new_password_confirmation' => 'required|same:new_password',
        ]);
        
        // Met à jour le mot de passe de l'utilisateur
        $user->mdp = bcrypt($request->input('new_password'));
        $user->save();
        
        // Redirige vers la page de la liste des utilisateurs avec un message de succès
        return redirect()->route('admin.users.index')
                ->with('success', 'Le mot de passe de l\'utilisateur a été modifié avec succès.');
    }
    
    /**
     * Met à jour les informations personnelles d'un utilisateur
     * 
     * Cette méthode traite la soumission du formulaire de mise à jour des
     * informations de base d'un utilisateur (nom, prénom, login).
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les nouvelles informations
     * @param  \App\Models\User  $user             L'utilisateur à mettre à jour
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        // Valide les données du formulaire de mise à jour d'utilisateur
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login,' . $user->id,
        ]);
        
        // Met à jour les informations de l'utilisateur avec les données validées
        $user->update($validated);
        
        // Redirige vers la page précédente avec un message de succès
        return redirect()->back()->with('success', 'Les informations de l\'utilisateur ont été mises à jour.');
    }

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur
     * 
     * Cette méthode prépare et affiche le formulaire permettant à l'administrateur
     * de créer un nouvel utilisateur dans le système.
     *
     * @return \Illuminate\View\View  Vue contenant le formulaire de création d'utilisateur
     */
    public function showCreateUserForm(): View
    {
        $formations = Formation::all();
        return view('admin.create-user', ['formations' => $formations]);
    }
    
    /**
     * Crée un nouvel utilisateur dans le système
     * 
     * Cette méthode traite la soumission du formulaire de création d'utilisateur,
     * valide les données, et crée un nouvel enregistrement utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les informations du nouvel utilisateur
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès
     */
    public function create(Request $request): RedirectResponse
    {
        // Valide les données de base du formulaire de création d'utilisateur
        $validated = $request->validate([
            'nom' => 'required|string|max:40',
            'prenom' => 'required|string|max:40',
            'login' => 'required|string|max:30|unique:users',
            'mdp' => 'required|string|min:1',
            'type' => 'required|in:etudiant,enseignant,admin',
            'formation_id' => 'nullable|exists:formations,id'
        ]);

        // Récupère l'ID de la formation et le type d'utilisateur du formulaire
        $formation_id = $request->input('formation_id');
        $type = $request->input('type');

        // Ajuste les données selon le type d'utilisateur
        if ($type == 'admin' || $type == 'enseignant') {
            // Les administrateurs et enseignants n'ont pas de formation associée
            $formation_id = null;
        } else {
            // Les étudiants doivent avoir une formation
            $request->validate([
                'formation_id' => 'required|exists:formations,id'
            ]);
        }

        // Crée le nouvel utilisateur avec les données validées
        User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'mdp' => bcrypt($validated['mdp']),
            'formation_id' => $formation_id,
            'type' => $validated['type']
        ]);
        
        // Redirige vers la page d'administration avec un message de succès
        return redirect('/admin')->with('success', 'Utilisateur créé avec succès.');
    }
}