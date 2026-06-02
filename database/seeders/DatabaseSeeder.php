<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Protezione dati utente ────────────────────────────────────────────
        // Se nel DB esistono già utenti, il sistema è già stato inizializzato.
        // Eseguiamo SOLO i seeder idempotenti di lookup (ruoli, sport, parametri,
        // gesti tecnici) che non toccano dati dell'allenatore.
        // I seeder demo (team, stagioni, sedute, esercizi) vengono saltati per
        // non sovrascrivere i dati reali inseriti dall'utente.
        if (\App\Models\User::exists()) {
            $this->call([
                RoleSeeder::class,          // firstOrCreate → sicuro
                SportSeeder::class,         // firstOrCreate → sicuro
                CapacitaSeeder::class,      // firstOrCreate → sicuro
                GestoTecnicoSeeder::class,  // firstOrCreate → sicuro
                CategoriaGestoSeeder::class,// firstOrCreate → sicuro
                ParametroEsercizioSeeder::class, // firstOrCreate → sicuro
            ]);
            $this->command->info('Seed parziale: DB già popolato, dati utente preservati.');
            return;
        }

        // Prima installazione: seed completo con dati demo
        $this->command->info('Seed completo: primo avvio, carico dati demo...');
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
