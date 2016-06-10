<?php

namespace KodiCMS\API\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use KodiCMS\Users\Model\User;

/**
 * KodiCMS\API\Model\Token
 *
 * @property string $id
 * @property integer $user_id
 * @property string $name
 * @property string $token
 * @property string $last_used_at
 * @property string $expires_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \KodiCMS\Users\Model\User $user
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereLastUsedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereExpiresAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KodiCMS\API\Model\Token notExpired()
 */
class Token extends Model
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Token $token) {
            $token->id = \Ramsey\Uuid\Uuid::uuid4();
            $token->token = str_random(60);
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_tokens';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'last_used_at' => 'date',
        'expires_at' => 'date',
    ];

    /**
     * Get the user that the token belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Update the last used timestamp for the token.
     *
     * @return void
     */
    public function touchLastUsedTimestamp()
    {
        $this->last_used_at = Carbon::now();

        $this->save();
    }

    /**
     * Determine if the token is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return Carbon::now()->gte($this->expires_at);
    }

    /**
     * @param $query
     */
    public function scopeNotExpired($query)
    {
        $query->where(function ($q) {
            return $q->whereNull('expires_at')->orWhere('expires_at', '>=', Carbon::now());
        });
    }
}
