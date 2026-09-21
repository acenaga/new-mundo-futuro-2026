<?php

namespace App\Support\Sources;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class DeveloperResourceExtractor
{
    private const int MAX_BODY_BYTES = 1_500_000;

    private const int MAX_TEXT_LENGTH = 45_000;

    private const int MAX_RESEARCH_LINKS = 2;

    /** @var list<string> */
    private const array RESEARCH_KEYWORDS = ['pricing', 'price', 'plans', 'plan', 'billing', 'documentation', 'docs', 'limits', 'free', 'gratis'];

    public function __construct(private readonly SafeExternalHttpClient $http) {}

    public function extract(string $url): DeveloperResourceSource
    {
        UrlSafety::assertAllowed($url);
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $initial = $this->extractPage($url);
        $links = $this->researchLinks($initial['html'], $initial['url'], $host);
        $pages = [[
            'url' => $initial['url'],
            'title' => $initial['title'],
            'text' => $initial['text'],
        ]];

        foreach ($links as $link) {
            $page = $this->extractPage($link, $host);
            $pages[] = ['url' => $page['url'], 'title' => $page['title'], 'text' => $page['text']];
        }

        return new DeveloperResourceSource($initial['url'], $initial['title'] ?? '', $pages);
    }

    /** @return array{url: string, title: ?string, text: string, html: string} */
    private function extractPage(string $url, ?string $requiredHost = null): array
    {
        $response = $this->http->get($url, 'text/html,application/xhtml+xml', $requiredHost);
        $this->assertHtml($response);
        $html = $response->body();

        if (strlen($html) > self::MAX_BODY_BYTES) {
            throw SourceUnavailableException::tooLarge();
        }

        [$document, $xpath] = $this->document($html);
        $canonical = $this->canonicalUrl($xpath, $url, $requiredHost ?? strtolower((string) parse_url($url, PHP_URL_HOST)));
        $text = implode("\n", array_filter([
            $this->firstMeta($xpath, ['//meta[@name="description"]/@content', '//meta[@property="og:description"]/@content']),
            $this->visibleText($document),
            $this->jsonLd($xpath),
        ]));

        if (Str::length($text) < 80) {
            throw SourceUnavailableException::noReadableContent();
        }

        return [
            'url' => $canonical,
            'title' => $this->firstMeta($xpath, ['//meta[@property="og:title"]/@content', '//title/text()']),
            'text' => Str::limit($text, self::MAX_TEXT_LENGTH, ''),
            'html' => $html,
        ];
    }

    private function assertHtml(Response $response): void
    {
        if (! $response->successful()) {
            throw SourceUnavailableException::unreachable('El sitio respondió con HTTP '.$response->status().'.');
        }

        if (! Str::startsWith(strtolower((string) $response->header('Content-Type')), ['text/html', 'application/xhtml+xml'])) {
            throw SourceUnavailableException::notHtml();
        }
    }

    /** @return array{0: DOMDocument, 1: DOMXPath} */
    private function document(string $html): array
    {
        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument;
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return [$document, new DOMXPath($document)];
    }

    private function visibleText(DOMDocument $document): string
    {
        $xpath = new DOMXPath($document);
        foreach ($xpath->query('//script|//style|//noscript|//svg|//nav|//footer|//form') ?: [] as $node) {
            $node->parentNode?->removeChild($node);
        }

        return trim((string) preg_replace('/\s+/u', ' ', $document->textContent ?? ''));
    }

    private function jsonLd(DOMXPath $xpath): ?string
    {
        $items = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $node) {
            $json = trim($node->textContent ?? '');
            if ($json !== '') {
                $items[] = $json;
            }
        }

        return $items === [] ? null : 'Datos estructurados: '.implode(' ', $items);
    }

    private function canonicalUrl(DOMXPath $xpath, string $fallback, string $host): string
    {
        $href = $this->firstMeta($xpath, ['//link[translate(@rel, "CANONICAL", "canonical")="canonical"]/@href']);
        $candidate = $href === null ? $fallback : $this->absoluteUrl($href, $fallback);

        if (strcasecmp((string) parse_url($candidate, PHP_URL_HOST), $host) !== 0) {
            return $fallback;
        }

        UrlSafety::assertAllowed($candidate);

        return $candidate;
    }

    /** @return list<string> */
    private function researchLinks(string $html, string $baseUrl, string $host): array
    {
        [, $xpath] = $this->document($html);
        $links = [];

        foreach ($xpath->query('//a[@href]') ?: [] as $link) {
            $href = trim((string) $link->attributes?->getNamedItem('href')?->nodeValue);
            $label = strtolower($href.' '.trim($link->textContent ?? ''));
            if (! Str::contains($label, self::RESEARCH_KEYWORDS)) {
                continue;
            }

            $candidate = $this->absoluteUrl($href, $baseUrl);
            if (strcasecmp((string) parse_url($candidate, PHP_URL_HOST), $host) !== 0 || in_array($candidate, $links, true)) {
                continue;
            }

            try {
                UrlSafety::assertAllowed($candidate);
            } catch (SourceUnavailableException) {
                continue;
            }

            $links[] = $candidate;
            if (count($links) === self::MAX_RESEARCH_LINKS) {
                break;
            }
        }

        return $links;
    }

    /** @param list<string> $queries */
    private function firstMeta(DOMXPath $xpath, array $queries): ?string
    {
        foreach ($queries as $query) {
            $node = $xpath->query($query)?->item(0);
            $value = trim($node?->nodeValue ?? '');
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function absoluteUrl(string $href, string $baseUrl): string
    {
        if (Str::startsWith($href, ['http://', 'https://'])) {
            return $href;
        }

        $base = parse_url($baseUrl);
        $origin = $base['scheme'].'://'.$base['host'].(isset($base['port']) ? ':'.$base['port'] : '');

        if (Str::startsWith($href, '//')) {
            return $base['scheme'].':'.$href;
        }

        return Str::startsWith($href, '/') ? $origin.$href : $origin.'/'.ltrim($href, '/');
    }
}
