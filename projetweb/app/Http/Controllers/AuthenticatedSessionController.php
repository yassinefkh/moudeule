<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour gérer les sessions authentifiées
 * 
 * Ce contrôleur gère l'ensemble du processus d'authentification des utilisateurs,
 * y compris l'affichage du formulaire de connexion, la vérification des identifiants
 * et la déconnexion des utilisateurs.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     *
     * @return \Illuminate\View\View Vue contenant le formulaire de connexion
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Authentifie et connecte un utilisateur
     * 
     * Vérifie les identifiants fournis, valide le statut de l'utilisateur,
     * puis l'authentifie dans le système si tout est correct.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les identifiants
     * @return \Illuminate\Http\RedirectResponse    Redirection vers la page appropriée ou retour avec erreurs
     */
    public function login(Request $request): RedirectResponse
    {
        // Valide les données de la requête
        $validated = $request->validate([
            'login' => 'required|string',
            'mdp' => 'required|string'
        ]);

        // Recherche l'utilisateur correspondant au login fourni
        $user = User::where('login', $validated['login'])->first();
        
        // Vérifie si l'utilisateur existe
        if ($user) {
            // Vérifie si le type d'utilisateur est null (inscription refusée ou en attente)
            if ($user->type === NULL) {
                return back()->withErrors([
                    'login' => 'Votre inscription a été refusée ou bien mise en attente.',
                ]);
            }
            
            // Enregistre dans les logs qu'un utilisateur correspondant au login a été trouvé
            Log::debug('Found user with login: ' . $validated['login']);
            Log::debug('Stored password hash: ' . $user->mdp);
            
            // Vérifie si le mot de passe fourni correspond au mot de passe hashé stocké
            if (Hash::check($validated['mdp'], $user->mdp)) {
                // Authentifie l'utilisateur manuellement
                Auth::login($user);
                
                // Régénère la session pour éviter les attaques de fixation de session
                $request->session()->regenerate();
                
                // Ajoute un message flash pour indiquer que la connexion a réussi
                $request->session()->flash('etat', 'Login successful');
                
                // Redirige l'utilisateur vers la page d'accueil en fonction de son type
                if ($user->isAdmin()) {
                    return redirect()->route('admin.home');
                } else {
                    return redirect('/');
                }
            } else {
                // Enregistre dans les logs que la vérification du mot de passe a échoué
                Log::debug('Hash::check failed for login: ' . $validated['login']);
            }
        } else {
            // Enregistre dans les logs qu'aucun utilisateur correspondant au login n'a été trouvé
            Log::debug('No user found with login: ' . $validated['login']);
        }
        
        // Si la connexion échoue, renvoie l'utilisateur à la page précédente avec un message d'erreur
        return back()->withErrors([
            'login' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnecte un utilisateur et invalide sa session
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP
     * @return \Illuminate\Http\RedirectResponse    Redirection vers la page d'accueil
     */
    public function logout(Request $request): RedirectResponse
    {
        // Déconnecte l'utilisateur actuellement authentifié
        Auth::logout();
        
        // Invalide les données de la session en cours
        $request->session()->invalidate();
        
        // Régénère le jeton de la session pour éviter les attaques de fixation de session
        $request->session()->regenerateToken();
        
        // Redirige l'utilisateur vers la page d'accueil
        return redirect('/');
    }
}