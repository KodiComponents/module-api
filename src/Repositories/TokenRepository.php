<?php

namespace KodiCMS\API\Repositories;

use Carbon\Carbon;
use KodiCMS\API\JWT;
use KodiCMS\API\Model\Token;
use KodiCMS\CMS\Repository\BaseRepository;
use KodiCMS\Users\Model\User;

class TokenRepository extends BaseRepository
{

    /**
     * TokenRepository constructor.
     *
     * @param Token $model
     */
    public function __construct(Token $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $token
     *
     * @return Token
     */
    public function valid($token)
    {
        return $this->model
            ->where('id', $token)
            ->notExpired()
            ->first();
    }

    /**
     * @param User $user
     * @param string $name
     *
     * @return mixed
     */
    public function createForUser(User $user, $name)
    {
        return $user->tokens()->create([
            'user_id' => $user->id,
            'name' => $name,
            'expires_at' => null,
        ]);
    }

    /**
     * @param Token $token
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function createCookie(Token $token)
    {
        $token = JWT::encode([
            'token' => $token->id,
            'expiry' => Carbon::now()->addMinutes(5)->getTimestamp(),
        ]);

        return cookie(
            'kodicms_token', $token, 5, null,
            config('session.domain'), config('session.secure'), true
        );
    }

    public function deleteExpiredTokens()
    {
        $this->query()->where('expires_at', '<=', Carbon::now())->delete();
    }

    /**
     * @param User $user
     * @param string $id
     *
     * @return mixed
     */
    public function deleteForUser(User $user, $id)
    {
        return $user->tokens()->where('id', $id)->delete();
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function findAllByUser(User $user)
    {
        return $user->tokens()->get();
    }
}