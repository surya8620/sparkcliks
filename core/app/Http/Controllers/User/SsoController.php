<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    /**
     * Generate a short-lived encrypted+signed SSO token and show a preloader
     * that auto-redirects the user to SparkProxy.
     *
     * Profile fields are only included the first time (sparkproxy_synced = 0).
     * Subsequent visits send only email + expiry, keeping the token minimal.
     */
    public function redirectToSparkProxy()
    {
        $user   = Auth::user();
        $secret = env('SSO_SECRET');

        if (empty($secret)) {
            abort(500, 'SSO is not configured. Please set SSO_SECRET in .env.');
        }

        $isFirstSync = !$user->sparkproxy_synced;

        if ($isFirstSync) {
            $data = [
                'email'        => $user->email,
                'firstname'    => $user->firstname    ?? '',
                'lastname'     => $user->lastname     ?? '',
                'mobile'       => $user->mobile       ?? '',
                'dial_code'    => $user->dial_code    ?? '',
                'country'      => $user->country      ?? '',
                'country_code' => $user->country_code ?? '',
                'address'      => $user->address      ?? '',
                'city'         => $user->city         ?? '',
                'state'        => $user->state        ?? '',
                'zip'          => $user->zip          ?? '',
                'org'          => $user->org          ?? '',
                'vat'          => $user->vat          ?? '',
                'exp'          => time() + 60,
            ];
        } else {
            // Already synced — send minimal token
            $data = [
                'email' => $user->email,
                'exp'   => time() + 60,
            ];
        }

        // Encrypt with AES-256-CBC — nothing readable in the URL
        $key       = hash('sha256', $secret, true);
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt(json_encode($data), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $payload   = base64_encode($iv . $encrypted);

        // Sign to prevent tampering
        $signature = hash_hmac('sha256', $payload, $secret);
        $token     = $payload . '.' . $signature;

        $sparkproxyUrl = rtrim(env('SPARKPROXY_URL', 'https://app.sparkproxy.io'), '/');
        $redirectUrl   = $sparkproxyUrl . '/sso/sparkcliks?token=' . urlencode($token);

        // Mark as synced after generating the full-profile token
        if ($isFirstSync) {
            $user->sparkproxy_synced = 1;
            $user->save();
        }

        return view('Template::user.sso_redirect', compact('redirectUrl'));
    }
}

