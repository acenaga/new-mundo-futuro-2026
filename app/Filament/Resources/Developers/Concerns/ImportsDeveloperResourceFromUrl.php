<?php

namespace App\Filament\Resources\Developers\Concerns;

use App\Support\Sources\DeveloperResourceDraftStore;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

trait ImportsDeveloperResourceFromUrl
{
    public ?string $pendingDeveloperResourceDraftKey = null;

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->pendingDeveloperResourceDraftKey === null) {
            return parent::getSubheading();
        }

        $draft = app(DeveloperResourceDraftStore::class)->get($this->pendingDeveloperResourceDraftKey);

        return new HtmlString('<span wire:poll.4s="checkPendingDeveloperResourceDraft" aria-live="polite" class="fi-ta-text-item inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">'
            .'<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>'
            .e($this->pendingDeveloperResourceDraftMessage($draft['stage'] ?? null)).' Esto puede tardar uno o dos minutos; puedes seguir editando.</span>');
    }

    public function checkPendingDeveloperResourceDraft(): void
    {
        if ($this->pendingDeveloperResourceDraftKey === null) {
            return;
        }

        $store = app(DeveloperResourceDraftStore::class);
        $draft = $store->get($this->pendingDeveloperResourceDraftKey);
        if ($draft === null) {
            $this->pendingDeveloperResourceDraftKey = null;
            Notification::make()->title('El borrador ya no está disponible')->body('Caducó o no se pudo recuperar. Vuelve a intentarlo.')->danger()->send();

            return;
        }
        if (($draft['user_id'] ?? null) !== auth()->id()) {
            $this->pendingDeveloperResourceDraftKey = null;
            Notification::make()->title('No tienes acceso a este borrador')->danger()->send();

            return;
        }
        if ($draft['status'] === DeveloperResourceDraftStore::STATUS_PENDING) {
            return;
        }

        $store->forget($this->pendingDeveloperResourceDraftKey);
        $this->pendingDeveloperResourceDraftKey = null;
        if ($draft['status'] === DeveloperResourceDraftStore::STATUS_FAILED) {
            Notification::make()->title('No se pudo completar el recurso')->body($draft['error'] ?? 'Error desconocido.')->danger()->persistent()->send();

            return;
        }

        $this->form->fill([...$this->form->getRawState(), ...($draft['fields'] ?? [])]);
        Notification::make()->title('Campos completados, sin guardar')->body('Revisa el resultado y guarda manualmente. Publicación, destacado, logo y tecnologías no se modificaron.')->success()->persistent()->send();
        $suggestions = $draft['technology_suggestions'] ?? [];
        if ($suggestions !== []) {
            Notification::make()->title('Tecnologías sugeridas')->body(implode(', ', $suggestions).'. Añádelas manualmente si corresponden.')->warning()->persistent()->send();
        }
        foreach ($draft['evidence'] ?? [] as $label => $evidence) {
            Notification::make()->title('Evidencia: '.$label)->body((string) $evidence)->info()->persistent()->send();
        }
    }

    private function pendingDeveloperResourceDraftMessage(?string $stage): string
    {
        return match ($stage) {
            'extracting' => 'Descargando el sitio oficial.',
            'researching' => 'Verificando precios, límites y documentación oficial.',
            'generating' => 'Completando los campos del recurso.',
            'reviewing' => 'Revisando la evidencia editorial.',
            'queued' => 'El recurso está en cola para investigarse.',
            default => 'Investigando el recurso desde la URL.',
        };
    }
}
