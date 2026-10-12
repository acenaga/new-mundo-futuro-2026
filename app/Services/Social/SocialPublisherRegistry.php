<?php

namespace App\Services\Social;

use App\Contracts\SocialPublisher;
use InvalidArgumentException;

class SocialPublisherRegistry
{
    private const NETWORKS = [
        'discord' => DiscordPublisher::class,
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
