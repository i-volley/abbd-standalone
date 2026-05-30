<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Microciclo extends Model
{
    protected $fillable = ['macrociclo_id', 'numero', 'data_inizio', 'intensita', 'note'];

    protected function casts(): array
    {
        return ['data_inizio' => 'date'];
    }

    public function macrociclo()
    {
        return $this->belongsTo(Macrociclo::class);
    }

    public function sedute()
    {
        return $this->hasMany(Seduta::class);
    }
}
