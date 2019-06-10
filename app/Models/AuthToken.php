<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\AuthToken
 *
 * @property-read User $user
 * @method static Builder|AuthToken newModelQuery()
 * @method static Builder|AuthToken newQuery()
 * @method static Builder|AuthToken query()
 * @mixin Eloquent
 * @property string $token
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|AuthToken whereCreatedAt($value)
 * @method static Builder|AuthToken whereToken($value)
 * @method static Builder|AuthToken whereUpdatedAt($value)
 * @method static Builder|AuthToken whereUserId($value)
 * @property Carbon $expiration_time
 * @method static Builder|AuthToken whereExpirationTime($value)
 */
class AuthToken extends Model
{
    public $incrementing = false;
    protected $fillable = ["token", "user_id"];
    protected $primaryKey = 'token';
    protected $keyType = 'string';

    protected $dates = [
        'expiration_time',
    ];


    public static function createForUser($user, $expirationTime)
    {
        return static::create([
            'user_id' => $user->id,
            'token' => self::generateRandomToken(),
            'expiration_time' => $expirationTime
        ]);
    }

    /**
     * @return string
     */
    private static function generateRandomToken(): string
    {
        return Str::random(32);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refreshToken()
    {
        $this->token = self::generateRandomToken();
        $this->save();
        return $this;
    }

    public function expired()
    {
        return $this->expiration_time != null ? $this->expiration_time->isPast() : false;
    }

}
