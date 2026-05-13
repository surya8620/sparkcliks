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

        $webhookUrl = env('SPARKPROXY_WEBHOOK_URL');
        $secret     = env('SPARKPROXY_SECRET');

        if (empty($webhookUrl) || empty($secret)) {
            Log::error('SparkProxyWebhookService: SPARKPROXY_WEBHOOK_URL or SPARKPROXY_SECRET not set');
            return;
        }

        $payload = [
            'ref'                    => $deposit->sparkproxy_ref,
            'amount'                 => (float) $deposit->amount,
            'currency'               => $deposit->method_currency ?? 'USD',
            'user_email'             => optional($deposit->user)->email ?? '',
            'sparkcliks_trx'         => $deposit->trx,
            'sparkcliks_deposit_id'  => $deposit->id,
            'exp'                    => time() + 300, // 5-min window for delivery
        ];

        // Encrypt + sign exactly as SparkProxy does for its outbound tokens
        $key       = hash('sha256', $secret, true);
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt(json_encode($payload), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $encoded   = base64_encode($iv . $encrypted);
        $signature = hash_hmac('sha256', $encoded, $secret);
        $token     = $encoded . '.' . $signature;

        try {
            $response = Http::timeout(10)->post($webhookUrl, ['token' => $token]);

            if ($response->successful()) {
                $deposit->webhook_sent_at  = now();
                $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
                $deposit->save();
                Log::info('SparkProxyWebhookService: webhook delivered', ['ref' => $deposit->sparkproxy_ref]);
            } else {
                $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
                $deposit->save();
                Log::warning('SparkProxyWebhookService: webhook failed', [
                    'ref'    => $deposit->sparkproxy_ref,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            $deposit->webhook_attempts = ($deposit->webhook_attempts ?? 0) + 1;
            $deposit->save();
            Log::error('SparkProxyWebhookService: webhook exception', [
                'ref'     => $deposit->sparkproxy_ref,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
