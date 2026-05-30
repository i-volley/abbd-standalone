<?php

use App\Http\Controllers\Allenatore\AllenatoreDashboardController;
use App\Http\Controllers\Allenatore\EsercizioController;
use App\Http\Controllers\Allenatore\GestoTecnicoController;
use App\Http\Controllers\Allenatore\MacrocicloController;
use App\Http\Controllers\Allenatore\MicrocicloController;
use App\Http\Controllers\Allenatore\SeduteController;
use App\Http\Controllers\Allenatore\SportController;
use App\Http\Controllers\Allenatore\StagioneController;
use App\Http\Controllers\Allenatore\TeamController;
use App\Http\Controllers\Atleta\AtletaSeduteController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('allenatore')) return redirect()->route('allenatore.dashboard');
        return redirect()->route('atleta.sedute');
    }
    return redirect()->route('login');
});

// ── AREA ALLENATORE ──────────────────────────────────────────────────────────
Route::prefix('allenatore')->name('allenatore.')
    ->middleware(['auth', 'role:allenatore'])->group(function () {

    Route::get('/dashboard', [AllenatoreDashboardController::class, 'index'])->name('dashboard');

    // Catalogo esercizi
    Route::get('esercizi/cerca', [EsercizioController::class, 'cerca'])->name('esercizi.cerca');
    Route::resource('esercizi', EsercizioController::class);

    // Team
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/atleti', [TeamController::class, 'aggiungiAtleta'])->name('teams.atleti.add');
    Route::delete('teams/{team}/atleti/{atleta}', [TeamController::class, 'rimuoviAtleta'])->name('teams.atleti.remove');

    // Pianificazione
    Route::resource('stagioni', StagioneController::class);
    Route::resource('stagioni.macrocicli', MacrocicloController::class)->shallow();
    Route::resource('macrocicli.microcicli', MicrocicloController::class)->shallow();

    // Sedute
    Route::post('sedute/{seduta}/pubblica', [SeduteController::class, 'pubblica'])->name('sedute.pubblica');
    Route::post('sedute/{seduta}/visibilita', [SeduteController::class, 'toggleVisibilita'])->name('sedute.visibilita');
    Route::post('sedute/{seduta}/esercizi', [SeduteController::class, 'aggiungiEsercizio'])->name('sedute.esercizi.store');
    Route::delete('sedute/{seduta}/esercizi/{pivot}', [SeduteController::class, 'rimuoviEsercizio'])->name('sedute.esercizi.destroy');
    Route::post('sedute/{seduta}/ordine', [SeduteController::class, 'aggiornaOrdine'])->name('sedute.ordine');
    Route::patch('sedute/{seduta}/esercizi/{pivot}/voto', [SeduteController::class, 'toggleVotoEsercizio'])->name('sedute.esercizi.voto');
    Route::resource('sedute', SeduteController::class);

    // Impostazioni
    Route::resource('sports', SportController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('gesti-tecnici', GestoTecnicoController::class);
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
