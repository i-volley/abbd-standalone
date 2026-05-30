<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'atleta_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_atleta', 'user_id', 'team_id');
    }

    public function teamsAllenati()
    {
        return $this->hasMany(Team::class, 'allenatore_id');
    }
}
