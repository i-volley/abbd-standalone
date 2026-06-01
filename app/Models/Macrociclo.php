<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Macrociclo extends Model
{
    protected $table = 'macrocicli';

    protected $fillable = ['stagione_id', 'nome', 'fase', 'colore', 'obiettivi', 'data_inizio', 'data_fine'];

    /** Colori predefiniti per i 3 macrocicli standard */
    public static function coloriDefault(): array
    {
        return [
            'preparazione' => '#3b82f6',  // blu
            'competizione' => '#10b981',  // verde
            'transizione'  => '#f59e0b',  // ambra
        ];
    }

    protected function casts(): array
    {
        return [
            'data_inizio' => 'date',
            'data_fine'   => 'date',
        ];
    }

    public function stagione()
    {
        return $this->belongsTo(Stagione::class);
    }

    public function microcicli()
    {
        return $this->hasMany(Microciclo::class);
    }
}
