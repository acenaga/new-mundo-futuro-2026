<?php

namespace App\Support\Sources;

use Illuminate\Support\Str;

/**
 * Guards outbound HTTP requests against internal or private destinations.
 */
class UrlSafety
{
    /** @var list<int> */
    private const array ALLOWED_PORTS = [80, 443];

    public static function assertAllowed(string $url): void
    {
        $parts = parse_url($url);

        if ($parts === false || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            throw SourceUnavailableException::invalidUrl();
        }

        if (isset($parts['port']) && ! in_array((int) $parts['port'], self::ALLOWED_PORTS, true)) {
            throw SourceUnavailableException::hostNotAllowed();
        }

        $host = strtolower($parts['host']);

        if (in_array($host, ['localhost', 'localhost.localdomain', '0.0.0.0'], true) || Str::endsWith($host, ['.local', '.internal', '.localhost'])) {
            throw SourceUnavailableException::hostNotAllowed();
        }

        $ips = self::resolve($host);

        if ($ips === []) {
            throw SourceUnavailableException::hostNotAllowed();
        }

        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw SourceUnavailableException::hostNotAllowed();
            }
        }
    }

    /** @return list<string> */
    public static function resolve(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA) ?: [];

        return array_values(array_unique(array_filter(array_map(
            fn (array $record): ?string => $record['ip'] ?? $record['ipv6'] ?? null,
            $records,
        ))));
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
