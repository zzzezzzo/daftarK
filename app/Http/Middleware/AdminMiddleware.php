<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , string $is_admin): Response
    {
        if(Auth::check()){
            if(Auth::user()->typeUser == $is_admin){
                return $next($request);
            }
            abort(403, 'Anda bukan admin');
        }
    }
}
