<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Замовлення клієнта (лише перегляд; редагування — у розділі «Замовлення»). */
class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.users.orders');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ! $ownerRecord->isStaff();
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('status.translations'))
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('№'),
                Tables\Columns\TextColumn::make('status.name')->label(__('admin.orders.status'))->badge()
                    ->color(fn(Order $record) => $record->status?->color ? Color::hex($record->status->color) : 'gray'),
                Tables\Columns\TextColumn::make('total')->label(__('admin.orders.total'))->money('UAH'),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(fn(Order $record) => OrderResource::getUrl('view', ['record' => $record]));
    }
}
