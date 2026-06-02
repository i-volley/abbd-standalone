<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiornoAllenamento extends Model
{
    protected $table = 'giorni_allenamento';

    protected $fillable = ['stagione_id', 'giorno_settimana', 'ora_inizio', 'ora_fine', 'note', 'luogo'];

    protected function casts(): array
    {
        return [
            'giorno_settimana' => 'integer',
        ];
    }

    public function stagione()
    {
        return $this->belongsTo(Stagione::class);
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
