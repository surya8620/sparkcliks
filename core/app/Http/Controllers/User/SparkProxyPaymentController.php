<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\User;
use App\Constants\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Handles inbound payment requests from SparkProxy.
 *
 * Flow:
 *   1. SparkProxy sends user to /sparkproxy/pay?token=...
 *   2. We verify the token, SSO-log-in the user (matching by email), and
 *      show a pre-filled checkout page.
 *   3. After the user pays via any normal Sparkcliks gateway, the deposit
 *      is tagged with sparkproxy_ref.
 *   4. userDataUpdate() in the gateway PaymentController fires the webhook
 *      back to SparkProxy (handled by SparkProxyWebhookService).
 */
class SparkProxyPaymentController extends Controller
{
    private const TOKEN_TTL = 600; // must match SparkProxy's TOKEN_TTL

    public function showPaymentPage(Request $request)
    {
        $token  = $request->query('token');
        if (empty($token)) {
            abort(400, 'Missing payment token.');
        }

        $payload = $this->decryptAndVerify($token);

        // ── SSO-style login: find user by email ────────────────────────────
        $email = strtolower(trim($payload['user_email'] ?? ''));
        $user  = User::where('email', $email)->first();

        if (!$user) {
            abort(403, 'No SparkCliks account found for this email. Please sign up first.');
        }

        if (!Auth::check()) {
            Auth::login($user, remember: false);
        } elseif (Auth::id() !== $user->id) {
            // Logged-in as a different user — switch to the correct account
            Auth::logout();
            Auth::login($user, remember: false);
        }

        // Store the token payload in session for the confirm step
        session(['sparkproxy_payment' => [
            'ref'          => $payload['ref'],
            'plan_id'      => $payload['plan_id'],
            'plan_name'    => $payload['plan_name'],
            'amount'       => $payload['amount'],
            'currency'     => $payload['currency'],
            'return_url'   => $payload['return_url'],
            'webhook_url'  => $payload['webhook_url'],
            'exp'          => $payload['exp'],
        ]]);

        $pageTitle   = 'Complete Your SparkProxy Payment';
        $spPayment   = session('sparkproxy_payment');

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($q) {
            $q->where('status', Status::ENABLE);
        })->with('method')->orderBy('method_code')->get();

        return view('Template::user.sparkproxy.payment', compact('pageTitle', 'spPayment', 'gatewayCurrency'));
    }

    /**
     * Decrypt + verify an incoming SparkProxy payment token.
     * Returns the payload array or aborts.
     */
    private function decryptAndVerify(string $token): array
    {
        $secret = env('SPARKPROXY_SECRET');
        if (empty($secret)) {
            abort(500, 'SparkProxy integration is not configured on this server.');
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            abort(400, 'Invalid payment token format.');
        }

        [$encoded, $signature] = $parts;

        $expected = hash_hmac('sha256', $encoded, $secret);
        if (!hash_equals($expected, $signature)) {
            Log::warning('SparkProxy payment: invalid token signature', ['ip' => request()->ip()]);
            abort(403, 'Invalid payment token signature.');
        }

        $key = hash('sha256', $secret, true);
        $raw = base64_decode($encoded);

        if (strlen($raw) <= 16) {
            abort(400, 'Malformed payment token.');
        }

        $iv        = substr($raw, 0, 16);
        $encrypted = substr($raw, 16);
        $json      = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $payload   = json_decode($json, true);

        if (!is_array($payload)
            || empty($payload['ref'])
            || empty($payload['exp'])
            || empty($payload['user_email'])
            || empty($payload['amount'])
        ) {
            abort(400, 'Malformed payment token payload.');
        }

        if (time() > (int) $payload['exp']) {
            abort(403, 'This payment link has expired. Please return to SparkProxy and try again.');
        }

        return $payload;
    }
}
