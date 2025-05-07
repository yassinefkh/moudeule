<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les utilisateurs et leurs profils
 *
 * Ce contrôleur permet la gestion des utilisateurs, incluant l'affichage et
 * la modification des profils, le changement de mot de passe, et la suppression
 * des comptes utilisateurs.
 * 
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Vérifie si l'utilisateur est administrateur
     *
     * @return bool  Vrai si l'utilisateur est administrateur, faux sinon
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Affiche le profil de l'utilisateur connecté
     *
     * Cette méthode récupère les informations de l'utilisateur connecté,
     * y compris les détails de sa formation s'il s'agit d'un étudiant.
     *
     * @return \Illuminate\View\View  Vue du profil utilisateur
     */
    public function profil(): View
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        // Récupérer l'intitulé de la formation de l'utilisateur, s'il en a une
        $formation = optional($user->formation)->intitule ?? "non-étudiant";
        // Renvoyer la vue du profil de l'utilisateur en passant les données récupérées précédemment
        return view('user.profile', compact('user', 'formation'));
    }

    /**
     * Supprime un utilisateur du système
     *
     * Cette méthode tente de supprimer un utilisateur et gère les erreurs
     * pouvant survenir lors de la suppression, notamment les contraintes de clé étrangère.
     *
     * @param  \App\Models\User  $user  L'utilisateur à supprimer
     * @return \Illuminate\Http\RedirectResponse  Redirection avec message de succès ou d'erreur
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Supprime l'utilisateur
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'L\'utilisateur a été supprimé avec succès.');
        } catch (\Illuminate\Database\QueryException $e) {
            $message = $e->getMessage();
            
            if (str_contains($message, 'FOREIGN KEY constraint failed')) {
                return redirect()->route('admin.users.index')->with('error', 'Impossible de supprimer l\'utilisateur car des données lui sont liées (cours, documents, notes, etc).');
            }
        
            return redirect()->route('admin.users.index')->with('warning', 'Une erreur est survenue : ' . $message);
        }
        
        
    }

    /**
     * Met à jour les informations personnelles de l'utilisateur connecté
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données à mettre à jour
     * @return \Illuminate\Http\RedirectResponse    Redirection vers le profil avec message de succès
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
        ]);
        
        // Mise à jour des informations de l'utilisateur
        $user->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
        ]);

        return redirect()->route('profil')->with('success', 'Les informations ont été mises à jour avec succès.');
    }

    /**
     * Affiche le profil de l'utilisateur connecté avec ses cours associés
     *
     * Cette méthode détermine si l'utilisateur est un étudiant ou un enseignant
     * et récupère les cours correspondants à son statut.
     *
     * @return \Illuminate\View\View  Vue du profil avec les cours associés
     */
    public function show(): View
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est un étudiant ou un enseignant et récupérer les cours correspondants
        if ($user->type == 'etudiant') {
            $courses = $user->courses;
        } else {
            $courses = $user->assignedCourses;
        }

        return view('profil', [
            'user' => $user,
            'courses' => $courses,
        ]);
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     *
     * @return \Illuminate\View\View  Vue du formulaire de changement de mot de passe
     */
    public function showChangePasswordForm(): View
    {
        return view('user.changepassword');
    }

    /**
     * Change le mot de passe de l'utilisateur connecté
     *
     * Cette méthode vérifie que le mot de passe actuel est correct,
     * puis met à jour le mot de passe avec la nouvelle valeur.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les mots de passe
     * @return \Illuminate\Http\RedirectResponse    Redirection avec message de succès ou d'erreur
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:2',
            'new_password_confirmation' => 'required|same:new_password',
        ]);
        
        // Vérification de la correspondance du mot de passe actuel avec celui de l'utilisateur
        if (!Hash::check($validated['current_password'], $user->mdp)) {
            return redirect()->back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }
        
        // Mise à jour du mot de passe de l'utilisateur
        $user->mdp = bcrypt($validated['new_password']);
        $user->save();

        return redirect()->route('profil')->with('success', 'Le mot de passe a été modifié avec succès.');
    }

    /**
     * Crée un nouvel utilisateur dans le système
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données du nouvel utilisateur
     * @return \Illuminate\Http\RedirectResponse    Redirection vers la liste des utilisateurs
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:40',
            'prenom' => 'required|string|max:40',
            'login' => 'required|string|max:30|unique:users',
            'mdp' => 'required|string|min:5',
            'formation_id' => 'nullable|exists:formations,id',
            'type' => 'required|in:etudiant,enseignant,admin',
        ]);

        // Création d'un nouvel utilisateur avec les données du formulaire
        User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'mdp' => bcrypt($validated['mdp']),
            'formation_id' => $validated['formation_id'],
            'type' => $validated['type']
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }
}