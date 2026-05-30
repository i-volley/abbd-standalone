<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $allenatore = User::firstOrCreate(
            ['email' => 'allenatore@demo.it'],
            ['name' => 'Coach Demo', 'password' => Hash::make('password')]
        );
        $allenatore->assignRole('allenatore');

        for ($i = 1; $i <= 6; $i++) {
            $atleta = User::firstOrCreate(
                ['email' => "atleta{$i}@demo.it"],
                ['name' => "Atleta {$i}", 'password' => Hash::make('password')]
            );
            $atleta->assignRole('atleta');
        }
    }
}
