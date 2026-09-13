<?php

namespace App\Support\Sources;

use RuntimeException;

class SourceUnavailableException extends RuntimeException
{
    public static function invalidUrl(): self
    {
        return new self('La URL no es válida. Debe comenzar por http:// o https://.');
    }

    public static function hostNotAllowed(): self
    {
        return new self('No se permite importar contenido desde direcciones internas o privadas.');
    }

    public static function unreachable(string $reason = ''): self
    {
        return new self(trim('No se pudo descargar la página. '.$reason));
    }

    public static function notHtml(): self
    {
        return new self('La URL no devolvió una página HTML.');
    }

    public static function tooLarge(): self
    {
        return new self('La página es demasiado grande para procesarla.');
    }

    public static function noReadableContent(): self
    {
        return new self('No se encontró contenido legible en la página.');
    }

    public static function generationFailed(string $reason = ''): self
    {
        return new self(trim('El agente no pudo generar el borrador. '.$reason));
    }
}
