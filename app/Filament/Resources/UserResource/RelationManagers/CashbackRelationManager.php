<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Services\Cashback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Кешбек клієнта: автоматичні нарахування за виконані замовлення + ручні корекції менеджера. */
class CashbackRelationManager extends RelationManager
{
    protected static string $relationship = 'cashbackTransactions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.users.cashback');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ! $ownerRecord->isStaff();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')->label(__('admin.users.amount'))
                ->numeric()->required()->notIn([0])->helperText(__('admin.users.amount_hint')),
            Forms\Components\TextInput::make('description')->label(__('admin.users.description'))
                ->required()->maxLength(255)->placeholder('Списано при оплаті #A-2026-00042'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description(fn() => __('admin.users.balance') . ': ' . number_format(app(Cashback::class)->balance($this->getOwnerRecord()), 2, '.', ' ') . ' ₴')
            ->columns([
                Tables\Columns\TextColumn::make('amount')->label(__('admin.users.amount'))
                    ->color(fn($state) => $state < 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn($state) => ($state > 0 ? '+' : '') . number_format((float) $state, 2, '.', ' ')),
                Tables\Columns\TextColumn::make('description')->label(__('admin.users.description')),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\DeleteAction::make()]);
    }
}
