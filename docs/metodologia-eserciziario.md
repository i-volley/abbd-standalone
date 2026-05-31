# Metodologia eserciziario — assi di classificazione

Fonte: **Manuale Allenatore di PRIMO GRADO FIPAV** (Calzetti & Mariucci, 2016 —
Mencarelli, Barbiero, Paolini). Area Metodologia.

Questo documento definisce **come classificare e cercare gli esercizi**. È la fonte
unica condivisa tra le macchine (vive nel repo, non nelle memory locali di Claude
Code che NON sincronizzano tra PC).

---

## Principio guida (Metodologia 2)

L'asse primario è la **forma dell'esercizio**, scelta in base alla *problematica
diagnosticata nella squadra*:

| Forma (`metodologia`) | Definizione manuale | Quando usarla (Metodologia 2-4/5/6/7) |
|---|---|---|
| **analitico** | movimento dei segmenti corporei / tecnica nel fondamentale | numero elevato di errori *tecnici* esecutivi |
| **sintetico** | fondamentale nella sua sequenza motoria specifica di un'azione | errori legati a *ritmo / velocità* di gioco |
| **globale** | fondamentale in situazione di gara | errori da *complessità situazionale / tattica* |

Flusso ricerca naturale per l'allenatore: **problema osservato → forma → filtro per
fondamentale / ruolo / fase di gioco**.

---

## Assi di classificazione

### Già presenti in `esercizi`
- `metodologia` enum {analitico, sintetico, globale} — asse primario ✓
- `gesto_tecnico_id` → **fondamentale** (battuta, ricezione, alzata, attacco, muro,
  difesa, copertura). Modellato come `GestoTecnico` con `categoria_gesto`, sport-scoped.
- `fase` enum {riscaldamento, potenziamento, stretching} — fase *fisica* della seduta
  (≠ fase metodologica, vedi sotto).
- `capacita` (pivot `esercizio_capacita`)
- `categoria_eta`, `is_pubblico`, `n_salti`, `n_gesti`, `durata_min`, `video_url`

### Da integrare (assi del manuale non ancora modellati)

| Asse | Valori | Riferimento manuale |
|---|---|---|
| `obiettivo` | permanente, principale, secondario | Metodologia 1-3/1-4 |
| `fase_seduta` | preparatoria, centrale, finale | Metodologia 1-5 |
| `fase_gioco` | cambio_palla, break_point, ricostruzione | Sistemi / Sviluppo del gioco |
| `componente` | tecnica, tattica | distinzione "obiettivi tecnici / tattici" |
| `ruolo` | alzatore, ricevitore_attaccante, centrale, opposto, libero, tutti | Didattica Tecniche 1-5 |
| `rendimento` | positivita, gestione_errore, efficienza | esercizi "a obiettivo / a punteggio" |
| `n_giocatori` | intero o sigla (1, 2, ... `6vs6`) | — |
| `livello` | base, medio, alto | fasce di qualificazione |

> Nota: `fase` (fisica) e `fase_seduta` (metodologica) sono concetti distinti.
> Tenerli separati per non perdere informazione.

---

## Struttura del manuale (per `modulo_ref`)

Ogni esercizio dovrebbe tracciare da quale modulo nasce (campo libero `modulo_ref`,
es. `"Sistemi 4-3"`), per risalire alla fonte.

1. **Metodologia** — seduta tecnico-tattica · analitico/sintetico/globale · forza e prevenzione
2. **Preparazione Motoria** — errore nell'apprendimento · valutazione
3. **Didattica delle Tecniche** — alzatore · ricevitore-attaccante · centrale · opposto · libero
4. **Sistemi di Allenamento** — battuta-ricezione · attacco cambio palla · attacco vs muro · difesa-contrattacco · sistema difesa · copertura-contrattacco
5. **Sviluppo del Gioco** — sintesi cambio palla · sintesi break point/ricostruzione · 6vs6 tattico

---

## Ricerca / filtri

Faccette combinabili in AND:

```
metodologia × gesto_tecnico (fondamentale) × ruolo × fase_gioco × componente
```

Esempio: `sintetico + ricezione + libero + cambio_palla`
→ esercizi di sintesi sulla ricezione del libero in fase cambio palla.
