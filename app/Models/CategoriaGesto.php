<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGesto extends Model
{
    protected $table = 'categorie_gesto';

    protected $fillable = ['sport_id', 'nome', 'colore', 'ordinamento'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function gestiTecnici()
    {
        return $this->hasMany(GestoTecnico::class, 'categoria_id');
    }
}
