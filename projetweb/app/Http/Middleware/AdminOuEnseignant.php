<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOuEnseignant
{

    public function handle(Request $request, Closure $next)
    {   
        // Vérifier si l'utilisateur est de type enseignant ou admin
        if (auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin') {
            return $next($request);
        }
        // Sinon, rediriger l'utilisateur vers la page d'accueil avec un message d'erreur
        return redirect('/')->withErrors(['Vous n\'avez pas l\'autorisation d\'accéder à cette page.']);
    }
}