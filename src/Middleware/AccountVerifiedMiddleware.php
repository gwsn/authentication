<?php

namespace Gwsn\Authentication\Middleware;

use Closure;
use Gwsn\Authentication\Models\AuthenticateService;

class AccountVerifiedMiddleware
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
            return response(
                json_encode(['data'=>[], 'metadata'=>[], 'status'=> [ 'message'=>'Unauthorized.', 'code' => 401]]),
                401,
                ['Content-Type'=>'application/json']
            );
        }

        if($authService->checkVerified() === false) {
            return response(
                json_encode(['data'=>[], 'metadata'=>[], 'status'=> [ 'message'=>'Forbidden.', 'code' => 403]]),
                403,
                ['Content-Type'=>'application/json']
            );
        }

        $authUser = $authService->getAuthenticatedUser();

        $request->request->add(['authUser' => $authUser->accountGUID]);

        return $next($request);
    }
}
