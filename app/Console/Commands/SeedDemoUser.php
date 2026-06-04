<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeedDemoUser extends Command
{
    protected $signature   = 'demo:seed';
    protected $description = 'Crea utente demo + account coach da env (idempotente)';

    public function handle(): int
    {
        // Ruoli
        foreach (['allenatore', 'atleta'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Account reale da env var (COACH_EMAIL + COACH_PASSWORD) ──────────
        $coachEmail    = env('COACH_EMAIL');
        $coachPassword = env('COACH_PASSWORD');

        if ($coachEmail && $coachPassword) {
            $coach = User::firstOrCreate(
                ['email' => $coachEmail],
                ['name' => env('COACH_NAME', 'Allenatore'), 'password' => Hash::make($coachPassword)]
            );
            // Aggiorna password se cambiata
            if (!$coach->wasRecentlyCreated) {
                $coach->update(['password' => Hash::make($coachPassword)]);
            }
            $coach->assignRole('allenatore');
            $this->info("Coach: {$coach->email} " . ($coach->wasRecentlyCreated ? '(creato)' : '(aggiornato)'));
        }

        // ── Allenatore demo ───────────────────────────────────────────────────
        $allenatore = User::firstOrCreate(
            ['email' => 'allenatore@demo.it'],
            ['name' => 'Coach Demo', 'password' => Hash::make('password')]
        );
        $allenatore->assignRole('allenatore');
        $this->info("Demo: {$allenatore->email} " . ($allenatore->wasRecentlyCreated ? '(creato)' : '(già esistente)'));

        // ── Atleti demo ───────────────────────────────────────────────────────
        for ($i = 1; $i <= 6; $i++) {
            $atleta = User::firstOrCreate(
                ['email' => "atleta{$i}@demo.it"],
                ['name' => "Atleta {$i}", 'password' => Hash::make('password')]
            );
            $atleta->assignRole('atleta');
        }
        $this->info('Atleti demo: OK');

        return 0;
    }
}
