<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('abbd:reminder-feedback')->hourly();

/*
|--------------------------------------------------------------------------
| Integrazione Passport — fine microciclo (microcycle.end)
|--------------------------------------------------------------------------
| ABBD non ha un campo data_fine sul microciclo: il command qui sotto inferisce
| la fine come (data_inizio del microciclo successivo) - 1 giorno, oppure
| macrociclo.data_fine per l'ultimo, e notifica gli atleti via
| PassportWebhookService::microcycleEnd. È no-op se il modulo è disattivo.
*/
Schedule::command('abbd:passport-microcicli-end')->dailyAt('01:00');
