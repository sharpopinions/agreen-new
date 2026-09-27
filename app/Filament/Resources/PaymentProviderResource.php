<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\PaymentProviderResource\Pages;
use App\Filament\Support\TranslationTabs;
use App\Models\PaymentProvider;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Способи оплати для оформлення замовлення (ТЗ на кошик). Driver — ключ майбутньої інтеграції. */
class PaymentProviderResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin'];

    protected static ?string $model = PaymentProvider::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string { return __('admin.resources.payment_providers'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.sales'); }
    public static function getModelLabel(): string { return __('admin.resources.payment_provider'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.payment_providers'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('driver')
                    ->label(__('admin.fields.driver'))
                    ->helperText(__('admin.hints.driver'))
                    ->required()->alphaDash()->maxLength(50),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\CheckboxList::make('settings.roles')
                    ->label(__('admin.fields.available_roles'))
                    ->helperText(__('admin.hints.available_roles'))
                    ->options(collect(User::CUSTOMER_ROLES)->mapWithKeys(fn($r) => [$r => __('admin.users.roles.' . $r)]))
                    ->columns(3),
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
                    ->description(fn(PaymentProvider $record) => $record->description),
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
            'index'  => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProvider::route('/create'),
            'edit'   => Pages\EditPaymentProvider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
