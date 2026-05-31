<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SedutaEsercizio extends Model
{
    public $timestamps = false;

    protected $table = 'seduta_esercizi';

    protected $fillable = [
        'seduta_id', 'esercizio_id', 'ordinamento', 'track',
        'serie', 'ripetizioni', 'recupero_sec', 'voto_abilitato', 'note',
    ];

    protected function casts(): array
    {
        return ['voto_abilitato' => 'boolean'];
    }

    public function seduta()
    {
        return $this->belongsTo(Seduta::class);
    }

    public function esercizio()
    {
        return $this->belongsTo(Esercizio::class);
    }

    public function feedbackEsercizi()
    {
        return $this->hasMany(FeedbackEsercizio::class);
    }
}
