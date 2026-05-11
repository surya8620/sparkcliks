<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Manages the local disposable-email domain blocklist.
 *
 * The file at storage/framework/disposable_domains.json is owned by
 * propaganistas/laravel-disposable-email. We piggyback on it safely:
 *  - We ONLY append to the list, never overwrite or truncate.
 *  - When the package runs `disposable:update`, it merges the remote
 *    list with whatever is already in the file, so our additions persist.
 */
class DisposableDomainGuard
{
    public static function storagePath(): string
    {
        return storage_path('framework/disposable_domains.json');
    }

    /**
     * Extract the domain from an email address and add it to the blocklist.
     *
     * @return bool  true if the domain was newly added, false if already present
     */
    public static function addFromEmail(string $email): bool
    {
        $parts = explode('@', strtolower(trim($email)));
        if (count($parts) !== 2 || empty($parts[1])) {
            return false;
        }

        return self::addDomain($parts[1]);
    }

    /**
     * Add a bare domain (e.g. "mailinator.com") to the blocklist.
     *
     * @return bool  true if newly added, false if it was already there
     */
    public static function addDomain(string $domain): bool
    {
        $domain = strtolower(trim($domain));
        if (!$domain || !str_contains($domain, '.')) {
            return false;
        }

        $path    = self::storagePath();
        $domains = [];

        if (file_exists($path)) {
            $raw = file_get_contents($path);
            if ($raw !== false) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $domains = $decoded;
                }
            }
        }

        // Already present — nothing to do
        if (in_array($domain, $domains, true)) {
            return false;
        }

        $domains[] = $domain;
        sort($domains);

        file_put_contents(
            $path,
            json_encode(array_values($domains), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            LOCK_EX
        );

        // Bust the package's cache so the next validation picks up the new domain
        Cache::forget(config('disposable-email.cache.key', 'disposable_email:domains'));

        return true;
    }
}
