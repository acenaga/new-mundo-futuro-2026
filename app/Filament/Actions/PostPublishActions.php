<?php

namespace App\Filament\Actions;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Rules\ScheduledAtHalfHour;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class PostPublishActions
{
    public static function make(): array
    {
        return [
            Action::make('publish')
                ->label('Publicar ahora')
                ->icon('heroicon-m-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('¿Publicar esta entrada? Se fijará la fecha de publicación a la hora actual.')
                ->visible(fn (Post $record): bool => $record->status !== PostStatus::Published)
                ->action(function (Post $record): void {
                    $record->forceFill([
                        'status' => PostStatus::Published,
                        'published_at' => $record->published_at ?? now(),
                    ])->save();
                }),
            Action::make('schedule')
                ->label('Programar')
                ->icon('heroicon-m-clock')
                ->color('info')
                ->visible(fn (Post $record): bool => $record->status !== PostStatus::Published)
                ->schema([
                    DateTimePicker::make('scheduled_at')
                        ->label('Fecha y hora de publicación')
                        ->required()
                        ->minutesStep(30)
                        ->formatStateUsing(
                            fn ($state) => filled($state) ? $state : ScheduledAtHalfHour::nextSlot()->format('Y-m-d H:i:s')
                        )
                        ->rule(new ScheduledAtHalfHour),
                ])
                ->action(function (Post $record, array $data): void {
                    $publishedAt = $data['scheduled_at'] instanceof CarbonInterface
                        ? $data['scheduled_at']
                        : Carbon::parse($data['scheduled_at']);

                    $record->forceFill([
                        'status' => PostStatus::Scheduled,
                        'published_at' => $publishedAt,
                    ])->save();

                    Notification::make()
                        ->title('Publicación programada')
                        ->body('Se publicará automáticamente el '.$publishedAt->translatedFormat('d M, Y H:i').'.')
                        ->success()
                        ->send();
                }),
            Action::make('unpublish')
                ->label('Despublicar')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('¿Devolver esta entrada a borrador? Dejará de ser visible en el sitio público y se cancelará la programación.')
                ->visible(fn (Post $record): bool => in_array($record->status, [PostStatus::Published, PostStatus::Scheduled], true))
                ->action(fn (Post $record) => $record->forceFill(['status' => PostStatus::Draft])->save()),
        ];
    }
}
