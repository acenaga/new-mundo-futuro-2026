<?php

namespace App\Support\Sources;

use Carbon\CarbonImmutable;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class WebArticleExtractor
{
    public const int MAX_BYTES = 2_000_000;

    public const int MAX_TEXT_LENGTH = 20_000;

    private const string USER_AGENT = 'Mozilla/5.0 (compatible; MundoFuturoBot/1.0; +https://mundofuturo.ca)';

    /**
     * @var list<string>
     */
    private const array NOISE_TAGS = ['script', 'style', 'noscript', 'nav', 'header', 'footer', 'aside', 'form', 'iframe', 'svg'];

    public const int MAX_IMAGES = 8;

    /**
     * @var list<string>
     */
    private const array BLOCK_TAGS = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'li', 'blockquote', 'pre', 'div', 'section', 'article', 'br', 'tr'];

    public function extract(string $url): ExtractedSource
    {
        $url = trim($url);

        UrlSafety::assertAllowed($url);

        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'en,es;q=0.8',
            ])
                ->timeout(15)
                ->connectTimeout(5)
                ->withOptions(['allow_redirects' => ['max' => 5]])
                ->get($url);
        } catch (ConnectionException $exception) {
            throw SourceUnavailableException::unreachable($exception->getMessage());
        }

        if ($response->failed()) {
            throw SourceUnavailableException::unreachable('Código de respuesta: '.$response->status().'.');
        }

        $contentType = strtolower($response->header('Content-Type'));

        if ($contentType !== '' && ! Str::contains($contentType, ['text/html', 'application/xhtml+xml'])) {
            throw SourceUnavailableException::notHtml();
        }

        $html = $response->body();

        if (strlen($html) > self::MAX_BYTES) {
            throw SourceUnavailableException::tooLarge();
        }

        if (trim($html) === '' || ! Str::contains(strtolower($html), '<html')) {
            throw SourceUnavailableException::notHtml();
        }

        return $this->parse($url, $html);
    }

    private function parse(string $url, string $html): ExtractedSource
    {
        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($document);

        $jsonLd = $this->jsonLdArticle($xpath);

        $canonical = $this->attribute($xpath, '//link[@rel="canonical"]', 'href');
        $title = $this->meta($xpath, 'og:title') ?? $jsonLd['headline'] ?? $this->text($xpath, '//title');
        $author = $this->personName($jsonLd['author'] ?? null)
            ?? $this->nameOrNull($this->meta($xpath, 'author'))
            ?? $this->nameOrNull($this->meta($xpath, 'article:author'))
            ?? $this->nameOrNull($this->meta($xpath, 'twitter:creator'));
        $site = $this->meta($xpath, 'og:site_name') ?? $this->personName($jsonLd['publisher'] ?? null) ?? parse_url($url, PHP_URL_HOST);
        $publishedAt = $this->meta($xpath, 'article:published_time') ?? $jsonLd['datePublished'] ?? $this->attribute($xpath, '//time[@datetime]', 'datetime');

        $root = $xpath->query('//article')->item(0)
            ?? $xpath->query('//main')->item(0)
            ?? $xpath->query('//body')->item(0);

        if (! $root instanceof DOMNode) {
            throw SourceUnavailableException::noReadableContent();
        }

        foreach (self::NOISE_TAGS as $tag) {
            foreach (iterator_to_array($xpath->query('.//'.$tag, $root)) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        $images = $this->images($xpath, $root, $canonical && $this->isAbsoluteHttpUrl($canonical) ? $canonical : $url);

        $text = $this->normalizeText($this->toText($root));

        if (Str::length($text) < 200) {
            throw SourceUnavailableException::noReadableContent();
        }

        return new ExtractedSource(
            url: $this->isAbsoluteHttpUrl($canonical) ? $canonical : $url,
            title: $this->clean($title),
            author: $this->clean($author),
            site: $this->clean($site),
            publishedAt: $this->parseDate($publishedAt),
            text: Str::limit($text, self::MAX_TEXT_LENGTH, ''),
            images: $images,
        );
    }

    /**
     * Collect the content images of the article, resolved to absolute URLs.
     *
     * @return list<SourceImage>
     */
    private function images(DOMXPath $xpath, DOMNode $root, string $baseUrl): array
    {
        $images = [];

        foreach ($xpath->query('.//img', $root) as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $src = trim($node->getAttribute('src'));

            foreach (['data-src', 'data-lazy-src', 'data-original'] as $lazyAttribute) {
                if ($src === '' || Str::startsWith($src, 'data:')) {
                    $src = trim($node->getAttribute($lazyAttribute));
                }
            }

            if ($src === '' && $node->hasAttribute('srcset')) {
                $src = trim(Str::before(trim($node->getAttribute('srcset')), ' '));
            }

            if ($src === '' || Str::startsWith($src, 'data:')) {
                continue;
            }

            $width = (int) $node->getAttribute('width');
            $height = (int) $node->getAttribute('height');

            if (($width > 0 && $width < 120) || ($height > 0 && $height < 120)) {
                continue;
            }

            $absolute = $this->absoluteUrl($src, $baseUrl);

            if ($absolute === null || Str::endsWith(strtolower(parse_url($absolute, PHP_URL_PATH) ?? ''), ['.svg', '.gif', '.ico'])) {
                continue;
            }

            if (isset($images[$absolute])) {
                continue;
            }

            $images[$absolute] = new SourceImage($absolute, $this->clean($node->getAttribute('alt') ?: null));

            if (count($images) >= self::MAX_IMAGES) {
                break;
            }
        }

        return array_values($images);
    }

    private function absoluteUrl(string $src, string $baseUrl): ?string
    {
        if ($this->isAbsoluteHttpUrl($src)) {
            return $src;
        }

        $base = parse_url($baseUrl);

        if (! is_array($base) || empty($base['scheme']) || empty($base['host'])) {
            return null;
        }

        $origin = $base['scheme'].'://'.$base['host'].(isset($base['port']) ? ':'.$base['port'] : '');

        if (Str::startsWith($src, '//')) {
            return $base['scheme'].':'.$src;
        }

        if (Str::startsWith($src, '/')) {
            return $origin.$src;
        }

        $directory = rtrim(dirname($base['path'] ?? '/'), '/');

        return $origin.$directory.'/'.$src;
    }

    /**
     * Find the first JSON-LD block describing an article.
     *
     * @return array<string, mixed>
     */
    private function jsonLdArticle(DOMXPath $xpath): array
    {
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $node) {
            $decoded = json_decode($node->textContent, true);

            if (! is_array($decoded)) {
                continue;
            }

            $candidates = isset($decoded['@graph']) && is_array($decoded['@graph']) ? $decoded['@graph'] : [$decoded];

            foreach ($candidates as $candidate) {
                if (! is_array($candidate)) {
                    continue;
                }

                $type = (array) ($candidate['@type'] ?? []);

                if (array_intersect($type, ['Article', 'NewsArticle', 'BlogPosting', 'TechArticle']) !== []) {
                    return $candidate;
                }
            }
        }

        return [];
    }

    /**
     * Extract a person's or organisation's name from a JSON-LD value.
     */
    private function personName(mixed $value): ?string
    {
        if (is_array($value) && array_is_list($value)) {
            $value = $value[0] ?? null;
        }

        if (is_array($value)) {
            $value = $value['name'] ?? null;
        }

        return $this->nameOrNull(is_string($value) ? $value : null);
    }

    /**
     * Discard values that are URLs or handles rather than names.
     */
    private function nameOrNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || $this->isAbsoluteHttpUrl($value) || Str::startsWith($value, '@')) {
            return null;
        }

        return $value;
    }

    private function meta(DOMXPath $xpath, string $name): ?string
    {
        return $this->attribute($xpath, sprintf('//meta[@property="%1$s" or @name="%1$s"]', $name), 'content');
    }

    private function attribute(DOMXPath $xpath, string $query, string $attribute): ?string
    {
        $node = $xpath->query($query)->item(0);

        if (! $node instanceof \DOMElement) {
            return null;
        }

        $value = trim($node->getAttribute($attribute));

        return $value === '' ? null : $value;
    }

    private function text(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);

        $value = $node ? trim($node->textContent) : '';

        return $value === '' ? null : $value;
    }

    private function toText(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return preg_replace('/\s+/u', ' ', $node->textContent) ?? '';
        }

        $output = '';

        foreach ($node->childNodes as $child) {
            $output .= $this->toText($child);
        }

        if (in_array(strtolower($node->nodeName), self::BLOCK_TAGS, true)) {
            $output = "\n".$output."\n";
        }

        return $output;
    }

    private function normalizeText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s*\n\s*/u', "\n", $text) ?? $text;
        $text = preg_replace('/\n{2,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? $value);

        return $value === '' ? null : Str::limit($value, 255, '');
    }

    private function parseDate(?string $value): ?CarbonImmutable
    {
        if ($value === null) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function isAbsoluteHttpUrl(?string $value): bool
    {
        return $value !== null && Str::startsWith(strtolower($value), ['http://', 'https://']);
    }
}
