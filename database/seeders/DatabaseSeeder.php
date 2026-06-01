<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SportSeeder::class,
            CapacitaSeeder::class,
            GestoTecnicoSeeder::class,
            CategoriaGestoSeeder::class,
            UserSeeder::class,
            TeamSeeder::class,
            ParametroEsercizioSeeder::class,
            EsercizioSeeder::class,
            EsercizioFipavSeeder::class,
            PianificazioneSeeder::class,
            SeduteSeeder::class,
        ]);
    }
}
