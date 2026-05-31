<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Macrociclo extends Model
{
    protected $table = 'macrocicli';

    protected $fillable = ['stagione_id', 'nome', 'fase', 'obiettivi', 'data_inizio', 'data_fine'];

    protected function casts(): array
    {
        return [
            'data_inizio' => 'date',
            'data_fine'   => 'date',
        ];
    }

    public function stagione()
    {
        return $this->belongsTo(Stagione::class);
    }

    public function microcicli()
    {
        return $this->hasMany(Microciclo::class);
    }
}
