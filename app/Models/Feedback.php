<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'seduta_id', 'atleta_id', 'rpe', 'qualita_prestazione',
        'impegno_squadra', 'miglioramento_fondamentale', 'nota', 'inviato_in_scadenza',
    ];

    protected function casts(): array
    {
        return ['inviato_in_scadenza' => 'boolean'];
    }

    public function seduta()
    {
        return $this->belongsTo(Seduta::class);
    }

    public function atleta()
    {
        return $this->belongsTo(User::class, 'atleta_id');
    }

    public function feedbackEsercizi()
    {
        return $this->hasMany(FeedbackEsercizio::class);
    }
}
