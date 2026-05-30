<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $sport     = Sport::where('slug', 'pallavolo')->first();
        $allenatore = User::where('email', 'allenatore@demo.it')->first();

        if (!$sport || !$allenatore) return;

        $team = Team::firstOrCreate(
            ['allenatore_id' => $allenatore->id, 'nome' => 'Under 18 Femminile'],
            ['sport_id' => $sport->id, 'stagione' => '2024-2025']
        );

        $atleti = User::whereIn('email', array_map(fn($i) => "atleta{$i}@demo.it", range(1, 6)))->get();
        $team->atleti()->syncWithoutDetaching($atleti->pluck('id'));
    }
}
