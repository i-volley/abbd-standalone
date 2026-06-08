<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitaDidattica extends Model
{
    protected $table = 'unita_didattiche';

    protected $fillable = [
        'team_id', 'allenatore_id',
        'titolo', 'obiettivo_permanente',
        'data_inizio', 'data_fine', 'colore', 'note',
    ];

    protected function casts(): array
    {
        return [
            'data_inizio' => 'date',
            'data_fine'   => 'date',
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

    public function sedute()
    {
        return $this->hasMany(Seduta::class)->orderBy('data');
    }
}
