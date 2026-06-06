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
        // Assi metodologici FIPAV (docs/metodologia-eserciziario.md)
        'obiettivo', 'fase_seduta', 'fase_gioco', 'componente',
        'rendimento', 'livello', 'n_giocatori',
        'prevenzione_distretto',
        // Paradigm tags
        'exercise_category', 'paradigm_primary', 'constraint_type',
        'representativeness', 'feedback_suggestion', 'affordance_targets',
        // Campo visivo
        'campo_visivo',
    ];

    protected function casts(): array
    {
        return [
            'n_salti'            => 'integer',
            'n_gesti'            => 'integer',
            'durata_min'         => 'integer',
            'is_pubblico'        => 'boolean',
            'affordance_targets' => 'array',
            'campo_visivo'       => 'array',
        ];
    }

    // ── Scopes paradigma ─────────────────────────────────────────────────────

    public function scopeForParadigm($query, string $paradigm)
    {
        // Non esclude, ordina per rilevanza: esercizi del paradigma in testa
        return $query->orderByRaw(
            "CASE WHEN paradigm_primary = ? THEN 0 WHEN paradigm_primary = 'neutral' THEN 1 ELSE 2 END",
            [$paradigm]
        );
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('exercise_category', $category);
    }

    public function scopeByConstraint($query, string $type)
    {
        return $query->where('constraint_type', $type);
    }

    public function scopeHighRepresentativeness($query)
    {
        return $query->where('representativeness', 'high');
    }

    /** Categorie età disponibili */
    public static function categorieEta(): array
    {
        return ['Minivolley', 'U10', 'U12', 'U13', 'U15', 'U17', 'U19', 'Senior'];
    }

    /** Ruoli pallavolo disponibili (pivot esercizio_ruolo) */
    public static function ruoliDisponibili(): array
    {
        return ['alzatore', 'ricevitore_attaccante', 'centrale', 'opposto', 'libero'];
    }

    /** Distretti prevenzione (Metodologia 3-12/13/14/15) */
    public static function distretti(): array
    {
        return ['caviglia', 'ginocchio', 'lombare', 'spalla'];
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

    /** Righe pivot dei ruoli associati all'esercizio */
    public function ruoli()
    {
        return $this->hasMany(EsercizioRuolo::class);
    }

    /** Lista dei ruoli come array di stringhe */
    public function getRuoliListAttribute(): array
    {
        return $this->ruoli->pluck('ruolo')->all();
    }

    /** Sincronizza i ruoli dell'esercizio da un array di stringhe */
    public function syncRuoli(array $ruoli): void
    {
        $validi = array_values(array_intersect($ruoli, self::ruoliDisponibili()));
        $this->ruoli()->delete();
        foreach (array_unique($validi) as $ruolo) {
            $this->ruoli()->create(['ruolo' => $ruolo]);
        }
    }

    public function sedute()
    {
        return $this->belongsToMany(Seduta::class, 'seduta_esercizi')
            ->withPivot(['id', 'ordinamento', 'serie', 'ripetizioni', 'recupero_sec', 'voto_abilitato', 'note'])
            ->orderByPivot('ordinamento');
    }
}
