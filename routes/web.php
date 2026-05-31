<?php

use App\Http\Controllers\Allenatore\AllenatoreDashboardController;
use App\Http\Controllers\Allenatore\CategoriaGestoController;
use App\Http\Controllers\Allenatore\EsercizioController;
use App\Http\Controllers\Allenatore\GestoTecnicoController;
use App\Http\Controllers\Allenatore\MacrocicloController;
use App\Http\Controllers\Allenatore\MicrocicloController;
use App\Http\Controllers\Allenatore\SeduteController;
use App\Http\Controllers\Allenatore\SportController;
use App\Http\Controllers\Allenatore\StagioneController;
use App\Http\Controllers\Allenatore\TeamController;
use App\Http\Controllers\Allenatore\UnitaDidatticaController;
use App\Http\Controllers\Allenatore\WizardController;
use App\Http\Controllers\Atleta\AtletaSeduteController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', [HomeController::class, 'index']);

// ── AREA ALLENATORE ──────────────────────────────────────────────────────────
Route::prefix('allenatore')->name('allenatore.')
    ->middleware(['auth', 'role:allenatore'])->group(function () {

    Route::get('/dashboard', [AllenatoreDashboardController::class, 'index'])->name('dashboard');

    // ── SEED DIAGNOSTICO (temporaneo — rimuovere dopo verifica) ─────────────
    Route::get('debug-seed', function () {
        $lines = [];
        $lines[] = 'PHP: ' . phpversion();
        $lines[] = 'DB esercizi PRIMA: ' . \App\Models\Esercizio::count();
        $lines[] = 'Sport pallavolo ID: ' . (\App\Models\Sport::where('slug', 'pallavolo')->value('id') ?? 'NULL');
        $lines[] = 'User::first email: ' . (\App\Models\User::first()?->email ?? 'NULL');
        $lines[] = 'allenatore@demo.it: ' . (\App\Models\User::where('email', 'allenatore@demo.it')->exists() ? 'SI' : 'NO');
        $lines[] = 'esercizio_ruolo table: ' . (\Illuminate\Support\Facades\Schema::hasTable('esercizio_ruolo') ? 'SI' : 'NO');
        $lines[] = 'obiettivo column: ' . (\Illuminate\Support\Facades\Schema::hasColumn('esercizi', 'obiettivo') ? 'SI' : 'NO');
        try {
            \Artisan::call('db:seed', ['--class' => 'RoleSeeder',           '--force' => true]);
            $lines[] = 'RoleSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'SportSeeder',          '--force' => true]);
            $lines[] = 'SportSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'CapacitaSeeder',       '--force' => true]);
            $lines[] = 'CapacitaSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'GestoTecnicoSeeder',   '--force' => true]);
            $lines[] = 'GestoTecnicoSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'UserSeeder',           '--force' => true]);
            $lines[] = 'UserSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'EsercizioSeeder',      '--force' => true]);
            $lines[] = 'EsercizioSeeder: OK';
            \Artisan::call('db:seed', ['--class' => 'EsercizioFipavSeeder', '--force' => true]);
            $lines[] = 'EsercizioFipavSeeder: OK';
        } catch (\Throwable $e) {
            $lines[] = 'ERRORE: ' . $e->getMessage();
            $lines[] = 'File: ' . $e->getFile() . ':' . $e->getLine();
        }
        $lines[] = 'DB esercizi DOPO: ' . \App\Models\Esercizio::count();
        return '<pre>' . implode("\n", $lines) . '</pre>';
    })->name('debug.seed');
    // ────────────────────────────────────────────────────────────────────────

    // Wizard diagnostico FIPAV
    Route::get('wizard', [WizardController::class, 'index'])->name('wizard.index');
    Route::get('wizard/risultati', [WizardController::class, 'risultati'])->name('wizard.risultati');

    // Unità didattiche
    Route::resource('unita-didattiche', UnitaDidatticaController::class)
         ->parameters(['unita-didattiche' => 'unitaDidattica']);

    // Catalogo esercizi
    Route::get('esercizi/cerca', [EsercizioController::class, 'cerca'])->name('esercizi.cerca');
    Route::resource('esercizi', EsercizioController::class)->parameters(['esercizi' => 'esercizio']);

    // Team
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/atleti', [TeamController::class, 'aggiungiAtleta'])->name('teams.atleti.add');
    Route::delete('teams/{team}/atleti/{atleta}', [TeamController::class, 'rimuoviAtleta'])->name('teams.atleti.remove');

    // Pianificazione
    Route::resource('stagioni', StagioneController::class)
        ->parameters(['stagioni' => 'stagione']);
    Route::resource('stagioni.macrocicli', MacrocicloController::class)->shallow()
        ->parameters(['stagioni' => 'stagione', 'macrocicli' => 'macrociclo']);
    Route::resource('macrocicli.microcicli', MicrocicloController::class)->shallow()
        ->parameters(['macrocicli' => 'macrociclo', 'microcicli' => 'microciclo']);

    // Sedute
    Route::post('sedute/{seduta}/pubblica', [SeduteController::class, 'pubblica'])->name('sedute.pubblica');
    Route::post('sedute/{seduta}/visibilita', [SeduteController::class, 'toggleVisibilita'])->name('sedute.visibilita');
    Route::post('sedute/{seduta}/esercizi', [SeduteController::class, 'aggiungiEsercizio'])->name('sedute.esercizi.store');
    Route::delete('sedute/{seduta}/esercizi/{pivot}', [SeduteController::class, 'rimuoviEsercizio'])->name('sedute.esercizi.destroy');
    Route::post('sedute/{seduta}/ordine', [SeduteController::class, 'aggiornaOrdine'])->name('sedute.ordine');
    Route::patch('sedute/{seduta}/esercizi/{pivot}/voto', [SeduteController::class, 'toggleVotoEsercizio'])->name('sedute.esercizi.voto');
    Route::resource('sedute', SeduteController::class)
        ->parameters(['sedute' => 'seduta']);

    // Impostazioni
    Route::resource('sports', SportController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('gesti-tecnici', GestoTecnicoController::class)
        ->parameters(['gesti-tecnici' => 'gestoTecnico']);

    // Categorie gesti tecnici
    Route::post('categorie-gesto', [CategoriaGestoController::class, 'store'])->name('categorie-gesto.store');
    Route::patch('categorie-gesto/{categoriaGesto}', [CategoriaGestoController::class, 'update'])->name('categorie-gesto.update');
    Route::delete('categorie-gesto/{categoriaGesto}', [CategoriaGestoController::class, 'destroy'])->name('categorie-gesto.destroy');
});

// ── AREA ATLETA ──────────────────────────────────────────────────────────────
Route::prefix('atleta')->name('atleta.')
    ->middleware(['auth', 'role:atleta'])->group(function () {

    Route::get('/sedute', [AtletaSeduteController::class, 'index'])->name('sedute');
    Route::get('/sedute/{seduta}', [AtletaSeduteController::class, 'show'])->name('sedute.show');
    Route::get('/storico', [AtletaSeduteController::class, 'storico'])->name('storico');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// ── WEB PUSH ─────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
});
