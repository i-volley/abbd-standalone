<?php

use App\Http\Controllers\Allenatore\AllenatoreDashboardController;
use App\Http\Controllers\Allenatore\CategoriaGestoController;
use App\Http\Controllers\Allenatore\EsercizioController;
use App\Http\Controllers\Allenatore\GestoTecnicoController;
use App\Http\Controllers\Allenatore\GiornoAllenamentoController;
use App\Http\Controllers\Allenatore\MacrocicloController;
use App\Http\Controllers\Allenatore\TipoAllenamentoController;
use App\Http\Controllers\Allenatore\MicrocicloController;
use App\Http\Controllers\Allenatore\ParametroEsercizioController;
use App\Http\Controllers\Allenatore\SeduteController;
use App\Http\Controllers\Allenatore\SportController;
use App\Http\Controllers\Allenatore\StagioneController;
use App\Http\Controllers\Allenatore\TeamController;
use App\Http\Controllers\Allenatore\UnitaDidatticaController;
use App\Http\Controllers\Allenatore\ParadigmaController;
use App\Http\Controllers\Allenatore\TemplateCustomController;
use App\Http\Controllers\Allenatore\WizardController;
use App\Http\Controllers\Atleta\AtletaSeduteController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', [HomeController::class, 'index']);

// TEMP DEBUG — rimuovere dopo diagnosi
Route::get('/_debug_templates', function () {
    try {
        $templates = \App\Models\SessionTemplate::with('blocks')
            ->where(fn($q) => $q->where('is_system', true))
            ->get();
        $json = json_encode($templates->keyBy('id')->map(fn($t) => [
            'name'   => $t->name,
            'blocks' => $t->blocks->map(fn($b) => [
                'block_name'  => $b->block_name,
                'block_type'  => $b->block_type,
                'duration'    => $b->suggested_duration_minutes,
            ])->values(),
        ]), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        return response($json)->header('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        return response('ERROR: ' . get_class($e) . ': ' . $e->getMessage() . "\n\nTrace:\n" . $e->getTraceAsString(), 500)
            ->header('Content-Type', 'text/plain');
    }
});

// ── LANGUAGE SWITCHER ────────────────────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['it', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withInput();
})->name('lang.switch');

// ── AREA ALLENATORE ──────────────────────────────────────────────────────────
Route::prefix('allenatore')->name('allenatore.')
    ->middleware(['auth', 'role:allenatore'])->group(function () {

    Route::get('/dashboard', [AllenatoreDashboardController::class, 'index'])->name('dashboard');

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
    Route::get('teams/{team}/entra',  [TeamController::class, 'entra'])->name('teams.entra');
    Route::get('teams/{team}/hub',    [TeamController::class, 'hub'])->name('teams.hub');
    Route::get('teams/{team}/giorno/{data}', [TeamController::class, 'giorno'])->name('teams.giorno');
    Route::post('teams/{team}/atleti', [TeamController::class, 'aggiungiAtleta'])->name('teams.atleti.add');
    Route::delete('teams/{team}/atleti/{atleta}', [TeamController::class, 'rimuoviAtleta'])->name('teams.atleti.remove');

    // Pianificazione
    Route::resource('stagioni', StagioneController::class)
        ->parameters(['stagioni' => 'stagione']);
    Route::post('stagioni/{stagione}/giorni', [GiornoAllenamentoController::class, 'store'])->name('stagioni.giorni.store');
    Route::put('stagioni/{stagione}/giorni/{giorno}', [GiornoAllenamentoController::class, 'update'])->name('stagioni.giorni.update');
    Route::delete('stagioni/{stagione}/giorni/{giorno}', [GiornoAllenamentoController::class, 'destroy'])->name('stagioni.giorni.destroy');
    Route::post('stagioni/{stagione}/giorni/{giorno}/genera', [GiornoAllenamentoController::class, 'generaGiorno'])->name('stagioni.giorni.genera');
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
    Route::patch('sedute/{seduta}/esercizi/{pivot}/metriche', [SeduteController::class, 'aggiornaMetriche'])->name('sedute.esercizi.metriche');
    Route::post('sedute/{seduta}/campi', [SeduteController::class, 'aggiungiCampo'])->name('sedute.campi.store');
    Route::delete('sedute/{seduta}/campi/{campo}', [SeduteController::class, 'rimuoviCampo'])->name('sedute.campi.destroy');
    Route::resource('sedute', SeduteController::class)
        ->parameters(['sedute' => 'seduta']);

    // Paradigma pedagogico
    Route::get('paradigma',              [ParadigmaController::class, 'settings'])->name('paradigma.settings');
    Route::post('paradigma',             [ParadigmaController::class, 'updateSettings'])->name('paradigma.update');
    Route::get('paradigma/templates',    [ParadigmaController::class, 'listTemplates'])->name('paradigma.templates');
    Route::get('paradigma/preview/{template}', [ParadigmaController::class, 'previewTemplate'])->name('paradigma.preview');
    Route::resource('paradigma/template-custom', TemplateCustomController::class)
        ->parameters(['template-custom' => 'template'])
        ->names([
            'index'   => 'paradigma.template-custom.index',
            'create'  => 'paradigma.template-custom.create',
            'store'   => 'paradigma.template-custom.store',
            'edit'    => 'paradigma.template-custom.edit',
            'update'  => 'paradigma.template-custom.update',
            'destroy' => 'paradigma.template-custom.destroy',
        ])
        ->except(['show']);

    // Impostazioni
    Route::resource('sports', SportController::class)->only(['index', 'store', 'update', 'destroy']);

    // Tipi allenamento (impostazioni per team)
    Route::get('tipo-allenamento', [TipoAllenamentoController::class, 'index'])->name('tipo-allenamento.index');
    Route::post('tipo-allenamento', [TipoAllenamentoController::class, 'store'])->name('tipo-allenamento.store');
    Route::patch('tipo-allenamento/{tipoAllenamento}', [TipoAllenamentoController::class, 'update'])->name('tipo-allenamento.update');
    Route::delete('tipo-allenamento/{tipoAllenamento}', [TipoAllenamentoController::class, 'destroy'])->name('tipo-allenamento.destroy');

    // Parametri scheda esercizio (fase, metodologia, assi FIPAV)
    Route::get('parametri', [ParametroEsercizioController::class, 'index'])->name('parametri.index');
    Route::post('parametri', [ParametroEsercizioController::class, 'store'])->name('parametri.store');
    Route::patch('parametri/{parametro}', [ParametroEsercizioController::class, 'update'])->name('parametri.update');
    Route::delete('parametri/{parametro}', [ParametroEsercizioController::class, 'destroy'])->name('parametri.destroy');
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
