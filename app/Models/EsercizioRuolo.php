<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsercizioRuolo extends Model
{
    protected $table = 'esercizio_ruolo';

    protected $fillable = ['esercizio_id', 'ruolo'];

    public function esercizio()
    {
        return $this->belongsTo(Esercizio::class);
    }
}
