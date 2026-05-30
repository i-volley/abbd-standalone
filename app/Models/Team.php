<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['sport_id', 'allenatore_id', 'nome', 'stagione'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function allenatore()
    {
        return $this->belongsTo(User::class, 'allenatore_id');
    }

    public function atleti()
    {
        return $this->belongsToMany(User::class, 'team_atleta', 'team_id', 'user_id');
    }

    public function sedute()
    {
        return $this->hasMany(Seduta::class);
    }

    public function stagioni()
    {
        return $this->hasMany(Stagione::class);
    }
}
