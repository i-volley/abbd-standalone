<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esercizio extends Model
{
    protected $table = 'esercizi';

    protected $fillable = [
        'sport_id', 'gesto_tecnico_id', 'creato_da', 'nome',
        'fase', 'metodologia', 'n_salti', 'n_gesti', 'durata_min',
        'video_url', 'descrizione', 'categoria_eta', 'is_pubblico',
    ];

    protected function casts(): array
    {
        return [
            'n_salti'     => 'integer',
            'n_gesti'     => 'integer',
            'durata_min'  => 'integer',
            'is_pubblico' => 'boolean',
        ];
    }

    /** Categorie età disponibili */
    public static function categorieEta(): array
    {
        return ['Minivolley', 'U10', 'U12', 'U13', 'U15', 'U17', 'U19', 'Senior'];
    }

    /** Colore HEX per una categoria età */
    public static function catEtaColore(string $cat): string
    {
        return match($cat) {
            'Minivolley' => '#FF8C00',
            'U10'        => '#20B2AA',
            'U12'        => '#4169E1',
            'U13'        => '#2E8B57',
            'U15'        => '#9370DB',
            'U17'        => '#E91E8C',
            'U19'        => '#DC143C',
            'Senior'     => '#37474F',
            default      => '#6c757d',
        };
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function gestoTecnico()
    {
        return $this->belongsTo(GestoTecnico::class);
    }

    public function creatoDa()
    {
        return $this->belongsTo(User::class, 'creato_da');
    }

    public function capacita()
    {
        return $this->belongsToMany(Capacita::class, 'esercizio_capacita');
    }

    public function sedute()
    {
        return $this->belongsToMany(Seduta::class, 'seduta_esercizi')
            ->withPivot(['id', 'ordinamento', 'serie', 'ripetizioni', 'recupero_sec', 'voto_abilitato', 'note'])
            ->orderByPivot('ordinamento');
    }
}
