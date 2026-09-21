<?php

namespace App\Support\Sources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SafeExternalHttpClient
{
    public const int MAX_REDIRECTS = 5;

    public function get(string $url, string $accept, ?string $requiredHost = null): Response
    {
        for ($redirects = 0; $redirects <= self::MAX_REDIRECTS; $redirects++) {
            UrlSafety::assertAllowed($url);
            $parts = parse_url($url);
            $host = (string) $parts['host'];

            if ($requiredHost !== null && strcasecmp($host, $requiredHost) !== 0) {
                throw SourceUnavailableException::unreachable('La página redirigió fuera del sitio oficial.');
            }
            $ip = UrlSafety::resolve($host)[0] ?? null;
            $options = ['allow_redirects' => false];

            if ($ip !== null && defined('CURLOPT_RESOLVE')) {
                $port = $parts['port'] ?? (strtolower((string) $parts['scheme']) === 'https' ? 443 : 80);
                $options['curl'] = [CURLOPT_RESOLVE => ["{$host}:{$port}:{$ip}"]];
            }

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; MundoFuturoBot/1.0; +https://mundofuturo.ca)',
                    'Accept' => $accept,
                ])->timeout(15)->connectTimeout(5)->withOptions($options)->get($url);
            } catch (\Throwable $exception) {
                throw SourceUnavailableException::unreachable($exception->getMessage());
            }

            if (! $response->redirect()) {
                return $response;
            }

            $location = $response->header('Location');

            if (! is_string($location) || $location === '') {
                throw SourceUnavailableException::unreachable('La redirección no indicó un destino.');
            }

            $url = $this->absoluteUrl($location, $url);
        }

        throw SourceUnavailableException::unreachable('La página redirige demasiadas veces.');
    }

    private function absoluteUrl(string $location, string $baseUrl): string
    {
        if (Str::startsWith($location, ['http://', 'https://'])) {
            return $location;
        }

        $base = parse_url($baseUrl);
        $origin = $base['scheme'].'://'.$base['host'].(isset($base['port']) ? ':'.$base['port'] : '');

        if (Str::startsWith($location, '//')) {
            return $base['scheme'].':'.$location;
        }

        return Str::startsWith($location, '/') ? $origin.$location : $origin.'/'.ltrim($location, '/');
    }
}
