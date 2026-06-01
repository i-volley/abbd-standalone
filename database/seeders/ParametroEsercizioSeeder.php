<?php

namespace Database\Seeders;

use App\Models\ParametroEsercizio;
use Illuminate\Database\Seeder;

/**
 * Default FIPAV dei parametri della scheda esercizio.
 * firstOrCreate su (tipo, valore): le voci personalizzate dall'allenatore
 * NON vengono toccate al re-seed dei deploy. di_sistema=true => non eliminabili.
 */
class ParametroEsercizioSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'fase' => [
                ['riscaldamento', 'Riscaldamento'],
                ['potenziamento', 'Potenziamento'],
                ['stretching', 'Stretching'],
            ],
            'metodologia' => [
                ['analitico', 'Analitico'],
                ['sintetico', 'Sintetico'],
                ['globale', 'Globale'],
            ],
            'obiettivo' => [
                ['permanente', 'Permanente'],
                ['principale', 'Principale'],
                ['secondario', 'Secondario'],
            ],
            'fase_seduta' => [
                ['preparatoria', 'Preparatoria'],
                ['centrale', 'Centrale'],
                ['finale', 'Finale'],
            ],
            'fase_gioco' => [
                ['cambio_palla', 'Cambio palla'],
                ['break_point', 'Break point'],
                ['ricostruzione', 'Ricostruzione'],
            ],
            'componente' => [
                ['tecnica', 'Tecnica'],
                ['tattica', 'Tattica'],
            ],
            'rendimento' => [
                ['positivita', 'Positività'],
                ['gestione_errore', 'Gestione errore'],
                ['efficienza', 'Efficienza'],
            ],
            'livello' => [
                ['base', 'Base'],
                ['medio', 'Medio'],
                ['alto', 'Alto'],
            ],
        ];

        foreach ($defaults as $tipo => $voci) {
            foreach ($voci as $i => [$valore, $etichetta]) {
                ParametroEsercizio::firstOrCreate(
                    ['tipo' => $tipo, 'valore' => $valore],
                    [
                        'etichetta'   => $etichetta,
                        'ordinamento' => $i,
                        'attivo'      => true,
                        'di_sistema'  => true,
                    ]
                );
            }
        }
    }
}
