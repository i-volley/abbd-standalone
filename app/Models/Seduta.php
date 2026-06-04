<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seduta extends Model
{
    protected $table = 'sedute';

    protected $fillable = [
        'microciclo_id', 'unita_didattica_id', 'team_id', 'allenatore_id',
        'titolo', 'obiettivo_seduta', 'data', 'luogo',
        'durata_tot_min', 'stato', 'visibile_atleti', 'scadenza_feedback',
        'reminder_inviato', 'note_allenatore',
        'n_atlete', 'obiettivo_principale', 'obiettivo_secondario',
    ];

    protected function casts(): array
    {
        return [
            'data'               => 'date',
            'scadenza_feedback'  => 'datetime',
            'visibile_atleti'    => 'boolean',
            'reminder_inviato'   => 'boolean',
        ];
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

    public function unitaDidattica()
    {
        return $this->belongsTo(UnitaDidattica::class);
    }

    public function esercizi()
    {
        return $this->belongsToMany(Esercizio::class, 'seduta_esercizi')
            ->withPivot(['id', 'ordinamento', 'track', 'serie', 'ripetizioni', 'recupero_sec', 'voto_abilitato', 'note'])
            ->orderByPivot('ordinamento');
    }

    public function sedutaEsercizi()
    {
        return $this->hasMany(SedutaEsercizio::class)->orderBy('ordinamento');
    }

    public function campi()
    {
        return $this->hasMany(CampoSeduta::class)->orderBy('ordine');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function durataCalcolata(): int
    {
        return $this->sedutaEsercizi->sum(fn($se) => $se->esercizio->durata_min ?? 0);
    }
}
