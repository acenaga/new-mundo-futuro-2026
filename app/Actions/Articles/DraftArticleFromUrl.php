<?php

namespace App\Actions\Articles;

use App\Ai\Agents\ArticleFromUrlAgent;
use App\Support\Images\CoverImageGenerator;
use App\Support\Sources\ArticleDraft;
use App\Support\Sources\ExtractedSource;
use App\Support\Sources\SourceImageImporter;
use App\Support\Sources\SourceUnavailableException;
use App\Support\Sources\WebArticleExtractor;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Laravel\Ai\Exceptions\AiException;
use Throwable;

class DraftArticleFromUrl
{
    /**
     * @var list<string>
     */
    private const array ALLOWED_TAGS = ['p', 'h2', 'h3', 'ul', 'ol', 'li', 'strong', 'em', 'a', 'blockquote', 'pre', 'code', 'br'];

    public function __construct(
        private readonly WebArticleExtractor $extractor,
        private readonly ArticleFromUrlAgent $agent,
        private readonly SourceImageImporter $images,
        private readonly CoverImageGenerator $covers,
    ) {}

    /**
     * Seconds allowed for the whole pipeline (download, text generation, images and cover).
     */
    public const int TIME_LIMIT = 300;

    /**
     * Build a Spanish article draft from an external URL.
     *
     * The pipeline performs several slow network calls, so the PHP execution time limit
     * is raised for this request; the web server timeout may still need to allow it.
     */
    public function __invoke(string $url, bool $importImages = true, bool $generateCover = true): ArticleDraft
    {
        @set_time_limit(self::TIME_LIMIT);

        $source = $this->extractor->extract($url);
        $warnings = [];

        try {
            $response = $this->agent->prompt($this->buildPrompt($source, $importImages));
        } catch (AiException $exception) {
            throw SourceUnavailableException::generationFailed($exception->getMessage());
        }

        $draft = $response->toArray();

        $title = trim((string) ($draft['title'] ?? ''));
        $body = $this->sanitizeHtml((string) ($draft['body_html'] ?? ''));

        if ($title === '' || trim(strip_tags($body)) === '') {
            throw SourceUnavailableException::generationFailed('La respuesta no incluía título o contenido.');
        }

        $body = $importImages
            ? $this->replaceImageTokens($body, $source, $warnings)
            : $this->removeImageTokens($body);

        $excerpt = Str::limit(trim(strip_tags((string) ($draft['excerpt'] ?? ''))), 500, '');
        $publishedAt = $source->publishedAt ?? $this->normalizeDate($draft['source_published_at'] ?? null);

        $fields = [
            'title' => Str::limit($title, 255, ''),
            'slug' => Str::slug($title),
            'excerpt' => $excerpt,
            'body' => $body,
            'source_url' => $source->url,
            'source_title' => $source->title ?? $this->stringOrNull($draft['source_title'] ?? null),
            'source_author' => $source->author ?? $this->stringOrNull($draft['source_author'] ?? null),
            'source_site' => $source->site ?? $this->stringOrNull($draft['source_site'] ?? null),
            'source_published_at' => $publishedAt?->toDateTimeString(),
        ];

        if ($generateCover) {
            try {
                $fields['cover_image_path'] = $this->covers->generate(
                    $fields['title'],
                    $excerpt,
                    $this->stringOrNull($draft['cover_concept'] ?? null),
                    $this->stringOrNull($draft['cover_headline'] ?? null),
                );
            } catch (Throwable $exception) {
                report($exception);

                $warnings[] = 'No se pudo generar la imagen de portada. Puedes subir una manualmente.';
            }
        }

        return new ArticleDraft($fields, $warnings);
    }

    private function buildPrompt(ExtractedSource $source, bool $includeImages): string
    {
        $lines = [
            'Metadatos de la fuente:',
            '- URL: '.$source->url,
            '- Título: '.($source->title ?? 'desconocido'),
            '- Autor: '.($source->author ?? 'desconocido'),
            '- Sitio: '.($source->site ?? 'desconocido'),
            '- Fecha de publicación: '.($source->publishedAt?->toIso8601String() ?? 'desconocida'),
            '',
        ];

        if ($includeImages && $source->images !== []) {
            $lines[] = 'Imágenes disponibles en la fuente (usa [[imagen:N]] para colocarlas):';

            foreach ($source->images as $index => $image) {
                $lines[] = sprintf('%d. %s%s', $index + 1, $image->url, $image->alt ? ' — '.$image->alt : '');
            }

            $lines[] = '';
        }

        return implode("\n", [
            ...$lines,
            'Texto del artículo original:',
            '"""',
            $source->text,
            '"""',
        ]);
    }

    /**
     * @param  list<string>  $warnings
     */
    private function replaceImageTokens(string $body, ExtractedSource $source, array &$warnings): string
    {
        $imported = [];
        $failed = 0;

        $body = preg_replace_callback('/\[\[imagen:(\d+)\]\]/u', function (array $matches) use ($source, &$imported, &$failed): string {
            $index = (int) $matches[1] - 1;
            $image = $source->images[$index] ?? null;

            if ($image === null || isset($imported[$index])) {
                return '';
            }

            $url = $this->images->import($image->url);
            $imported[$index] = true;

            if ($url === null) {
                $failed++;

                return '';
            }

            return '<img src="'.e($url).'" alt="'.e($image->alt ?? '').'">';
        }, $body) ?? $body;

        if ($failed > 0) {
            $warnings[] = $failed === 1
                ? 'Una imagen de la fuente no se pudo descargar y se omitió.'
                : $failed.' imágenes de la fuente no se pudieron descargar y se omitieron.';
        }

        return $this->removeEmptyParagraphs($body);
    }

    private function removeImageTokens(string $body): string
    {
        return $this->removeEmptyParagraphs(preg_replace('/\[\[imagen:\d+\]\]/u', '', $body) ?? $body);
    }

    private function removeEmptyParagraphs(string $body): string
    {
        return preg_replace('/<p>\s*<\/p>/u', '', $body) ?? $body;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $html = preg_replace('/<(\/?)h1\b[^>]*>/i', '<$1h2>', $html) ?? $html;
        $html = strip_tags($html, self::ALLOWED_TAGS);

        // Keep only the href attribute on anchors and drop every attribute elsewhere.
        $html = preg_replace_callback('/<a\b[^>]*>/i', function (array $matches): string {
            if (preg_match('/\bhref\s*=\s*(["\'])(https?:\/\/[^"\']+)\1/i', $matches[0], $href)) {
                return '<a href="'.e($href[2]).'" target="_blank" rel="noopener noreferrer nofollow">';
            }

            return '<a>';
        }, $html) ?? $html;

        $html = preg_replace('/<(?!a\b)([a-z0-9]+)\b[^>]*>/i', '<$1>', $html) ?? $html;

        return trim($html);
    }

    private function normalizeDate(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim(Str::before(trim($value), "\n"));

        return $value === '' || strtolower($value) === 'null' ? null : Str::limit($value, 255, '');
    }
}
