<?php

namespace App\Support\Sources;

use Illuminate\Support\Str;

/**
 * Guards outbound HTTP requests against internal or private destinations.
 */
class UrlSafety
{
    public static function assertAllowed(string $url): void
    {
        $parts = parse_url($url);

        if ($parts === false || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host'])) {
            throw SourceUnavailableException::invalidUrl();
        }

        $host = strtolower($parts['host']);

        if (in_array($host, ['localhost', 'localhost.localdomain', '0.0.0.0'], true) || Str::endsWith($host, ['.local', '.internal', '.localhost'])) {
            throw SourceUnavailableException::hostNotAllowed();
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);

        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw SourceUnavailableException::hostNotAllowed();
            }
        }
    }

    public static function isAllowed(string $url): bool
    {
        try {
            self::assertAllowed($url);

            return true;
        } catch (SourceUnavailableException) {
            return false;
        }
    }
}
