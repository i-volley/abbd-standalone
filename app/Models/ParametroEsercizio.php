<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroEsercizio extends Model
{
    protected $table = 'parametri_esercizio';

    protected $fillable = [
        'tipo', 'valore', 'etichetta', 'colore', 'ordinamento', 'attivo', 'di_sistema',
    ];

    protected function casts(): array
    {
        return [
            'ordinamento' => 'integer',
            'attivo'      => 'boolean',
            'di_sistema'  => 'boolean',
        ];
    }

    /**
     * Tipi di parametro gestibili + etichetta umana.
     * L'ordine qui definisce l'ordine delle sezioni in Impostazioni.
     */
    public static function tipi(): array
    {
        return [
            'fase'        => 'Fase',
            'metodologia' => 'Metodologia',
            'obiettivo'   => 'Obiettivo nella seduta',
            'fase_seduta' => 'Fase seduta',
            'fase_gioco'  => 'Fase di gioco',
            'componente'  => 'Componente',
            'rendimento'  => 'Obiettivo rendimento',
            'livello'     => 'Livello',
        ];
    }

    /** Voci attive di un tipo, ordinate */
    public static function perTipo(string $tipo)
    {
        return static::where('tipo', $tipo)->where('attivo', true)
            ->orderBy('ordinamento')->orderBy('etichetta')->get();
    }

    /** Tutti i parametri attivi raggruppati per tipo (per i form) */
    public static function attiviRaggruppati()
    {
        return static::where('attivo', true)
            ->orderBy('ordinamento')->orderBy('etichetta')
            ->get()->groupBy('tipo');
    }

    /** Array dei valori validi di un tipo (per validazione `in:`) */
    public static function valoriValidi(string $tipo): array
    {
        return static::where('tipo', $tipo)->where('attivo', true)
            ->pluck('valore')->all();
    }

    /** Mappa valore => etichetta per un tipo */
    public static function etichette(string $tipo): array
    {
        return static::where('tipo', $tipo)
            ->orderBy('ordinamento')->pluck('etichetta', 'valore')->all();
    }
}
