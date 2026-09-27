<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LanguageResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin'];

    protected static ?string $model = Language::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('admin.resources.languages'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.settings'); }
    public static function getModelLabel(): string { return __('admin.resources.language'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.languages'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label(__('admin.fields.code') . ' (uk, en...)')
                ->required()
                ->maxLength(5),
            Forms\Components\TextInput::make('name')
                ->label(__('admin.fields.name'))
                ->required()
                ->maxLength(255),
            Forms\Components\Toggle::make('is_default')
                ->label(__('admin.fields.is_default')),
            Forms\Components\Toggle::make('is_active')
                ->label(__('admin.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('admin.fields.code'))->searchable(),
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))->searchable(),
                Tables\Columns\IconColumn::make('is_default')->label(__('admin.fields.is_default'))->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit'   => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('store_id', 1);
    }
}
