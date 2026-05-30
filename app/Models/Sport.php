<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['nome', 'slug', 'attivo'];

    protected function casts(): array
    {
        return ['attivo' => 'boolean'];
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function gestiTecnici()
    {
        return $this->hasMany(GestoTecnico::class);
    }

    public function esercizi()
    {
        return $this->hasMany(Esercizio::class);
    }
}
