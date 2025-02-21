<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Passport\Http\Middleware\CheckClientCredentials as PassportCheckClientCredentials;

class CheckClientCredentials extends PassportCheckClientCredentials
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        return parent::handle($request, $next, ...$scopes);
    }
}
