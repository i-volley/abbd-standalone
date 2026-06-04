<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoAllenamento extends Model
{
    protected $table = 'tipo_allenamenti';

    protected $fillable = ['team_id', 'nome', 'ordine'];

    /** Tipi predefiniti da creare quando si crea un nuovo team */
    public static function predefiniti(): array
    {
        return ['Allenamento', 'Sala Pesi', 'Piscina', 'Campo da Beach'];
    }

    /** Crea i tipi predefiniti per un team */
    public static function creaPerTeam(int $teamId): void
    {
        foreach (self::predefiniti() as $i => $nome) {
            self::firstOrCreate(
                ['team_id' => $teamId, 'nome' => $nome],
                ['ordine' => $i]
            );
        }
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function giorniAllenamento(): HasMany
    {
        return $this->hasMany(GiornoAllenamento::class);
    }
}
