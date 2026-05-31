<?php

namespace Database\Seeders;

use App\Models\CategoriaGesto;
use App\Models\GestoTecnico;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class CategoriaGestoSeeder extends Seeder
{
    public function run(): void
    {
        $sport = Sport::where('slug', 'pallavolo')->first();
        if (!$sport) return;

        // Categorie default per la Pallavolo
        $defaults = [
            ['nome' => 'Fondamentale base',     'colore' => '#0d6efd', 'ordinamento' => 1],
            ['nome' => 'Fondamentale di gioco',  'colore' => '#198754', 'ordinamento' => 2],
        ];

        foreach ($defaults as $d) {
            CategoriaGesto::firstOrCreate(
                ['sport_id' => $sport->id, 'nome' => $d['nome']],
                $d + ['sport_id' => $sport->id]
            );
        }

        // Migra i gesti tecnici esistenti che hanno ancora il vecchio campo categoria
        $catBase  = CategoriaGesto::where('sport_id', $sport->id)->where('nome', 'Fondamentale base')->first();
        $catGioco = CategoriaGesto::where('sport_id', $sport->id)->where('nome', 'Fondamentale di gioco')->first();

        if ($catBase) {
            GestoTecnico::where('sport_id', $sport->id)
                ->where('categoria', 'fondamentale_base')
                ->whereNull('categoria_id')
                ->update(['categoria_id' => $catBase->id]);
        }

        if ($catGioco) {
            GestoTecnico::where('sport_id', $sport->id)
                ->where('categoria', 'fondamentale_gioco')
                ->whereNull('categoria_id')
                ->update(['categoria_id' => $catGioco->id]);
        }
    }
}
