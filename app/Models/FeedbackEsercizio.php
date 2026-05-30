<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackEsercizio extends Model
{
    protected $table = 'feedback_esercizi';

    protected $fillable = ['feedback_id', 'seduta_esercizio_id', 'atleta_id', 'gradimento'];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function sedutaEsercizio()
    {
        return $this->belongsTo(SedutaEsercizio::class);
    }

    public function atleta()
    {
        return $this->belongsTo(User::class, 'atleta_id');
    }
}
