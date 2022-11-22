<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\BugReport;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
        'two_fa_enabled_at',
        'two_fa_secret', // encrypted at rest
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get bug reports of this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bug_reports()
    {
        return $this->hasMany(BugReport::class, 'user_id', 'id');
    }

    /**
     * Save generated 2FA secret
     *
     * @param string $encryptedSecret
     * 
     * @return void
     */
    public function save2FASecret(string $encryptedSecret)
    {
        $this->fill(['two_fa_secret' => $encryptedSecret])
             ->save();
    }
}
