<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capacita extends Model
{
    public $timestamps = false;

    protected $table = 'capacita';

    protected $fillable = ['tipo', 'nome', 'colore'];

    public function esercizi()
    {
        return $this->belongsToMany(Esercizio::class, 'esercizio_capacita');
    }
}
