<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        // Vérifier si l'utilisateur est connecté et que son type est "null"
        if (auth()->check() && ($user->type === null)) {
            // Déconnecter l'utilisateur
            auth()->logout();
            // Rediriger l'utilisateur vers la page de connexion avec un message d'erreur
            return redirect('/login')->with('error', 'Votre compte est en attente d\'approbation par un administrateur.');
        }
        return $next($request);
    }
    
    
}
