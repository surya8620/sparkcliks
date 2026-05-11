<?php

namespace App\Rules;

use Closure;
use App\Services\DisposableDomainGuard;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Two-layer disposable / throwaway email check:
 *
 *  Layer 1 — Propaganistas\LaravelDisposableEmail
 *             Checks a locally-cached list of ~50k known disposable domains.
 *             Fast, zero network round-trip.
 *
 *  Layer 2 — QuickEmailVerification API  (https://quickemailverification.com)
 *             Live check for role accounts, catch-all inboxes and disposable
 *             domains that slipped through the local list.
 *             Only called when Layer 1 passes and QEV_API_KEY is configured.
 *             Treated as advisory — if the API is unavailable we fail open
 *             (registration allowed) so a network blip never breaks sign-up.
 *
 *  Auto-learning: if QEV confirms a domain is disposable, it is immediately
 *  added to the local list so future attempts are caught by Layer 1 with no
 *  API call.
 */
class NotDisposableEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));

        // ── Layer 1: local disposable domain list ──────────────────────────
        if (class_exists(\Propaganistas\LaravelDisposableEmail\Facades\DisposableDomains::class)) {
            if (\Propaganistas\LaravelDisposableEmail\Facades\DisposableDomains::isDisposable($email)) {
                $fail('Disposable or temporary email addresses are not allowed.');
                return;
            }
        }

        // ── Layer 2: QuickEmailVerification API ────────────────────────────
        $apiKey = config('services.quickemailverification.key');
        if (!$apiKey) {
            // Key not configured → skip Layer 2 silently
            return;
        }

        try {
            $response = Http::timeout(5)
                ->get('https://api.quickemailverification.com/v1/verify', [
                    'email'  => $email,
                    'apikey' => $apiKey,
                ]);

            if (!$response->successful()) {
                // API error → fail open, let registration proceed
                return;
            }

            $data = $response->json();

            // Block only if QEV explicitly flags it as disposable
            if (isset($data['disposable']) && $data['disposable'] === 'true') {
                // Teach the local list so next time Layer 1 catches it (no API call needed)
                DisposableDomainGuard::addFromEmail($email);
                $fail('Disposable or temporary email addresses are not allowed.');
                return;
            }

            // Block invalid / undeliverable emails (bad MX, non-existent domain, etc.)
            // Only block on a hard 'invalid' to avoid false positives on 'unknown'
            if (isset($data['result']) && $data['result'] === 'invalid') {
                $fail('This email address appears to be invalid. Please enter a valid email address.');
            }
        } catch (\Throwable) {
            // Timeout or network error → fail open
        }
    }
}
