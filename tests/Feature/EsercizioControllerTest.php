<?php

namespace Tests\Feature;

use App\Models\Esercizio;
use App\Models\EsercizioRuolo;
use App\Models\GestoTecnico;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EsercizioControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $allenatore;
    private Sport $sport;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed ruoli Spatie (necessari per middleware role:allenatore)
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Parametri scheda esercizio: la validazione di fase/metodologia/assi
        // legge i valori validi da parametri_esercizio (non più enum fissi).
        $this->seed(\Database\Seeders\ParametroEsercizioSeeder::class);

        // Crea sport + allenatore + team (necessari per sportId() nel controller)
        $this->sport = Sport::create(['nome' => 'Pallavolo', 'slug' => 'pallavolo']);

        $this->allenatore = User::factory()->create(['email' => 'test@test.it']);
        $this->allenatore->assignRole('allenatore');

        Team::create([
            'nome'          => 'Team Test',
            'sport_id'      => $this->sport->id,
            'allenatore_id' => $this->allenatore->id,
            'stagione'      => '2025-26',
        ]);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'nome'        => 'Esercizio test',
            'fase'        => 'potenziamento',
            'metodologia' => 'sintetico',
            'durata_min'  => 10,
        ], $overrides);
    }

    // ── store ─────────────────────────────────────────────────────────────────

    public function test_store_crea_esercizio_con_campi_base(): void
    {
        $this->actingAs($this->allenatore)
             ->post(route('allenatore.esercizi.store'), $this->basePayload())
             ->assertRedirect(route('allenatore.esercizi.index'));

        $this->assertDatabaseHas('esercizi', [
            'nome'        => 'Esercizio test',
            'metodologia' => 'sintetico',
            'sport_id'    => $this->sport->id,
        ]);
    }

    public function test_store_salva_assi_metodologici_fipav(): void
    {
        $payload = $this->basePayload([
            'obiettivo'   => 'permanente',
            'fase_seduta' => 'centrale',
            'fase_gioco'  => 'cambio_palla',
            'componente'  => 'tecnica',
            'rendimento'  => 'positivita',
            'livello'     => 'medio',
            'n_giocatori' => '6vs6',
        ]);

        $this->actingAs($this->allenatore)
             ->post(route('allenatore.esercizi.store'), $payload)
             ->assertRedirect(route('allenatore.esercizi.index'));

        $this->assertDatabaseHas('esercizi', [
            'nome'         => 'Esercizio test',
            'obiettivo'    => 'permanente',
            'fase_seduta'  => 'centrale',
            'fase_gioco'   => 'cambio_palla',
            'componente'   => 'tecnica',
            'rendimento'   => 'positivita',
            'livello'      => 'medio',
            'n_giocatori'  => '6vs6',
        ]);
    }

    public function test_store_salva_ruoli_multipli(): void
    {
        $payload = $this->basePayload([
            'ruoli' => ['alzatore', 'centrale'],
        ]);

        $this->actingAs($this->allenatore)
             ->post(route('allenatore.esercizi.store'), $payload)
             ->assertRedirect(route('allenatore.esercizi.index'));

        $esercizio = Esercizio::where('nome', 'Esercizio test')->first();
        $this->assertNotNull($esercizio);

        $ruoli = EsercizioRuolo::where('esercizio_id', $esercizio->id)->pluck('ruolo')->sort()->values()->all();
        $this->assertEquals(['alzatore', 'centrale'], $ruoli);
    }

    public function test_store_fallisce_con_valori_enum_non_validi(): void
    {
        $payload = $this->basePayload(['fase_gioco' => 'valore_inventato']);

        $this->actingAs($this->allenatore)
             ->post(route('allenatore.esercizi.store'), $payload)
             ->assertSessionHasErrors('fase_gioco');

        $this->assertDatabaseMissing('esercizi', ['nome' => 'Esercizio test']);
    }

    public function test_store_fallisce_con_ruolo_non_valido(): void
    {
        $payload = $this->basePayload(['ruoli' => ['ruolo_inventato']]);

        $this->actingAs($this->allenatore)
             ->post(route('allenatore.esercizi.store'), $payload)
             ->assertSessionHasErrors('ruoli.0');
    }

    // ── update ────────────────────────────────────────────────────────────────

    public function test_update_modifica_assi_metodologici(): void
    {
        $esercizio = Esercizio::create([
            ...$this->basePayload(),
            'sport_id'  => $this->sport->id,
            'creato_da' => $this->allenatore->id,
        ]);

        $this->actingAs($this->allenatore)
             ->put(route('allenatore.esercizi.update', $esercizio), $this->basePayload([
                 'obiettivo'  => 'principale',
                 'fase_gioco' => 'break_point',
                 'componente' => 'tattica',
                 'livello'    => 'alto',
             ]))
             ->assertRedirect(route('allenatore.esercizi.index'));

        $this->assertDatabaseHas('esercizi', [
            'id'         => $esercizio->id,
            'obiettivo'  => 'principale',
            'fase_gioco' => 'break_point',
            'componente' => 'tattica',
            'livello'    => 'alto',
        ]);
    }

    public function test_update_sincronizza_ruoli(): void
    {
        $esercizio = Esercizio::create([
            ...$this->basePayload(),
            'sport_id'  => $this->sport->id,
            'creato_da' => $this->allenatore->id,
        ]);
        // Ruolo iniziale
        $esercizio->syncRuoli(['alzatore', 'centrale']);

        // Aggiorna: cambia in libero + opposto
        $this->actingAs($this->allenatore)
             ->put(route('allenatore.esercizi.update', $esercizio), $this->basePayload([
                 'ruoli' => ['libero', 'opposto'],
             ]))
             ->assertRedirect(route('allenatore.esercizi.index'));

        $ruoli = EsercizioRuolo::where('esercizio_id', $esercizio->id)->pluck('ruolo')->sort()->values()->all();
        $this->assertEquals(['libero', 'opposto'], $ruoli);
        // vecchi non esistono più
        $this->assertDatabaseMissing('esercizio_ruolo', ['esercizio_id' => $esercizio->id, 'ruolo' => 'alzatore']);
    }

    public function test_update_svuota_ruoli_se_array_vuoto(): void
    {
        $esercizio = Esercizio::create([
            ...$this->basePayload(),
            'sport_id'  => $this->sport->id,
            'creato_da' => $this->allenatore->id,
        ]);
        $esercizio->syncRuoli(['alzatore']);

        $this->actingAs($this->allenatore)
             ->put(route('allenatore.esercizi.update', $esercizio), $this->basePayload())
             ->assertRedirect(route('allenatore.esercizi.index'));

        $this->assertEquals(0, EsercizioRuolo::where('esercizio_id', $esercizio->id)->count());
    }

    // ── cerca ────────────────────────────────────────────────────────────────

    public function test_cerca_filtra_per_fase_gioco(): void
    {
        $cambio = Esercizio::create([
            ...$this->basePayload(['nome' => 'CP exercise']),
            'sport_id'   => $this->sport->id,
            'creato_da'  => $this->allenatore->id,
            'fase_gioco' => 'cambio_palla',
            'is_pubblico'=> false,
        ]);
        $break = Esercizio::create([
            ...$this->basePayload(['nome' => 'BP exercise']),
            'sport_id'   => $this->sport->id,
            'creato_da'  => $this->allenatore->id,
            'fase_gioco' => 'break_point',
            'is_pubblico'=> false,
        ]);

        $response = $this->actingAs($this->allenatore)
                         ->get(route('allenatore.esercizi.cerca', ['fase_gioco' => 'cambio_palla']));

        $response->assertStatus(200)
                 ->assertSee('CP exercise')
                 ->assertDontSee('BP exercise');
    }

    public function test_cerca_filtra_per_ruolo(): void
    {
        $alzatore_ex = Esercizio::create([
            ...$this->basePayload(['nome' => 'Es alzatore']),
            'sport_id'  => $this->sport->id,
            'creato_da' => $this->allenatore->id,
            'is_pubblico'=> false,
        ]);
        $alzatore_ex->syncRuoli(['alzatore']);

        $libero_ex = Esercizio::create([
            ...$this->basePayload(['nome' => 'Es libero']),
            'sport_id'  => $this->sport->id,
            'creato_da' => $this->allenatore->id,
            'is_pubblico'=> false,
        ]);
        $libero_ex->syncRuoli(['libero']);

        $response = $this->actingAs($this->allenatore)
                         ->get(route('allenatore.esercizi.cerca', ['ruolo' => 'alzatore']));

        $response->assertStatus(200)
                 ->assertSee('Es alzatore')
                 ->assertDontSee('Es libero');
    }

    // ── guest redirect ────────────────────────────────────────────────────────

    public function test_guest_viene_rediretto_al_login(): void
    {
        $this->get(route('allenatore.esercizi.index'))
             ->assertRedirect(route('login'));
    }
}
