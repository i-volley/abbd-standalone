<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampoSeduta extends Model
{
    protected $table = 'campi_seduta';

    protected $fillable = ['seduta_id', 'nome', 'colore', 'ordine'];

    /** Palette colori per auto-assegnazione. */
    public static function palette(): array
    {
        return ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
    }

    public function seduta()
    {
        return $this->belongsTo(Seduta::class);
    }

    public function sedutaEsercizi()
    {
        return $this->hasMany(SedutaEsercizio::class, 'campo_id')->orderBy('ordinamento');
    }
}
