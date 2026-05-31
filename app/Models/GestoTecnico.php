<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GestoTecnico extends Model
{
    protected $table = 'gesti_tecnici';

    protected $fillable = ['sport_id', 'nome', 'categoria', 'categoria_id', 'ordinamento'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function categoriaGesto()
    {
        return $this->belongsTo(CategoriaGesto::class, 'categoria_id');
    }

    public function esercizi()
    {
        return $this->hasMany(Esercizio::class);
    }
}
