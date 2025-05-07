<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;





class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
    
        $user = Auth::user();
    
        if (!$user->isAdmin()) {
            return redirect('/main');
        }
    
        return $next($request);
    }
    
}


/*

Ce middleware vérifie si l'utilisateur connecté est un admin avant de permettre l'accès à une certaine route.
Si l'user n'est pas authentifié, le middleware redirige l'user vers la page de connexion.
Si  ----- est authentifié mais pas admin, il renvoie une erreur HTTP 403.
Sinon il permet l'accès à la route.


*/