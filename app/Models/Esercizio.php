<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esercizio extends Model
{
    protected $table = 'esercizi';

    protected $fillable = [
        'sport_id', 'gesto_tecnico_id', 'creato_da', 'nome',
        'fase', 'metodologia', 'n_salti', 'n_gesti', 'durata_min',
        'video_url', 'descrizione',
    ];

    protected function casts(): array
    {
        return [
            'n_salti'    => 'integer',
            'n_gesti'    => 'integer',
            'durata_min' => 'integer',
        ];
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
