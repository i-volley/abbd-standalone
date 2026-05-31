<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitaDidattica extends Model
{
    protected $table = 'unita_didattiche';

    protected $fillable = [
        'team_id', 'allenatore_id', 'microciclo_id',
        'titolo', 'obiettivo_permanente', 'progressione',
        'data_inizio', 'note',
    ];

    protected function casts(): array
    {
        return ['data_inizio' => 'date'];
    }

    /** Label progressione → metodologie in sequenza */
    public static function progressioni(): array
    {
        return [
            'analitico_globale' => 'Analitico → Sintetico → Globale',
            'sintetico_globale' => 'Sintetico → Globale',
            'libera'            => 'Sequenza libera',
        ];
    }

    /** Metodologie attese per ogni seduta nell'unità, in ordine */
    public static function sequenzaMetodologie(string $progressione): array
    {
        return match($progressione) {
            'analitico_globale' => ['analitico', 'sintetico', 'globale'],
            'sintetico_globale' => ['sintetico', 'globale'],
            default             => [],
        };
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function allenatore()
    {
        return $this->belongsTo(User::class, 'allenatore_id');
    }

    public function microciclo()
    {
        return $this->belongsTo(Microciclo::class);
    }

    public function sedute()
    {
        return $this->hasMany(Seduta::class)->orderBy('data');
    }
}
