<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string { return __('admin.resources.currencies'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.settings'); }
    public static function getModelLabel(): string { return __('admin.resources.currency'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.currencies'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label(__('admin.fields.code') . ' (UAH, USD...)')
                ->required()
                ->maxLength(3),
            Forms\Components\TextInput::make('name')
                ->label(__('admin.fields.name'))
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('symbol')
                ->label(__('admin.fields.symbol') . ' (₴, $...)')
                ->required()
                ->maxLength(10),
            Forms\Components\TextInput::make('rate')
                ->label('Rate')
                ->required()
                ->numeric()
                ->default(1),
            Forms\Components\Toggle::make('is_default')
                ->label(__('admin.fields.is_default')),
            Forms\Components\Toggle::make('is_active')
                ->label(__('admin.fields.is_active')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('admin.fields.code'))->searchable(),
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))->searchable(),
                Tables\Columns\TextColumn::make('symbol')->label(__('admin.fields.symbol')),
                Tables\Columns\TextColumn::make('rate')->label('Rate')->numeric()->sortable(),
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
            'index'  => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit'   => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('store_id', 1);
    }
}
