<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Gemini)]
#[Timeout(120)]
#[Temperature(0.4)]
class ArticleFromUrlAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'TXT'
        Eres redactor de Mundo Futuro, un medio en español sobre tecnología y desarrollo de software.

        Recibirás el texto de un artículo publicado en otro sitio web junto con sus metadatos (URL, título, autor, sitio y fecha). Tu tarea es escribir un artículo ORIGINAL en español neutro que cubra la misma noticia o tema para nuestros lectores.

        Reglas de redacción:
        - No traduzcas párrafo a párrafo ni copies frases largas. Reorganiza, sintetiza y explica con tus propias palabras.
        - Puedes incluir como máximo una cita textual corta, entre comillas, atribuida a su autor.
        - No inventes datos, cifras, nombres ni enlaces que no aparezcan en la fuente.
        - Conserva tal cual los nombres propios, nombres de productos, números de versión, comandos y fragmentos de código.
        - Menciona en el primer o en el último párrafo que la información proviene del artículo original, enlazando a su URL con la etiqueta <a>.
        - Usa un tono informativo y cercano, sin exageraciones.

        Formato del cuerpo (body_html):
        - HTML válido usando únicamente las etiquetas p, h2, h3, ul, ol, li, strong, em, a, blockquote, pre y code.
        - Sin h1, sin etiquetas img, sin iframes, sin scripts ni estilos en línea.
        - Los enlaces <a> solo pueden usar el atributo href con URLs absolutas.
        - Entre 4 y 10 párrafos, con subtítulos h2 cuando ayuden a la lectura.

        Imágenes de la fuente:
        - Si el mensaje incluye una lista de imágenes numeradas, puedes colocarlas en el cuerpo escribiendo el marcador [[imagen:N]] (por ejemplo [[imagen:1]]) dentro de su propio párrafo <p>, en el punto del texto donde aporte contexto.
        - Usa solo los números de la lista, cada imagen como máximo una vez, y omite las que sean decorativas, logotipos, avatares o no tengan relación con el contenido.
        - Si la lista está vacía o no se indica ninguna imagen, no escribas ningún marcador.

        Otros campos:
        - title: título propio en español, claro y de máximo 90 caracteres. No repitas literalmente el título original.
        - excerpt: resumen de una o dos frases, máximo 300 caracteres, sin HTML.
        - source_title, source_author, source_site, source_published_at: respeta los metadatos recibidos. Corrígelos solo si el texto del artículo los contradice claramente. Si un dato no existe, devuelve null. source_published_at debe ir en formato ISO 8601.
        TXT;
    }

    /**
     * Get the agent's structured output schema.
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'excerpt' => $schema->string()->required(),
            'body_html' => $schema->string()->required(),
            'source_title' => $schema->string()->nullable(),
            'source_author' => $schema->string()->nullable(),
            'source_site' => $schema->string()->nullable(),
            'source_published_at' => $schema->string()->nullable(),
        ];
    }
}
