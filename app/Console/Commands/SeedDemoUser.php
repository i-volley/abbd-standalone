<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeedDemoUser extends Command
{
    protected $signature   = 'demo:seed';
    protected $description = 'Crea utente demo allenatore@demo.it se non esiste (sicuro, idempotente)';

    public function handle(): int
    {
        // Ruoli (sempre sicuri da creare)
        foreach (['allenatore', 'atleta'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Allenatore demo
        $allenatore = User::firstOrCreate(
            ['email' => 'allenatore@demo.it'],
            ['name' => 'Coach Demo', 'password' => Hash::make('password')]
        );
        $allenatore->assignRole('allenatore');
        $this->info("Allenatore: {$allenatore->email} " . ($allenatore->wasRecentlyCreated ? '(creato)' : '(già esistente)'));

        // Atleti demo
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
