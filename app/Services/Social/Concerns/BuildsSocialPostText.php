<?php

namespace App\Services\Social\Concerns;

use App\Models\Post;
use Illuminate\Support\Str;

trait BuildsSocialPostText
{
    /**
     * Texto plano a partir del excerpt o del body, sin HTML.
     */
    protected function plainText(Post $post): string
    {
        $text = $post->excerpt ?: trim(strip_tags((string) $post->body));

        return preg_replace('/[\r\n]+/', ' ', $text) ?? '';
    }

    /**
     * Cuerpo del mensaje con título + texto plano, limitado a $limit caracteres
     * dejando aire para la URL.
     */
    protected function message(Post $post, int $limit): string
    {
        $title = trim($post->title);
        $text = $this->plainText($post);

        $body = $title !== '' && mb_stripos($text, $title) !== 0
            ? $title."\n\n".$text
            : $text;

        return Str::limit(trim($body), $limit, '…');
    }
}
