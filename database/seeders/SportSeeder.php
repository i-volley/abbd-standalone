<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['nome' => 'Pallavolo', 'slug' => 'pallavolo'],
            ['nome' => 'Basket',    'slug' => 'basket'],
            ['nome' => 'Calcio',    'slug' => 'calcio'],
        ];

        foreach ($sports as $s) {
            Sport::firstOrCreate(['slug' => $s['slug']], $s);
        }
    }
}
