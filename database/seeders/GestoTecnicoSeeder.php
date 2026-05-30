<?php

namespace Database\Seeders;

use App\Models\GestoTecnico;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class GestoTecnicoSeeder extends Seeder
{
    public function run(): void
    {
        $palla = Sport::where('slug', 'pallavolo')->first();
        if (!$palla) return;

        $gesti = [
            ['nome' => 'Bagher',    'categoria' => 'fondamentale_base',  'ordinamento' => 1],
            ['nome' => 'Palleggio', 'categoria' => 'fondamentale_base',  'ordinamento' => 2],
            ['nome' => 'Battuta',   'categoria' => 'fondamentale_base',  'ordinamento' => 3],
            ['nome' => 'Attacco',   'categoria' => 'fondamentale_gioco', 'ordinamento' => 4],
            ['nome' => 'Muro',      'categoria' => 'fondamentale_gioco', 'ordinamento' => 5],
            ['nome' => 'Ricezione', 'categoria' => 'fondamentale_gioco', 'ordinamento' => 6],
        ];

        foreach ($gesti as $g) {
            GestoTecnico::firstOrCreate(
                ['sport_id' => $palla->id, 'nome' => $g['nome']],
                array_merge($g, ['sport_id' => $palla->id])
            );
        }
    }
}
