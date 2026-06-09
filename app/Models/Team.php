<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'sport_id', 'allenatore_id', 'nome', 'stagione',
        'soglia_salti_warn', 'soglia_salti_danger',
        'soglia_gesti_warn', 'soglia_gesti_danger',
    ];

    protected $attributes = [
        'soglia_salti_warn'   => 250,
        'soglia_salti_danger' => 400,
        'soglia_gesti_warn'   => 400,
        'soglia_gesti_danger' => 600,
    ];

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

    public function tipiAllenamento()
    {
        return $this->hasMany(TipoAllenamento::class)->orderBy('ordine')->orderBy('nome');
    }

    public function coAllenatori()
    {
        return $this->belongsToMany(User::class, 'team_co_allenatori', 'team_id', 'user_id');
    }

    public function scopeAccessibleBy($query, int $userId): void
    {
        $query->where(function ($q) use ($userId) {
            $q->where('allenatore_id', $userId)
              ->orWhereHas('coAllenatori', fn ($r) => $r->where('users.id', $userId));
        });
    }

    public function isAccessibleBy(int $userId): bool
    {
        return $this->allenatore_id === $userId
            || $this->coAllenatori()->where('users.id', $userId)->exists();
    }
}
