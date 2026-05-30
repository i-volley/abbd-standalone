<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GestoTecnico extends Model
{
    protected $table = 'gesti_tecnici';

    protected $fillable = ['sport_id', 'nome', 'categoria', 'ordinamento'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function esercizi()
    {
        return $this->hasMany(Esercizio::class);
    }
}
