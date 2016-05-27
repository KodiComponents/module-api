<?php

namespace KodiCMS\API\Http\Middleware;

use KodiCMS\API\Repositories\TokenRepository;

class VerifyApiToken
{

    /**
     * @var TokenRepository
     */
    private $repository;

    public function __construct(TokenRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Verify the incoming request's user belongs to team.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if (! backend_auth()->check()) {
            if ($user = \Auth::guard('api')->user()) {
                backend_auth()->setUser($user);

                $response = $next($request);

                $response->withCookie(
                    $this->repository->createCookie($user->token())
                );

                return $response;
            }
        }

        return $next($request);
    }
}
