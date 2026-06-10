<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends outbound webhooks from ABBD (eserciziario) to the iA-Volley Passport
 * module when gamification-relevant events occur.
 *
 * Configuration lives under config('services.passport'):
 *   - attivo  : master on/off switch (PASSPORT_MODULE_ATTIVO)
 *   - url     : base URL of the Passport backend (PASSPORT_MODULE_URL)
 *   - api_key : shared secret (PASSPORT_MODULE_API_KEY) sent as X-Passport-Api-Key
 *
 * Every method is a no-op when the module is disabled or misconfigured, and
 * never throws into the calling request: webhook delivery is best-effort and
 * failures are logged, not propagated.
 */
class PassportWebhookService
{
    /**
     * Notify: an athlete compiled the feedback for a session.
     */
    public function feedbackCompiled(int $atletaId, array $data = []): void
    {
        $this->send('feedback.compiled', array_merge([
            'atleta_id' => $atletaId,
        ], $data));
    }

    /**
     * Notify: a microcycle has ended / completed.
     */
    public function microcycleEnd(int $atletaId, array $data = []): void
    {
        $this->send('microcycle.end', array_merge([
            'atleta_id' => $atletaId,
        ], $data));
    }

    /**
     * Notify: an athlete's attendance for a session was registered.
     */
    public function attendanceRegistered(int $atletaId, array $data = []): void
    {
        $this->send('attendance.registered', array_merge([
            'atleta_id' => $atletaId,
        ], $data));
    }

    /**
     * Notify: an athlete's RPE (rate of perceived exertion) was registered.
     */
    public function rpeRegistered(int $atletaId, array $data = []): void
    {
        $this->send('rpe.registered', array_merge([
            'atleta_id' => $atletaId,
        ], $data));
    }

    /**
     * Low-level dispatcher. No-op unless the module is active and configured.
     */
    protected function send(string $event, array $data): void
    {
        if (! config('services.passport.attivo')) {
            return;
        }

        $url    = config('services.passport.url');
        $apiKey = config('services.passport.api_key');

        if (empty($url) || empty($apiKey)) {
            Log::warning('Passport webhook skipped: missing url or api_key', ['event' => $event]);

            return;
        }

        $endpoint = rtrim($url, '/').'/api/passport/webhook/abbd';

        try {
            $response = Http::withHeaders([
                    'X-Passport-Api-Key' => $apiKey,
                    'Accept'             => 'application/json',
                ])
                ->timeout(5)
                ->post($endpoint, [
                    'event' => $event,
                    'data'  => $data,
                ]);

            if (! $response->successful()) {
                Log::warning('Passport webhook non-2xx response', [
                    'event'  => $event,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            // Best-effort: never break the host request because of a webhook.
            Log::warning('Passport webhook delivery failed', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
