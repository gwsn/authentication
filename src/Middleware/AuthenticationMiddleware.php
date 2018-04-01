<?php

namespace Gwsn\Authentication\Middleware;

use Closure;
use Gwsn\Authentication\Models\AuthenticateService;


class AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization', null);
        $authService = new AuthenticateService($request);

        // Check if Authenticate headers are set and if the base64(username:password) exists
        if($authService->checkBasicAuth($authHeader) === false) {
            return response('Unauthorized.', 401);
        }
        $authUser = $authService->getAuthenticatedUser();

        $request->request->add(['authUser' => $authUser->accountGUID]);

        return $next($request);
    }
}
