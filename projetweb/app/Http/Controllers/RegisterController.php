<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant l'inscription des utilisateurs
 *
 * Ce contrôleur gère le processus d'inscription des différents types
 * d'utilisateurs (étudiants, enseignants, administrateurs) à la plateforme.
 *
 * @package App\Http\Controllers
 */
class RegisterController extends Controller
{
    /**
     * Affiche le formulaire d'inscription
     *
     * @return \Illuminate\View\View  Vue du formulaire d'inscription
     */
    public function showRegistrationForm(): View
    {
        $formations = Formation::all();
        return view('auth.register', ['formations' => $formations]);
    }

    /**
     * Traite la demande d'inscription d'un nouvel utilisateur
     *
     * Cette méthode valide les données soumises selon le type d'utilisateur,
     * crée un nouvel utilisateur, puis redirige vers la page de connexion
     * avec un message adapté au type d'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données d'inscription
     * @return \Illuminate\Http\RedirectResponse   Redirection vers la page de connexion
     */
    public function register(Request $request): RedirectResponse
    {
        $type = $request->input('type');

        $baseRules = [
            'nom' => 'required|string|max:40',
            'prenom' => 'required|string|max:40',
            'login' => 'required|string|max:30|unique:users',
            'mdp' => 'required|string|min:5',
            'type' => 'required|in:etudiant,enseignant,admin',
        ];

        if ($type === 'etudiant') {
            $baseRules['formation_id'] = 'required|exists:formations,id';
        } else {
            $baseRules['formation_id'] = 'nullable|exists:formations,id';
        }

        $validated = $request->validate($baseRules);

        $formation_id = ($type === 'etudiant') ? $validated['formation_id'] : null;

        User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'login' => $validated['login'],
            'mdp' => bcrypt($validated['mdp']),
            'formation_id' => $formation_id,
            'type' => $type === 'etudiant' ? null : $type
        ]);

        if ($type === 'admin') {
            return redirect('/login')->with('success', 'Compte administrateur créé avec succès.');
        } elseif ($type === 'enseignant') {
            return redirect('/login')->with('success', 'Compte enseignant créé avec succès.');
        } else {
            return redirect('/login')->with('success', 'Votre compte a été créé avec succès. Veuillez attendre que l\'administrateur valide votre compte.');
        }
    }
}