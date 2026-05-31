<?php

namespace Database\Seeders;

use App\Models\Capacita;
use App\Models\Esercizio;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Esercizi estratti dal Manuale Allenatore di Primo Grado FIPAV
 * Autori: Marco Mencarelli, Mario Barbiero, Marco Paolini
 * Editore: Calzetti & Mariucci, 2016
 *
 * Ogni esercizio riporta il modulo_ref (es. "Sistemi 1-3") nella descrizione
 * per tracciabilità con il manuale originale.
 */
class EsercizioFipavSeeder extends Seeder
{
    public function run(): void
    {
        $sport      = Sport::where('slug', 'pallavolo')->first();
        // In prod non esiste allenatore@demo.it — usa il primo utente disponibile
        $allenatore = User::where('email', 'allenatore@demo.it')->first() ?? User::first();

        if (!$sport || !$allenatore) return;

        $gesti    = GestoTecnico::where('sport_id', $sport->id)->pluck('id', 'nome');
        $capacita = Capacita::pluck('id', 'nome');

        $esercizi = [

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 1 — Battuta-Ricezione / Sistema Ricezione
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Battuta-ricezione: identificazione dell\'errore prevalente',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Ricezione', 'durata_min' => 15, 'n_gesti' => 40,
                'categoria_eta' => 'U15', 'n_giocatori' => '6',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Identificazione forma di errore prevalente nella ricezione. Battuta dal campo, ricevitori in sistema W o 3 ricevitori. Focus: errore tecnico individuale vs errore nelle zone di conflitto. Modulo: Sistemi 1-3.',
                'capacita'    => ['Percezione', 'Anticipazione'],
                'ruoli'       => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome'        => 'Battuta-ricezione: zone di competenza sovrapposte',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Ricezione', 'durata_min' => 18, 'n_gesti' => 50,
                'categoria_eta' => 'U17', 'n_giocatori' => '6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Sviluppo del collegamento tra ricevitori. Zone sovrapposte o incrociate tra libero e ricevitore-attaccante. Lavoro sulla chiamata anticipo e sul subentrare tattico. Modulo: Sistemi 1-4.',
                'capacita'    => ['Percezione', 'Anticipazione', 'Attenzione'],
                'ruoli'       => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome'        => 'Battuta-ricezione con collegamento rincorsa attacco',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Ricezione', 'durata_min' => 20, 'n_gesti' => 45, 'n_salti' => 20,
                'categoria_eta' => 'U17', 'n_giocatori' => '5',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Collegamento ricezione → rincorsa attacco. Alzata veloce dopo ricezione, rincorsa da dentro il campo, velocizzazione dello stacco, orientamento stacco alla direzione d\'attacco. Modulo: Sistemi 1-5.',
                'capacita'    => ['Coordinazione', 'Velocità', 'Anticipazione'],
                'ruoli'       => ['ricevitore_attaccante'],
            ],
            [
                'nome'        => 'Battuta-ricezione a obiettivo positività',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Ricezione', 'durata_min' => 15, 'n_gesti' => 40,
                'categoria_eta' => 'U15', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento'  => 'positivita', 'livello' => 'base',
                'descrizione' => 'Esercizio a obiettivo tecnico. Rendimento = percentuale colpi positivi (palla perfetta + palla accettabile). Gestione dell\'errore: evidenziare la positività nei processi di apprendimento. Obiettivo: raggiungere >70% positività. Modulo: Sistemi 1-7.',
                'capacita'    => ['Percezione', 'Attenzione'],
                'ruoli'       => ['ricevitore_attaccante', 'libero'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 2 — Sistema d\'Attacco Cambio Palla
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Attacco su tutta la rete: posizioni e rincorse',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Attacco', 'durata_min' => 15, 'n_salti' => 30, 'n_gesti' => 30,
                'categoria_eta' => 'U15', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Definizione posizioni ottimali di partenza e tipi di rincorsa per ogni attaccante. Principio tattico: attacco su tutta la rete. Alzate varie zone: 2, 3, 4. Modulo: Sistemi 2-3.',
                'capacita'    => ['Coordinazione', 'Potenza'],
                'ruoli'       => ['ricevitore_attaccante', 'centrale', 'opposto'],
            ],
            [
                'nome'        => 'Sistema attacco in apertura e sovrapposizione',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Attacco', 'durata_min' => 20, 'n_salti' => 40,
                'categoria_eta' => 'U19', 'n_giocatori' => '6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'efficienza', 'livello' => 'alto',
                'descrizione' => 'Criteri di distribuzione alzata: gioco in apertura vs gioco in sovrapposizione. Identificazione attaccante di riferimento per situazione. Valutazione efficacia attaccanti e prevedibilità alzatore. Modulo: Sistemi 2-4.',
                'capacita'    => ['Decision Making', 'Anticipazione'],
                'ruoli'       => ['alzatore', 'ricevitore_attaccante', 'centrale', 'opposto'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 3 — Attacco contro Muro / Sistema Muro
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Muro singolo 1vs1: competenza primaria',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Muro', 'durata_min' => 12, 'n_salti' => 30,
                'categoria_eta' => 'U15', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tecnica',
                'livello'     => 'base',
                'descrizione' => 'Competenza primaria di muro: l\'uno contro uno. Posizionamento rispetto alla rincorsa d\'attacco, tempo di salto, adattamento piano di rimbalzo. Direzioni d\'attacco da marcare. Modulo: Sistemi 3-3.',
                'capacita'    => ['Coordinazione', 'Anticipazione', 'Potenza'],
                'ruoli'       => ['centrale', 'opposto'],
            ],
            [
                'nome'        => 'Attacco contro muro: scelta tecnica e manualità',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Attacco', 'durata_min' => 15, 'n_salti' => 35,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Scelte contro 1 giocatore: manualità alla massima velocità. Scelte contro 2-3 giocatori: controllo della potenza all\'altezza massima del colpo. Competenze chiusura muro composto. Modulo: Sistemi 3-4.',
                'capacita'    => ['Decision Making', 'Potenza', 'Coordinazione'],
                'ruoli'       => ['ricevitore_attaccante', 'opposto'],
            ],
            [
                'nome'        => 'Mani-out: colpo alto e colpo ritardato',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Attacco', 'durata_min' => 12, 'n_salti' => 25,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'livello'     => 'alto',
                'descrizione' => 'Tecnica del mani-out: colpo alto per mani-out lungo, colpo ritardato per mani-out laterale. Colpo piazzato sul muro per copertura e ricostruzione. Scelta tattica: affrontare muro vs affrontare difesa. Modulo: Sistemi 3-5.',
                'capacita'    => ['Decision Making', 'Coordinazione'],
                'ruoli'       => ['ricevitore_attaccante', 'centrale', 'opposto'],
            ],
            [
                'nome'        => 'Muro a lettura: tattica dell\'uno contro uno',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Muro', 'durata_min' => 15, 'n_salti' => 30,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'livello'     => 'medio',
                'descrizione' => 'Tattica dell\'uno contro uno in lettura. Obiettivi: tempo di salto comune, quale direzione precludere all\'avversario, adattamento piano di rimbalzo. Non subire il mani-out. Salvaguardia competenze difesa. Modulo: Sistemi 3-8.',
                'capacita'    => ['Anticipazione', 'Decision Making'],
                'ruoli'       => ['centrale', 'opposto'],
            ],
            [
                'nome'        => 'Assistenza e sovraccarico a muro: lettura vs opzione',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Muro', 'durata_min' => 20, 'n_salti' => 40,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'rendimento'  => 'gestione_errore', 'livello' => 'alto',
                'descrizione' => 'Criteri di scelta: assistenza vs sovraccarico in base a tipo di rincorsa del centrale avversario, comportamento alzatore avversario, grado di prevedibilità. Comportamento a lettura (anticipo zero) vs comportamento ad opzione (anticipo su probabilità). Modulo: Sistemi 3-9.',
                'capacita'    => ['Anticipazione', 'Decision Making', 'Attenzione'],
                'ruoli'       => ['centrale', 'opposto'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 4 — Esercizio Difesa e Contrattacco
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Difesa per la ricostruzione: palla alta su attacco potente',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Difesa', 'durata_min' => 12, 'n_gesti' => 40,
                'categoria_eta' => 'U17', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'La difesa in funzione del secondo tocco. Contro attacco potente: palla alta verso centro campo. Contro attacco piazzato: palla adeguata al timing dell\'alzatore. Contro freeball: palla precisa nel punto rete. Linee di difesa e distanza dall\'attaccante. Modulo: Sistemi 4-4.',
                'capacita'    => ['Percezione', 'Anticipazione', 'Coordinazione'],
                'ruoli'       => ['libero', 'ricevitore_attaccante'],
            ],
            [
                'nome'        => 'Alzata dei non-alzatori per il contrattacco',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Alzata', 'durata_min' => 12, 'n_gesti' => 45,
                'categoria_eta' => 'U15', 'n_giocatori' => '4',
                'obiettivo'   => 'secondario', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tecnica',
                'livello'     => 'base',
                'descrizione' => 'Scelta tecnica per alzata di contrattacco da parte di non-alzatori: palleggio vs bagher in base alla situazione. Criteri di precisione sull\'alzata di contrattacco. Preparazione delle rincorse: distanza da rete, uscita laterale per visuale. Modulo: Sistemi 4-5.',
                'capacita'    => ['Coordinazione', 'Decision Making'],
                'ruoli'       => ['ricevitore_attaccante', 'centrale', 'opposto'],
            ],
            [
                'nome'        => 'Preparazione rincorse contrattacco da difesa',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Difesa', 'durata_min' => 15, 'n_gesti' => 35, 'n_salti' => 20,
                'categoria_eta' => 'U17', 'n_giocatori' => '5',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tecnica',
                'rendimento'  => 'gestione_errore', 'livello' => 'medio',
                'descrizione' => 'Collegamento difesa → ricostruzione → contrattacco. Problema principale: preparazione tardiva delle rincorse. Lavoro su: ricerca distanza da rete dopo difesa, uscita dal campo laterale, orientamento stacco. Modulo: Sistemi 4-5.',
                'capacita'    => ['Velocità', 'Coordinazione', 'Anticipazione'],
                'ruoli'       => ['ricevitore_attaccante', 'centrale', 'opposto'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 5 — Sistema Difesa e Contrattacco
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Sistema difesa: posizionamento libero e posto 6',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Difesa', 'durata_min' => 15, 'n_gesti' => 40,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'livello'     => 'medio',
                'descrizione' => 'Criteri di scelta sistema difesa: posizionamento libero basato su qualità tecniche e caratteristiche attaccanti avversari. Posizione posto 6 in funzione della capacità di chiudere il muro del centrale. Copertura pallonetto. Modulo: Sistemi 5-3.',
                'capacita'    => ['Anticipazione', 'Decision Making'],
                'ruoli'       => ['libero'],
            ],
            [
                'nome'        => 'Contrattacco su tutta la rete: obiettivo minimo',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Attacco', 'durata_min' => 20, 'n_salti' => 30,
                'categoria_eta' => 'U19', 'n_giocatori' => '6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'alto',
                'descrizione' => 'Obiettivo minimo della ricostruzione: contrattacco su tutta la rete. Priorità: preparazione rincorsa prima del tocco per garantire possibilità di contrattacco. Utilizzo del centrale nel contrattacco. Competenze primarie di alzata per rotazione. Modulo: Sistemi 5-5.',
                'capacita'    => ['Decision Making', 'Velocità', 'Anticipazione'],
                'ruoli'       => ['alzatore', 'ricevitore_attaccante', 'centrale', 'opposto'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SISTEMI DI ALLENAMENTO 6 — Sistema Copertura e Contrattacco
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Copertura pallonetto: competenze per ruolo',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Difesa', 'durata_min' => 10, 'n_gesti' => 35,
                'categoria_eta' => 'U15', 'n_giocatori' => '4',
                'obiettivo'   => 'secondario', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tecnica',
                'livello'     => 'base',
                'descrizione' => 'Definizione competenze copertura: libero (zona corta vicino all\'attaccante), attaccanti di seconda linea, alzatore. Copertura corta vs copertura lunga. Modulo: Sistemi 6-3.',
                'capacita'    => ['Coordinazione', 'Anticipazione'],
                'ruoli'       => ['libero', 'alzatore'],
            ],
            [
                'nome'        => 'Copertura: priorità per tipo di attacco',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Difesa', 'durata_min' => 18, 'n_gesti' => 40,
                'categoria_eta' => 'U19', 'n_giocatori' => '6',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tattica',
                'livello'     => 'alto',
                'descrizione' => 'Casistica situazionale copertura: priorità su primo tempo (copertura stretta), su attacco laterale, da seconda linea zona centrale/laterale, su palla alta. Principio ricostruzione: sull\'attaccante più lontano. Modulo: Sistemi 6-4/6-5.',
                'capacita'    => ['Anticipazione', 'Decision Making', 'Attenzione'],
                'ruoli'       => ['libero', 'alzatore', 'ricevitore_attaccante'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SVILUPPO DEL GIOCO 1 — Sintesi Cambio Palla
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Sintesi cambio palla: sviluppo della positività',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 20, 'n_salti' => 30,
                'categoria_eta' => 'U19', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'alto',
                'descrizione' => 'Esercitazione a punteggio per sviluppo positività nell\'azione cambio palla. Enfasi su numero e frequenza azioni positive. Gestione dell\'errore: iniziativa individuale nel momento di prendere responsabilità. Zone rete e tipi di attacco definiti. Modulo: Sviluppo 1-3.',
                'capacita'    => ['Decision Making', 'Anticipazione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Sintesi cambio palla: sviluppo dell\'efficienza',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 25, 'n_salti' => 35,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'efficienza', 'livello' => 'alto',
                'descrizione' => 'Esercitazione a obiettivo per sviluppo efficienza nel cambio palla: % positivi meno % errori. Strategie: definizione zone rete, tipi di attacco, combinazioni di attacco utilizzabili per ridurre variabili situazionali. Modulo: Sviluppo 1-4.',
                'capacita'    => ['Decision Making', 'Anticipazione', 'Attenzione'],
                'ruoli'       => [],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SVILUPPO DEL GIOCO 2 — Sintesi Break Point e Ricostruzione
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Sintesi break point: positività muro-difesa-contrattacco',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 20, 'n_salti' => 25,
                'categoria_eta' => 'U19', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'alto',
                'descrizione' => 'Sviluppo positività nel break point: incremento contrattacchi giocati su difese, difese utilizzabili per ricostruzione, azioni di muro che rispettano i 3 obiettivi (murare, non inibire difesa, non subire mani-out). Modulo: Sviluppo 2-3.',
                'capacita'    => ['Decision Making', 'Anticipazione', 'Attenzione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Collegamento battuta-muro-difesa-contrattacco',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => null, 'durata_min' => 20, 'n_salti' => 20,
                'categoria_eta' => 'U19', 'n_giocatori' => '6',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'rendimento'  => 'gestione_errore', 'livello' => 'alto',
                'descrizione' => 'Principi tattici nel collegamento battuta-muro: posizioni partenza muro in base alla battuta. Sistema muro-difesa: competenza primaria e secondaria rispetto alle scelte di muro. Sistema difesa-contrattacco: giocatore di riferimento per ogni rotazione del sestetto. Modulo: Sviluppo 2-4.',
                'capacita'    => ['Decision Making', 'Anticipazione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Break point con numero muro variabile',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 25, 'n_salti' => 30,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'rendimento'  => 'efficienza', 'livello' => 'alto',
                'descrizione' => 'Strategie per controllare variabili situazionali: zone rete definite, tipi attacco utilizzabili, numero giocatori a muro variabile (1, 2 o 3). Utile per progressione didattica dalla situazione semplice alla gara completa. Modulo: Sviluppo 2-5.',
                'capacita'    => ['Decision Making', 'Anticipazione', 'Attenzione'],
                'ruoli'       => [],
            ],

            // ══════════════════════════════════════════════════════════════════
            // SVILUPPO DEL GIOCO 3 — Allenamento Tattico attraverso il Gioco
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => '6vs6 obiettivo tattico individuale',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 25,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'alto',
                'descrizione' => 'Iniziativa individuale efficace premiata con vantaggio situazionale (es. +2 punti). Principio: capacità di assumersi responsabilità nei momenti decisivi. Lavoro su: identificazione inefficienze avversarie, scelta attinente con principi sistema di gioco. Modulo: Sviluppo 3-3.',
                'capacita'    => ['Decision Making', 'Anticipazione', 'Attenzione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => '6vs6 a punteggio: enfasi sul cambio palla',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 25,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'efficienza', 'livello' => 'alto',
                'descrizione' => 'Esercitazione a punteggio: vantaggio di efficienza del cambio palla sul break point. Rispetto dei principi tattici nel cambio palla. Rispetto obiettivi minimi del sistema. Adattamento alla difficoltà. Modulo: Sviluppo 3-4.',
                'capacita'    => ['Decision Making', 'Attenzione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => '6vs6 a punteggio: enfasi sul break point',
                'fase'        => 'potenziamento', 'metodologia' => 'globale',
                'gesto'       => null, 'durata_min' => 25,
                'categoria_eta' => 'Senior', 'n_giocatori' => '6vs6',
                'obiettivo'   => 'permanente', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'rendimento'  => 'efficienza', 'livello' => 'alto',
                'descrizione' => 'Esercitazione a punteggio: enfasi su break point. Rispetto principi tattici: scelte caratteristiche delle azioni di ricostruzione. Rispetto obiettivi minimi muro-difesa-contrattacco. Grado di adattamento alla difficoltà del compito. Modulo: Sviluppo 3-4.',
                'capacita'    => ['Decision Making', 'Anticipazione'],
                'ruoli'       => [],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DIDATTICA TECNICHE 1 — Alzatore
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Alzatore: autonomia delle mani nel palleggio',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => 'Alzata', 'durata_min' => 10, 'n_gesti' => 60,
                'categoria_eta' => 'U13', 'n_giocatori' => '1',
                'obiettivo'   => 'principale', 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'descrizione' => 'Costruzione autonomia mani: indipendenza nell\'azione di palleggio d\'alzata. Coordinazione spinte sole mani → mani + arti sup. → mani + arti sup. + arti inf. Stabilizzazione relazione alla traiettoria di arrivo. Modulo: Didattica Tecnica 1-4.',
                'capacita'    => ['Coordinazione', 'Percezione'],
                'ruoli'       => ['alzatore'],
            ],
            [
                'nome'        => 'Alzatore: stabilizzazione traiettorie e apice',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Alzata', 'durata_min' => 12, 'n_gesti' => 50,
                'categoria_eta' => 'U13', 'n_giocatori' => '2',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'componente'  => 'tecnica', 'livello' => 'base',
                'descrizione' => 'Obiettivo: stabilizzazione altezza dell\'apice di ciascuna traiettoria. Prerequisiti: stabilizzazione atteggiamento tecnico nell\'approccio, altezza tocco di palla. Salvaguardia imprevedibilità: neutralità nell\'approccio all\'alzata. Modulo: Didattica Tecnica 1-5.',
                'capacita'    => ['Coordinazione', 'Percezione', 'Attenzione'],
                'ruoli'       => ['alzatore'],
            ],
            [
                'nome'        => 'Alzatore: casistica situazionale in cambio palla',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Alzata', 'durata_min' => 18, 'n_gesti' => 50,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tattica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Casistica situazionale: tutte le traiettorie usate nel cambio palla. Costruzione azione di cambio palla, uscita dalla difesa, uscita dal muro. Relazione con il bersaglio: consapevolezza posizione bersaglio. Modulo: Didattica Tecnica 1-6.',
                'capacita'    => ['Decision Making', 'Percezione', 'Attenzione'],
                'ruoli'       => ['alzatore'],
            ],
            [
                'nome'        => 'Alzatore: uscita da difesa e muro per contrattacco',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Alzata', 'durata_min' => 15, 'n_gesti' => 40,
                'categoria_eta' => 'U19', 'n_giocatori' => '5',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tattica',
                'rendimento'  => 'gestione_errore', 'livello' => 'alto',
                'descrizione' => 'Uscita dalla situazione di difesa: alzata in movimento allontanandosi dalla rete e verso la rete. Uscita da situazione di muro. Relazione al bersaglio nel contrattacco: consapevolezza posizione bersaglio da angolazioni difficili. Modulo: Didattica Tecnica 1-6.',
                'capacita'    => ['Coordinazione', 'Decision Making', 'Percezione'],
                'ruoli'       => ['alzatore'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DIDATTICA TECNICHE 2 — Ricevitore-Attaccante
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Ricevitore: sensibilizzazione piano di rimbalzo',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => 'Ricezione', 'durata_min' => 8, 'n_gesti' => 50,
                'categoria_eta' => 'U13', 'n_giocatori' => '2',
                'obiettivo'   => null, 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'descrizione' => 'Riscaldamento specifico: sensibilizzazione piano di rimbalzo del bagher. Controllo spinte, orientamento al bersaglio del piano, dinamica spalle, orientamento sguardo. Traiettoria ottimale di ricezione. Modulo: Didattica Tecnica 2-3.',
                'capacita'    => ['Coordinazione', 'Percezione'],
                'ruoli'       => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome'        => 'Ricezione: scelta tecnica bagher laterale e frontale',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Ricezione', 'durata_min' => 15, 'n_gesti' => 45,
                'categoria_eta' => 'U15', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Dall\'impostazione bagher laterale alto → gestione con bagher laterale e frontale. Scelta tecnica ottimale in funzione della traiettoria del servizio. Posizionamento di partenza nella zona di competenza. Uso prevalente bagher frontale. Modulo: Didattica Tecnica 2-4/5.',
                'capacita'    => ['Percezione', 'Decision Making', 'Coordinazione'],
                'ruoli'       => ['ricevitore_attaccante', 'libero'],
            ],
            [
                'nome'        => 'La pipe: ricezione e attacco da seconda linea',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Attacco', 'durata_min' => 18, 'n_gesti' => 30, 'n_salti' => 25,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Ricezione + attacco da seconda linea (pipe). Gestione quando il ricevitore è in seconda linea: salvaguardia tempo di rincorsa, uso bagher laterale, collegamento ricezione-rincorsa. Aspetti tecnici specializzazione: direzione rincorsa, orientamento stacco, manualità. Modulo: Didattica Tecnica 2-5.',
                'capacita'    => ['Coordinazione', 'Velocità', 'Potenza'],
                'ruoli'       => ['ricevitore_attaccante'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DIDATTICA TECNICHE 3 — Centrale
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Centrale: anticipo — palla 7 e palla 2',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Attacco', 'durata_min' => 12, 'n_salti' => 30,
                'categoria_eta' => 'U15', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Stabilizzazione del concetto di anticipo in attacco. Trasferimento nei punti rete: palla 7 (tesa avanti), palla 2 (primo tempo dietro l\'alzatore). Salvaguardia anticipo nel contrattacco: sviluppo in apertura e in sovrapposizione. Modulo: Didattica Tecnica 3-3.',
                'capacita'    => ['Coordinazione', 'Anticipazione', 'Potenza'],
                'ruoli'       => ['centrale'],
            ],
            [
                'nome'        => 'Centrale: stacco ad un piede — palla 2 e fast di secondo tempo',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Attacco', 'durata_min' => 15, 'n_salti' => 35,
                'categoria_eta' => 'U17', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Pallavolo femminile: stacco ad un piede. Progressione didattica: stacco e ricaduta perpendicolari alla rete → rincorse aperte mantenendo perpendicolarità → spostamento punto rete. Fast di secondo tempo: traiettoria alzata didattica usabile anche nel contrattacco. Modulo: Didattica Tecnica 3-4.',
                'capacita'    => ['Coordinazione', 'Velocità', 'Potenza'],
                'ruoli'       => ['centrale'],
            ],
            [
                'nome'        => 'Centrale a muro: lettura 1vs1 e dinamica spostamento',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Muro', 'durata_min' => 15, 'n_salti' => 35,
                'categoria_eta' => 'U17', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tattica',
                'livello'     => 'medio',
                'descrizione' => 'Lettura 1vs1 contro alzatore avversario. Caricamento corrispondente all\'arrivo palla all\'alzatore. Dinamica spostamento: spostamento rapido mantenendo angolo di caricamento per il salto. Tempo di salto corretto. Adattamento piano di rimbalzo nel muro singolo e organizzato. Modulo: Didattica Tecnica 3-5.',
                'capacita'    => ['Anticipazione', 'Coordinazione', 'Decision Making'],
                'ruoli'       => ['centrale'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DIDATTICA TECNICHE 4 — Opposto
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Opposto: attacco con anticipo del colpo in fase ascendente',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Attacco', 'durata_min' => 15, 'n_salti' => 35, 'n_gesti' => 25,
                'categoria_eta' => 'U19', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'livello'     => 'alto',
                'descrizione' => 'Pallavolo maschile: salvaguardia altezza colpo sulla palla. Anticipo del colpo nella fase ascendente del salto. Salvaguardia potenza: inerzia nel colpo, colpo avanti all\'asse corporeo. Attacco zona 1 dalla seconda linea con direzioni di colpo. Modulo: Didattica Tecnica 4-3.',
                'capacita'    => ['Potenza', 'Coordinazione'],
                'ruoli'       => ['opposto'],
            ],
            [
                'nome'        => 'Opposto: servizio in salto float e spin',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Battuta', 'durata_min' => 15, 'n_gesti' => 25,
                'categoria_eta' => 'U19', 'n_giocatori' => '1',
                'obiettivo'   => 'secondario', 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'alto',
                'descrizione' => 'Tecniche servizio in salto: colpo spin per servizio di potenza, colpo spin per obiettivo specifico, colpo spin bloccato per servizio tattico piazzato, colpo floating. Tecnica esecutiva: lancio palla, rincorsa e stacco, salto e colpo. Modulo: Didattica Tecnica 4-6.',
                'capacita'    => ['Coordinazione', 'Potenza', 'Decision Making'],
                'ruoli'       => ['opposto'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DIDATTICA TECNICHE 5 — Libero
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Libero: ricezione in zone di competenza',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Ricezione', 'durata_min' => 12, 'n_gesti' => 55,
                'categoria_eta' => 'U15', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'cambio_palla', 'componente' => 'tecnica',
                'rendimento'  => 'positivita', 'livello' => 'medio',
                'descrizione' => 'Responsabilizzazione libero in ricezione. Bagaglio tecnico: adattabilità piano di rimbalzo, sviluppo % palla perfetta. Zone di competenza in ricezione in funzione della capacità di spostamento. Relazione capacità spostamento-efficacia. Modulo: Didattica Tecnica 5-3.',
                'capacita'    => ['Percezione', 'Coordinazione', 'Anticipazione'],
                'ruoli'       => ['libero'],
            ],
            [
                'nome'        => 'Libero: difesa con posizionamento rispetto all\'attaccante',
                'fase'        => 'potenziamento', 'metodologia' => 'analitico',
                'gesto'       => 'Difesa', 'durata_min' => 12, 'n_gesti' => 45,
                'categoria_eta' => 'U15', 'n_giocatori' => '3',
                'obiettivo'   => 'principale', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'break_point', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Stabilizzazione controllo palla negli interventi difensivi. Zona di competenza libero in difesa. Posizionamento nei confronti dell\'attaccante avversario e del sistema d\'attacco avversario. Bagaglio tecnico completo in difesa. Modulo: Didattica Tecnica 5-4.',
                'capacita'    => ['Percezione', 'Anticipazione', 'Coordinazione'],
                'ruoli'       => ['libero'],
            ],
            [
                'nome'        => 'Libero: alzata per contrattacco e copertura',
                'fase'        => 'potenziamento', 'metodologia' => 'sintetico',
                'gesto'       => 'Alzata', 'durata_min' => 12, 'n_gesti' => 35,
                'categoria_eta' => 'U17', 'n_giocatori' => '4',
                'obiettivo'   => 'secondario', 'fase_seduta' => 'centrale',
                'fase_gioco'  => 'ricostruzione', 'componente' => 'tecnica',
                'livello'     => 'medio',
                'descrizione' => 'Allenamento competenze di alzata del libero per contrattacco: precisione palleggio e bagher. Allenamento situazionale competenze di copertura: aree di competenza e controllo palla. Modulo: Didattica Tecnica 5-5.',
                'capacita'    => ['Coordinazione', 'Percezione', 'Decision Making'],
                'ruoli'       => ['libero'],
            ],

            // ══════════════════════════════════════════════════════════════════
            // PREVENZIONE — Metodologia 3
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Prevenzione caviglia: propriocettiva e mobilizzazione',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => null, 'durata_min' => 8,
                'categoria_eta' => null, 'n_giocatori' => null,
                'obiettivo'   => null, 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'prevenzione_distretto' => 'caviglia',
                'descrizione' => 'Controllo funzionale del disequilibrio (propriocettiva). Mobilizzazione della caviglia: range articolare attivo. La caviglia nel salto: completamento spinte, ricerca stabilità nella ricaduta. La caviglia nell\'accosciata: chiusura tibio-tarsica, controllo spostamenti in accosciata. Modulo: Metodologia 3-12.',
                'capacita'    => ['Equilibrio', 'Coordinazione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Prevenzione ginocchio: propriocettiva e dinamica articolare',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => null, 'durata_min' => 8,
                'categoria_eta' => null, 'n_giocatori' => null,
                'obiettivo'   => null, 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'prevenzione_distretto' => 'ginocchio',
                'descrizione' => 'Controllo disequilibrio abbassando centro di gravità. Controllo angoli di accosciata. Equilibrio funzionale nella flesso-estensione. Dinamica della rotula nell\'accosciata. Stabilità laterale. Dinamica articolare nelle traslocazioni e nelle ricadute dai salti. Modulo: Metodologia 3-13.',
                'capacita'    => ['Equilibrio', 'Coordinazione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Prevenzione zona lombare: core stability e respirazione',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => null, 'durata_min' => 10,
                'categoria_eta' => null, 'n_giocatori' => null,
                'obiettivo'   => null, 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'prevenzione_distretto' => 'lombare',
                'descrizione' => 'Potenziamento addominale e lombare: funzione stabilizzatrice (statico) + trasmissione spinte (dinamico). Controllo equilibrio posturale. Controllo retrazioni muscoli posturali sulla colonna (pettorali, ischio-crurali). Respirazione toracica e diaframmatica. Modulo: Metodologia 3-14.',
                'capacita'    => ['Equilibrio', 'Forza', 'Coordinazione'],
                'ruoli'       => [],
            ],
            [
                'nome'        => 'Prevenzione spalla: mobilità scapolare e range articolare',
                'fase'        => 'riscaldamento', 'metodologia' => 'analitico',
                'gesto'       => null, 'durata_min' => 10,
                'categoria_eta' => null, 'n_giocatori' => null,
                'obiettivo'   => null, 'fase_seduta' => 'preparatoria',
                'componente'  => 'tecnica', 'livello' => 'base',
                'prevenzione_distretto' => 'spalla',
                'descrizione' => 'Stabilità: gradi di libertà articolari fisiologici, potenziamento esteso a tutti i gradi di libertà, carichi che non attivino compensi. Mobilità attiva scapole: scivolamento scapola sul piatto toracico nel movimento di trazione e di spinta degli arti superiori. Carichi liberi preferiti alle resistenze vincolate. Modulo: Metodologia 3-15.',
                'capacita'    => ['Equilibrio', 'Forza', 'Coordinazione'],
                'ruoli'       => [],
            ],

            // ══════════════════════════════════════════════════════════════════
            // DEFATICAMENTO / STRETCHING
            // ══════════════════════════════════════════════════════════════════

            [
                'nome'        => 'Defaticamento: controllo respirazione e ritorno calma',
                'fase'        => 'stretching', 'metodologia' => 'analitico',
                'gesto'       => null, 'durata_min' => 10,
                'categoria_eta' => null, 'n_giocatori' => null,
                'obiettivo'   => null, 'fase_seduta' => 'finale',
                'componente'  => 'tecnica', 'livello' => 'base',
                'descrizione' => 'Fase finale seduta: defaticamento e completamento lavori differenziati. Controllo respirazione, ritorno frequenza cardiaca, mobilizzazione articolare leggera. Gestione recupero tissutale: tempi differenziati muscolare vs tendineo-legamentoso. Modulo: Metodologia 1-5 / 3-4.',
                'capacita'    => ['Equilibrio'],
                'ruoli'       => [],
            ],

        ];

        foreach ($esercizi as $e) {
            $esercizio = Esercizio::firstOrCreate(
                ['nome' => $e['nome'], 'sport_id' => $sport->id],
                [
                    'sport_id'               => $sport->id,
                    'gesto_tecnico_id'        => isset($e['gesto']) && $e['gesto'] ? ($gesti[$e['gesto']] ?? null) : null,
                    'creato_da'               => $allenatore->id,
                    'fase'                    => $e['fase'],
                    'metodologia'             => $e['metodologia'],
                    'durata_min'              => $e['durata_min'],
                    'n_salti'                 => $e['n_salti'] ?? 0,
                    'n_gesti'                 => $e['n_gesti'] ?? 0,
                    'n_giocatori'             => $e['n_giocatori'] ?? null,
                    'categoria_eta'           => $e['categoria_eta'] ?? null,
                    'is_pubblico'             => true,
                    'obiettivo'               => $e['obiettivo'] ?? null,
                    'fase_seduta'             => $e['fase_seduta'] ?? null,
                    'fase_gioco'              => $e['fase_gioco'] ?? null,
                    'componente'              => $e['componente'] ?? null,
                    'rendimento'              => $e['rendimento'] ?? null,
                    'livello'                 => $e['livello'] ?? null,
                    'prevenzione_distretto'   => $e['prevenzione_distretto'] ?? null,
                    'descrizione'             => $e['descrizione'] ?? null,
                ]
            );

            // Aggiorna sempre (ri-seed idempotente)
            $esercizio->update([
                'is_pubblico'           => true,
                'categoria_eta'         => $e['categoria_eta'] ?? null,
                'obiettivo'             => $e['obiettivo'] ?? null,
                'fase_seduta'           => $e['fase_seduta'] ?? null,
                'fase_gioco'            => $e['fase_gioco'] ?? null,
                'componente'            => $e['componente'] ?? null,
                'rendimento'            => $e['rendimento'] ?? null,
                'livello'               => $e['livello'] ?? null,
                'prevenzione_distretto' => $e['prevenzione_distretto'] ?? null,
                'descrizione'           => $e['descrizione'] ?? null,
            ]);

            $capIds = collect($e['capacita'] ?? [])
                ->map(fn($c) => $capacita[$c] ?? null)
                ->filter();
            $esercizio->capacita()->syncWithoutDetaching($capIds);
            $esercizio->syncRuoli($e['ruoli'] ?? []);
        }
    }
}
