<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    /**
     * Generate a short-lived HMAC-signed SSO token and redirect the logged-in
     * user to SparkProxy so they are automatically provisioned and logged in.
     */
    public function redirectToSparkProxy()
    {
        $user   = Auth::user();
        $secret = env('SSO_SECRET');

        if (empty($secret)) {
            abort(500, 'SSO is not configured. Please set SSO_SECRET in .env.');
        }

        $payload = base64_encode(json_encode([
            'email'     => $user->email,
            'firstname' => $user->firstname ?? '',
            'lastname'  => $user->lastname  ?? '',
            'exp'       => time() + 60,  // token valid for 60 seconds
        ]));

        $signature = hash_hmac('sha256', $payload, $secret);
        $token     = $payload . '.' . $signature;

        $sparkproxyUrl = rtrim(env('SPARKPROXY_URL', 'https://app.sparkproxy.io'), '/');

        return redirect($sparkproxyUrl . '/sso/sparkcliks?token=' . urlencode($token));
    }
}
