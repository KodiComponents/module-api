<?php

namespace KodiCMS\API;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KodiCMS\API\Model\Token;
use KodiCMS\API\Repositories\TokenRepository;

class TokenGuard
{
    /**
     * The token repository implementation.
     *
     * @var TokenRepository
     */
    protected $tokens;

    /**
     * Create a new token guard instance.
     *
     * @param  TokenRepository  $tokens
     * @return void
     */
    public function __construct(TokenRepository $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Get the authenticated user for the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(Request $request)
    {
        if (! $token = $this->getToken($request)) {
            return;
        }

        // If the token is valid we will return the user instance that is associated with
        // the token as well as populate the token usage time. If a token wasn't found
        // of course this method will return null and no user will be authenticated.
        Auth::setDefaultDriver('api');

        $token->touchLastUsedTimestamp();

        return $token->user->setToken($token);
    }

    /**
     * Get the token instance from the database.
     *
     * @param  Request  $request
     * @return Token
     */
    protected function getToken(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        if ($token instanceof Token) {
            return $token;
        } else {
            return $token ? $this->tokens->valid($token) : null;
        }
    }

    /**
     * Get the token for the given request.
     *
     * @param  Request  $request
     * @return Token|string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $bearer = $request->bearerToken();

        // First we will check to see if the token is in the request input data or is a bearer
        // token on the request. If it is, we will consider this the token, otherwise we'll
        // look for the token in the cookies then attempt to validate that it is correct.
        if ($token = $request->input('api_token', $bearer)) {
            return $token;
        }

        if ($request->cookie('kodicms_token')) {
            return $this->getTokenFromCookie($request);
        }
    }

    /**
     * Get the token for the given request cookie.
     *
     * @param  Request  $request
     * @return Token
     */
    protected function getTokenFromCookie($request)
    {
        // If we need to retrieve the token from the cookie, it'll be encrypted so we must
        // first decrypt the cookie and then attempt to find the token value within the
        // database. If we can't decrypt the value we'll bail out with a null return.
        try {
            $token = JWT::decode($request->cookie('kodicms_token'));
        } catch (Exception $e) {
            return;
        }

        return $this->tokens->valid($token['token']);
    }
}
