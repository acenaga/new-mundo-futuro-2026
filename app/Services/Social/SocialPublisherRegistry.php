<?php

namespace App\Services\Social;

use App\Contracts\SocialPublisher;
use App\Services\Social\Publishers\DiscordPublisher;
use App\Services\Social\Publishers\FacebookPagePublisher;
use App\Services\Social\Publishers\InstagramPublisher;
use App\Services\Social\Publishers\ThreadsPublisher;
use App\Services\Social\Publishers\XPublisher;
use InvalidArgumentException;

class SocialPublisherRegistry
{
    private const NETWORKS = [
        'discord' => DiscordPublisher::class,
        'x' => XPublisher::class,
        'facebook' => FacebookPagePublisher::class,
        'threads' => ThreadsPublisher::class,
        'instagram' => InstagramPublisher::class,
    ];

    /**
     * @return list<string>
     */
    public function enabledNetworks(): array
    {
        $configured = (array) config('services.social.networks', []);

        return array_values(array_intersect($configured, array_keys(self::NETWORKS)));
    }

    public function for(string $network): SocialPublisher
    {
        $class = self::NETWORKS[$network] ?? null;

        if (! $class) {
            throw new InvalidArgumentException("No hay publicador registrado para la red [{$network}].");
        }

        return app($class);
    }
}
