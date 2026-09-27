<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\OrderStatusResource\Pages;
use App\Filament\Support\TranslationTabs;
use App\Models\OrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderStatusResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin'];

    protected static ?string $model = OrderStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string { return __('admin.resources.order_statuses'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.sales'); }
    public static function getModelLabel(): string { return __('admin.resources.order_status'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.order_statuses'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('key')
                    ->label(__('admin.fields.code'))
                    ->helperText(__('admin.hints.status_key'))
                    ->required()->alphaDash()->maxLength(40)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('store_id', \App\Support\CurrentStore::id()))
                    // Код 'pending' використовує сайт для нових замовлень
                    ->disabled(fn(?OrderStatus $record) => $record?->key === OrderStatus::INITIAL),
                Forms\Components\ColorPicker::make('color')->label(__('admin.fields.color'))->required()->default('#71717a'),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_final')->label(__('admin.fields.is_final'))->helperText(__('admin.hints.is_final')),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true),
            ])->columns(2),
            TranslationTabs::make(['name' => true]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name')),
                Tables\Columns\TextColumn::make('key')->label(__('admin.fields.code'))->color('gray'),
                Tables\Columns\IconColumn::make('is_final')->label(__('admin.fields.is_final'))->boolean(),
                Tables\Columns\TextColumn::make('orders_count')->label(__('admin.resources.orders'))->counts('orders'),
                Tables\Columns\IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                // Не видаляємо статус, який використовують замовлення, і стартовий статус
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(OrderStatus $record) => $record->key === OrderStatus::INITIAL || $record->orders()->exists()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrderStatuses::route('/'),
            'create' => Pages\CreateOrderStatus::route('/create'),
            'edit'   => Pages\EditOrderStatus::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
