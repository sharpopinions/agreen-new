<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager'];

    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('admin.resources.orders'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.sales'); }
    public static function getModelLabel(): string { return __('admin.resources.order'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.orders'); }

    public static function getNavigationBadge(): ?string
    {
        $new = Order::query()->where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    // Замовлення створює лише сайт; в адмінці — перегляд і зміна статусу
    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label(__('admin.orders.status'))
                ->options(Order::STATUSES)
                ->required(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make(__('admin.orders.order'))->schema([
                Infolists\Components\TextEntry::make('number')->label('№'),
                Infolists\Components\TextEntry::make('type')->label(__('admin.orders.type'))
                    ->formatStateUsing(fn($state) => __('admin.orders.types.' . $state))
                    ->badge()->color(fn($state) => $state === 'preorder' ? 'warning' : 'gray'),
                Infolists\Components\TextEntry::make('status')->label(__('admin.orders.status'))
                    ->formatStateUsing(fn($state) => Order::STATUSES[$state] ?? $state)->badge(),
                Infolists\Components\TextEntry::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i'),
                Infolists\Components\TextEntry::make('total')->label(__('admin.orders.total'))->money('UAH'),
            ])->columns(5),

            Infolists\Components\Section::make(__('admin.orders.customer'))->schema([
                Infolists\Components\TextEntry::make('customer_name')->label(__('admin.orders.name')),
                Infolists\Components\TextEntry::make('customer_phone')->label(__('admin.orders.phone')),
                Infolists\Components\TextEntry::make('customer_email')->label('Email'),
                Infolists\Components\TextEntry::make('customer_company')->label(__('admin.orders.company'))->placeholder('—'),
            ])->columns(4),

            Infolists\Components\Section::make(__('admin.orders.shipping'))->schema([
                Infolists\Components\TextEntry::make('delivery_method')->label(__('admin.orders.delivery'))
                    ->formatStateUsing(fn($state) => Order::DELIVERY_METHODS[$state] ?? $state)->placeholder('—'),
                Infolists\Components\TextEntry::make('delivery_city')->label(__('admin.orders.city'))->placeholder('—'),
                Infolists\Components\TextEntry::make('delivery_address')->label(__('admin.orders.address'))->placeholder('—'),
                Infolists\Components\TextEntry::make('payment_method')->label(__('admin.orders.payment'))
                    ->formatStateUsing(fn($state) => Order::PAYMENT_METHODS[$state] ?? $state)->placeholder('—'),
                Infolists\Components\TextEntry::make('comment')->label(__('admin.orders.comment'))->placeholder('—')->columnSpanFull(),
            ])->columns(4)->hidden(fn(Order $record) => $record->isPreorder() && ! $record->comment),

            Infolists\Components\Section::make(__('admin.orders.items'))->schema([
                Infolists\Components\RepeatableEntry::make('items')->hiddenLabel()->schema([
                    Infolists\Components\TextEntry::make('sku')->label('SKU'),
                    Infolists\Components\TextEntry::make('name')->label(__('admin.fields.name'))->columnSpan(2),
                    Infolists\Components\TextEntry::make('price')->label(__('admin.orders.price'))->money('UAH'),
                    Infolists\Components\TextEntry::make('quantity')->label(__('admin.orders.quantity')),
                    Infolists\Components\TextEntry::make('total')->label(__('admin.orders.total'))->money('UAH'),
                ])->columns(6),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('№')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__('admin.orders.type'))
                    ->formatStateUsing(fn($state) => __('admin.orders.types.' . $state))
                    ->badge()->color(fn($state) => $state === 'preorder' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('status')->label(__('admin.orders.status'))
                    ->formatStateUsing(fn($state) => Order::STATUSES[$state] ?? $state)
                    ->badge()->color(fn($state) => match ($state) {
                        'new' => 'info', 'completed' => 'success', 'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('customer_name')->label(__('admin.orders.customer'))->searchable()
                    ->description(fn(Order $o) => $o->customer_phone),
                Tables\Columns\TextColumn::make('total')->label(__('admin.orders.total'))->money('UAH')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('admin.orders.status'))->options(Order::STATUSES),
                Tables\Filters\SelectFilter::make('type')->label(__('admin.orders.type'))->options([
                    'regular'  => __('admin.orders.types.regular'),
                    'preorder' => __('admin.orders.types.preorder'),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->label(__('admin.orders.change_status')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('items');
    }
}
