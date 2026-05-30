<?php

namespace Database\Seeders;

use App\Models\Capacita;
use Illuminate\Database\Seeder;

class CapacitaSeeder extends Seeder
{
    public function run(): void
    {
        $capacita = [
            ['tipo' => 'motoria',   'nome' => 'Forza',            'colore' => '#ef4444'],
            ['tipo' => 'motoria',   'nome' => 'Resistenza',       'colore' => '#3b82f6'],
            ['tipo' => 'motoria',   'nome' => 'Velocità',         'colore' => '#f97316'],
            ['tipo' => 'motoria',   'nome' => 'Equilibrio',       'colore' => '#8b5cf6'],
            ['tipo' => 'motoria',   'nome' => 'Coordinazione',    'colore' => '#06b6d4'],
            ['tipo' => 'motoria',   'nome' => 'Potenza',          'colore' => '#f59e0b'],
            ['tipo' => 'cognitiva', 'nome' => 'Decision Making',  'colore' => '#10b981'],
            ['tipo' => 'cognitiva', 'nome' => 'Attenzione',       'colore' => '#ec4899'],
            ['tipo' => 'cognitiva', 'nome' => 'Percezione',       'colore' => '#6366f1'],
            ['tipo' => 'cognitiva', 'nome' => 'Anticipazione',    'colore' => '#84cc16'],
        ];

        foreach ($capacita as $c) {
            Capacita::firstOrCreate(['nome' => $c['nome']], $c);
        }
    }
}
