<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiornoAllenamento extends Model
{
    protected $table = 'giorni_allenamento';

    protected $fillable = [
        'stagione_id', 'giorno_settimana', 'titolo_base',
        'ora_inizio', 'ora_fine', 'note', 'luogo',
        'tipo_allenamento_id', 'indirizzo', 'citta', 'lat', 'lng',
        'ora_ritrovo', 'note_ritrovo',
    ];

    protected function casts(): array
    {
        return [
            'giorno_settimana' => 'integer',
        ];
    }

    public function stagione(): BelongsTo
    {
        return $this->belongsTo(Stagione::class);
    }

    public function tipoAllenamento(): BelongsTo
    {
        return $this->belongsTo(TipoAllenamento::class, 'tipo_allenamento_id');
    }

    // Labels giorni settimana (Carbon usa 0=Dom, 1=Lun, ... 6=Sab)
    public static function labelGiorni(): array
    {
        return [
            0 => 'Domenica',
            1 => 'Lunedì',
            2 => 'Martedì',
            3 => 'Mercoledì',
            4 => 'Giovedì',
            5 => 'Venerdì',
            6 => 'Sabato',
        ];
    }

    public function getLabelGiornoAttribute(): string
    {
        return self::labelGiorni()[$this->giorno_settimana] ?? '?';
    }

    public function getOrarioAttribute(): string
    {
        return $this->ora_fine
            ? substr($this->ora_inizio, 0, 5) . '–' . substr($this->ora_fine, 0, 5)
            : substr($this->ora_inizio, 0, 5);
    }
}
