<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stagione extends Model
{
    protected $table = 'stagioni';

    protected $fillable = ['team_id', 'nome', 'data_inizio', 'data_fine', 'attiva'];

    protected function casts(): array
    {
        return [
            'data_inizio' => 'date',
            'data_fine'   => 'date',
            'attiva'      => 'boolean',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function macrocicli()
    {
        return $this->hasMany(Macrociclo::class);
    }

    public function giorniAllenamento()
    {
        return $this->hasMany(GiornoAllenamento::class)->orderBy('giorno_settimana')->orderBy('ora_inizio');
    }
}
