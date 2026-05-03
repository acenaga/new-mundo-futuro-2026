<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Signature('media:cleanup-orphans {--dry-run : Listar huérfanos sin eliminar}')]
#[Description('Elimina archivos adjuntos del rich editor que ya no están referenciados en el contenido')]
class CleanupOrphanedMediaCommand extends Command
{
    /** @var array<string, string> */
    private const COLLECTION_FIELD_MAP = [
        'post-body-attachments' => 'body',
        'course-description-attachments' => 'description',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $count = 0;

        $media = Media::query()
            ->whereIn('collection_name', array_keys(self::COLLECTION_FIELD_MAP))
            ->with('model')
            ->get();

        foreach ($media as $item) {
            $model = $item->model;

            if (! $model) {
                continue;
            }

            $field = self::COLLECTION_FIELD_MAP[$item->collection_name];
            $content = (string) ($model->$field ?? '');

            if (str_contains($content, 'id="'.$item->uuid.'"')) {
                continue;
            }

            $this->line("Huérfano: [{$item->model_type}#{$item->model_id}] {$item->file_name} ({$item->uuid})");
            $count++;

            if (! $dryRun) {
                $item->delete();
            }
        }

        $verb = $dryRun ? 'encontrados' : 'eliminados';
        $this->info("{$count} adjuntos huérfanos {$verb}.");

        return self::SUCCESS;
    }
}
