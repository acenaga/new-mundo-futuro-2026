<?php

namespace App\Contracts;

use App\Models\Post;

interface SocialPublisher
{
    /**
     * Publica el post en su red y devuelve el identificador externo.
     *
     * @return array{external_id: ?string, external_url: ?string}
     */
    public function publish(Post $post): array;
}
