<?php

namespace App\Filament\Resources\Tutorials;

use App\Filament\Resources\Tutorials\Pages\CreateTutorial;
use App\Filament\Resources\Tutorials\Pages\EditTutorial;
use App\Filament\Resources\Tutorials\Pages\ListTutorials;
use App\Filament\Resources\Tutorials\Pages\ViewTutorial;
use App\Filament\Resources\Tutorials\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Tutorials\Schemas\TutorialForm;
use App\Filament\Resources\Tutorials\Schemas\TutorialInfolist;
use App\Filament\Resources\Tutorials\Tables\TutorialsTable;
use App\Models\Tutorial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TutorialResource extends Resource
{
    protected static ?string $model = Tutorial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Tutoriales';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    public static function form(Schema $schema): Schema
    {
        return TutorialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TutorialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TutorialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTutorials::route('/'),
            'create' => CreateTutorial::route('/create'),
            'view' => ViewTutorial::route('/{record}'),
            'edit' => EditTutorial::route('/{record}/edit'),
        ];
    }
}
