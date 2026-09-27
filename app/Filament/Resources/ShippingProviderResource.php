<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\ShippingProviderResource\Pages;
use App\Filament\Support\TranslationTabs;
use App\Models\ShippingProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Способи доставки для оформлення замовлення (ТЗ на кошик). Driver — ключ майбутньої інтеграції. */
class ShippingProviderResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin'];

    protected static ?string $model = ShippingProvider::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string { return __('admin.resources.shipping_providers'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.sales'); }
    public static function getModelLabel(): string { return __('admin.resources.shipping_provider'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.shipping_providers'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('driver')
                    ->label(__('admin.fields.driver'))
                    ->helperText(__('admin.hints.driver'))
                    ->required()->alphaDash()->maxLength(50),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('settings.requires_address')
                    ->label(__('admin.fields.requires_address'))
                    ->helperText(__('admin.hints.requires_address'))
                    ->default(true),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true),
            ])->columns(2),
            TranslationTabs::make(['name' => true, 'description' => false]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))
                    ->description(fn(ShippingProvider $record) => $record->description),
                Tables\Columns\TextColumn::make('driver')->label(__('admin.fields.driver'))->color('gray'),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShippingProviders::route('/'),
            'create' => Pages\CreateShippingProvider::route('/create'),
            'edit'   => Pages\EditShippingProvider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
