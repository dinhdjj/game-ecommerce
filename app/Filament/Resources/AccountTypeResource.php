<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountTypeResource\Pages;
use App\Filament\Resources\AccountTypeResource\RelationManagers;
use App\Models\AccountType;
use App\Models\User;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AccountTypeResource extends Resource
{
    protected static ?string $model = AccountType::class;

    protected static ?string $navigationGroup = 'Accounts';

    protected static ?string $navigationIcon = 'heroicon-o-puzzle';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'game' => $record->game->name,
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['game']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                BelongsToSelect::make('game_id')
                    ->relationship('game', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(125),
                TextInput::make('description')
                    ->maxLength(255),
                MultiSelect::make('usable_user_logins')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $searchQuery) => User::where('login', 'like', "%{$searchQuery}%")->limit(50)->pluck('login', 'login')),
            ])
        ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(),
                TextColumn::make('game.name')
                    ->label('Game'),
                TextColumn::make('creator.login')
                    ->label('Creator'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
            ])
        ;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountTypes::route('/'),
            'create' => Pages\CreateAccountType::route('/create'),
            'view' => Pages\ViewAccountType::route('/{record}'),
            'edit' => Pages\EditAccountType::route('/{record}/edit'),
        ];
    }
}
