<?php

namespace KodiCMS\API\Contracts;

use KodiCMS\API\Model\Token;

interface Tokenable
{

    /**
     * Get all of the API tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function tokens();

    /**
     * Get the currently used API token for the user.
     *
     * @return Token
     */
    public function token();

    /**
     * Set the current API token for the user.
     *
     * @param  Token $token
     *
     * @return $this
     */
    public function setToken(Token $token);
}