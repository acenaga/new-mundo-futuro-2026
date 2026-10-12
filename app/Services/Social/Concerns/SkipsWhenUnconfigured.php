<?php

namespace App\Services\Social\Concerns;

/**
 * Devuelve el resultado vacío cuando la red no tiene credenciales en el
 * entorno (el job registra la fila con ids nulos y no reintenta).
 */
trait SkipsWhenUnconfigured
{
    /**
     * @return array{external_id: null, external_url: null}
     */
    protected static function skipped(): array
    {
        return ['external_id' => null, 'external_url' => null];
    }
}
