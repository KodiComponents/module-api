<?php

namespace KodiCMS\API\Traits;

use KodiCMS\API\Model\Token;

trait HasApiTokens
{
    /**
     * @var Token
     */
    protected $currentToken;

    /**
     * Get all of the API tokens for the user.
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Get the currently used API token for the user.
     *
     * @return Token
     */
    public function token()
    {
        return $this->currentToken;
    }

    /**
     * Set the current API token for the user.
     *
     * @param  Token $token
     *
     * @return $this
     */
    public function setToken(Token $token)
    {
        $this->currentToken = $token;

        return $this;
    }
}
