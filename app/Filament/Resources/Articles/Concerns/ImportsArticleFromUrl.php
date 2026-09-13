<?php

namespace App\Filament\Resources\Articles\Concerns;

use App\Support\Sources\ArticleDraftStore;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Lets a create/edit article page wait for a draft generated in the background
 * and fill the form with it once it is ready.
 */
trait ImportsArticleFromUrl
{
    public ?string $pendingDraftKey = null;

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->pendingDraftKey === null) {
            return parent::getSubheading();
        }

        return new HtmlString(
            '<span wire:poll.4s="checkPendingDraft" class="fi-ta-text-item inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">'
            .'<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">'
            .'<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>'
            .'<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>'
            .'Generando borrador desde la URL. Esto puede tardar uno o dos minutos; puedes seguir editando.'
            .'</span>'
        );
    }

    public function checkPendingDraft(): void
    {
        if ($this->pendingDraftKey === null) {
            return;
        }

        $store = app(ArticleDraftStore::class);
        $draft = $store->get($this->pendingDraftKey);

        if ($draft === null) {
            $this->pendingDraftKey = null;

            Notification::make()
                ->title('El borrador ya no está disponible')
                ->body('Caducó o no se pudo recuperar. Vuelve a importar la URL.')
                ->danger()
                ->send();

            return;
        }

        if ($draft['status'] === ArticleDraftStore::STATUS_PENDING) {
            return;
        }

        $store->forget($this->pendingDraftKey);
        $this->pendingDraftKey = null;

        if ($draft['status'] === ArticleDraftStore::STATUS_FAILED) {
            Notification::make()
                ->title('No se pudo generar el borrador')
                ->body($draft['error'] ?? 'Error desconocido.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $this->form->fill([
            ...$this->form->getRawState(),
            ...($draft['fields'] ?? []),
        ]);

        Notification::make()
            ->title('Borrador generado')
            ->body('Revisa el contenido, las imágenes y la cita de la fuente antes de guardar.')
            ->success()
            ->send();

        foreach ($draft['warnings'] ?? [] as $warning) {
            Notification::make()
                ->title('Aviso')
                ->body($warning)
                ->warning()
                ->send();
        }
    }
}
