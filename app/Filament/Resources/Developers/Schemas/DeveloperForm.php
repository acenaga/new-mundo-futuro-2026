<?php

namespace App\Filament\Resources\Developers\Schemas;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResourceTechnology;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DeveloperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Section::make('Identidad y enlace')
                ->columnSpan(['default' => 1, 'lg' => 2])
                ->schema([
                    TextInput::make('name')->label('Nombre')->required()->maxLength(255)->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, Set $set) => $set('slug', Str::slug($state ?? ''))),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('external_url')->label('URL oficial')->url()->required()->maxLength(255)->unique(ignoreRecord: true)
                        ->helperText('Solo el sitio oficial del recurso; el enlace se abrirá en una pestaña nueva.'),
                    Textarea::make('summary')->label('Resumen')->required()->rows(3)->maxLength(500),
                ]),
            Section::make('Criterio editorial')
                ->columnSpan(['default' => 1, 'lg' => 2])
                ->schema([
                    Textarea::make('why_use_it')->label('¿Para qué sirve?')->required()->rows(4),
                    Textarea::make('when_not_to_use_it')->label('¿Cuándo no conviene usarlo?')->required()->rows(4),
                ]),
            Section::make('Plan gratuito')
                ->columnSpan(['default' => 1, 'lg' => 2])
                ->schema([
                    Select::make('pricing')->label('Modalidad')->options(DeveloperResourcePricing::class)->default(DeveloperResourcePricing::Freemium)->required(),
                    Textarea::make('free_tier_details')->label('Límites y detalles del plan')->required()->rows(4),
                    Toggle::make('no_card_required')->label('No requiere tarjeta')->default(false),
                ]),
            Section::make('Clasificación')
                ->columnSpan(['default' => 1, 'lg' => 2])
                ->schema([
                    Select::make('developer_resource_category_id')->label('Categoría')->relationship('category', 'name')->searchable()->preload()->required(),
                    Select::make('technologies')->label('Tecnologías')->relationship('technologies', 'name')->multiple()->searchable()->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required()->maxLength(255),
                            TextInput::make('slug')->required()->maxLength(255),
                        ])
                        ->createOptionUsing(fn (array $data): int => DeveloperResourceTechnology::create([
                            'name' => $data['name'],
                            'slug' => filled($data['slug']) ? $data['slug'] : Str::slug($data['name']),
                        ])->getKey()),
                ]),
            Section::make('Publicación')
                ->columnSpan(['default' => 1, 'lg' => 1])
                ->schema([
                    Select::make('user_id')->label('Autor')->relationship('author', 'name')->searchable()->preload()
                        ->default(fn (): ?int => auth()->id())->required()->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false),
                    Select::make('status')->label('Estado')->options(DeveloperResourceStatus::class)->default(DeveloperResourceStatus::Draft)->required(),
                    DateTimePicker::make('published_at')->label('Publicado el'),
                    DateTimePicker::make('last_verified_at')->label('Verificado el'),
                    Toggle::make('is_featured')->label('Destacado')->default(false),
                    FileUpload::make('logo_path')->label('Logotipo')->disk('public')->visibility('public')->directory('developer-resources/logos')
                        ->image()->imageEditor()->maxSize(2048)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Súbelo solo si el uso editorial está permitido. Si lo omites, se usarán iniciales.'),
                ]),
        ]);
    }
}
