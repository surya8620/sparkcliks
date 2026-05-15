<?php

namespace App\Services;

use App\Models\Deposit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends a signed webhook to SparkProxy after a deposit with a sparkproxy_ref
 * is confirmed as successful.
 *
 * Called from PaymentController::userDataUpdate().
 * Safe to call multiple times — idempotency is enforced on the SparkProxy side.
 */
class SparkProxyWebhookService
{
    public static function dispatch(Deposit $deposit): void
    {
        if (empty($deposit->sparkproxy_ref)) {
            return; // not a SparkProxy payment
        }

        // Use the URL stored on the deposit (from the original signed token).
        // This ensures every deposit knows exactly where to deliver, regardless
        // of env changes or multi-instance deployments.
        // Fall back to env only for legacy deposits that predate the column.
        $webhookUrl = $deposit->webhook_url ?: env('SPARKPROXY_WEBHOOK_URL');
        $secret     = env('SPARKPROXY_SECRET');

        if (empty($webhookUrl)) {
            Log::error('SparkProxyWebhookService: no webhook_url on deposit and SPARKPROXY_WEBHOOK_URL env not set', [
                'deposit_id' => $deposit->id,
                'ref'        => $deposit->sparkproxy_ref,
            ]);
            return;
        }

        if (empty($secret)) {
            Log::error('SparkProxyWebhookService: SPARKPROXY_SECRET not set', [
                'deposit_id' => $deposit->id,
            ]);
            return;
        }

        // Build a fresh token each dispatch so retries never send an expired one.
        $payload = [
            'ref'                    => $deposit->sparkproxy_ref,
            'amount'                 => (float) $deposit->amount,
            'currency'               => $deposit->method_currency ?? 'USD',
            'user_email'             => optional($deposit->user)->email ?? '',
            'sparkcliks_trx'         => $deposit->trx,
            'sparkcliks_deposit_id'  => $deposit->id,
            'exp'                    => time() + 300, // 5-min window for delivery
        ];

        $key       = hash('sha256', $secret, true);
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt(json_encode($payload), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $encoded   = base64_encode($iv . $encrypted);
        $signature = hash_hmac('sha256', $encoded, $secret);
        $token     = $encoded . '.' . $signature;

        try {
            $response = Http::timeout(15)->post($webhookUrl, ['token' => $token]);

            if ($response->successful()) {
                $deposit->webhook_sent_at  = now();
                $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
                $deposit->save();
                Log::info('SparkProxyWebhookService: delivered', [
                    'ref'        => $deposit->sparkproxy_ref,
                    'deposit_id' => $deposit->id,
                    'attempt'    => $deposit->webhook_attempts,
                ]);
            } else {
                $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
                $deposit->save();
                Log::warning('SparkProxyWebhookService: HTTP failure', [
                    'ref'        => $deposit->sparkproxy_ref,
                    'deposit_id' => $deposit->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                    'attempt'    => $deposit->webhook_attempts,
                ]);
            }
        } catch (\Throwable $e) {
            $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
            $deposit->save();
            Log::error('SparkProxyWebhookService: exception', [
                'ref'        => $deposit->sparkproxy_ref,
                'deposit_id' => $deposit->id,
                'message'    => $e->getMessage(),
                'attempt'    => $deposit->webhook_attempts,
            ]);
        }
    }
}
